<?php
// Somente bancos efêmeros, explicitamente configurados; nunca carrega .env.
require_once __DIR__.'/../../src/infra/Database/DatabaseMigrator.php';
require_once __DIR__.'/../../src/modules/familiares/repository/AsiloFamiliarRepository.php';
require_once __DIR__.'/../../src/modules/familiares/services/AsiloFamiliarService.php';
foreach (['PORTA'=>'PORT','USUARIO'=>'USER','SENHA'=>'PASSWORD','NOME'=>'NAME'] as $campo=>$alias) {
    if (getenv('ASILOSOFT_TEST_DB_'.$campo) === false && getenv('ASILOSOFT_TEST_DB_'.$alias) !== false) putenv('ASILOSOFT_TEST_DB_'.$campo.'='.getenv('ASILOSOFT_TEST_DB_'.$alias));
}
foreach (['HOST','PORTA','USUARIO','SENHA','NOME'] as $campo) {
    if (getenv('ASILOSOFT_TEST_DB_'.$campo) === false) throw new RuntimeException('Defina ASILOSOFT_TEST_DB_'.$campo);
}
$db=getenv('ASILOSOFT_TEST_DB_NOME');
if (!preg_match('/^asilosoft_test_[a-z0-9_]+$/', $db)) throw new RuntimeException('Nome de banco de teste inválido.');
$dsn='mysql:host='.getenv('ASILOSOFT_TEST_DB_HOST').';port='.getenv('ASILOSOFT_TEST_DB_PORTA').';charset=utf8mb4';
$options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];
$connect=fn()=>new PDO($dsn.';dbname='.$db,getenv('ASILOSOFT_TEST_DB_USUARIO'),getenv('ASILOSOFT_TEST_DB_SENHA'),$options);
if (($argv[1]??'')==='--principal') {
    $repo=new AsiloFamiliarRepository($connect());
    for($i=0;$i<12;$i++) $repo->vincular(1,(int)$argv[2],'Filho',true);
    exit;
}
function check(bool $ok,string $label):void { if(!$ok) throw new RuntimeException($label); }
function rejeita(callable $f):void { try {$f();}catch(DomainException $e){return;}throw new RuntimeException('Esperada rejeição de domínio.'); }
$server=new PDO($dsn,getenv('ASILOSOFT_TEST_DB_USUARIO'),getenv('ASILOSOFT_TEST_DB_SENHA'),$options);
// Sem IF NOT EXISTS: jamais apaga uma base preexistente.
$server->exec("CREATE DATABASE `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
try {
    $pdo=$connect(); (new DatabaseMigrator())->migrate($pdo,__DIR__.'/../../src/infra/Database/migrations');
    $pdo->exec("INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES ('Familiar A','a@example.test','hash','Familiar'),('Familiar B','b@example.test','hash','Familiar'),('Profissional','p@example.test','hash','Profissional de Saúde')");
    $pdo->exec("INSERT INTO idosos(nome,cpf,identificador) VALUES ('Idoso A','00000000001','TEST-A'),('Idoso B','00000000002','TEST-B')");
    $repo=new AsiloFamiliarRepository($pdo);$service=new AsiloFamiliarService($repo);
    $dados=['nome'=>'Contato A','telefone'=>'123','email'=>'a@example.test','usuario_id'=>1];
    $a=$service->salvar($dados); $b=$service->salvar(['nome'=>'Contato B','telefone'=>'456','usuario_id'=>2]);
    unset($dados['usuario_id']);$service->salvar($dados,(int)$a['id']);
    check((int)$repo->buscar(1)['usuario_id']===1,'T01 preserva associação ausente');
    rejeita(fn()=>$service->salvar(['nome'=>'X','telefone'=>'1','usuario_id'=>3]));
    rejeita(fn()=>$service->salvar(['nome'=>'X','telefone'=>'1','usuario_id'=>1]));
    rejeita(fn()=>$service->salvar(['nome'=>'X','telefone'=>'1','email'=>'inválido']));
    rejeita(fn()=>$service->salvar(['nome'=>str_repeat('x',121),'telefone'=>'1']));
    $service->vincular(1,1,['parentesco'=>'Filha','responsavel_principal'=>1]);
    $service->vincular(2,1,['parentesco'=>'Filha']);
    $service->vincular(1,2,['parentesco'=>'Filho','responsavel_principal'=>1]);
    $service->vincular(1,2,['parentesco'=>'Filho','responsavel_principal'=>1]);
    check(count($repo->idososDoUsuario(1))===2 && count($repo->porIdoso(1))===2,'T02 vários vínculos sem duplicar');
    rejeita(fn()=>$service->vincular(1,999,['parentesco'=>'Filho','responsavel_principal'=>1]));
    check((int)$repo->porIdoso(1)[0]['id']===2,'Rollback mantém principal');
    $pdo->exec("INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar) VALUES (1,3,'PUBLICO',1),(1,3,'PRIVADO',0)");
    check(count($repo->acompanhamentosPublicos(1,1))===1 && $repo->acompanhamentosPublicos(1,1)[0]['descricao']==='PUBLICO','T05 apenas público');
    check($repo->acompanhamentosPublicos(2,2)===null,'T06 troca id bloqueada');
    check($repo->acompanhamentosPublicos(1,2)===[],'T06 idoso sem publicação');
    $service->desvincular(1,1);check($repo->acompanhamentosPublicos(1,1)===null,'T06 vínculo removido');
    $pdo->exec('UPDATE idosos SET ativo=0 WHERE id=2');
    check($repo->idososDoUsuario(1)===[] && $repo->acompanhamentosPublicos(1,2)===null,'T06 inativo lista e URL');
    $pdo->exec('UPDATE usuarios SET ativo=0 WHERE id=2');
    check($repo->idososDoUsuario(2)===[] && $repo->acompanhamentosPublicos(2,1)===null,'T06 conta desativada');
    check($repo->idososDoUsuario(999)===[],'Conta sem cadastro');
    $pdo->exec('UPDATE usuarios SET ativo=1 WHERE id=2');
    $processos=[];
    for($i=1;$i<=2;$i++) $processos[]=proc_open([PHP_BINARY,__FILE__,'--principal',(string)$i],[0=>['file','/dev/null','r'],1=>STDOUT,2=>STDERR],$pipes);
    foreach($processos as $p) check(is_resource($p) && proc_close($p)===0,'Concorrência sem falhas');
    check((int)$pdo->query('SELECT COUNT(*) FROM idosos_familiares WHERE idoso_id=1 AND responsavel_principal=1')->fetchColumn()===1,'T02 concorrência mantém um principal');
    $service->salvar($dados+['usuario_id'=>''],1);check($repo->buscar(1)['usuario_id']===null,'T01 remoção explícita');
    echo "OK Familiares: T01 T02 T05 T06, rollback e concorrência (24 operações).\n";
} finally { $server->exec("DROP DATABASE `$db`"); }

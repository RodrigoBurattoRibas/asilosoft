<?php
declare(strict_types=1);
require_once dirname(__DIR__, 3) . '/src/core/Config/Environment.php';
require_once dirname(__DIR__, 3) . '/src/infra/Database/DatabaseMigrator.php';

$name=Environment::get('ASILOSOFT_TEST_DB_NOME');
if($name===''||!str_starts_with($name,'asilosoft_test_')){fwrite(STDERR,"Defina ASILOSOFT_TEST_DB_NOME com o prefixo asilosoft_test_.\n");exit(1);}
$host=Environment::get('ASILOSOFT_TEST_DB_HOST','127.0.0.1');$port=Environment::get('ASILOSOFT_TEST_DB_PORTA','3306');$user=Environment::get('ASILOSOFT_TEST_DB_USUARIO');$pass=Environment::get('ASILOSOFT_TEST_DB_SENHA');
function ok(bool $value,string $message):void {if(!$value)throw new RuntimeException($message);}
function scalar(PDO $pdo,string $sql,array $args=[]):mixed{$s=$pdo->prepare($sql);$s->execute($args);return $s->fetchColumn();}
function rejects(PDO $pdo,string $sql,array $args=[],string $contains=''):void{try{$pdo->prepare($sql)->execute($args);}catch(Throwable $e){ok($contains===''||str_contains($e->getMessage(),$contains),'Erro SQL inesperado: '.$e->getMessage());return;}throw new RuntimeException('A operação deveria falhar.');}
try {
 $server=new PDO("mysql:host=$host;port=$port;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $server->exec("DROP DATABASE IF EXISTS `$name`");$server->exec("CREATE DATABASE `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
 $pdo=new PDO("mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $versions=DatabaseMigrator::migrate($pdo,dirname(__DIR__,3).'/src/infra/Database/migrations');ok(count($versions)===5,'Todas as migrações devem ser aplicadas.');ok(DatabaseMigrator::migrate($pdo,dirname(__DIR__,3).'/src/infra/Database/migrations')===[],'A segunda execução deve ser vazia.');
 $hash=password_hash('x',PASSWORD_DEFAULT);$pdo->prepare("INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES(?,?,?,'Profissional de Saúde')")->execute(['Prof','prof@test.local',$hash]);$prof=(int)$pdo->lastInsertId();$pdo->prepare("INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES(?,?,?,'Familiar')")->execute(['Fam','fam@test.local',$hash]);$famUser=(int)$pdo->lastInsertId();
 $pdo->prepare('INSERT INTO quartos(codigo,capacidade) VALUES(?,?)')->execute(['T-1',2]);$room=(int)$pdo->lastInsertId();$pdo->prepare('INSERT INTO idosos(nome,cpf,identificador,quarto_id) VALUES(?,?,?,?)')->execute(['Teste','00000000000','TESTE-1',$room]);$idoso=(int)$pdo->lastInsertId();
 $pdo->prepare('INSERT INTO familiares(usuario_id,nome,telefone) VALUES(?,?,?)')->execute([$famUser,'Familiar','000']);$familiar=(int)$pdo->lastInsertId();$pdo->prepare('INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco) VALUES(?,?,?)')->execute([$idoso,$familiar,'Filha']);rejects($pdo,'INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco) VALUES(?,?,?)',[$idoso,$familiar,'Filha']);
 $pdo->prepare('INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar) VALUES(?,?,?,0)')->execute([$idoso,$prof,'conteúdo confidencial']);$private=(int)$pdo->lastInsertId();ok((int)scalar($pdo,'SELECT COUNT(*) FROM vw_acompanhamento_familiar WHERE familiar_id=? AND id=?',[$familiar,$private])===0,'A view familiar vazou registro privado.');
 $pdo->prepare('INSERT INTO materiais(nome,unidade_medida,estoque_minimo) VALUES(?,?,?)')->execute(['Material teste','un',1]);$material=(int)$pdo->lastInsertId();ok((float)scalar($pdo,'SELECT quantidade_atual FROM estoques WHERE material_id=?',[$material])===0.0,'Saldo inicial deve ser zero.');rejects($pdo,"INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem) VALUES(?,'Entrada',1,NULL)",[$material]);
 $pdo->prepare("INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id) VALUES(?,'Entrada',10,'SUS',?)")->execute([$material,$prof]);$pdo->prepare('INSERT INTO usos_materiais(idoso_id,material_id,usuario_id,quantidade,observacao) VALUES(?,?,?,?,?)')->execute([$idoso,$material,$prof,1,'Uso teste']);ok((float)scalar($pdo,'SELECT quantidade_atual FROM estoques WHERE material_id=?',[$material])===9.0,'Projeção de estoque incorreta.');rejects($pdo,'UPDATE movimentacoes_estoque SET quantidade=99 WHERE material_id=?',[$material],'imutável');
 ok((int)scalar($pdo,"SELECT COUNT(*) FROM logs_auditoria WHERE tabela='registros_saude' AND JSON_SEARCH(COALESCE(valores_novos,JSON_OBJECT()),'one','conteúdo confidencial') IS NOT NULL")===0,'Auditoria contém conteúdo clínico.');
 echo "PASS: migrations, assistencial, estoque-schema, invariantes, consultas\n";
} catch(Throwable $e){fwrite(STDERR,"FAIL: {$e->getMessage()}\n");exit(1);}

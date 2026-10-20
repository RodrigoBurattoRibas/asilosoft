<?php
/** HTTP real com PHP built-in e MySQL descartável; não lê .env. */
require_once __DIR__.'/../../src/infra/Database/DatabaseMigrator.php';
function ok(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); echo "OK: $message\n"; }
$cfg=[];
foreach (['HOST','PORTA','NOME','USUARIO','SENHA'] as $key) {
    $cfg[$key]=getenv('ASILOSOFT_TEST_DB_'.$key);
    if ($cfg[$key]===false) throw new RuntimeException('Defina ASILOSOFT_TEST_DB_'.$key);
}
if (!preg_match('/^asilosoft_test_[a-z0-9_]+$/D',$cfg['NOME'])) throw new RuntimeException('Base insegura');
$pdo=new PDO("mysql:host={$cfg['HOST']};port={$cfg['PORTA']};charset=utf8mb4",$cfg['USUARIO'],$cfg['SENHA'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$pdo->exec('CREATE DATABASE `'.$cfg['NOME'].'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$pdo->exec('USE `'.$cfg['NOME'].'`');
$root=dirname(__DIR__,2); $server=null; $cookies=[]; $prepend=null;
try {
    (new DatabaseMigrator())->migrate($pdo,$root.'/src/infra/Database/migrations');
    $insert=$pdo->prepare('INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES (?,?,?,?)');
    foreach (['admin'=>'Administrador','saude'=>'Profissional de Saúde','estoque'=>'Almoxarife','familia'=>'Familiar','outro'=>'Familiar'] as $name=>$role) $insert->execute([$name,$name.'@example.test',password_hash('SenhaTeste123',PASSWORD_DEFAULT),$role]);
    $pdo->exec("INSERT INTO quartos(codigo,capacidade) VALUES ('A',5)");
    $pdo->exec("INSERT INTO idosos(nome,cpf,identificador,quarto_id) VALUES ('Ana Teste','12345678901','RES-001',1),('Bruno Exclusivo','12345678902','RES-002',1)");
    $pdo->exec("INSERT INTO materiais(nome,unidade_medida) VALUES ('Gaze Teste','un')");
    $pdo->exec("INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id) VALUES(1,'Entrada',10,'SUS',1)");
    $env=getenv();
    foreach (['MYSQLHOST'=>'HOST','MYSQLPORT'=>'PORTA','MYSQLDATABASE'=>'NOME','MYSQLUSER'=>'USUARIO','MYSQLPASSWORD'=>'SENHA'] as $key=>$value) $env[$key]=$cfg[$value];
    $env['ASILOSOFT_DEBUG']='0';
    // proc_open omite valores vazios em alguns ambientes; force a senha de teste
    // dentro de cada requisição para jamais buscar a senha do .env.
    $prepend=tempnam(sys_get_temp_dir(),'asilo-http-env-');
    file_put_contents($prepend, '<?php putenv('.var_export('MYSQLPASSWORD='.$cfg['SENHA'],true).');');
    $port=(int)(getenv('ASILOSOFT_TEST_HTTP_PORT') ?: 18087);
    $server=proc_open([PHP_BINARY,'-d','auto_prepend_file='.$prepend,'-S','127.0.0.1:'.$port,'-t',$root],[0=>['pipe','r'],1=>['file','/tmp/asilosoft-http-test.log','a'],2=>['file','/tmp/asilosoft-http-test.log','a']],$pipes,$root,$env);
    if (!is_resource($server)) throw new RuntimeException('Servidor indisponível');
    for ($i=0;$i<40;$i++) { $socket=@fsockopen('127.0.0.1',$port); if ($socket) { fclose($socket); break; } usleep(50000); }
    function request(string $user,string $route,?array $post=null,string $method=''): array {
        global $port,$cookies;
        $cookies[$user] ??= tempnam(sys_get_temp_dir(),'asilo-cookie-');
        $curl=curl_init('http://127.0.0.1:'.$port.'/?route='.$route);
        curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEFILE=>$cookies[$user],CURLOPT_COOKIEJAR=>$cookies[$user],CURLOPT_TIMEOUT=>10]);
        if ($post!==null) { curl_setopt($curl,CURLOPT_POST,true); curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($post)); }
        if ($method!=='') curl_setopt($curl,CURLOPT_CUSTOMREQUEST,$method);
        $body=curl_exec($curl);$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);curl_close($curl);
        if ($body===false) throw new RuntimeException('Falha HTTP');
        if ($code>=500) throw new RuntimeException('Erro servidor em '.$route);
        if (getenv('ASILOSOFT_TEST_CAPTURE') === '1' && $code === 200 && $post === null) {
            $directory = sys_get_temp_dir().'/asilosoft-ui';
            if (!is_dir($directory)) mkdir($directory, 0700, true);
            file_put_contents($directory.'/'.$user.'_'.preg_replace('/[^a-zA-Z0-9_-]/','_',$route).'.html', $body);
        }
        return [$code,$body];
    }
    function field(string $html,string $name='csrf_token'): string {
        if (!preg_match('/name="'.preg_quote($name,'/').'"\s+value="([^"]+)"/',$html,$m)) throw new RuntimeException('Campo ausente: '.$name);
        return html_entity_decode($m[1],ENT_QUOTES);
    }
    function formToken(string $html): string {
        preg_match_all('/name="csrf_token"\s+value="([^"]+)"/',$html,$m);
        if (empty($m[1])) throw new RuntimeException('CSRF ausente');
        return html_entity_decode(end($m[1]),ENT_QUOTES);
    }
    foreach (['admin','saude','estoque','familia','outro'] as $u) {
        $token=field(request($u,'login')[1]);
        ok(request($u,'entrar',['csrf_token'=>$token,'email'=>$u.'@example.test','senha'=>'SenhaTeste123'])[0]===303,'T14 login '.$u);
        ok(request($u,'home')[0]===200,'Home '.$u);
    }
    $userToken=formToken(request('admin','usuarios-novo')[1]);
    $userData=['csrf_token'=>$userToken,'nome'=>'Novo usuário','email'=>'novo@example.test','perfil'=>'Familiar','senha'=>'SenhaNova123'];
    request('admin','usuarios-criar',$userData);
    $created=$pdo->query("SELECT * FROM usuarios WHERE email='novo@example.test'")->fetch();
    ok($created && password_verify('SenhaNova123',$created['senha_hash']),'T14 usuário criado com hash');
    $userData['nome']='Usuário editado';$userData['senha']='';
    request('admin','usuarios-atualizar&id='.$created['id'],$userData);
    ok($pdo->query('SELECT nome FROM usuarios WHERE id='.$created['id'])->fetchColumn()==='Usuário editado','T14 editar usuário');
    request('admin','usuarios-status&id=1',['csrf_token'=>$userToken,'ativo'=>'0']);
    ok((int)$pdo->query('SELECT ativo FROM usuarios WHERE id=1')->fetchColumn()===1,'T14 último administrador preservado');
    $t=formToken(request('admin','familiares-novo')[1]);
    ok(request('admin','familiares-criar',['csrf_token'=>$t,'nome'=>'Contato Ana','telefone'=>'555','usuario_id'=>4])[0]===303,'T01 criar familiar via HTTP');
    request('admin','familiares-atualizar&id=1',['csrf_token'=>$t,'nome'=>'Contato editado','telefone'=>'555']);
    ok((int)$pdo->query('SELECT usuario_id FROM familiares WHERE id=1')->fetchColumn()===4,'T01 edição preserva associação');
    request('admin','idosos-familiares-vincular&id=1',['csrf_token'=>$t,'familiar_id'=>1,'parentesco'=>'Filha','responsavel_principal'=>1]);
    ok((int)$pdo->query('SELECT COUNT(*) FROM idosos_familiares')->fetchColumn()===1,'T02 vínculo pela ficha');
    foreach (['Ana','123.456.789-01','RES-001'] as $term) ok(str_contains(request('saude','idosos&busca='.urlencode($term))[1],'Ana Teste'),'T03 busca '.$term);
    ok(!str_contains(request('saude','idosos')[1],'idosos-novo'),'Profissional sem cadastro administrativo');
    ok(request('admin','idosos-ficha&id=1')[0]===200,'Administrador consulta ficha');
    $adminToken=formToken(request('admin','idosos')[1]);
    ok(request('admin','idosos-saude-registrar&id=1',['csrf_token'=>$adminToken,'descricao'=>'Proibida'])[0]===403,'T04 POST clínico admin negado');
    $tSaude=formToken(request('saude','idosos-saude&id=1')[1]);
    foreach ([['PRIVATE_MARKER',0],['PUBLIC_MARKER',1]] as [$description,$visible]) request('saude','idosos-saude-registrar&id=1',['csrf_token'=>$tSaude,'descricao'=>$description,'visivel_para_familiar'=>$visible,'usuario_id'=>1]);
    ok((int)$pdo->query('SELECT COUNT(*) FROM registros_saude WHERE usuario_id=2')->fetchColumn()===2,'T04 autor derivado da sessão');
    $body=request('familia','acompanhamento-familiar&id=1')[1];
    ok(str_contains($body,'PUBLIC_MARKER')&&!str_contains($body,'PRIVATE_MARKER'),'T05 portal somente publicado');
    ok(request('familia','acompanhamento-familiar&id=2')[0]===403,'T06 troca ID bloqueada');
    ok(request('familia','idosos-ficha&id=1')[0]===403,'Ficha clínica proibida familiar');
    ok(request('estoque','idosos-ficha&id=1')[0]===403,'Ficha clínica proibida almoxarife');
    $use=request('saude','almoxarifado-uso')[1];
    $useData=['csrf_token'=>formToken($use),'chave_requisicao'=>field($use,'chave_requisicao'),'idoso_id'=>1,'material_id'=>1,'quantidade'=>'2.125'];
    request('saude','almoxarifado-registrar-uso',$useData);request('saude','almoxarifado-registrar-uso',$useData);
    ok((int)$pdo->query('SELECT COUNT(*) FROM usos_materiais')->fetchColumn()===1,'T07 reenvio HTTP não duplica uso');
    ok((float)$pdo->query('SELECT quantidade_atual FROM estoques WHERE material_id=1')->fetchColumn()===7.875,'T07 saldo uma única baixa');
    ok(str_contains(request('admin','idosos-ficha&id=1')[1],'Gaze Teste'),'Uso aparece na ficha');
    $buy=formToken(request('estoque','compras-nova')[1]);
    request('estoque','compras-criar',['csrf_token'=>$buy,'material_id'=>1,'quantidade'=>'3','observacao'=>'Reposição']);
    ok(request('estoque','compras-situacao&id=1',['csrf_token'=>$buy,'situacao_atual'=>'Pendente','situacao'=>'Aprovada'])[0]===403,'T09 almoxarife não aprova');
    $buyAdmin=formToken(request('admin','compras-nova')[1]);
    request('admin','compras-situacao&id=1',['csrf_token'=>$buyAdmin,'situacao_atual'=>'Pendente','situacao'=>'Aprovada']);
    ok(str_contains(request('admin','compras')[1],'Atendida'),'T09 ação atender navegável');
    request('admin','compras-situacao&id=1',['csrf_token'=>$buyAdmin,'situacao_atual'=>'Aprovada','situacao'=>'Atendida']);
    ok($pdo->query('SELECT situacao FROM solicitacoes_compra WHERE id=1')->fetchColumn()==='Atendida','T09 ciclo HTTP');
    require_once $root.'/src/infra/Routers/Router.php';require_once $root.'/src/infra/Routers/RouteLoader.php';RouteLoader::load($root.'/src/modules');
    foreach (Router::rotas() as $route=>$config) {
        $post=false; foreach ($config['middlewares'] as $middleware) if ($middleware instanceof CsrfMiddleware) $post=true;
        if ($post) { ok(request('admin',$route,[])[0]===419,'T10 sem CSRF '.$route);ok(request('admin',$route,['csrf_token'=>'invalido'])[0]===419,'T10 CSRF inválido '.$route);ok(request('admin',$route)[0]===405,'T10 GET mutação '.$route); }
        else ok(request('admin',$route,[])[0]===405,'T10 POST leitura '.$route);
    }
    $roomToken=formToken(request('admin','quartos-novo')[1]);
    request('admin','quartos-criar',['csrf_token'=>$roomToken,'codigo'=>'B','capacidade'=>3]);
    request('admin','idosos-quartos-criar',['csrf_token'=>$adminToken,'codigo'=>'C','capacidade'=>3]);
    ok((int)$pdo->query('SELECT COUNT(*) FROM quartos')->fetchColumn()===3,'T11 ambos formulários de quartos');
    request('admin','quartos-atualizar&id=2',['csrf_token'=>$roomToken,'codigo'=>'B','capacidade'=>4]);
    request('admin','idosos-quartos-atualizar&id=3',['csrf_token'=>$adminToken,'codigo'=>'C','capacidade'=>4]);
    ok((int)$pdo->query('SELECT SUM(capacidade) FROM quartos WHERE id IN (2,3)')->fetchColumn()===8,'T11 edição pelas duas rotas');
    request('admin','idosos-atualizar&id=1',['csrf_token'=>$adminToken,'nome'=>'Ana Teste','cpf'=>'12345678901','identificador'=>'RES-001','quarto_id'=>1]);
    ok((int)$pdo->query('SELECT quarto_id FROM idosos WHERE id=1')->fetchColumn()===1,'T14 edição preserva quarto');
    $stockToken=formToken(request('estoque','almoxarifado-entrada&id=1')[1]);
    request('estoque','almoxarifado-registrar-entrada&id=1',['csrf_token'=>$stockToken,'quantidade'=>'1']);
    ok((float)$pdo->query('SELECT quantidade_atual FROM estoques WHERE material_id=1')->fetchColumn()===7.875,'T08 entrada sem origem não grava');
    request('estoque','almoxarifado-registrar-entrada&id=1',['csrf_token'=>$stockToken,'quantidade'=>'1','origem'=>'Instituição']);
    request('estoque','almoxarifado-registrar-retirada&id=1',['csrf_token'=>$stockToken,'quantidade'=>'1']);
    ok((float)$pdo->query('SELECT quantidade_atual FROM estoques WHERE material_id=1')->fetchColumn()===7.875,'T14 entrada e retirada genéricas preservadas');
    request('admin','idosos-familiares-desvincular&id=1',['csrf_token'=>$t,'familiar_id'=>1]);
    ok(request('familia','acompanhamento-familiar&id=1')[0]===403,'T06 remoção revoga próxima requisição');
    $pdo->exec('UPDATE usuarios SET ativo=0 WHERE id=4');
    ok(request('familia','meus-idosos')[0]===303,'T06 conta desativada bloqueada');
    $logout=field(request('admin','home')[1]);request('admin','sair',['csrf_token'=>$logout]);
    ok(request('admin','home')[0]===303,'T13 logout bloqueia sessão');
    $token=field(request('admin','login')[1]);request('admin','entrar',['csrf_token'=>$token,'email'=>'admin@example.test','senha'=>'SenhaTeste123']);
    ok(str_contains(request('admin','familiares')[1],'Contato editado'),'T13 dados persistem após login');
    echo "HTTP e regressões aprovados.\n";
} finally {
    if (is_resource($server)) { proc_terminate($server);proc_close($server); }
    foreach ($cookies as $cookie) @unlink($cookie);
    if ($prepend) @unlink($prepend);
    $pdo->exec('DROP DATABASE `'.$cfg['NOME'].'`');
}

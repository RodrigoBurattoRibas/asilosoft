<?php
// Executar somente contra MySQL local descartável, sem usar o .env.
require_once __DIR__.'/../../../src/infra/Database/DatabaseMigrator.php';
require_once __DIR__.'/../../../src/modules/usuarios/repository/AsiloUsuarioRepository.php';
$socket = getenv('ASILOSOFT_ADMIN_TEST_SOCKET');
if (!$socket) throw new RuntimeException('Defina ASILOSOFT_ADMIN_TEST_SOCKET para um MySQL local descartável.');
$db = 'asilosoft_test_admin_protecao';
$connect = fn()=>new PDO('mysql:unix_socket='.$socket.';charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$pdo = $connect();
if (($argv[1] ?? '') === '--concorrente') {
    $pdo->exec('USE '.$db);
    try { $pdo->exec("UPDATE usuarios SET perfil='Familiar' WHERE id=1"); exit(1); }
    catch (PDOException $e) { exit(($e->errorInfo[0] ?? '')==='45000' ? 0 : 2); }
}
function adminCheck(bool $ok, string $message): void {
    if (!$ok) throw new RuntimeException($message);
    echo "OK: $message\n";
}
function adminReject(PDO $pdo, string $sql): void {
    try { $pdo->exec($sql); throw new RuntimeException('Operação indevida foi aceita: '.$sql); }
    catch (PDOException $e) { adminCheck(($e->errorInfo[0] ?? '')==='45000', 'Bloqueado: '.$sql); }
}
$pdo->exec('CREATE DATABASE '.$db.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
try {
    $pdo->exec('USE '.$db);
    $migrator = new DatabaseMigrator();
    $directory = __DIR__.'/../../../src/infra/Database/migrations';
    $migrator->migrate($pdo, $directory);
    $register = function(string $password) use ($pdo): void {
        $pdo->prepare('SET @cadastro_admin_nome=?, @cadastro_admin_email=?, @cadastro_admin_hash=?')
            ->execute(['Admin cadastrado','admin@asilosoft.local',password_hash($password,PASSWORD_DEFAULT)]);
        $pdo->exec(file_get_contents(__DIR__.'/../../../src/infra/Database/seeds/001_registrar_administrador.sql'));
    };
    $register('SenhaNova!2026');
    adminCheck(password_verify('SenhaNova!2026', $pdo->query('SELECT senha_hash FROM usuarios WHERE id=1')->fetchColumn()), 'Nova conta cadastrada com hash válido');
    foreach (["DELETE FROM usuarios WHERE id=1", "UPDATE usuarios SET perfil='Familiar' WHERE id=1", 'UPDATE usuarios SET ativo=0 WHERE id=1'] as $sql) adminReject($pdo,$sql);
    $pdo->exec("UPDATE usuarios SET nome='Nome editado' WHERE id=1");
    adminCheck($pdo->query('SELECT nome FROM usuarios WHERE id=1')->fetchColumn()==='Nome editado','Edição comum do último administrador permitida');
    $pdo->exec("INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES ('Outro','outro@example.com','hash','Administrador')");
    adminReject($pdo,"UPDATE usuarios SET perfil='Familiar'");
    adminReject($pdo,'DELETE FROM usuarios');
    adminCheck((int)$pdo->query('SELECT quantidade FROM controle_administradores')->fetchColumn()===2,'Operação em lote recusada reverte contador');
    $pdo->beginTransaction();
    $pdo->exec("UPDATE usuarios SET perfil='Familiar' WHERE id=2");
    $process = proc_open([PHP_BINARY,__FILE__,'--concorrente'],[0=>['file','/dev/null','r'],1=>['file','/tmp/asilosoft-admin-concorrente.log','w'],2=>['redirect',1]],$pipes);
    if (!is_resource($process)) throw new RuntimeException('Falha ao iniciar teste concorrente');
    usleep(200000);
    $pdo->commit();
    adminCheck(proc_close($process)===0,'Duas conexões não removem simultaneamente os dois administradores');
    $pdo->exec("UPDATE usuarios SET perfil='Administrador' WHERE id=2");
    $pdo->exec('DELETE FROM usuarios WHERE id=1');
    $register('OutraSenha!2026');
    adminCheck((int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil='Administrador' AND ativo=1")->fetchColumn()===2,'Novo cadastro preserva outro administrador');
    $id=(int)$pdo->query("SELECT id FROM usuarios WHERE email='admin@asilosoft.local'")->fetchColumn();
    $pdo->exec("UPDATE usuarios SET perfil='Familiar',ativo=0 WHERE id=$id");
    $before=$pdo->query("SELECT * FROM usuarios WHERE id=$id")->fetch();
    try { $register('SenhaFinal!2026'); throw new RuntimeException('E-mail duplicado aceito'); }
    catch (PDOException $e) { adminCheck((int)($e->errorInfo[1] ?? 0)===1062,'Cadastro rejeita e-mail existente'); }
    adminCheck($pdo->query("SELECT * FROM usuarios WHERE id=$id")->fetch()===$before,'Conta existente preservada integralmente');
    $pdo->exec("UPDATE usuarios SET perfil='Administrador',ativo=1 WHERE id=$id");
    adminCheck($migrator->migrate($pdo,$directory)===[],'Migrations não reaplicam o cadastro');
    $pdo->exec('DELETE FROM usuarios WHERE id=2');
    try { (new AsiloUsuarioRepository($pdo))->atualizar($id,['perfil'=>'Familiar']); throw new RuntimeException('Repositório não bloqueou'); }
    catch (DomainException $e) { adminCheck($e->getMessage()==='Mantenha ao menos um administrador ativo.','Repositório traduz proteção para mensagem de domínio'); }
    adminCheck((int)$pdo->query('SELECT quantidade FROM controle_administradores')->fetchColumn()===1,'Contador final consistente');
    $env = getenv();
    // proc_open omite variáveis de ambiente vazias; use uma conta local com senha
    // para que o .env da aplicação nunca forneça uma senha ao processo de teste.
    $pdo->exec("CREATE USER 'asilosoft_admin_test'@'localhost' IDENTIFIED BY 'LocalTest!2026'");
    $pdo->exec("GRANT ALL ON $db.* TO 'asilosoft_admin_test'@'localhost'");
    foreach (['MYSQLHOST'=>'localhost','MYSQLPORT'=>'3306','MYSQLDATABASE'=>$db,'MYSQLUSER'=>'asilosoft_admin_test','MYSQLPASSWORD'=>'LocalTest!2026',
        'ASILOSOFT_ADMIN_EMAIL'=>'novo-admin@asilosoft.local','ASILOSOFT_ADMIN_NOME'=>'Administrador','ASILOSOFT_ADMIN_SENHA'=>'SenhaCLI!2026'] as $key=>$value) $env[$key]=$value;
    $process=proc_open([PHP_BINARY,'-d','pdo_mysql.default_socket='.$socket,__DIR__.'/../../../scripts/registrar-administrador.php'],
        [0=>['file','/dev/null','r'],1=>['file','/tmp/asilosoft-admin-cli.log','w'],2=>['redirect',1]],$pipes,null,$env);
    adminCheck(is_resource($process) && proc_close($process)===0,'Comando CLI cadastra novo administrador');
    adminCheck(password_verify('SenhaCLI!2026',$pdo->query("SELECT senha_hash FROM usuarios WHERE email='novo-admin@asilosoft.local'")->fetchColumn()),'Senha do comando CLI válida');
    $before=$pdo->query("SELECT * FROM usuarios WHERE email='novo-admin@asilosoft.local'")->fetch();
    $process=proc_open([PHP_BINARY,'-d','pdo_mysql.default_socket='.$socket,__DIR__.'/../../../scripts/registrar-administrador.php'],
        [0=>['file','/dev/null','r'],1=>['file','/tmp/asilosoft-admin-cli.log','w'],2=>['redirect',1]],$pipes,null,$env);
    adminCheck(is_resource($process) && proc_close($process)===1,'CLI rejeita e-mail já cadastrado');
    adminCheck($pdo->query("SELECT * FROM usuarios WHERE email='novo-admin@asilosoft.local'")->fetch()===$before,'CLI preserva conta existente');
    $pdo->exec("DELETE FROM usuarios WHERE email='novo-admin@asilosoft.local'");
    foreach (['ai_usuarios_contar_admin','bu_usuarios_preservar_admin','bd_usuarios_preservar_admin'] as $trigger) $pdo->exec('DROP TRIGGER '.$trigger);
    $pdo->exec('DROP TABLE controle_administradores');
    $pdo->exec("DELETE FROM schema_migrations WHERE versao='009_preservar_administrador'");
    $migrator->migrate($pdo,$directory);
    adminCheck((int)$pdo->query('SELECT quantidade FROM controle_administradores')->fetchColumn()===1,'Migration inicializa contador em base já populada');
    adminReject($pdo,"DELETE FROM usuarios WHERE id=$id");
} finally {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $pdo->exec("DROP USER IF EXISTS 'asilosoft_admin_test'@'localhost'");
    $pdo->exec('DROP DATABASE '.$db);
}

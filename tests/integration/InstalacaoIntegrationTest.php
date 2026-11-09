<?php
require_once __DIR__ . '/../../src/infra/Database/DatabaseMigrator.php';
$cfg = [];
foreach (['HOST', 'PORTA', 'NOME', 'USUARIO', 'SENHA'] as $key) {
    $cfg[$key] = getenv('ASILOSOFT_TEST_DB_' . $key);
    if ($cfg[$key] === false) throw new RuntimeException('Defina ASILOSOFT_TEST_DB_' . $key);
}
if (!preg_match('/^asilosoft_test_[a-z0-9_]+$/D', $cfg['NOME'])) throw new RuntimeException('Base insegura');
$pdo = new PDO("mysql:host={$cfg['HOST']};port={$cfg['PORTA']};charset=utf8mb4", $cfg['USUARIO'], $cfg['SENHA'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE DATABASE `' . $cfg['NOME'] . '` CHARACTER SET utf8mb4');
try {
    $pdo->exec('USE `' . $cfg['NOME'] . '`');
    $root = dirname(__DIR__, 2);
    (new DatabaseMigrator())->migrate($pdo, $root . '/src/infra/Database/migrations');
    $env = getenv();
    foreach (['MYSQLHOST'=>'HOST','MYSQLPORT'=>'PORTA','MYSQLDATABASE'=>'NOME','MYSQLUSER'=>'USUARIO','MYSQLPASSWORD'=>'SENHA'] as $key=>$value) $env[$key]=$cfg[$value];
    // A senha vazia precisa existir no processo filho para impedir fallback ao .env.
    $command = 'putenv(' . var_export('MYSQLPASSWORD='.$cfg['SENHA'], true) . '); require ' . var_export($root.'/scripts/criar-administrador.php', true) . ';';
    $run = static function (array $inputs) use ($env, $command, $root): int {
        $process = proc_open([PHP_BINARY, '-r', $command], [0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']], $pipes, $root, array_replace($env, $inputs));
        if (!is_resource($process)) throw new RuntimeException('Processo indisponível');
        fclose($pipes[0]);
        stream_get_contents($pipes[1]); fclose($pipes[1]);
        stream_get_contents($pipes[2]); fclose($pipes[2]);
        return proc_close($process);
    };
    $input = ['ASILOSOFT_ADMIN_NOME'=>'Administrador Teste','ASILOSOFT_ADMIN_EMAIL'=>'admin@example.test','ASILOSOFT_ADMIN_SENHA'=>'SenhaFicticia123!'];
    if ($run(array_replace($input, ['ASILOSOFT_ADMIN_SENHA'=>'curta'])) !== 1 || (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() !== 0) throw new RuntimeException('Senha curta aceita');
    if ($run($input) !== 0) throw new RuntimeException('Falha na inicialização');
    $user = $pdo->query('SELECT * FROM usuarios')->fetch(PDO::FETCH_ASSOC);
    if (!$user || $user['perfil'] !== 'Administrador' || !password_verify($input['ASILOSOFT_ADMIN_SENHA'], $user['senha_hash'])) throw new RuntimeException('Conta ou hash inválido');
    if ($run(array_replace($input, ['ASILOSOFT_ADMIN_EMAIL'=>'segundo@example.test'])) !== 1 || (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() !== 1) throw new RuntimeException('Inicialização repetida alterou contas');
    echo "OK instalação: validação, primeiro administrador com hash e recusa de repetição.\n";
} finally {
    $pdo->exec('DROP DATABASE `' . $cfg['NOME'] . '`');
}

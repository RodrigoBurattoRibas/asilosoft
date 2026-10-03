<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/core/Config/Environment.php';
require_once __DIR__ . '/DatabaseMigrator.php';

$name = Environment::get('ASILOSOFT_DB_NOME', 'asilosoft');
if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) { fwrite(STDERR, "Nome de banco inválido.\n"); exit(1); }
try {
    $host = Environment::get('ASILOSOFT_DB_HOST', '127.0.0.1');
    $port = Environment::get('ASILOSOFT_DB_PORTA', '3306');
    $user = Environment::get('ASILOSOFT_DB_USUARIO', 'root');
    $pass = Environment::get('ASILOSOFT_DB_SENHA');
    $server = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $applied = DatabaseMigrator::migrate($pdo, __DIR__ . '/migrations');
    echo $applied === [] ? "Banco já atualizado.\n" : 'Migrações aplicadas: ' . implode(', ', $applied) . "\n";
} catch (Throwable $error) { fwrite(STDERR, "Bootstrap falhou: {$error->getMessage()}\n"); exit(1); }

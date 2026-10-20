<?php
// Exclusivamente base descartável, sem ler .env da aplicação.
require_once __DIR__ . '/../../../src/infra/Database/DatabaseMigrator.php';
function check(bool $ok, string $message): void {
    if (!$ok) throw new RuntimeException($message);
    echo "OK: $message\n";
}
$env = [];
foreach (['HOST', 'PORTA', 'NOME', 'USUARIO', 'SENHA'] as $key) {
    $value = getenv('ASILOSOFT_TEST_DB_' . $key);
    if ($value === false) throw new RuntimeException('Defina ASILOSOFT_TEST_DB_' . $key);
    $env[$key] = $value;
}
if (!preg_match('/^asilosoft_test_[a-z0-9_]+$/D', $env['NOME'])) throw new RuntimeException('Nome de base de teste inseguro.');
$pdo = new PDO("mysql:host={$env['HOST']};port={$env['PORTA']};charset=utf8mb4", $env['USUARIO'], $env['SENHA'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
$name = $env['NOME'];
$pdo->exec("DROP DATABASE IF EXISTS `$name`");
$pdo->exec("CREATE DATABASE `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `$name`");
$migrator = new DatabaseMigrator();
$dir = __DIR__ . '/../../../src/infra/Database/migrations';
check(count($migrator->migrate($pdo, $dir)) >= 9, 'T12 instalação nova aplica migrations');
$type = $pdo->query("SHOW COLUMNS FROM materiais LIKE 'tipo'")->fetch()['Type'];
check($type === 'varchar(30)', 'Contrato convergente materiais.tipo VARCHAR(30)');
$pdo->exec("INSERT INTO materiais(nome,tipo,unidade_medida) VALUES ('Material preservado','Medicamento','un')");
$pdo->exec("INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem) VALUES (1,'Entrada',10,'SUS')");
check($migrator->migrate($pdo, $dir) === [], 'T12 segundo bootstrap não reaplica migrations');
// Simula instalação histórica que aplicou ENUM antes da outra migration 006.
$pdo->exec("ALTER TABLE materiais MODIFY tipo ENUM('Material','Insumo','Medicamento') NOT NULL DEFAULT 'Material'");
$pdo->exec("DELETE FROM schema_migrations WHERE versao='007_convergir_tipo_material'");
check($migrator->migrate($pdo, $dir) === ['007_convergir_tipo_material'], 'T12 atualização histórica converge');
check($pdo->query("SHOW COLUMNS FROM materiais LIKE 'tipo'")->fetch()['Type'] === $type, 'Schemas novo e atualizado equivalentes');
check($pdo->query('SELECT tipo FROM materiais WHERE id=1')->fetchColumn() === 'Medicamento', 'Dados preexistentes preservados');
check((float)$pdo->query('SELECT quantidade_atual FROM estoques WHERE material_id=1')->fetchColumn() === 10.0, 'Saldo preservado');
check((int)$pdo->query('SELECT COUNT(*) FROM movimentacoes_estoque')->fetchColumn() === 1, 'Histórico preservado');
check($migrator->migrate($pdo, $dir) === [], 'Bootstrap após atualização idempotente');
require_once __DIR__ . '/../../../src/modules/quartos/repository/AsiloQuartoRepository.php';
require_once __DIR__ . '/../../../src/modules/quartos/services/AsiloQuartoService.php';
$q = new AsiloQuartoService(new AsiloQuartoRepository($pdo));
$room = $q->criar(['codigo'=>'Teste','capacidade'=>'1']);
$pdo->exec("INSERT INTO idosos(nome,cpf,identificador,quarto_id) VALUES ('Residente teste','12345678901','TESTE',{$room['id']})");
try { $q->alterarStatus($room['id'],false); throw new RuntimeException('Desativou ocupado'); } catch (DomainException $e) { echo "OK: T14 quarto ocupado protegido\n"; }
try { $pdo->exec("INSERT INTO idosos(nome,cpf,identificador,quarto_id) VALUES ('Sem vaga','12345678902','TESTE2',{$room['id']})"); throw new RuntimeException('Excedeu capacidade'); } catch (PDOException $e) { echo "OK: T14 capacidade protegida no banco\n"; }
require_once __DIR__ . '/../../../src/infra/Routers/Router.php';
require_once __DIR__ . '/../../../src/infra/Routers/RouteLoader.php';
RouteLoader::load(__DIR__ . '/../../../src/modules');
check(Router::configuracao('quartos-criar')['modulo'] === 'quartos', 'T11 handler administrativo de quartos');
check(Router::configuracao('idosos-quartos-criar')['modulo'] === 'idosos', 'T11 handler de quartos no cadastro');
foreach (Router::rotas() as $route=>$config) {
    check($config['middlewares'] !== [], "Middleware presente: $route");
}
echo "Banco, migrations e contratos de rotas aprovados.\n";

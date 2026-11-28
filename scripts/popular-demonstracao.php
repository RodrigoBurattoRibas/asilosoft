<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../src/infra/Database/Database.php';
require_once __DIR__ . '/../src/infra/Database/DatabaseMigrator.php';

/** Repara apenas as proteções conhecidas; não oculta erros de permissão/conexão. */
function repararProtecoesDemonstracao(PDO $pdo): void
{
    $migration = file_get_contents(__DIR__ . '/../src/infra/Database/migrations/004_auditoria_e_invariantes.sql');
    if ($migration === false) {
        throw new RuntimeException('Migration de proteções do histórico indisponível.');
    }
    $definitions = [];
    foreach ((new DatabaseMigrator())->statements($migration) as $statement) {
        if (preg_match('/^CREATE TRIGGER (bd_me_imutavel|bd_um_imutavel)\\b/i', $statement, $match)) {
            $definitions[$match[1]] = $statement;
        }
    }
    foreach (['bd_me_imutavel', 'bd_um_imutavel'] as $name) {
        $check = $pdo->prepare('SELECT COUNT(*) FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = DATABASE() AND TRIGGER_NAME = ?');
        $check->execute([$name]);
        if ((int) $check->fetchColumn() === 0) {
            if (!isset($definitions[$name])) {
                throw new RuntimeException("Definição original da trigger $name indisponível.");
            }
            $pdo->exec($definitions[$name]);
        }
    }
}

/** Migration destrutiva e opcional: executar com a aplicação parada. */
function popularDemonstracao(PDO $pdo, ?callable $progress = null): bool
{
    $pdo->exec('SET SESSION lock_wait_timeout = 15');
    $pdo->exec('SET SESSION innodb_lock_wait_timeout = 15');
    $lock = $pdo->query("SELECT GET_LOCK('asilosoft_demo_migration', 0)")->fetchColumn();
    if ((int) $lock !== 1) {
        throw new RuntimeException('Outra carga de demonstração está em execução. Aguarde sua conclusão.');
    }
    try {
        return executarCargaDemonstracao($pdo, $progress ?? static function (string $message): void {});
    } finally {
        $pdo->query("SELECT RELEASE_LOCK('asilosoft_demo_migration')")->closeCursor();
    }
}

function executarCargaDemonstracao(PDO $pdo, callable $progress): bool
{
    $progress('Conferindo histórico de execução e proteções do banco.');
    repararProtecoesDemonstracao($pdo);
    $version = 'demo_001_reset_demonstracao';
    $check = $pdo->prepare('SELECT COUNT(*) FROM schema_migrations WHERE versao = ?');
    $check->execute([$version]);
    if ((int) $check->fetchColumn() > 0) {
        return false;
    }
    $admin = $pdo->query("SELECT id FROM usuarios WHERE email='admin@asilosoft.local' AND perfil='Administrador' AND ativo=1")->fetchColumn();
    if ($admin === false) {
        throw new RuntimeException('Administrador padrão ativo não encontrado. Nenhum dado foi removido.');
    }
    // Estas duas triggers impedem DELETE mesmo em uma limpeza administrativa.
    // Guardar o DDL original permite restaurar a proteção também após rollback.
    $triggers = [];
    foreach (['bd_me_imutavel', 'bd_um_imutavel'] as $name) {
        $row = $pdo->query("SHOW CREATE TRIGGER `$name`")->fetch(PDO::FETCH_ASSOC);
        if (!$row || !isset($row['SQL Original Statement'])) {
            throw new RuntimeException("Definição da trigger $name indisponível.");
        }
        $triggers[$name] = $row['SQL Original Statement'];
    }
    $sql = file_get_contents(__DIR__ . '/../src/infra/Database/demo/001_reset_demonstracao.sql');
    if ($sql === false) {
        throw new RuntimeException('Arquivo da migration de demonstração indisponível.');
    }
    $statements = (new DatabaseMigrator())->statements($sql);
    // Um único CALL evita milhares de viagens de rede até o MySQL remoto.
    // CREATE/DROP PROCEDURE ficam fora da transação para evitar commits implícitos.
    $procedure = 'asilosoft_demo_' . bin2hex(random_bytes(8));
    $procedureCreated = false;
    $dropped = [];
    $oldActor = $pdo->query('SELECT @asilosoft_usuario_id')->fetchColumn();
    try {
        $progress('Preparando execução no servidor (' . count($statements) . ' instruções).');
        $pdo->exec("CREATE PROCEDURE `$procedure`() BEGIN\n" . implode(";\n", $statements) . ";\nEND");
        $procedureCreated = true;
        foreach ($triggers as $name => $ddl) {
            $pdo->exec("DROP TRIGGER `$name`");
            $dropped[$name] = $ddl;
        }
        $pdo->beginTransaction();
        $pdo->exec('SET @demo_admin = ' . (int) $admin);
        $pdo->exec('SET @asilosoft_usuario_id = @demo_admin');
        foreach (['saude'=>'DemoSaude!2026', 'almoxarife'=>'DemoEstoque!2026', 'familiar'=>'DemoFamilia!2026'] as $role => $password) {
            $pdo->exec('SET @demo_hash_' . $role . ' = ' . $pdo->quote(password_hash($password, PASSWORD_DEFAULT)));
        }
        $progress('Carregando idosos, familiares, quartos, estoque e compras em uma transação.');
        $cursor = $pdo->query("CALL `$procedure`()");
        while ($cursor->nextRowset()) {
            // Drena todos os resultados antes da próxima consulta.
        }
        $cursor->closeCursor();
        $progress('Dados inseridos; confirmando a transação.');
        $pdo->prepare('INSERT INTO schema_migrations(versao) VALUES (?)')->execute([$version]);
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    } finally {
        $progress('Restaurando proteções do histórico.');
        $restoreErrors = [];
        if ($procedureCreated) {
            try {
                $pdo->exec("DROP PROCEDURE `$procedure`");
            } catch (Throwable $error) {
                $restoreErrors[] = "Procedimento temporário $procedure: {$error->getMessage()}";
            }
        }
        foreach ($dropped as $name => $ddl) {
            try {
                $pdo->exec($ddl);
            } catch (Throwable $error) {
                $restoreErrors[] = "$name: {$error->getMessage()}";
            }
        }
        $pdo->exec('SET @asilosoft_usuario_id = ' . ($oldActor === null ? 'NULL' : $pdo->quote((string) $oldActor)));
        if ($restoreErrors !== []) {
            throw new RuntimeException('Falha ao restaurar proteção de histórico: ' . implode('; ', $restoreErrors));
        }
    }
    return true;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        $started = microtime(true);
        $progress = static function (string $message) use ($started): void {
            fwrite(STDOUT, sprintf("[%5.1fs] %s\n", microtime(true) - $started, $message));
            fflush(STDOUT);
        };
        // A carga remota em CALL pode demorar mais que uma consulta da aplicação.
        $progress('Conectando ao banco (limite de conexão: 5 segundos).');
        $pdo = PDOMysqli::connection(120);
        $pdo->exec('SET SESSION lock_wait_timeout = 15');
        $pdo->exec('SET SESSION innodb_lock_wait_timeout = 15');
        $progress('Conexão estabelecida; verificando migrations estruturais.');
        (new DatabaseMigrator())->migrate($pdo, __DIR__ . '/../src/infra/Database/migrations');
        echo popularDemonstracao($pdo, $progress)
            ? "Demonstração instalada. Administrador preservado. Credenciais: docs/DEMONSTRACAO.md\n"
            : "Migration de demonstração já aplicada; nenhum dado alterado.\n";
    } catch (Throwable $error) {
        fwrite(STDERR, "Erro: {$error->getMessage()}\n");
        exit(1);
    }
}

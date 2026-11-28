<?php
/** Novo cadastro explícito de conta administrativa; nunca executada pelo bootstrap. */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}
require_once dirname(__DIR__) . '/src/infra/Database/Database.php';
require_once dirname(__DIR__) . '/src/infra/Database/DatabaseMigrator.php';

try {
    $nome = trim(getenv('ASILOSOFT_ADMIN_NOME') ?: 'Administrador');
    $email = mb_strtolower(trim(getenv('ASILOSOFT_ADMIN_EMAIL') ?: 'admin@asilosoft.local'));
    $senha = getenv('ASILOSOFT_ADMIN_SENHA') ?: '';
    if ($nome === '' || mb_strlen($nome) > 120 || strlen($email) > 160
        || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 12 || strlen($senha) > 72) {
        throw new DomainException('Informe ASILOSOFT_ADMIN_SENHA (12 a 72 bytes), nome de até 120 caracteres e e-mail válido.');
    }
    $pdo = PDOMysqli::connection();
    // A proteção deve estar instalada antes de cadastrar a conta.
    (new DatabaseMigrator())->migrate($pdo, dirname(__DIR__) . '/src/infra/Database/migrations');
    $pdo->beginTransaction();
    $pdo->prepare('SET @cadastro_admin_nome=?, @cadastro_admin_email=?, @cadastro_admin_hash=?')
        ->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);
    $sql = file_get_contents(dirname(__DIR__) . '/src/infra/Database/seeds/001_registrar_administrador.sql');
    if ($sql === false) throw new RuntimeException('Migration de cadastro indisponível.');
    $pdo->exec($sql);
    $pdo->commit();
    echo "Administrador cadastrado e ativo. Utilize a senha informada.\n";
} catch (Throwable $error) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    if ($error instanceof PDOException && (int)($error->errorInfo[1] ?? 0) === 1062) {
        fwrite(STDERR, "E-mail já cadastrado. Nenhuma conta existente foi alterada.\n");
        exit(1);
    }
    $detail = $error instanceof PDOException ? ' SQLSTATE '.($error->errorInfo[0] ?? $error->getCode()).'; MySQL '.($error->errorInfo[1] ?? 'indisponível').'.' : '';
    fwrite(STDERR, $error instanceof DomainException ? $error->getMessage()."\n" : 'Não foi possível cadastrar o administrador. Confira conexão, migrations e permissões do banco.'.$detail."\n");
    exit(1);
}

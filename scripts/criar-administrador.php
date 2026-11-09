<?php
/** Inicialização de uma instalação nova, exclusivamente por CLI. */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once dirname(__DIR__) . '/src/infra/Database/Database.php';

$pdo = null;
$locked = false;
try {
    $nome = trim(getenv('ASILOSOFT_ADMIN_NOME') ?: 'Administrador');
    $email = mb_strtolower(trim(getenv('ASILOSOFT_ADMIN_EMAIL') ?: ''));
    $senha = getenv('ASILOSOFT_ADMIN_SENHA') ?: '';
    if ($nome === '' || mb_strlen($nome) > 120 || strlen($email) > 160
        || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 12 || strlen($senha) > 72) {
        throw new DomainException('Defina ASILOSOFT_ADMIN_NOME (até 120 caracteres), ASILOSOFT_ADMIN_EMAIL válido e ASILOSOFT_ADMIN_SENHA (12 a 72 bytes).');
    }
    $pdo = PDOMysqli::connection();
    $locked = (int) $pdo->query("SELECT GET_LOCK('asilosoft_primeiro_admin', 5)")->fetchColumn() === 1;
    if (!$locked) {
        throw new DomainException('Outra inicialização está em andamento. Tente novamente.');
    }
    $pdo->beginTransaction();
    if ((int) $pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil='Administrador' AND ativo=1")->fetchColumn() > 0) {
        throw new DomainException('Já existe administrador ativo. Gerencie novas contas pela aplicação.');
    }
    $query = $pdo->prepare("INSERT INTO usuarios (nome,email,senha_hash,perfil) VALUES (?,?,?,'Administrador')");
    $query->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);
    $pdo->commit();
    fwrite(STDOUT, "Administrador inicial criado.\n");
} catch (Throwable $error) {
    if ($pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, $error instanceof DomainException ? $error->getMessage() . "\n" : "Não foi possível criar o administrador. Verifique a conexão, as migrations e se o e-mail já está cadastrado.\n");
    exit(1);
} finally {
    if ($locked) {
        $pdo->query("SELECT RELEASE_LOCK('asilosoft_primeiro_admin')");
    }
}

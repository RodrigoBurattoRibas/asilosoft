<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}
require_once dirname(__DIR__).'/src/infra/Database/Database.php';
try {
    $sql = file_get_contents(dirname(__DIR__).'/src/infra/Database/seeds/002_admin_teste.sql');
    if ($sql === false) throw new RuntimeException('SQL de cadastro não encontrado.');
    PDOMysqli::connection()->exec($sql);
    echo "Cadastrado: admin@asilosoft.local\n";
} catch (Throwable $error) {
    if ($error instanceof PDOException && (int)($error->errorInfo[1] ?? 0) === 1062) {
        fwrite(STDERR, "E-mail já existe. Nenhum cadastro foi alterado.\n");
    } else {
        fwrite(STDERR, "Não foi possível inserir o administrador de teste. Confira a conexão e a tabela usuarios.\n");
    }
    exit(1);
}

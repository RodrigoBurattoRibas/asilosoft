<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/DatabaseMigrator.php';

try {
    $versions = (new DatabaseMigrator())->migrate(PDOMysqli::connection(), __DIR__ . '/migrations');
    fwrite(STDOUT, $versions === [] ? "Banco de dados já está atualizado.\n" : 'Migrations aplicadas: ' . implode(', ', $versions) . "\n");
} catch (Throwable $exception) {
    fwrite(STDERR, "Erro na migração: {$exception->getMessage()}\n");
    exit(1);
}

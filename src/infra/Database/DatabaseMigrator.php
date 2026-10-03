<?php
declare(strict_types=1);

final class DatabaseMigrator
{
    /** @return list<string> */
    public static function migrate(PDO $pdo, string $migrationDirectory): array
    {
        $directory = realpath($migrationDirectory);
        if ($directory === false || !is_dir($directory)) {
            throw new InvalidArgumentException('Diretório de migrações inválido.');
        }
        $pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (versao VARCHAR(100) PRIMARY KEY, aplicada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $files = glob($directory . DIRECTORY_SEPARATOR . '[0-9][0-9][0-9]_*.sql') ?: [];
        sort($files, SORT_STRING);
        $applied = [];
        $known = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE versao = ?');
        $record = $pdo->prepare('INSERT INTO schema_migrations (versao) VALUES (?)');
        foreach ($files as $file) {
            $realFile = realpath($file);
            if ($realFile === false || !str_starts_with($realFile, $directory . DIRECTORY_SEPARATOR)) {
                throw new RuntimeException('Migração fora do diretório permitido.');
            }
            $version = pathinfo($realFile, PATHINFO_FILENAME);
            $known->execute([$version]);
            if ($known->fetchColumn()) {
                continue;
            }
            foreach (self::statements((string) file_get_contents($realFile)) as $statement) {
                $pdo->exec($statement);
            }
            $record->execute([$version]);
            $applied[] = $version;
        }
        return $applied;
    }

    /** @return list<string> */
    private static function statements(string $sql): array
    {
        $delimiter = ';'; $buffer = ''; $result = [];
        foreach (preg_split('/\R/', $sql) ?: [] as $line) {
            if (preg_match('/^\s*DELIMITER\s+(.+)\s*$/i', $line, $matches)) {
                $delimiter = trim($matches[1]);
                continue;
            }
            $buffer .= $line . "\n";
            if (str_ends_with(rtrim($buffer), $delimiter)) {
                $statement = trim(substr(rtrim($buffer), 0, -strlen($delimiter)));
                if ($statement !== '' && !str_starts_with($statement, '--')) {
                    $result[] = $statement;
                }
                $buffer = '';
            }
        }
        if (trim($buffer) !== '') {
            throw new RuntimeException('SQL sem delimitador final.');
        }
        return $result;
    }
}

<?php

final class DatabaseMigrator
{
    /** @return list<string> */
    public function migrate(PDO $pdo, string $migrationDirectory): array
    {
        $directory = realpath($migrationDirectory);
        if ($directory === false || !is_dir($directory)) {
            throw new InvalidArgumentException('Diretório de migrations inválido.');
        }

        $pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (
            versao VARCHAR(100) NOT NULL PRIMARY KEY,
            aplicada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $applied = [];
        foreach ($pdo->query('SELECT versao FROM schema_migrations') as $row) {
            $applied[$row['versao']] = true;
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . '[0-9][0-9][0-9]_*.sql') ?: [];
        sort($files, SORT_STRING);
        $executed = [];

        foreach ($files as $file) {
            $version = pathinfo($file, PATHINFO_FILENAME);
            if (isset($applied[$version])) {
                continue;
            }

            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new RuntimeException("Não foi possível ler a migration {$version}.");
            }

            try {
                foreach ($this->statements($sql) as $statement) {
                    $pdo->exec($statement);
                }
                $pdo->prepare('INSERT INTO schema_migrations (versao) VALUES (:versao)')
                    ->execute(['versao' => $version]);
            } catch (Throwable $exception) {
                throw new RuntimeException("Falha ao aplicar a migration {$version}: {$exception->getMessage()}", previous: $exception);
            }

            $executed[] = $version;
        }

        return $executed;
    }

    /** @return list<string> */
    private function statements(string $sql): array
    {
        $delimiter = ';';
        $buffer = '';
        $statements = [];
        $quote = null;
        $length = strlen($sql);

        for ($index = 0; $index < $length; $index++) {
            if ($quote === null && ($index === 0 || $sql[$index - 1] === "\n")) {
                $lineEnd = strpos($sql, "\n", $index);
                $lineEnd = $lineEnd === false ? $length : $lineEnd;
                $line = substr($sql, $index, $lineEnd - $index);
                if (preg_match('/^\\s*DELIMITER\\s+(.+?)\\s*$/i', $line, $matches) === 1) {
                    if (trim($buffer) !== '') {
                        throw new RuntimeException('DELIMITER encontrado no meio de uma instrução SQL.');
                    }
                    $delimiter = $matches[1];
                    $index = $lineEnd;
                    continue;
                }
            }

            $character = $sql[$index];
            if ($quote !== null) {
                $buffer .= $character;
                if ($character === '\\' && $index + 1 < $length) {
                    $buffer .= $sql[++$index];
                } elseif ($character === $quote) {
                    $quote = null;
                }
                continue;
            }
            if ($character === "'" || $character === '"' || $character === '`') {
                $quote = $character;
                $buffer .= $character;
                continue;
            }
            if (substr($sql, $index, strlen($delimiter)) === $delimiter) {
                $statement = trim($buffer);
                if ($statement !== '') {
                    $statements[] = $statement;
                }
                $buffer = '';
                $index += strlen($delimiter) - 1;
                continue;
            }
            $buffer .= $character;
        }

        if (trim($buffer) !== '') {
            throw new RuntimeException('Arquivo SQL terminou sem o delimitador esperado.');
        }

        return $statements;
    }
}

<?php

class PDOMysqli
{
    private static ?PDO $conn = null;
    private static ?array $environment = null;

    public static function connection(): PDO
    {
        if (self::$conn instanceof PDO) {
            return self::$conn;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            self::envValue(['MYSQLHOST', 'ASILOSOFT_DB_HOST']),
            self::envValue(['MYSQLPORT', 'ASILOSOFT_DB_PORTA'], '3306'),
            self::envValue(['MYSQLDATABASE', 'MYSQL_DATABASE', 'ASILOSOFT_DB_NOME'])
        );

        self::$conn = new PDO(
            $dsn,
            self::envValue(['MYSQLUSER', 'ASILOSOFT_DB_USUARIO']),
            self::envValue(['MYSQLPASSWORD', 'MYSQL_ROOT_PASSWORD', 'ASILOSOFT_DB_SENHA'], ''),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return self::$conn;
    }

    private static function envValue(array $keys, ?string $default = null): string
    {
        foreach ($keys as $key) {
            $systemValue = getenv($key);
            if ($systemValue !== false) {
                $value = $systemValue;
            } else {
                self::loadEnvironment();
                if (!array_key_exists($key, self::$environment)) {
                    continue;
                }

                $value = self::$environment[$key];
            }

            if (!str_contains($value, '${{')) {
                return $value;
            }
        }

        if ($default !== null) {
            return $default;
        }

        throw new PDOException(
            sprintf('Configure uma destas variáveis no .env: %s.', implode(', ', $keys))
        );
    }

    private static function loadEnvironment(): void
    {
        if (self::$environment !== null) {
            return;
        }

        self::$environment = [];
        $file = dirname(__DIR__, 3) . '/.env';
        if (!is_file($file)) {
            return;
        }

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            if (str_starts_with(ltrim($line), '#') || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            if ($name !== '' && !array_key_exists($name, self::$environment)) {
                self::$environment[$name] = trim($value, " \t\n\r\0\x0B\"'");
            }
        }
    }
}

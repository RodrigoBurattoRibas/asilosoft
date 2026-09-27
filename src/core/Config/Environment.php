<?php


final class Environment
{
    /** @var array<string, string>|null */
    private static ?array $fileValues = null;

    public static function get(string $key, string $default = ''): string
    {
        $systemValue = getenv($key);
        if ($systemValue !== false) {
            return $systemValue;
        }

        self::loadFiles();

        return self::$fileValues[$key] ?? $default;
    }

    private static function loadFiles(): void
    {
        if (self::$fileValues !== null) {
            return;
        }

        self::$fileValues = [];
        $projectRoot = dirname(__DIR__, 3);
        $candidates = [$projectRoot . '/.env', dirname($projectRoot) . '/.env'];

        foreach ($candidates as $file) {
            if (!is_file($file)) {
                continue;
            }

            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                if (str_starts_with(ltrim($line), '#') || !str_contains($line, '=')) {
                    continue;
                }

                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                if ($name !== '' && !array_key_exists($name, self::$fileValues)) {
                    self::$fileValues[$name] = trim($value, " \t\n\r\0\x0B\"'");
                }
            }
        }
    }
}

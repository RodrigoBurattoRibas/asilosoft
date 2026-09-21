<?php

declare(strict_types=1);

final class Database
{
    public static function conectar(array $configuracao): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $configuracao['host'],
            $configuracao['porta'],
            $configuracao['banco']
        );

        return new PDO($dsn, $configuracao['usuario'], $configuracao['senha'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}

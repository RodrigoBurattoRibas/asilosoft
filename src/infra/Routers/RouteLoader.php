<?php

final class RouteLoader
{
    public static function load(string $basePath): void
    {
        $pattern = rtrim($basePath, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . '*'
            . DIRECTORY_SEPARATOR . 'routes'
            . DIRECTORY_SEPARATOR . 'web.php';

        $files = glob($pattern);
        if ($files === false) {
            throw new RuntimeException('Não foi possível localizar os arquivos de rotas.');
        }

        sort($files, SORT_STRING);
        foreach ($files as $file) {
            require $file;
        }
    }
}

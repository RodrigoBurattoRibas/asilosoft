<?php

declare(strict_types=1);

function valorDoAmbiente(string $chave, string $padrao = ''): string
{
    static $valoresDoArquivo = null;

    if ($valoresDoArquivo === null) {
        $valoresDoArquivo = [];
        $arquivo = __DIR__ . '/../.env';
        if (is_file($arquivo)) {
            $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($linhas as $linha) {
                if (str_starts_with(ltrim($linha), '#') || !str_contains($linha, '=')) {
                    continue;
                }
                [$nome, $valor] = explode('=', $linha, 2);
                $valoresDoArquivo[trim($nome)] = trim($valor, " \t\n\r\0\x0B\"'");
            }
        }
    }

    $valorDoSistema = getenv($chave);
    if ($valorDoSistema !== false) {
        return $valorDoSistema;
    }

    return $valoresDoArquivo[$chave] ?? $padrao;
}

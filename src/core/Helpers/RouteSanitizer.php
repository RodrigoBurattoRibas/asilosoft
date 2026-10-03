<?php

class RouteSanitizer
{
    public static function sanitizarEntrada(array $dados): array
    {
        $sanitizados = [];

        foreach ($dados as $chave => $valor) {
            if (is_array($valor)) {
                $sanitizados[$chave] = self::sanitizarEntrada($valor);
                continue;
            }

            if (is_string($valor)) {
                $sanitizados[$chave] = preg_replace(
                    '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/',
                    '',
                    $valor
                ) ?? '';
                continue;
            }

            if (is_scalar($valor) || $valor === null) {
                $sanitizados[$chave] = $valor;
            }
        }

        return $sanitizados;
    }

    public static function validar($rota)
    {
        $rota = trim((string) $rota);

        if (strlen($rota) > 200) {
            return '';
        }

        if (!preg_match('/^[a-zA-Z0-9\/_-]*$/', $rota)) {
            return '';
        }

        if (strpos($rota, '//') !== false) {
            return '';
        }

        return $rota;
    }
}

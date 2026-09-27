<?php

final class CsrfToken
{
    const SESSION_CONTAINER = '_csrf_tokens';

    public static function obter($escopo = 'global')
    {
        $escopo = self::validarEscopo($escopo);

        if (
            !isset($_SESSION[self::SESSION_CONTAINER])
            || !is_array($_SESSION[self::SESSION_CONTAINER])
        ) {
            $_SESSION[self::SESSION_CONTAINER] = [];
        }

        if (
            !isset($_SESSION[self::SESSION_CONTAINER][$escopo])
            || !is_string($_SESSION[self::SESSION_CONTAINER][$escopo])
            || $_SESSION[self::SESSION_CONTAINER][$escopo] === ''
        ) {
            $_SESSION[self::SESSION_CONTAINER][$escopo] = self::gerarToken();
        }

        return $_SESSION[self::SESSION_CONTAINER][$escopo];
    }

    public static function validar($token, $escopo = 'global')
    {
        $escopo = self::validarEscopo($escopo);

        if (
            !is_string($token)
            || $token === ''
            || !isset($_SESSION[self::SESSION_CONTAINER])
            || !is_array($_SESSION[self::SESSION_CONTAINER])
            || !isset($_SESSION[self::SESSION_CONTAINER][$escopo])
            || !is_string($_SESSION[self::SESSION_CONTAINER][$escopo])
        ) {
            return false;
        }

        $esperado = $_SESSION[self::SESSION_CONTAINER][$escopo];

        return function_exists('hash_equals')
            ? hash_equals($esperado, $token)
            : self::compararTokens($esperado, $token);
    }

    public static function renovar($escopo = 'global')
    {
        $escopo = self::validarEscopo($escopo);

        if (
            !isset($_SESSION[self::SESSION_CONTAINER])
            || !is_array($_SESSION[self::SESSION_CONTAINER])
        ) {
            $_SESSION[self::SESSION_CONTAINER] = [];
        }

        $_SESSION[self::SESSION_CONTAINER][$escopo] = self::gerarToken();

        return $_SESSION[self::SESSION_CONTAINER][$escopo];
    }

    private static function gerarToken()
    {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes(32));
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $forte = false;
            $bytes = openssl_random_pseudo_bytes(32, $forte);

            if ($bytes !== false && $forte) {
                return bin2hex($bytes);
            }
        }

        throw new RuntimeException('Não foi possível gerar o token de segurança.');
    }

    private static function validarEscopo($escopo)
    {
        $escopo = is_scalar($escopo) ? trim((string) $escopo) : '';

        if ($escopo === '' || !preg_match('/^[a-zA-Z0-9_.-]{1,80}$/D', $escopo)) {
            throw new InvalidArgumentException('O escopo do token CSRF é inválido.');
        }

        return $escopo;
    }

    private static function compararTokens($esperado, $recebido)
    {
        if (strlen($esperado) !== strlen($recebido)) {
            return false;
        }

        $diferenca = 0;
        $tamanho = strlen($esperado);
        for ($indice = 0; $indice < $tamanho; $indice++) {
            $diferenca |= ord($esperado[$indice]) ^ ord($recebido[$indice]);
        }

        return $diferenca === 0;
    }
}

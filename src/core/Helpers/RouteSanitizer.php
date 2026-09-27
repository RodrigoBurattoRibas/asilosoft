<?php

class RouteSanitizer
{
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

<?php


final class AsiloAuthMiddleware
{
    public static function validarSessao(): void
    {
        $userId = $_SESSION['asilosoft_usuario_id'] ?? null;
        $valid = (is_int($userId) && $userId > 0)
            || (is_string($userId) && ctype_digit($userId) && (int) $userId > 0);

        if ($valid) {
            return;
        }

        header('Location: ?route=login', true, 303);
        exit;
    }
}

<?php

final class AsiloAuthorization
{
    public static function permitir(?array $usuario, array $perfis): array
    {
        if ($usuario === null || empty($usuario['ativo'])) {
            throw new RuntimeException('AUTHENTICATION_REQUIRED');
        }
        if (!in_array($usuario['perfil'] ?? '', $perfis, true)) {
            throw new RuntimeException('FORBIDDEN');
        }
        return $usuario;
    }
}

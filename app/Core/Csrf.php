<?php

declare(strict_types=1);

final class Csrf
{
    public function __construct(private object $sessao)
    {
    }

    public function token(): string
    {
        $token = $this->sessao->obter('csrf_token');
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->sessao->definir('csrf_token', $token);
        }

        return $token;
    }

    public function validar(?string $tokenRecebido): bool
    {
        $tokenDaSessao = $this->sessao->obter('csrf_token');

        return is_string($tokenDaSessao) && is_string($tokenRecebido) && hash_equals($tokenDaSessao, $tokenRecebido);
    }
}

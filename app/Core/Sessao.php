<?php

declare(strict_types=1);

final class Sessao
{
    public function definir(string $chave, mixed $valor): void
    {
        $_SESSION[$chave] = $valor;
    }

    public function obter(string $chave): mixed
    {
        return $_SESSION[$chave] ?? null;
    }

    public function remover(string $chave): void
    {
        unset($_SESSION[$chave]);
    }

    public function regenerarId(): void
    {
        session_regenerate_id(true);
    }

    public function mensagem(string $chave, ?string $valor = null): ?string
    {
        if ($valor !== null) {
            $this->definir('mensagem_' . $chave, $valor);
            return null;
        }

        $mensagem = $this->obter('mensagem_' . $chave);
        $this->remover('mensagem_' . $chave);

        return is_string($mensagem) ? $mensagem : null;
    }
}

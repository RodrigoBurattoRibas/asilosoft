<?php

declare(strict_types=1);

final class FalhaDeTeste extends RuntimeException
{
}

function afirmar(bool $condicao, string $mensagem): void
{
    if (!$condicao) {
        throw new FalhaDeTeste($mensagem);
    }
}

function afirmarIgual(mixed $esperado, mixed $recebido, string $mensagem): void
{
    if ($esperado !== $recebido) {
        throw new FalhaDeTeste($mensagem . ' Esperado: ' . var_export($esperado, true) . '. Recebido: ' . var_export($recebido, true) . '.');
    }
}

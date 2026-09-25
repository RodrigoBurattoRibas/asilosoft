<?php

declare(strict_types=1);

final class FiltroDeSituacao
{
    public function normalizar(string $situacao): string
    {
        return $situacao === 'desativados' ? 'desativados' : 'ativos';
    }

    public function filtrar(array $cadastros, string $situacao): array
    {
        $ativo = $this->normalizar($situacao) === 'ativos';

        return array_values(array_filter(
            $cadastros,
            static fn (array $cadastro): bool => (bool) $cadastro['ativo'] === $ativo
        ));
    }
}

<?php


final class AsiloFiltroSituacao
{
    public function normalizar(string $status): string
    {
        return $status === 'desativados' ? 'desativados' : 'ativos';
    }

    public function filtrar(array $records, string $status): array
    {
        $active = $this->normalizar($status) === 'ativos';

        return array_values(array_filter(
            $records,
            static fn (array $record): bool => (bool) $record['ativo'] === $active
        ));
    }
}

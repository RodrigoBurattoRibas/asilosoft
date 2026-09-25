<?php

declare(strict_types=1);

final class QuartoRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function buscarPorId(int $id): ?array
    {
        $consulta = $this->pdo->prepare('SELECT id, codigo, capacidade, ativo FROM quartos WHERE id = :id LIMIT 1');
        $consulta->execute(['id' => $id]);
        $quarto = $consulta->fetch();

        return $quarto === false ? null : $this->mapear($quarto);
    }

    public function listarDisponiveis(): array
    {
        $consulta = $this->pdo->query(
            'SELECT q.id, q.codigo, q.capacidade, q.ativo, COUNT(i.id) AS ocupacao
             FROM quartos q
             LEFT JOIN idosos i ON i.quarto_id = q.id
             WHERE q.ativo = 1
             GROUP BY q.id, q.codigo, q.capacidade, q.ativo
             HAVING COUNT(i.id) < q.capacidade
             ORDER BY q.codigo ASC'
        );

        return array_map(fn (array $quarto): array => $this->mapear($quarto), $consulta->fetchAll());
    }

    private function mapear(array $quarto): array
    {
        $quarto['id'] = (int) $quarto['id'];
        $quarto['capacidade'] = (int) $quarto['capacidade'];
        $quarto['ativo'] = (bool) $quarto['ativo'];
        if (isset($quarto['ocupacao'])) {
            $quarto['ocupacao'] = (int) $quarto['ocupacao'];
        }

        return $quarto;
    }
}

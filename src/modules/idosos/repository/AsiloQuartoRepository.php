<?php


final class AsiloQuartoRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function buscarPorId(int $id): ?array
    {
        $query = $this->pdo->prepare('SELECT id, codigo, capacidade, ativo FROM quartos WHERE id = :id LIMIT 1');
        $query->execute(['id' => $id]);
        $room = $query->fetch();

        return $room === false ? null : $this->mapear($room);
    }

    public function listarDisponiveis(): array
    {
        $query = $this->pdo->query(
            'SELECT q.id, q.codigo, q.capacidade, q.ativo, COUNT(i.id) AS ocupacao
             FROM quartos q
             LEFT JOIN idosos i ON i.quarto_id = q.id AND i.ativo = 1
             WHERE q.ativo = 1
             GROUP BY q.id, q.codigo, q.capacidade, q.ativo
             HAVING COUNT(i.id) < q.capacidade
             ORDER BY q.codigo ASC'
        );

        return array_map(fn (array $room): array => $this->mapear($room), $query->fetchAll());
    }

    public function listar(): array
    {
        $query = $this->pdo->query(
            'SELECT q.id, q.codigo, q.capacidade, q.ativo, COUNT(i.id) AS ocupacao
             FROM quartos q
             LEFT JOIN idosos i ON i.quarto_id = q.id AND i.ativo = 1
             GROUP BY q.id, q.codigo, q.capacidade, q.ativo
             ORDER BY q.codigo ASC'
        );

        return array_map(fn (array $room): array => $this->mapear($room), $query->fetchAll());
    }

    public function criar(array $data): array
    {
        $query = $this->pdo->prepare(
            'INSERT INTO quartos (codigo, capacidade) VALUES (:codigo, :capacidade)'
        );
        $query->execute($data);

        return $this->buscarPorId((int) $this->pdo->lastInsertId())
            ?? throw new RuntimeException('Não foi possível carregar o quarto cadastrado.');
    }

    public function atualizar(int $id, array $data): array
    {
        $query = $this->pdo->prepare(
            'UPDATE quartos SET codigo = :codigo, capacidade = :capacidade, ativo = :ativo WHERE id = :id'
        );
        $query->execute($data + ['id' => $id]);

        return $this->buscarPorId($id) ?? throw new DomainException('Quarto não encontrado.');
    }

    private function mapear(array $room): array
    {
        $room['id'] = (int) $room['id'];
        $room['capacidade'] = (int) $room['capacidade'];
        $room['ativo'] = (bool) $room['ativo'];
        if (isset($room['ocupacao'])) {
            $room['ocupacao'] = (int) $room['ocupacao'];
        }

        return $room;
    }
}

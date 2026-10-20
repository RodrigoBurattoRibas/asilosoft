<?php

final class AsiloQuartoRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listar(): array
    {
        $query = $this->pdo->query(
            'SELECT q.id, q.codigo, q.capacidade, q.ativo,
                    COUNT(i.id) AS ocupacao
             FROM quartos q
             LEFT JOIN idosos i
                ON i.quarto_id = q.id
               AND i.ativo = 1
             GROUP BY q.id, q.codigo, q.capacidade, q.ativo
             ORDER BY q.codigo ASC'
        );

        return array_map(fn (array $room): array => $this->mapear($room), $query->fetchAll());
    }

    public function listarDisponiveis(): array
    {
        return array_values(array_filter(
            $this->listar(),
            static fn (array $room): bool => $room['ativo'] && $room['ocupacao'] < $room['capacidade']
        ));
    }

    public function buscarPorId(int $id): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT q.id, q.codigo, q.capacidade, q.ativo,
                    (SELECT COUNT(*)
                     FROM idosos i
                     WHERE i.quarto_id = q.id
                       AND i.ativo = 1) AS ocupacao
             FROM quartos q
             WHERE q.id = :id
             LIMIT 1'
        );
        $query->execute(['id' => $id]);
        $room = $query->fetch();

        return $room === false ? null : $this->mapear($room);
    }

    public function buscarPorCodigo(string $codigo): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT id, codigo, capacidade, ativo
             FROM quartos
             WHERE codigo = :codigo
             LIMIT 1'
        );
        $query->execute(['codigo' => $codigo]);
        $room = $query->fetch();

        return $room === false ? null : $this->mapear($room);
    }

    public function contarOcupacao(int $id): int
    {
        $query = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM idosos
             WHERE quarto_id = :quarto_id
               AND ativo = 1'
        );
        $query->execute(['quarto_id' => $id]);

        return (int) $query->fetchColumn();
    }

    public function criar(array $data): array
    {
        $query = $this->pdo->prepare(
            'INSERT INTO quartos (codigo, capacidade, ativo)
             VALUES (:codigo, :capacidade, :ativo)'
        );
        $query->execute([
            'codigo' => $data['codigo'],
            'capacidade' => $data['capacidade'],
            'ativo' => !empty($data['ativo']) ? 1 : 0,
        ]);

        return $this->buscarPorId((int) $this->pdo->lastInsertId())
            ?? throw new RuntimeException('Não foi possível carregar o quarto cadastrado.');
    }

    public function atualizar(int $id, array $data): array
    {
        $allowed = ['codigo', 'capacidade', 'ativo'];
        $data = array_intersect_key($data, array_flip($allowed));
        if ($data === []) {
            return $this->buscarPorId($id) ?? throw new DomainException('Quarto não encontrado.');
        }

        if (array_key_exists('ativo', $data)) {
            $data['ativo'] = !empty($data['ativo']) ? 1 : 0;
        }

        $fields = array_map(
            static fn (string $field): string => "{$field} = :{$field}",
            array_keys($data)
        );
        $data['id'] = $id;
        $query = $this->pdo->prepare(
            'UPDATE quartos SET ' . implode(', ', $fields) . ' WHERE id = :id'
        );
        $query->execute($data);

        return $this->buscarPorId($id)
            ?? throw new DomainException('Quarto não encontrado.');
    }

    private function mapear(array $room): array
    {
        $room['id'] = (int) $room['id'];
        $room['capacidade'] = (int) $room['capacidade'];
        $room['ativo'] = (bool) $room['ativo'];
        if (array_key_exists('ocupacao', $room)) {
            $room['ocupacao'] = (int) $room['ocupacao'];
        }

        return $room;
    }
}

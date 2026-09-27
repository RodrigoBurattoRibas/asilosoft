<?php


require_once __DIR__ . '/AsiloQuartoRepository.php';

final class AsiloIdosoRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function buscarPorCpf(string $cpf): ?array
    {
        return $this->buscarUm('cpf', $cpf);
    }

    public function buscarPorIdentificador(string $identifier): ?array
    {
        return $this->buscarUm('identificador', $identifier);
    }

    public function buscarPorId(int $id): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT id, nome, cpf, identificador, quarto_id, ativo FROM idosos WHERE id = :id LIMIT 1'
        );
        $query->execute(['id' => $id]);
        $resident = $query->fetch();

        return $resident === false ? null : $this->mapear($resident);
    }

    public function buscarQuartoPorId(int $id): ?array
    {
        return (new AsiloQuartoRepository($this->pdo))->buscarPorId($id);
    }

    public function contarOcupacaoDoQuarto(int $roomId): int
    {
        $query = $this->pdo->prepare('SELECT COUNT(*) FROM idosos WHERE quarto_id = :quarto_id AND ativo = 1');
        $query->execute(['quarto_id' => $roomId]);

        return (int) $query->fetchColumn();
    }

    public function criar(array $data): array
    {
        $query = $this->pdo->prepare(
            'INSERT INTO idosos (nome, cpf, identificador, quarto_id)
             VALUES (:nome, :cpf, :identificador, :quarto_id)'
        );
        $query->execute($data);

        return $this->buscarPorId((int) $this->pdo->lastInsertId());
    }

    public function atualizar(int $id, array $data): array
    {
        $current = $this->buscarPorId($id);
        if ($current === null) {
            throw new DomainException('Idoso não encontrado.');
        }

        $data = array_merge($current, $data, ['id' => $id]);
        $data['ativo'] = $data['ativo'] ? 1 : 0;
        unset($data['quarto_codigo']);

        $query = $this->pdo->prepare(
            'UPDATE idosos
             SET nome = :nome, cpf = :cpf, identificador = :identificador,
                 quarto_id = :quarto_id, ativo = :ativo
             WHERE id = :id'
        );
        $query->execute($data);

        return $this->buscarPorId($id);
    }

    public function listar(): array
    {
        $query = $this->pdo->query(
            'SELECT i.id, i.nome, i.cpf, i.identificador, i.quarto_id, i.ativo, q.codigo AS quarto_codigo
             FROM idosos i
             LEFT JOIN quartos q ON q.id = i.quarto_id
             ORDER BY i.nome ASC'
        );

        return array_map(fn (array $resident): array => $this->mapear($resident), $query->fetchAll());
    }

    private function buscarUm(string $field, string $value): ?array
    {
        if (!in_array($field, ['cpf', 'identificador'], true)) {
            throw new InvalidArgumentException('Campo de busca inválido.');
        }

        $query = $this->pdo->prepare(
            "SELECT id, nome, cpf, identificador, quarto_id, ativo
             FROM idosos WHERE {$field} = :value LIMIT 1"
        );
        $query->execute(['value' => $value]);
        $resident = $query->fetch();

        return $resident === false ? null : $this->mapear($resident);
    }

    private function mapear(array $resident): array
    {
        $resident['id'] = (int) $resident['id'];
        $resident['quarto_id'] = $resident['quarto_id'] === null ? null : (int) $resident['quarto_id'];
        $resident['ativo'] = (bool) $resident['ativo'];

        return $resident;
    }
}

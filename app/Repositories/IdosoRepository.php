<?php

declare(strict_types=1);

final class IdosoRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function buscarPorCpf(string $cpf): ?array
    {
        return $this->buscarUm('cpf', $cpf);
    }

    public function buscarPorIdentificador(string $identificador): ?array
    {
        return $this->buscarUm('identificador', $identificador);
    }

    public function buscarQuartoPorId(int $id): ?array
    {
        return (new QuartoRepository($this->pdo))->buscarPorId($id);
    }

    public function contarOcupacaoDoQuarto(int $quartoId): int
    {
        $consulta = $this->pdo->prepare('SELECT COUNT(*) FROM idosos WHERE quarto_id = :quarto_id');
        $consulta->execute(['quarto_id' => $quartoId]);

        return (int) $consulta->fetchColumn();
    }

    public function criar(array $dados): array
    {
        $consulta = $this->pdo->prepare(
            'INSERT INTO idosos (nome, cpf, identificador, quarto_id) VALUES (:nome, :cpf, :identificador, :quarto_id)'
        );
        $consulta->execute($dados);

        return $this->buscarPorId((int) $this->pdo->lastInsertId());
    }

    public function atualizar(int $id, array $dados): array
    {
        $dados['id'] = $id;
        $consulta = $this->pdo->prepare(
            'UPDATE idosos SET nome = :nome, cpf = :cpf, identificador = :identificador, quarto_id = :quarto_id WHERE id = :id'
        );
        $consulta->execute($dados);

        return $this->buscarPorId($id);
    }

    public function listar(): array
    {
        $consulta = $this->pdo->query(
            'SELECT i.id, i.nome, i.cpf, i.identificador, i.quarto_id, q.codigo AS quarto_codigo
             FROM idosos i INNER JOIN quartos q ON q.id = i.quarto_id
             ORDER BY i.nome ASC'
        );

        return array_map(fn (array $idoso): array => $this->mapear($idoso), $consulta->fetchAll());
    }

    private function buscarUm(string $campo, string $valor): ?array
    {
        $consulta = $this->pdo->prepare("SELECT id, nome, cpf, identificador, quarto_id FROM idosos WHERE {$campo} = :valor LIMIT 1");
        $consulta->execute(['valor' => $valor]);
        $idoso = $consulta->fetch();

        return $idoso === false ? null : $this->mapear($idoso);
    }

    public function buscarPorId(int $id): ?array
    {
        $consulta = $this->pdo->prepare('SELECT id, nome, cpf, identificador, quarto_id FROM idosos WHERE id = :id LIMIT 1');
        $consulta->execute(['id' => $id]);

        $idoso = $consulta->fetch();

        return $idoso === false ? null : $this->mapear($idoso);
    }

    private function mapear(array $idoso): array
    {
        $idoso['id'] = (int) $idoso['id'];
        $idoso['quarto_id'] = (int) $idoso['quarto_id'];

        return $idoso;
    }
}

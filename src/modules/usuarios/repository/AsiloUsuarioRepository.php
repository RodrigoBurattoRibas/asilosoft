<?php


final class AsiloUsuarioRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function buscarPorEmail(string $email): ?array
    {
        $query = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $query->execute(['email' => $email]);
        $user = $query->fetch();

        return $user === false ? null : $this->mapear($user);
    }

    public function buscarPorId(int $id): ?array
    {
        $query = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $query->execute(['id' => $id]);
        $user = $query->fetch();

        return $user === false ? null : $this->mapear($user);
    }

    public function listar(): array
    {
        $query = $this->pdo->query('SELECT * FROM usuarios ORDER BY nome ASC');

        return array_map(fn (array $user): array => $this->mapear($user), $query->fetchAll());
    }

    public function criar(array $data): array
    {
        $query = $this->pdo->prepare(
            'INSERT INTO usuarios (nome, email, senha_hash, perfil, ativo)
             VALUES (:nome, :email, :senha_hash, :perfil, :ativo)'
        );
        $query->execute($data);

        return $this->buscarPorId((int) $this->pdo->lastInsertId());
    }

    public function atualizar(int $id, array $data): array
    {
        $allowed = ['nome', 'email', 'senha_hash', 'perfil', 'ativo'];
        $data = array_intersect_key($data, array_flip($allowed));
        if ($data === []) {
            return $this->buscarPorId($id) ?? throw new DomainException('Usuário não encontrado.');
        }

        $fields = array_map(static fn (string $field): string => "{$field} = :{$field}", array_keys($data));
        $data['id'] = $id;
        $query = $this->pdo->prepare('UPDATE usuarios SET ' . implode(', ', $fields) . ' WHERE id = :id');
        $query->execute($data);

        return $this->buscarPorId($id) ?? throw new DomainException('Usuário não encontrado.');
    }

    public function contarAdministradoresAtivos(): int
    {
        return (int) $this->pdo
            ->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'Administrador' AND ativo = 1")
            ->fetchColumn();
    }

    private function mapear(array $user): array
    {
        $user['id'] = (int) $user['id'];
        $user['ativo'] = (bool) $user['ativo'];

        return $user;
    }
}

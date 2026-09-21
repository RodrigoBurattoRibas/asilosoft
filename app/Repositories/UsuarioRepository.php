<?php

declare(strict_types=1);

final class UsuarioRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function buscarPorEmail(string $email): ?array
    {
        $consulta = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $consulta->execute(['email' => $email]);
        $usuario = $consulta->fetch();

        return $usuario === false ? null : $this->mapear($usuario);
    }

    public function buscarPorId(int $id): ?array
    {
        $consulta = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $consulta->execute(['id' => $id]);
        $usuario = $consulta->fetch();

        return $usuario === false ? null : $this->mapear($usuario);
    }

    public function listar(): array
    {
        $consulta = $this->pdo->query('SELECT * FROM usuarios ORDER BY nome ASC');

        return array_map(fn (array $usuario): array => $this->mapear($usuario), $consulta->fetchAll());
    }

    public function criar(array $dados): array
    {
        $consulta = $this->pdo->prepare(
            'INSERT INTO usuarios (nome, email, senha_hash, perfil, ativo) VALUES (:nome, :email, :senha_hash, :perfil, :ativo)'
        );
        $consulta->execute($dados);

        return $this->buscarPorId((int) $this->pdo->lastInsertId());
    }

    public function atualizar(int $id, array $dados): array
    {
        $campos = [];
        foreach ($dados as $campo => $_) {
            $campos[] = "{$campo} = :{$campo}";
        }
        $dados['id'] = $id;
        $consulta = $this->pdo->prepare('UPDATE usuarios SET ' . implode(', ', $campos) . ' WHERE id = :id');
        $consulta->execute($dados);

        return $this->buscarPorId($id);
    }

    public function contarAdministradoresAtivos(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'Administrador' AND ativo = 1")->fetchColumn();
    }

    private function mapear(array $usuario): array
    {
        $usuario['id'] = (int) $usuario['id'];
        $usuario['ativo'] = (bool) $usuario['ativo'];

        return $usuario;
    }
}

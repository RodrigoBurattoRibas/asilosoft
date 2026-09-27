<?php


final class AsiloRepositorioUsuariosSessao
{
    private const KEY = 'asilosoft_usuarios_demonstracao';

    public function __construct(private object $session)
    {
        if (!is_array($this->session->get(self::KEY))) {
            $this->session->set(self::KEY, [[
                'id' => 1,
                'nome' => 'Administrador de demonstração',
                'email' => 'admin@asilosoft.local',
                'senha_hash' => password_hash('asilosoft123', PASSWORD_DEFAULT),
                'perfil' => 'Administrador',
                'ativo' => true,
            ]]);
        }
    }

    public function buscarPorEmail(string $email): ?array
    {
        foreach ($this->usuarios() as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }

        return null;
    }

    public function buscarPorId(int $id): ?array
    {
        foreach ($this->usuarios() as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }

        return null;
    }

    public function listar(): array
    {
        $users = $this->usuarios();
        usort($users, static fn (array $first, array $second): int => $first['nome'] <=> $second['nome']);

        return $users;
    }

    public function criar(array $data): array
    {
        $users = $this->usuarios();
        $ids = array_column($users, 'id');
        $user = $data + ['id' => $ids === [] ? 1 : max($ids) + 1];
        $users[] = $user;
        $this->save($users);

        return $user;
    }

    public function atualizar(int $id, array $data): array
    {
        $users = $this->usuarios();
        foreach ($users as $index => $user) {
            if ($user['id'] === $id) {
                $users[$index] = array_merge($user, $data);
                $this->save($users);

                return $users[$index];
            }
        }

        throw new DomainException('Usuário não encontrado.');
    }

    public function contarAdministradoresAtivos(): int
    {
        return count(array_filter(
            $this->usuarios(),
            static fn (array $user): bool => $user['perfil'] === 'Administrador' && $user['ativo'] === true
        ));
    }

    private function usuarios(): array
    {
        return $this->session->get(self::KEY);
    }

    private function save(array $users): void
    {
        $this->session->set(self::KEY, $users);
    }
}

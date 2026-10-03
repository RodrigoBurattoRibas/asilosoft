<?php


final class AsiloUsuarioService
{
    private const ALLOWED_PROFILES = [
        'Administrador',
        'Profissional de Saúde',
        'Almoxarife',
        'Familiar',
    ];

    public function __construct(private object $repository)
    {
    }

    public function listar(): array
    {
        return $this->repository->listar();
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->repository->buscarPorId($id);
    }

    public function criar(array $data): array
    {
        $name = trim((string) ($data['nome'] ?? ''));
        $email = mb_strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['senha'] ?? '');
        $profile = (string) ($data['perfil'] ?? '');

        $this->validarDados($name, $email, $password, $profile);

        if ($this->repository->buscarPorEmail($email) !== null) {
            throw new DomainException('Este e-mail já está em uso.');
        }

        return $this->repository->criar([
            'nome' => $name,
            'email' => $email,
            'senha_hash' => password_hash($password, PASSWORD_DEFAULT),
            'perfil' => $profile,
            'ativo' => true,
        ]);
    }

    public function atualizar(int $id, array $data): array
    {
        $user = $this->repository->buscarPorId($id);
        if ($user === null) {
            throw new DomainException('Usuário não encontrado.');
        }

        $name = trim((string) ($data['nome'] ?? ''));
        $email = mb_strtolower(trim((string) ($data['email'] ?? '')));
        $profile = (string) ($data['perfil'] ?? '');
        $password = (string) ($data['senha'] ?? '');

        $this->validarDados($name, $email, $password, $profile, true);
        $existing = $this->repository->buscarPorEmail($email);
        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new DomainException('Este e-mail já está em uso.');
        }

        $update = ['nome' => $name, 'email' => $email, 'perfil' => $profile];
        if ($password !== '') {
            $update['senha_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        return $this->repository->atualizar($id, $update);
    }

    public function alterarStatus(int $id, bool $active): array
    {
        $user = $this->repository->buscarPorId($id);
        if ($user === null) {
            throw new DomainException('Usuário não encontrado.');
        }

        if (
            !$active
            && $user['perfil'] === 'Administrador'
            && $user['ativo'] === true
            && $this->repository->contarAdministradoresAtivos() <= 1
        ) {
            throw new DomainException('Mantenha ao menos um administrador ativo.');
        }

        return $this->repository->atualizar($id, ['ativo' => $active]);
    }

    private function validarDados(
        string $name,
        string $email,
        string $password,
        string $profile,
        bool $passwordOptional = false
    ): void {
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Informe nome e e-mail válidos.');
        }

        if ((!$passwordOptional || $password !== '') && strlen($password) < 8) {
            throw new DomainException('A senha deve ter pelo menos 8 caracteres.');
        }

        if (!in_array($profile, self::ALLOWED_PROFILES, true)) {
            throw new DomainException('Selecione um perfil válido.');
        }
    }
}

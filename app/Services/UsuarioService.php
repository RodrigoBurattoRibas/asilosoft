<?php

declare(strict_types=1);

final class UsuarioService
{
    private const PERFIS_PERMITIDOS = [
        'Administrador',
        'Profissional de Saúde',
        'Almoxarife',
        'Familiar',
    ];

    public function __construct(private object $repositorio)
    {
    }

    public function criar(array $dados): array
    {
        $nome = trim((string) ($dados['nome'] ?? ''));
        $email = mb_strtolower(trim((string) ($dados['email'] ?? '')));
        $senha = (string) ($dados['senha'] ?? '');
        $perfil = (string) ($dados['perfil'] ?? '');

        $this->validarDados($nome, $email, $senha, $perfil);

        if ($this->repositorio->buscarPorEmail($email) !== null) {
            throw new DomainException('Este e-mail já está em uso.');
        }

        return $this->repositorio->criar([
            'nome' => $nome,
            'email' => $email,
            'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
            'perfil' => $perfil,
            'ativo' => true,
        ]);
    }

    public function alterarStatus(int $id, bool $ativo): array
    {
        $usuario = $this->repositorio->buscarPorId($id);
        if ($usuario === null) {
            throw new DomainException('Usuário não encontrado.');
        }

        if (!$ativo && $usuario['perfil'] === 'Administrador' && $usuario['ativo'] === true
            && $this->repositorio->contarAdministradoresAtivos() <= 1) {
            throw new DomainException('Mantenha ao menos um administrador ativo.');
        }

        return $this->repositorio->atualizar($id, ['ativo' => $ativo]);
    }

    public function atualizar(int $id, array $dados): array
    {
        $usuario = $this->repositorio->buscarPorId($id);
        if ($usuario === null) {
            throw new DomainException('Usuário não encontrado.');
        }

        $nome = trim((string) ($dados['nome'] ?? ''));
        $email = mb_strtolower(trim((string) ($dados['email'] ?? '')));
        $perfil = (string) ($dados['perfil'] ?? '');
        $senha = (string) ($dados['senha'] ?? '');

        $this->validarDados($nome, $email, $senha, $perfil, $senha === '');
        $existente = $this->repositorio->buscarPorEmail($email);
        if ($existente !== null && (int) $existente['id'] !== $id) {
            throw new DominioException('Este e-mail já está em uso.');
        }

        $atualizacao = ['nome' => $nome, 'email' => $email, 'perfil' => $perfil];
        if ($senha !== '') {
            $atualizacao['senha_hash'] = password_hash($senha, PASSWORD_DEFAULT);
        }

        return $this->repositorio->atualizar($id, $atualizacao);
    }

    private function validarDados(string $nome, string $email, string $senha, string $perfil, bool $senhaOpcional = false): void
    {
        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Informe nome e e-mail válidos.');
        }

        if (!$senhaOpcional && strlen($senha) < 8) {
            throw new DomainException('A senha deve ter pelo menos 8 caracteres.');
        }

        if (!in_array($perfil, self::PERFIS_PERMITIDOS, true)) {
            throw new DomainException('Selecione um perfil válido.');
        }
    }
}

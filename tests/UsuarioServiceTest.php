<?php

declare(strict_types=1);

require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../app/Services/UsuarioService.php';

final class RepositorioUsuariosEmMemoria
{
    /** @var array<int, array<string, mixed>> */
    private array $usuarios = [];
    private int $proximoId = 1;

    public function buscarPorEmail(string $email): ?array
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario['email'] === $email) {
                return $usuario;
            }
        }

        return null;
    }

    public function criar(array $dados): array
    {
        $usuario = $dados + ['id' => $this->proximoId++];
        $this->usuarios[$usuario['id']] = $usuario;

        return $usuario;
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->usuarios[$id] ?? null;
    }

    public function atualizar(int $id, array $dados): array
    {
        $this->usuarios[$id] = array_merge($this->usuarios[$id], $dados);

        return $this->usuarios[$id];
    }

    public function contarAdministradoresAtivos(): int
    {
        return count(array_filter(
            $this->usuarios,
            static fn (array $usuario): bool => $usuario['perfil'] === 'Administrador' && $usuario['ativo'] === true
        ));
    }
}

function testarCadastroNormalizaEmailEProtegeSenha(): void
{
    $servico = new UsuarioService(new RepositorioUsuariosEmMemoria());
    $usuario = $servico->criar([
        'nome' => 'Ana da Silva',
        'email' => ' ANA@EXEMPLO.COM ',
        'senha' => 'senha-segura-123',
        'perfil' => 'Administrador',
    ]);

    afirmarIgual('ana@exemplo.com', $usuario['email'], 'O e-mail precisa ser salvo normalizado.');
    afirmar($usuario['senha_hash'] !== 'senha-segura-123', 'A senha jamais pode ser armazenada em texto puro.');
    afirmar(password_verify('senha-segura-123', $usuario['senha_hash']), 'O hash deve validar a senha cadastrada.');
    afirmar($usuario['ativo'] === true, 'Um novo usuário deve começar ativo.');
}

function testarCadastroRejeitaEmailDuplicadoSemDiferenciarMaiusculas(): void
{
    $repositorio = new RepositorioUsuariosEmMemoria();
    $servico = new UsuarioService($repositorio);
    $servico->criar([
        'nome' => 'Ana',
        'email' => 'ana@exemplo.com',
        'senha' => 'senha-segura-123',
        'perfil' => 'Administrador',
    ]);

    try {
        $servico->criar([
            'nome' => 'Outra Ana',
            'email' => 'ANA@EXEMPLO.COM',
            'senha' => 'outra-senha-123',
            'perfil' => 'Familiar',
        ]);
        throw new FalhaDeTeste('O cadastro deveria impedir e-mail já utilizado.');
    } catch (DomainException $erro) {
        afirmarIgual('Este e-mail já está em uso.', $erro->getMessage(), 'O erro de duplicidade deve orientar o usuário.');
    }
}

function testarNaoDesativaUltimoAdministradorAtivo(): void
{
    $repositorio = new RepositorioUsuariosEmMemoria();
    $servico = new UsuarioService($repositorio);
    $administrador = $servico->criar([
        'nome' => 'Administrador',
        'email' => 'admin@exemplo.com',
        'senha' => 'senha-segura-123',
        'perfil' => 'Administrador',
    ]);

    try {
        $servico->alterarStatus($administrador['id'], false);
        throw new FalhaDeTeste('O último administrador ativo não pode ser desativado.');
    } catch (DomainException $erro) {
        afirmarIgual('Mantenha ao menos um administrador ativo.', $erro->getMessage(), 'A regra deve proteger o acesso administrativo.');
    }
}

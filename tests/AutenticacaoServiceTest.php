<?php

declare(strict_types=1);

require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../app/Services/AutenticacaoService.php';

final class RepositorioParaAutenticacao
{
    public function __construct(private ?array $usuario)
    {
    }

    public function buscarPorEmail(string $email): ?array
    {
        return $this->usuario !== null && $this->usuario['email'] === $email ? $this->usuario : null;
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->usuario !== null && $this->usuario['id'] === $id ? $this->usuario : null;
    }
}

final class SessaoEmMemoria
{
    public array $dados = [];
    public bool $regenerada = false;

    public function definir(string $chave, mixed $valor): void
    {
        $this->dados[$chave] = $valor;
    }

    public function obter(string $chave): mixed
    {
        return $this->dados[$chave] ?? null;
    }

    public function remover(string $chave): void
    {
        unset($this->dados[$chave]);
    }

    public function regenerarId(): void
    {
        $this->regenerada = true;
    }
}

function testarLoginValidoGuardaSomenteIdentificadorNaSessao(): void
{
    $usuario = [
        'id' => 8,
        'email' => 'profissional@exemplo.com',
        'senha_hash' => password_hash('senha-correta-123', PASSWORD_DEFAULT),
        'perfil' => 'Profissional de Saúde',
        'ativo' => true,
    ];
    $sessao = new SessaoEmMemoria();
    $servico = new AutenticacaoService(new RepositorioParaAutenticacao($usuario), $sessao);

    afirmar($servico->tentarLogin(' PROFISSIONAL@EXEMPLO.COM ', 'senha-correta-123'), 'Credenciais válidas devem iniciar a sessão.');
    afirmarIgual(['usuario_id' => 8], $sessao->dados, 'A sessão não deve guardar senha, e-mail ou perfil.');
    afirmar($sessao->regenerada, 'O identificador de sessão deve ser regenerado no login.');
}

function testarLoginRejeitaSenhaIncorretaComMensagemGenerica(): void
{
    $usuario = [
        'id' => 8,
        'email' => 'profissional@exemplo.com',
        'senha_hash' => password_hash('senha-correta-123', PASSWORD_DEFAULT),
        'perfil' => 'Profissional de Saúde',
        'ativo' => true,
    ];
    $servico = new AutenticacaoService(new RepositorioParaAutenticacao($usuario), new SessaoEmMemoria());

    try {
        $servico->tentarLogin('profissional@exemplo.com', 'senha-errada');
        throw new FalhaDeTeste('Uma senha incorreta não pode iniciar sessão.');
    } catch (DomainException $erro) {
        afirmarIgual('E-mail ou senha inválidos.', $erro->getMessage(), 'A falha não deve revelar qual credencial está errada.');
    }
}

function testarLoginRejeitaUsuarioInativo(): void
{
    $usuario = [
        'id' => 8,
        'email' => 'inativo@exemplo.com',
        'senha_hash' => password_hash('senha-correta-123', PASSWORD_DEFAULT),
        'perfil' => 'Familiar',
        'ativo' => false,
    ];
    $servico = new AutenticacaoService(new RepositorioParaAutenticacao($usuario), new SessaoEmMemoria());

    try {
        $servico->tentarLogin('inativo@exemplo.com', 'senha-correta-123');
        throw new FalhaDeTeste('Um usuário inativo não pode iniciar sessão.');
    } catch (DomainException $erro) {
        afirmarIgual('E-mail ou senha inválidos.', $erro->getMessage(), 'O retorno deve continuar genérico para usuário inativo.');
    }
}

<?php

declare(strict_types=1);

require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../app/Repositories/RepositorioDeSessao.php';

final class ArmazenamentoDeSessaoEmMemoria
{
    public array $dados = [];

    public function definir(string $chave, mixed $valor): void
    {
        $this->dados[$chave] = $valor;
    }

    public function obter(string $chave): mixed
    {
        return $this->dados[$chave] ?? null;
    }
}

function testarRepositorioDeSessaoDisponibilizaAdministradorDeDemonstracao(): void
{
    $repositorio = new RepositorioDeSessao(new ArmazenamentoDeSessaoEmMemoria());
    $administrador = $repositorio->buscarPorEmail('admin@asilosoft.local');

    afirmar($administrador !== null, 'O modo demonstração precisa disponibilizar um administrador inicial.');
    afirmar($administrador['ativo'] === true, 'O administrador temporário deve estar ativo.');
    afirmar($administrador['perfil'] === 'Administrador', 'O administrador temporário deve ter perfil administrativo.');
    afirmar(password_verify('asilosoft123', $administrador['senha_hash']), 'A senha de demonstração precisa permitir o primeiro acesso.');
}

function testarRepositorioDeSessaoMantemUsuarioCriadoDuranteASessao(): void
{
    $sessao = new ArmazenamentoDeSessaoEmMemoria();
    $repositorio = new RepositorioDeSessao($sessao);
    $criado = $repositorio->criar([
        'nome' => 'Novo usuário',
        'email' => 'novo@exemplo.com',
        'senha_hash' => password_hash('senha-segura-123', PASSWORD_DEFAULT),
        'perfil' => 'Familiar',
        'ativo' => true,
    ]);

    $repositorioReaberto = new RepositorioDeSessao($sessao);
    $encontrado = $repositorioReaberto->buscarPorId($criado['id']);

    afirmarIgual('novo@exemplo.com', $encontrado['email'], 'O usuário criado deve continuar disponível enquanto a sessão existir.');
}

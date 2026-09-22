<?php

declare(strict_types=1);

final class RepositorioDeSessao
{
    private const CHAVE_USUARIOS = 'usuarios_demonstracao';

    public function __construct(private object $sessao)
    {
        if (!is_array($this->sessao->obter(self::CHAVE_USUARIOS))) {
            $this->sessao->definir(self::CHAVE_USUARIOS, [[
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
        foreach ($this->usuarios() as $usuario) {
            if ($usuario['email'] === $email) {
                return $usuario;
            }
        }

        return null;
    }

    public function buscarPorId(int $id): ?array
    {
        foreach ($this->usuarios() as $usuario) {
            if ($usuario['id'] === $id) {
                return $usuario;
            }
        }

        return null;
    }

    public function listar(): array
    {
        $usuarios = $this->usuarios();
        usort($usuarios, static fn (array $primeiro, array $segundo): int => $primeiro['nome'] <=> $segundo['nome']);

        return $usuarios;
    }

    public function criar(array $dados): array
    {
        $usuarios = $this->usuarios();
        $ids = array_column($usuarios, 'id');
        $usuario = $dados + ['id' => $ids === [] ? 1 : max($ids) + 1];
        $usuarios[] = $usuario;
        $this->salvar($usuarios);

        return $usuario;
    }

    public function atualizar(int $id, array $dados): array
    {
        $usuarios = $this->usuarios();
        foreach ($usuarios as $indice => $usuario) {
            if ($usuario['id'] === $id) {
                $usuarios[$indice] = array_merge($usuario, $dados);
                $this->salvar($usuarios);
                return $usuarios[$indice];
            }
        }

        throw new DomainException('Usuário não encontrado.');
    }

    public function contarAdministradoresAtivos(): int
    {
        return count(array_filter(
            $this->usuarios(),
            static fn (array $usuario): bool => $usuario['perfil'] === 'Administrador' && $usuario['ativo'] === true
        ));
    }

    private function usuarios(): array
    {
        return $this->sessao->obter(self::CHAVE_USUARIOS);
    }

    private function salvar(array $usuarios): void
    {
        $this->sessao->definir(self::CHAVE_USUARIOS, $usuarios);
    }
}

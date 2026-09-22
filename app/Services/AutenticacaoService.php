<?php

declare(strict_types=1);

final class AutenticacaoService
{
    public function __construct(private object $repositorio, private object $sessao)
    {
    }

    public function tentarLogin(string $email, string $senha): bool
    {
        $email = mb_strtolower(trim($email));
        $usuario = $this->repositorio->buscarPorEmail($email);

        if ($usuario === null || !$usuario['ativo'] || !password_verify($senha, $usuario['senha_hash'])) {
            throw new DomainException('E-mail ou senha inválidos.');
        }

        $this->sessao->regenerarId();
        $this->sessao->definir('usuario_id', (int) $usuario['id']);

        return true;
    }

    public function usuarioAtual(): ?array
    {
        $id = $this->sessao->obter('usuario_id');
        if (!is_int($id) && !ctype_digit((string) $id)) {
            return null;
        }

        $usuario = $this->repositorio->buscarPorId((int) $id);
        if ($usuario === null || !$usuario['ativo']) {
            $this->encerrarSessao();
            return null;
        }

        return $usuario;
    }

    public function encerrarSessao(): void
    {
        $this->sessao->remover('usuario_id');
        $this->sessao->regenerarId();
    }
}

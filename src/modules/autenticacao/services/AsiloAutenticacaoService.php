<?php


final class AsiloAutenticacaoService
{
    private const SESSION_KEY = 'asilosoft_usuario_id';

    public function __construct(private object $repository, private object $session)
    {
    }

    public function tentarLogin(string $email, string $password): bool
    {
        $email = mb_strtolower(trim($email));
        $user = $this->repository->buscarPorEmail($email);

        if ($user === null || !$user['ativo'] || !password_verify($password, $user['senha_hash'])) {
            throw new DomainException('E-mail ou senha inválidos.');
        }

        $this->session->regenerateId();
        $this->session->set(self::SESSION_KEY, (int) $user['id']);

        return true;
    }

    public function usuarioAtual(): ?array
    {
        $id = $this->session->get(self::SESSION_KEY);
        if (!is_int($id) && !ctype_digit((string) $id)) {
            return null;
        }

        $user = $this->repository->buscarPorId((int) $id);
        if ($user === null || !$user['ativo']) {
            $this->encerrarSessao();
            return null;
        }

        return $user;
    }

    public function encerrarSessao(): void
    {
        $this->session->remove(self::SESSION_KEY);
        $this->session->regenerateId();
    }
}

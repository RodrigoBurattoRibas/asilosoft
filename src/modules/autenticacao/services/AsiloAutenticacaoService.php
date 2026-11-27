<?php


final class AsiloAutenticacaoService
{
    private const SESSION_KEY = 'asilosoft_usuario_id';
    public const MAXIMO_FALHAS = 5;
    public const JANELA_MINUTOS = 15;
    // Hash bcrypt de uma senha aleatória descartada. Comparar com ele quando o e-mail não
    // existe deixa o tempo de resposta igual ao de uma conta real (sem enumerar e-mails).
    private const HASH_FICTICIO = '$2y$12$aPeKmCmn1bo3DnaLTb8UZezuGqetOM5o0A9iE8yL.WCYo3CFhliOS';

    public function __construct(
        private object $repository,
        private object $session,
        private ?object $attempts = null
    ) {
    }

    public function tentarLogin(string $email, string $password): bool
    {
        $email = mb_strtolower(trim($email));
        $key = hash('sha256', $email);

        // Conta também e-mails inexistentes, para o bloqueio não revelar quais contas existem.
        if ($this->attempts !== null
            && (int) $this->attempts->falhasRecentes($key, self::JANELA_MINUTOS) >= self::MAXIMO_FALHAS) {
            throw new DomainException(
                'Muitas tentativas sem sucesso. Aguarde ' . self::JANELA_MINUTOS . ' minutos e tente novamente.'
            );
        }

        $user = $this->repository->buscarPorEmail($email);
        $validPassword = password_verify($password, $user['senha_hash'] ?? self::HASH_FICTICIO);

        if ($user === null || !$user['ativo'] || !$validPassword) {
            $this->attempts?->registrarFalha($key);
            throw new DomainException('E-mail ou senha inválidos.');
        }

        $this->attempts?->limpar($key);
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

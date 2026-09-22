<?php

declare(strict_types=1);

final class ControladorAutenticacao
{
    public function __construct(private AutenticacaoService $autenticacao, private Sessao $sessao, private Csrf $csrf)
    {
    }

    public function formulario(): void
    {
        if ($this->autenticacao->usuarioAtual() !== null) {
            redirecionar('/usuarios');
        }

        renderizar('login', ['titulo' => 'Entrar', 'csrf' => $this->csrf->token()]);
    }

    public function entrar(): void
    {
        validarCsrf($this->csrf);
        try {
            $this->autenticacao->tentarLogin((string) ($_POST['email'] ?? ''), (string) ($_POST['senha'] ?? ''));
            redirecionar('/usuarios');
        } catch (DomainException $erro) {
            $this->sessao->mensagem('erro', $erro->getMessage());
            redirecionar('/login');
        }
    }

    public function sair(): void
    {
        validarCsrf($this->csrf);
        $this->autenticacao->encerrarSessao();
        $this->sessao->mensagem('sucesso', 'Sessão encerrada com sucesso.');
        redirecionar('/login');
    }
}

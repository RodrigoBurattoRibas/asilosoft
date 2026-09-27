<?php


require_once dirname(__DIR__, 3) . '/core/Http/AsiloController.php';

final class AsiloAutenticacaoController extends AsiloController
{
    public function formulario(): void
    {
        if ($this->authentication->usuarioAtual() !== null) {
            $this->redirect('usuarios');
        }

        $this->view(dirname(__DIR__) . '/view/login.php', [
            'titulo' => 'Entrar',
            'csrfToken' => CsrfToken::obter('asilosoft_login'),
        ]);
    }

    public function entrar(): never
    {
        try {
            $this->authentication->tentarLogin(
                (string) ($_POST['email'] ?? ''),
                (string) ($_POST['senha'] ?? '')
            );
            $this->redirect('usuarios');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('login');
        }
    }

    public function sair(): never
    {
        $this->authentication->encerrarSessao();
        $this->session->flash('sucesso', 'Sessão encerrada com sucesso.');
        $this->redirect('login');
    }
}

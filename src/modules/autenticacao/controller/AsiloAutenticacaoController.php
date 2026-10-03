<?php

require_once dirname(__DIR__, 3) . '/core/Session/AsiloSession.php';
require_once dirname(__DIR__, 3) . '/core/Security/CsrfToken.php';
require_once dirname(__DIR__, 3) . '/infra/Database/Database.php';
require_once dirname(__DIR__, 3) . '/presentation/layout/AsiloView.php';
require_once dirname(__DIR__, 2) . '/usuarios/repository/AsiloUsuarioRepository.php';
require_once dirname(__DIR__, 2) . '/usuarios/repository/AsiloRepositorioUsuariosSessao.php';
require_once dirname(__DIR__) . '/services/AsiloAutenticacaoService.php';

final class AsiloAutenticacaoController
{
    private AsiloSession $session;
    private AsiloAutenticacaoService $authentication;
    private bool $demoMode = false;

    public function __construct()
    {
        $this->session = new AsiloSession();

        try {
            $repository = new AsiloUsuarioRepository(PDOMysqli::connection());
        } catch (PDOException) {
            $this->demoMode = true;
            $repository = new AsiloRepositorioUsuariosSessao($this->session);
        }

        $this->authentication = new AsiloAutenticacaoService($repository, $this->session);
    }

    public function formulario(): void
    {
        if ($this->authentication->usuarioAtual() !== null) {
            $this->redirect('usuarios');
        }

        AsiloView::render(dirname(__DIR__) . '/view/login.php', [
            'titulo' => 'Entrar',
            'csrfToken' => CsrfToken::obter('asilosoft_login'),
            'modoDemonstracao' => $this->demoMode,
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

    private function redirect(string $route, array $query = []): never
    {
        $parameters = http_build_query(['route' => $route] + $query);
        header('Location: ?' . $parameters, true, 303);
        exit;
    }
}

<?php


require_once dirname(__DIR__) . '/Bootstrap/AsiloRuntime.php';
require_once dirname(__DIR__, 2) . '/presentation/layout/AsiloView.php';
require_once dirname(__DIR__) . '/Security/CsrfToken.php';
require_once dirname(__DIR__, 2) . '/modules/autenticacao/services/AsiloAutenticacaoService.php';

abstract class AsiloController
{
    protected AsiloRuntime $runtime;
    protected AsiloSession $session;
    protected AsiloAutenticacaoService $authentication;

    public function __construct()
    {
        $this->runtime = new AsiloRuntime();
        $this->session = $this->runtime->session();
        $this->authentication = new AsiloAutenticacaoService(
            $this->runtime->userRepository(),
            $this->session
        );
    }

    protected function exigirAdministrador(): array
    {
        $user = $this->authentication->usuarioAtual();
        if ($user === null) {
            $this->redirect('login');
        }

        if ($user['perfil'] !== 'Administrador') {
            http_response_code(403);
            $this->view(
                dirname(__DIR__, 2) . '/presentation/components/asiloAcessoNegado.php',
                [
                    'titulo' => 'Acesso negado',
                    'usuarioAtual' => $user,
                    'csrfToken' => CsrfToken::obter('asilosoft_sair'),
                ]
            );
            exit;
        }

        return $user;
    }

    protected function view(string $viewFile, array $data = []): void
    {
        $data['modoDemonstracao'] = $this->runtime->demoMode();
        AsiloView::render($viewFile, $data);
    }

    protected function redirect(string $route, array $query = []): never
    {
        $parameters = http_build_query(['route' => $route] + $query);
        header('Location: ?' . $parameters, true, 303);
        exit;
    }

    protected function requireId(): int
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!is_int($id) || $id <= 0) {
            $this->notFound();
        }

        return $id;
    }

    protected function notFound(): never
    {
        http_response_code(404);
        $this->view(
            dirname(__DIR__, 2) . '/presentation/components/asiloNaoEncontrado.php',
            ['titulo' => 'Página não encontrada']
        );
        exit;
    }
}

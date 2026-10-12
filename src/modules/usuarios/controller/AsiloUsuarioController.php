<?php

require_once dirname(__DIR__, 3) . '/core/Session/AsiloSession.php';
require_once dirname(__DIR__, 3) . '/core/Security/CsrfToken.php';
require_once dirname(__DIR__, 3) . '/infra/Database/Database.php';
require_once dirname(__DIR__, 3) . '/presentation/layout/AsiloView.php';
require_once dirname(__DIR__, 2) . '/autenticacao/services/AsiloAutenticacaoService.php';
require_once dirname(__DIR__) . '/repository/AsiloUsuarioRepository.php';
require_once dirname(__DIR__) . '/services/AsiloUsuarioService.php';
require_once dirname(__DIR__) . '/services/AsiloFiltroSituacao.php';

final class AsiloUsuarioController
{
    private AsiloSession $session;
    private AsiloAutenticacaoService $authentication;
    private AsiloUsuarioService $users;
    private AsiloFiltroSituacao $filter;

    public function __construct()
    {
        $this->session = new AsiloSession();

        $repository = new AsiloUsuarioRepository(PDOMysqli::connection());
        $this->authentication = new AsiloAutenticacaoService($repository, $this->session);
        $this->users = new AsiloUsuarioService($repository);
        $this->filter = new AsiloFiltroSituacao();
    }

    public function listar(): void
    {
        $currentUser = $this->exigirAdministrador();
        $status = $this->filter->normalizar((string) ($_GET['situacao'] ?? 'ativos'));

        $this->view(dirname(__DIR__) . '/view/lista.php', [
            'titulo' => 'Usuários',
            'usuarioAtual' => $currentUser,
            'usuarios' => $this->filter->filtrar($this->users->listar(), $status),
            'situacao' => $status,
            'secaoAtual' => 'usuarios',
            'csrfToken' => CsrfToken::obter('asilosoft_usuarios'),
        ]);
    }

    public function novo(): void
    {
        $currentUser = $this->exigirAdministrador();

        $this->view(dirname(__DIR__) . '/view/formulario.php', [
            'titulo' => 'Novo usuário',
            'usuarioAtual' => $currentUser,
            'usuario' => null,
            'secaoAtual' => 'usuarios',
            'csrfToken' => CsrfToken::obter('asilosoft_usuarios'),
        ]);
    }

    public function criar(): never
    {
        $this->exigirAdministrador();

        try {
            $this->users->criar($_POST);
            $this->session->flash('sucesso', 'Usuário cadastrado com sucesso.');
            $this->redirect('usuarios');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('usuarios-novo');
        }
    }

    public function editar(): void
    {
        $currentUser = $this->exigirAdministrador();
        $user = $this->users->buscarPorId($this->requireId());
        if ($user === null) {
            $this->notFound();
        }

        $this->view(dirname(__DIR__) . '/view/formulario.php', [
            'titulo' => 'Editar usuário',
            'usuarioAtual' => $currentUser,
            'usuario' => $user,
            'secaoAtual' => 'usuarios',
            'csrfToken' => CsrfToken::obter('asilosoft_usuarios'),
        ]);
    }

    public function atualizar(): never
    {
        $this->exigirAdministrador();
        $id = $this->requireId();

        try {
            $this->users->atualizar($id, $_POST);
            $this->session->flash('sucesso', 'Dados do usuário atualizados.');
            $this->redirect('usuarios');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('usuarios-editar', ['id' => $id]);
        }
    }

    public function alterarStatus(): never
    {
        $this->exigirAdministrador();

        try {
            $status = $_POST['ativo'] ?? null;
            if (!is_string($status) || !in_array($status, ['0', '1'], true)) {
                throw new DomainException('Situação de usuário inválida.');
            }

            $this->users->alterarStatus(
                $this->requireId(),
                $status === '1'
            );
            $this->session->flash('sucesso', 'Situação do usuário atualizada.');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
        }

        $this->redirect('usuarios');
    }

    private function exigirAdministrador(): array
    {
        $user = $this->authentication->usuarioAtual();
        if ($user === null) {
            $this->redirect('login');
        }

        if ($user['perfil'] !== 'Administrador') {
            $this->session->flash('erro', 'Você não tem permissão para acessar a administração de usuários.');
            $this->redirect('home');
        }

        return $user;
    }

    private function view(string $viewFile, array $data = []): void
    {
        AsiloView::render($viewFile, $data);
    }

    private function redirect(string $route, array $query = []): never
    {
        $parameters = http_build_query(['route' => $route] + $query);
        header('Location: ?' . $parameters, true, 303);
        exit;
    }

    private function requireId(): int
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!is_int($id) || $id <= 0) {
            $this->notFound();
        }

        return $id;
    }

    private function notFound(): never
    {
        http_response_code(404);
        $this->view(
            dirname(__DIR__, 3) . '/presentation/components/asiloNaoEncontrado.php',
            ['titulo' => 'Página não encontrada']
        );
        exit;
    }

}

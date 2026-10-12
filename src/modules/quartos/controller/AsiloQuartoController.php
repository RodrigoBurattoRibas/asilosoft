<?php

require_once dirname(__DIR__, 3) . '/core/Session/AsiloSession.php';
require_once dirname(__DIR__, 3) . '/core/Security/CsrfToken.php';
require_once dirname(__DIR__, 3) . '/infra/Database/Database.php';
require_once dirname(__DIR__, 3) . '/presentation/layout/AsiloView.php';
require_once dirname(__DIR__, 2) . '/autenticacao/services/AsiloAutenticacaoService.php';
require_once dirname(__DIR__, 2) . '/usuarios/repository/AsiloUsuarioRepository.php';
require_once dirname(__DIR__, 2) . '/usuarios/services/AsiloFiltroSituacao.php';
require_once dirname(__DIR__) . '/repository/AsiloQuartoRepository.php';
require_once dirname(__DIR__) . '/services/AsiloQuartoService.php';

final class AsiloQuartoController
{
    private AsiloSession $session;
    private AsiloAutenticacaoService $authentication;
    private AsiloQuartoService $rooms;
    private AsiloFiltroSituacao $filter;

    public function __construct()
    {
        $this->session = new AsiloSession();
        $pdo = PDOMysqli::connection();
        $userRepository = new AsiloUsuarioRepository($pdo);
        $roomRepository = new AsiloQuartoRepository($pdo);

        $this->authentication = new AsiloAutenticacaoService($userRepository, $this->session);
        $this->rooms = new AsiloQuartoService($roomRepository);
        $this->filter = new AsiloFiltroSituacao();
    }

    public function listar(): void
    {
        $currentUser = $this->exigirAdministrador();
        $status = $this->filter->normalizar((string) ($_GET['situacao'] ?? 'ativos'));

        $this->view(dirname(__DIR__) . '/view/lista.php', [
            'titulo' => 'Quartos',
            'usuarioAtual' => $currentUser,
            'quartos' => $this->filter->filtrar($this->rooms->listar(), $status),
            'situacao' => $status,
            'secaoAtual' => 'quartos',
            'csrfToken' => CsrfToken::obter('asilosoft_quartos'),
        ]);
    }

    public function novo(): void
    {
        $currentUser = $this->exigirAdministrador();

        $this->view(dirname(__DIR__) . '/view/formulario.php', [
            'titulo' => 'Novo quarto',
            'usuarioAtual' => $currentUser,
            'quarto' => null,
            'secaoAtual' => 'quartos',
            'csrfToken' => CsrfToken::obter('asilosoft_quartos'),
        ]);
    }

    public function criar(): never
    {
        $this->exigirAdministrador();

        try {
            $this->rooms->criar($_POST);
            $this->session->flash('sucesso', 'Quarto cadastrado com sucesso.');
            $this->redirect('quartos');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('quartos-novo');
        }
    }

    public function editar(): void
    {
        $currentUser = $this->exigirAdministrador();
        $room = $this->rooms->buscarPorId($this->requireId());
        if ($room === null) {
            $this->notFound();
        }

        $this->view(dirname(__DIR__) . '/view/formulario.php', [
            'titulo' => 'Editar quarto',
            'usuarioAtual' => $currentUser,
            'quarto' => $room,
            'secaoAtual' => 'quartos',
            'csrfToken' => CsrfToken::obter('asilosoft_quartos'),
        ]);
    }

    public function atualizar(): never
    {
        $this->exigirAdministrador();
        $id = $this->requireId();

        try {
            $this->rooms->atualizar($id, $_POST);
            $this->session->flash('sucesso', 'Quarto atualizado com sucesso.');
            $this->redirect('quartos');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('quartos-editar', ['id' => $id]);
        }
    }

    public function alterarStatus(): never
    {
        $this->exigirAdministrador();

        try {
            $status = $_POST['ativo'] ?? null;
            if (!is_string($status) || !in_array($status, ['0', '1'], true)) {
                throw new DomainException('Situação de quarto inválida.');
            }

            $this->rooms->alterarStatus($this->requireId(), $status === '1');
            $this->session->flash('sucesso', 'Situação do quarto atualizada.');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
        }

        $this->redirect('quartos');
    }

    private function exigirAdministrador(): array
    {
        $user = $this->authentication->usuarioAtual();
        if ($user === null) {
            $this->redirect('login');
        }

        if ($user['perfil'] !== 'Administrador') {
            http_response_code(403);
            $this->view(
                dirname(__DIR__, 3) . '/presentation/components/asiloAcessoNegado.php',
                ['titulo' => 'Acesso negado', 'usuarioAtual' => $user]
            );
            exit;
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

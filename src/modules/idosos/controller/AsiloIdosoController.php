<?php

require_once dirname(__DIR__, 3) . '/core/Session/AsiloSession.php';
require_once dirname(__DIR__, 3) . '/core/Security/CsrfToken.php';
require_once dirname(__DIR__, 3) . '/infra/Database/Database.php';
require_once dirname(__DIR__, 3) . '/presentation/layout/AsiloView.php';
require_once dirname(__DIR__, 2) . '/autenticacao/services/AsiloAutenticacaoService.php';
require_once dirname(__DIR__, 2) . '/usuarios/repository/AsiloUsuarioRepository.php';
require_once dirname(__DIR__, 2) . '/usuarios/repository/AsiloRepositorioUsuariosSessao.php';
require_once dirname(__DIR__, 2) . '/usuarios/services/AsiloFiltroSituacao.php';
require_once dirname(__DIR__) . '/repository/AsiloIdosoRepository.php';
require_once dirname(__DIR__) . '/repository/AsiloQuartoRepository.php';
require_once dirname(__DIR__) . '/repository/AsiloRepositorioIdososSessao.php';
require_once dirname(__DIR__) . '/services/AsiloIdosoService.php';

final class AsiloIdosoController
{
    private AsiloSession $session;
    private AsiloAutenticacaoService $authentication;
    private AsiloIdosoService $residents;
    private AsiloFiltroSituacao $filter;
    private bool $demoMode = false;

    public function __construct()
    {
        $this->session = new AsiloSession();

        try {
            $pdo = PDOMysqli::connection();
            $userRepository = new AsiloUsuarioRepository($pdo);
            $residentRepository = new AsiloIdosoRepository($pdo);
            $roomRepository = new AsiloQuartoRepository($pdo);
        } catch (PDOException) {
            $this->demoMode = true;
            $userRepository = new AsiloRepositorioUsuariosSessao($this->session);
            $residentRepository = new AsiloRepositorioIdososSessao($this->session);
            $roomRepository = $residentRepository;
        }

        $this->authentication = new AsiloAutenticacaoService($userRepository, $this->session);
        $this->residents = new AsiloIdosoService($residentRepository, $roomRepository);
        $this->filter = new AsiloFiltroSituacao();
    }

    public function listar(): void
    {
        $currentUser = $this->exigirAdministrador();
        $status = $this->filter->normalizar((string) ($_GET['situacao'] ?? 'ativos'));

        $this->view(dirname(__DIR__) . '/view/lista.php', [
            'titulo' => 'Idosos',
            'usuarioAtual' => $currentUser,
            'idosos' => $this->filter->filtrar($this->residents->listar(), $status),
            'quartos' => $this->residents->listarQuartos(),
            'situacao' => $status,
            'secaoAtual' => 'idosos',
            'csrfToken' => CsrfToken::obter('asilosoft_idosos'),
        ]);
    }

    public function novo(): void
    {
        $currentUser = $this->exigirAdministrador();

        $this->view(dirname(__DIR__) . '/view/formulario.php', [
            'titulo' => 'Cadastrar idoso',
            'usuarioAtual' => $currentUser,
            'quartos' => $this->residents->listarQuartosDisponiveis(),
            'idoso' => null,
            'secaoAtual' => 'idosos',
            'csrfToken' => CsrfToken::obter('asilosoft_idosos'),
        ]);
    }

    public function editar(): void
    {
        $currentUser = $this->exigirAdministrador();
        $resident = $this->residents->buscarPorId($this->requireId());
        if ($resident === null) {
            $this->notFound();
        }

        $rooms = $this->residents->listarQuartosDisponiveis();
        $currentRoomInList = $resident['quarto_id'] === null
            || in_array((int) $resident['quarto_id'], array_column($rooms, 'id'), true);

        if (!$currentRoomInList) {
            $currentRoom = $this->residents->buscarQuartoPorId((int) $resident['quarto_id']);
            if ($currentRoom !== null) {
                $currentRoom['ocupacao'] = $this->residents
                    ->contarOcupacaoDoQuarto((int) $resident['quarto_id']);
                $rooms[] = $currentRoom;
            }
        }

        $this->view(dirname(__DIR__) . '/view/formulario.php', [
            'titulo' => 'Editar idoso',
            'usuarioAtual' => $currentUser,
            'idoso' => $resident,
            'quartos' => $rooms,
            'secaoAtual' => 'idosos',
            'csrfToken' => CsrfToken::obter('asilosoft_idosos'),
        ]);
    }

    public function criar(): never
    {
        $this->exigirAdministrador();

        try {
            $this->residents->criar($_POST);
            $this->session->flash('sucesso', 'Idoso cadastrado com sucesso.');
            $this->redirect('idosos');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('idosos-novo');
        }
    }

    public function atualizar(): never
    {
        $this->exigirAdministrador();
        $id = $this->requireId();

        try {
            $this->residents->atualizar($id, $_POST);
            $this->session->flash('sucesso', 'Dados do idoso atualizados.');
            $this->redirect('idosos');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('idosos-editar', ['id' => $id]);
        }
    }

    public function retirarDoQuarto(): never
    {
        $this->exigirAdministrador();

        try {
            $this->residents->retirarDoQuarto($this->requireId());
            $this->session->flash('sucesso', 'Idoso retirado do quarto.');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
        }

        $this->redirect('idosos');
    }

    public function alterarStatus(): never
    {
        $this->exigirAdministrador();

        try {
            $active = (string) ($_POST['ativo'] ?? '') === '1';
            $this->residents->alterarStatus($this->requireId(), $active);
            $this->session->flash(
                'sucesso',
                $active ? 'Idoso ativado.' : 'Idoso desativado e retirado do quarto.'
            );
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
        }

        $this->redirect('idosos');
    }

    public function criarQuarto(): never
    {
        $this->exigirAdministrador();
        try {
            $this->residents->criarQuarto($_POST);
            $this->session->flash('sucesso', 'Quarto cadastrado com sucesso.');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
        }
        $this->redirect('idosos');
    }

    public function atualizarQuarto(): never
    {
        $this->exigirAdministrador();
        $id = $this->requireId();
        try {
            $this->residents->atualizarQuarto($id, $_POST);
            $this->session->flash('sucesso', 'Quarto atualizado com sucesso.');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
        }
        $this->redirect('idosos');
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
        $data['modoDemonstracao'] = $this->demoMode;
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

<?php

require_once dirname(__DIR__, 3) . '/core/Session/AsiloSession.php';
require_once dirname(__DIR__, 3) . '/core/Security/CsrfToken.php';
require_once dirname(__DIR__, 3) . '/infra/Database/Database.php';
require_once dirname(__DIR__, 3) . '/presentation/layout/AsiloView.php';
require_once dirname(__DIR__, 2) . '/autenticacao/services/AsiloAutenticacaoService.php';
require_once dirname(__DIR__, 2) . '/usuarios/repository/AsiloUsuarioRepository.php';
require_once dirname(__DIR__) . '/repository/AsiloAlmoxarifadoRepository.php';
require_once dirname(__DIR__) . '/services/AsiloAlmoxarifadoService.php';

final class AsiloAlmoxarifadoController
{
    private AsiloSession $session;
    private AsiloAutenticacaoService $authentication;
    private AsiloAlmoxarifadoService $stock;

    public function __construct()
    {
        $this->session = new AsiloSession();

        $pdo = PDOMysqli::connection();
        $userRepository = new AsiloUsuarioRepository($pdo);
        $stockRepository = new AsiloAlmoxarifadoRepository($pdo);

        $this->authentication = new AsiloAutenticacaoService($userRepository, $this->session);
        $this->stock = new AsiloAlmoxarifadoService($stockRepository);
    }

    public function listar(): void
    {
        $currentUser = $this->exigirAcesso();

        $this->view(dirname(__DIR__) . '/view/lista.php', [
            'titulo' => 'Almoxarifado',
            'usuarioAtual' => $currentUser,
            'itens' => $this->stock->listarItens(),
            'movimentacoes' => $this->stock->listarMovimentacoes(),
            'secaoAtual' => 'almoxarifado',
        ]);
    }

    public function novo(): void
    {
        $currentUser = $this->exigirAcesso();

        $this->view(dirname(__DIR__) . '/view/formulario.php', [
            'titulo' => 'Novo item',
            'usuarioAtual' => $currentUser,
            'item' => null,
            'tipos' => $this->stock->tiposPermitidos(),
            'csrfToken' => CsrfToken::obter('asilosoft_almoxarifado'),
            'secaoAtual' => 'almoxarifado',
        ]);
    }

    public function criar(): never
    {
        $this->exigirAcesso();

        try {
            $this->stock->criarItem($_POST);
            $this->session->flash('sucesso', 'Item cadastrado com sucesso.');
            $this->redirect('almoxarifado');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('almoxarifado-novo');
        }
    }

    public function editar(): void
    {
        $currentUser = $this->exigirAcesso();
        $item = $this->stock->buscarItemPorId($this->requireId());
        if ($item === null) {
            $this->notFound();
        }

        $this->view(dirname(__DIR__) . '/view/formulario.php', [
            'titulo' => 'Editar item',
            'usuarioAtual' => $currentUser,
            'item' => $item,
            'tipos' => $this->stock->tiposPermitidos(),
            'csrfToken' => CsrfToken::obter('asilosoft_almoxarifado'),
            'secaoAtual' => 'almoxarifado',
        ]);
    }

    public function atualizar(): never
    {
        $this->exigirAcesso();
        $id = $this->requireId();

        try {
            $this->stock->atualizarItem($id, $_POST);
            $this->session->flash('sucesso', 'Item atualizado com sucesso.');
            $this->redirect('almoxarifado');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('almoxarifado-editar', ['id' => $id]);
        }
    }

    public function entrada(): void
    {
        $this->formularioMovimentacao('Entrada');
    }

    public function registrarEntrada(): never
    {
        $currentUser = $this->exigirAcesso();
        $id = $this->requireId();

        try {
            $this->stock->registrarEntrada($id, $_POST, (int) $currentUser['id']);
            $this->session->flash('sucesso', 'Entrada registrada e estoque atualizado.');
            $this->redirect('almoxarifado');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('almoxarifado-entrada', ['id' => $id]);
        }
    }

    public function retirada(): void
    {
        $this->formularioMovimentacao('Retirada');
    }

    public function registrarRetirada(): never
    {
        $currentUser = $this->exigirAcesso();
        $id = $this->requireId();

        try {
            $this->stock->registrarRetirada($id, $_POST, (int) $currentUser['id']);
            $this->session->flash('sucesso', 'Retirada registrada e estoque atualizado.');
            $this->redirect('almoxarifado');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
            $this->redirect('almoxarifado-retirada', ['id' => $id]);
        }
    }

    private function formularioMovimentacao(string $tipo): void
    {
        $currentUser = $this->exigirAcesso();
        $item = $this->stock->buscarItemPorId($this->requireId());
        if ($item === null) {
            $this->notFound();
        }

        $this->view(dirname(__DIR__) . '/view/movimentacao.php', [
            'titulo' => $tipo . ' de estoque',
            'usuarioAtual' => $currentUser,
            'item' => $item,
            'tipoMovimentacao' => $tipo,
            'origens' => $this->stock->origensPermitidas(),
            'csrfToken' => CsrfToken::obter('asilosoft_almoxarifado'),
            'secaoAtual' => 'almoxarifado',
        ]);
    }

    private function exigirAcesso(): array
    {
        $user = $this->authentication->usuarioAtual();
        if ($user === null) {
            $this->redirect('login');
        }

        if (!in_array($user['perfil'], ['Administrador', 'Almoxarife'], true)) {
            $this->session->flash('erro', 'Você não tem permissão para acessar o almoxarifado.');
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

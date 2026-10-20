<?php

require_once dirname(__DIR__, 3) . '/core/Session/AsiloSession.php';
require_once dirname(__DIR__, 3) . '/infra/Database/Database.php';
require_once dirname(__DIR__, 3) . '/presentation/layout/AsiloView.php';
require_once dirname(__DIR__, 2) . '/autenticacao/services/AsiloAutenticacaoService.php';
require_once dirname(__DIR__, 2) . '/usuarios/repository/AsiloUsuarioRepository.php';

final class AsiloHomeController
{
    private AsiloSession $session;
    private AsiloAutenticacaoService $authentication;

    public function __construct()
    {
        $this->session = new AsiloSession();
        $this->authentication = new AsiloAutenticacaoService(
            new AsiloUsuarioRepository(PDOMysqli::connection()),
            $this->session
        );
    }

    public function index(): void
    {
        $user = $this->authentication->usuarioAtual();
        if ($user === null) {
            $this->redirect('login');
        }

        $atalhos = [];
        if ($user['perfil'] === 'Administrador') {
            $atalhos = [
                [
                    'titulo' => 'Usuários',
                    'descricao' => 'Gerencie contas, perfis e situações de acesso.',
                    'rota' => 'usuarios',
                ],
                [
                    'titulo' => 'Idosos',
                    'descricao' => 'Cadastre residentes e faça a alocação em quartos.',
                    'rota' => 'idosos',
                ],
                [
                    'titulo' => 'Quartos',
                    'descricao' => 'Cadastre espaços, capacidades e acompanhe a ocupação.',
                    'rota' => 'quartos',
                ],
                [
                    'titulo' => 'Almoxarifado',
                    'descricao' => 'Controle materiais, entradas, retiradas e estoque.',
                    'rota' => 'almoxarifado',
                ],
            ];
        } elseif ($user['perfil'] === 'Almoxarife') {
            $atalhos = [[
                'titulo' => 'Almoxarifado',
                'descricao' => 'Controle materiais, entradas, retiradas e estoque.',
                'rota' => 'almoxarifado',
            ]];
        }

        if ($user['perfil'] === 'Administrador') {
            $atalhos[] = ['titulo' => 'Familiares', 'descricao' => 'Gerencie contatos, contas e vínculos dos residentes.', 'rota' => 'familiares'];
        }
        if (in_array($user['perfil'], ['Administrador', 'Almoxarife'], true)) {
            $atalhos[] = ['titulo' => 'Compras', 'descricao' => 'Acompanhe solicitações de materiais e medicamentos.', 'rota' => 'compras'];
        }
        if ($user['perfil'] === 'Profissional de Saúde') {
            $atalhos[] = ['titulo' => 'Fichas e prontuários', 'descricao' => 'Busque residentes e registre o acompanhamento clínico.', 'rota' => 'idosos'];
        }
        if (in_array($user['perfil'], ['Administrador', 'Almoxarife', 'Profissional de Saúde'], true)) {
            $atalhos[] = ['titulo' => 'Uso por idoso', 'descricao' => 'Registre o consumo individual de materiais.', 'rota' => 'almoxarifado-uso'];
        }
        if ($user['perfil'] === 'Familiar') {
            $atalhos[] = ['titulo' => 'Meus idosos', 'descricao' => 'Consulte o acompanhamento publicado dos seus familiares.', 'rota' => 'meus-idosos'];
        }

        $this->view(dirname(__DIR__) . '/view/index.php', [
            'titulo' => 'Início',
            'usuarioAtual' => $user,
            'atalhos' => $atalhos,
            'secaoAtual' => 'home',
        ]);
    }

    private function view(string $viewFile, array $data = []): void
    {
        AsiloView::render($viewFile, $data);
    }

    private function redirect(string $route): never
    {
        header('Location: ?route=' . rawurlencode($route), true, 303);
        exit;
    }
}

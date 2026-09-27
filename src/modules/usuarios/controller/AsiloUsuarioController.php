<?php


require_once dirname(__DIR__, 3) . '/core/Http/AsiloController.php';
require_once dirname(__DIR__) . '/services/AsiloUsuarioService.php';
require_once dirname(__DIR__) . '/services/AsiloFiltroSituacao.php';

final class AsiloUsuarioController extends AsiloController
{
    private AsiloUsuarioService $users;
    private AsiloFiltroSituacao $filter;

    public function __construct()
    {
        parent::__construct();
        $this->users = new AsiloUsuarioService($this->runtime->userRepository());
        $this->filter = new AsiloFiltroSituacao();
    }

    public function listar(): void
    {
        $currentUser = $this->exigirAdministrador();
        $status = $this->filter->normalizar((string) ($_GET['situacao'] ?? 'ativos'));

        $this->view(dirname(__DIR__) . '/view/lista.php', [
            'titulo' => 'Usuários',
            'usuarioAtual' => $currentUser,
            'usuarios' => $this->filter->filtrar($this->runtime->userRepository()->listar(), $status),
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
        $user = $this->runtime->userRepository()->buscarPorId($this->requireId());
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
            $this->users->alterarStatus(
                $this->requireId(),
                (string) ($_POST['ativo'] ?? '') === '1'
            );
            $this->session->flash('sucesso', 'Situação do usuário atualizada.');
        } catch (DomainException $error) {
            $this->session->flash('erro', $error->getMessage());
        }

        $this->redirect('usuarios');
    }
}

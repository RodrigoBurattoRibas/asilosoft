<?php


require_once dirname(__DIR__, 3) . '/core/Http/AsiloController.php';
require_once dirname(__DIR__) . '/services/AsiloIdosoService.php';
require_once dirname(__DIR__, 2) . '/usuarios/services/AsiloFiltroSituacao.php';

final class AsiloIdosoController extends AsiloController
{
    private AsiloIdosoService $residents;
    private AsiloFiltroSituacao $filter;

    public function __construct()
    {
        parent::__construct();
        $this->residents = new AsiloIdosoService($this->runtime->residentRepository());
        $this->filter = new AsiloFiltroSituacao();
    }

    public function listar(): void
    {
        $currentUser = $this->exigirAdministrador();
        $status = $this->filter->normalizar((string) ($_GET['situacao'] ?? 'ativos'));

        $this->view(dirname(__DIR__) . '/view/lista.php', [
            'titulo' => 'Idosos',
            'usuarioAtual' => $currentUser,
            'idosos' => $this->filter->filtrar($this->runtime->residentRepository()->listar(), $status),
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
            'quartos' => $this->runtime->roomRepository()->listarDisponiveis(),
            'idoso' => null,
            'secaoAtual' => 'idosos',
            'csrfToken' => CsrfToken::obter('asilosoft_idosos'),
        ]);
    }

    public function editar(): void
    {
        $currentUser = $this->exigirAdministrador();
        $resident = $this->runtime->residentRepository()->buscarPorId($this->requireId());
        if ($resident === null) {
            $this->notFound();
        }

        $rooms = $this->runtime->roomRepository()->listarDisponiveis();
        $currentRoomInList = $resident['quarto_id'] === null
            || in_array((int) $resident['quarto_id'], array_column($rooms, 'id'), true);

        if (!$currentRoomInList) {
            $currentRoom = $this->runtime->residentRepository()->buscarQuartoPorId((int) $resident['quarto_id']);
            if ($currentRoom !== null) {
                $currentRoom['ocupacao'] = $this->runtime
                    ->residentRepository()
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
}

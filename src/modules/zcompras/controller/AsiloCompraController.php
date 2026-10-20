<?php
require_once dirname(__DIR__,3).'/core/Session/AsiloSession.php';
require_once dirname(__DIR__,3).'/core/Security/CsrfToken.php';
require_once dirname(__DIR__,3).'/core/Security/AsiloAuthorization.php';
require_once dirname(__DIR__,3).'/infra/Database/Database.php';
require_once dirname(__DIR__,3).'/presentation/layout/AsiloView.php';
require_once dirname(__DIR__,2).'/autenticacao/services/AsiloAutenticacaoService.php';
require_once dirname(__DIR__,2).'/usuarios/repository/AsiloUsuarioRepository.php';
require_once dirname(__DIR__).'/services/AsiloCompraService.php';
require_once dirname(__DIR__).'/repository/AsiloCompraRepository.php';

final class AsiloCompraController
{
    private AsiloSession $s;
    private AsiloCompraService $compras;
    private array $u;

    public function __construct()
    {
        $this->s = new AsiloSession();
        $pdo = PDOMysqli::connection();
        $u = (new AsiloAutenticacaoService(new AsiloUsuarioRepository($pdo), $this->s))->usuarioAtual();
        if (!$u) $this->go('login');
        $this->u = $u;
        try { AsiloAuthorization::permitir($u, ['Administrador','Almoxarife']); }
        catch (RuntimeException $error) { $this->deny(); }
        $context = $pdo->prepare('SET @asilosoft_usuario_id = :id');
        $context->execute(['id'=>(int) $u['id']]);
        $this->compras = new AsiloCompraService(new AsiloCompraRepository($pdo));
    }

    public function listar(): void
    {
        $this->view('lista.php', ['solicitacoes'=>$this->compras->listar()]);
    }

    public function nova(): void
    {
        $this->view('formulario.php', ['materiais'=>$this->compras->materiais(), 'csrfToken'=>CsrfToken::obter('asilosoft_compras')]);
    }

    public function criar(): never
    {
        try {
            $this->compras->criar($_POST, (int) $this->u['id']);
            $this->s->flash('sucesso', 'Solicitação criada como pendente.');
        } catch (DomainException $error) {
            $this->s->flash('erro', $error->getMessage());
            $this->go('compras-nova');
        } catch (PDOException $error) {
            $this->s->flash('erro', 'Não foi possível criar a solicitação. Verifique os dados.');
            $this->go('compras-nova');
        }
        $this->go('compras');
    }

    public function situacao(): never
    {
        if ($this->u['perfil'] !== 'Administrador') $this->deny();
        try {
            $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
            $origem = $_POST['situacao_atual'] ?? '';
            $destino = $_POST['situacao'] ?? '';
            if (!is_string($origem) || !is_string($destino)) throw new DomainException('Situação inválida.');
            $this->compras->alterarSituacao($id ?: 0, $origem, $destino);
            $this->s->flash('sucesso', 'Situação atualizada.');
        } catch (DomainException $error) {
            $this->s->flash('erro', $error->getMessage());
        } catch (PDOException $error) {
            $this->s->flash('erro', 'Não foi possível atualizar a solicitação. Atualize a lista e tente novamente.');
        }
        $this->go('compras');
    }

    private function view(string $file, array $data): void
    {
        AsiloView::render(dirname(__DIR__).'/view/'.$file, $data + ['titulo'=>'Solicitações de compra',
            'usuarioAtual'=>$this->u,'secaoAtual'=>'almoxarifado','modoDemonstracao'=>false]);
    }
    private function go(string $route): never { header('Location: ?route='.$route, true, 303); exit; }
    private function deny(): never
    {
        http_response_code(403);
        AsiloView::render(dirname(__DIR__,3).'/presentation/components/asiloAcessoNegado.php',
            ['titulo'=>'Acesso negado','usuarioAtual'=>$this->u,'modoDemonstracao'=>false]);
        exit;
    }
}

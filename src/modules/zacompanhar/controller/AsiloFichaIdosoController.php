<?php
require_once dirname(__DIR__,3).'/core/Session/AsiloSession.php';
require_once dirname(__DIR__,3).'/core/Security/AsiloAuthorization.php';
require_once dirname(__DIR__,3).'/core/Security/CsrfToken.php';
require_once dirname(__DIR__,3).'/infra/Database/Database.php';
require_once dirname(__DIR__,3).'/presentation/layout/AsiloView.php';
require_once dirname(__DIR__,2).'/autenticacao/services/AsiloAutenticacaoService.php';
require_once dirname(__DIR__,2).'/usuarios/repository/AsiloUsuarioRepository.php';
require_once dirname(__DIR__,2).'/idosos/repository/AsiloIdosoRepository.php';
require_once dirname(__DIR__,2).'/idosos/repository/AsiloSaudeRepository.php';
require_once dirname(__DIR__,2).'/familiares/repository/AsiloFamiliarRepository.php';
require_once dirname(__DIR__).'/repository/AsiloFichaRepository.php';
require_once dirname(__DIR__).'/services/AsiloFichaService.php';

final class AsiloFichaIdosoController
{
    public function mostrar(): void
    {
        $session = new AsiloSession();
        $pdo = PDOMysqli::connection();
        $usuario = (new AsiloAutenticacaoService(new AsiloUsuarioRepository($pdo), $session))->usuarioAtual();
        if ($usuario === null) {
            header('Location: ?route=login', true, 303);
            return;
        }
        try {
            AsiloAuthorization::permitir($usuario, ['Administrador','Profissional de Saúde']);
        } catch (RuntimeException $error) {
            http_response_code(403);
            AsiloView::render(dirname(__DIR__,3).'/presentation/components/asiloAcessoNegado.php', ['titulo'=>'Acesso negado','usuarioAtual'=>$usuario]);
            return;
        }
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        $familiares = new AsiloFamiliarRepository($pdo);
        $service = new AsiloFichaService(new AsiloIdosoRepository($pdo), $familiares, new AsiloSaudeRepository($pdo), new AsiloFichaRepository($pdo));
        $ficha = $id && $id > 0 ? $service->consultar($id) : null;
        if ($ficha === null) {
            http_response_code(404);
            AsiloView::render(dirname(__DIR__,3).'/presentation/components/asiloNaoEncontrado.php', ['titulo'=>'Página não encontrada','usuarioAtual'=>$usuario]);
            return;
        }
        AsiloView::render(dirname(__DIR__).'/view/ficha.php', $ficha + [
            'titulo'=>'Ficha do idoso','usuarioAtual'=>$usuario,'secaoAtual'=>'idosos',
            'familiaresDisponiveis'=>$usuario['perfil'] === 'Administrador' ? $familiares->listar() : [],
            'csrfFamiliares'=>CsrfToken::obter('asilosoft_familiares'),
        ]);
    }
}

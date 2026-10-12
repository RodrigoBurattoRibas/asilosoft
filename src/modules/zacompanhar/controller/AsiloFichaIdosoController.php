<?php
require_once dirname(__DIR__,3).'/core/Session/AsiloSession.php';
require_once dirname(__DIR__,3).'/infra/Database/Database.php';
require_once dirname(__DIR__,3).'/presentation/layout/AsiloView.php';
require_once dirname(__DIR__,2).'/autenticacao/services/AsiloAutenticacaoService.php';
require_once dirname(__DIR__,2).'/usuarios/repository/AsiloUsuarioRepository.php';
require_once dirname(__DIR__,2).'/idosos/repository/AsiloIdosoRepository.php';
require_once dirname(__DIR__,2).'/idosos/repository/AsiloSaudeRepository.php';
require_once dirname(__DIR__,2).'/familiares/repository/AsiloFamiliarRepository.php';
final class AsiloFichaIdosoController {
 public function mostrar():void { $s=new AsiloSession();$pdo=PDOMysqli::connection();$u=(new AsiloAutenticacaoService(new AsiloUsuarioRepository($pdo),$s))->usuarioAtual();if($u===null){header('Location: ?route=login',true,303);exit;}if(!in_array($u['perfil'],['Administrador','Profissional de Saúde'],true)){http_response_code(403);AsiloView::render(dirname(__DIR__,3).'/presentation/components/asiloAcessoNegado.php',['titulo'=>'Acesso negado','usuarioAtual'=>$u,'modoDemonstracao'=>false]);return;}$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);$idoso=$id?(new AsiloIdosoRepository($pdo))->buscarPorId($id):null;if(!$idoso){http_response_code(404);AsiloView::render(dirname(__DIR__,3).'/presentation/components/asiloNaoEncontrado.php',['titulo'=>'Página não encontrada','usuarioAtual'=>$u,'modoDemonstracao'=>false]);return;}$familiares=(new AsiloFamiliarRepository($pdo))->porIdoso($id);$saude=(new AsiloSaudeRepository($pdo))->listarPorIdoso($id);$q=$pdo->prepare('SELECT um.usado_em,m.nome material_nome,um.quantidade,m.unidade_medida,um.observacao,u.nome usuario_nome FROM usos_materiais um JOIN materiais m ON m.id=um.material_id JOIN usuarios u ON u.id=um.usuario_id WHERE um.idoso_id=:id ORDER BY um.usado_em DESC,um.id DESC');$q->execute(['id'=>$id]);AsiloView::render(dirname(__DIR__).'/view/ficha.php',['titulo'=>'Ficha do idoso','usuarioAtual'=>$u,'idoso'=>$idoso,'familiares'=>$familiares,'registros'=>$saude,'usos'=>$q->fetchAll(),'secaoAtual'=>'idosos','modoDemonstracao'=>false]); }
}

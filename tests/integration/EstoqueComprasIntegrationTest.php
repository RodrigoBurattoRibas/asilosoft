<?php
// Disposable database only. Requires explicit ASILOSOFT_TEST_DB_* variables, never reads .env.
$root=dirname(__DIR__,2);
foreach(['HOST','PORTA','NOME','USUARIO','SENHA'] as $key) if(getenv('ASILOSOFT_TEST_DB_'.$key)===false) throw new RuntimeException('Set ASILOSOFT_TEST_DB_'.$key);
$name=getenv('ASILOSOFT_TEST_DB_NOME');
if(!preg_match('/^asilosoft_test_[a-z0-9_]+$/D',$name)) throw new RuntimeException('Use isolated test database');
function connectTest(): PDO { return new PDO('mysql:host='.getenv('ASILOSOFT_TEST_DB_HOST').';port='.getenv('ASILOSOFT_TEST_DB_PORTA').';charset=utf8mb4',getenv('ASILOSOFT_TEST_DB_USUARIO'),getenv('ASILOSOFT_TEST_DB_SENHA'),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); }
function check(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); echo "OK $message\n"; }
function denied(callable $fn,string $message):void { try{$fn();}catch(DomainException $e){check(true,$message);return;}throw new RuntimeException($message); }
require_once $root.'/src/infra/Database/DatabaseMigrator.php';
foreach(['almoxarifado/services/AsiloAlmoxarifadoService.php','almoxarifado/repository/AsiloAlmoxarifadoRepository.php','zcompras/services/AsiloCompraService.php','zcompras/repository/AsiloCompraRepository.php'] as $file) require_once $root.'/src/modules/'.$file;
$pdo=connectTest();$pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");$pdo->exec("USE `$name`");
(new DatabaseMigrator())->migrate($pdo,$root.'/src/infra/Database/migrations');
$tag=bin2hex(random_bytes(5));
$pdo->exec("INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES ('Teste','estoque-$tag@test.invalid','hash','Administrador')");$user=(int)$pdo->lastInsertId();$pdo->exec("SET @asilosoft_usuario_id=$user");
$cpf=str_pad((string)random_int(1,99999999999),11,'0',STR_PAD_LEFT);
$pdo->exec("INSERT INTO idosos(nome,cpf,identificador) VALUES ('Teste','$cpf','EST-$tag')");$idoso=(int)$pdo->lastInsertId();
$stock=new AsiloAlmoxarifadoService(new AsiloAlmoxarifadoRepository($pdo));
$material=$stock->criarItem(['nome'=>'Material '.$tag,'tipo'=>'Material','unidade_medida'=>'un','estoque_minimo'=>'0'])['id'];
$stock->registrarEntrada($material,['quantidade'=>'10','origem'=>'SUS'],$user);
$uso=['idoso_id'=>$idoso,'material_id'=>$material,'quantidade'=>'2.125','observacao'=>'Teste','chave_requisicao'=>bin2hex(random_bytes(32))];
$first=$stock->registrarUso($uso,$user);$again=$stock->registrarUso($uso,$user);
check((int)$first['id']===(int)$again['id'],'T07 idempotência');
check((float)$pdo->query("SELECT quantidade_atual FROM estoques WHERE material_id=$material")->fetchColumn()===7.875,'T07 baixa exata uma vez');
check((int)$pdo->query('SELECT COUNT(*) FROM movimentacoes_estoque WHERE uso_material_id='.$first['id'])->fetchColumn()===1,'T07 movimento vinculado único');
$changed=$uso;$changed['quantidade']='1';
denied(fn()=>$stock->registrarUso($changed,$user),'T07 chave reutilizada com payload diferente rejeitada');
$pdo->exec("UPDATE materiais SET ativo=0 WHERE id=$material");
$fresh=$uso;$fresh['chave_requisicao']=bin2hex(random_bytes(32));
denied(fn()=>$stock->registrarUso($fresh,$user),'T07 novo consumo de material inativo rejeitado');
check((int)$stock->registrarUso($uso,$user)['id']===(int)$first['id'],'T07 replay confirmado continua idempotente após desativação');
$buyInactive=new AsiloCompraService(new AsiloCompraRepository($pdo));
denied(fn()=>$buyInactive->criar(['material_id'=>$material,'quantidade'=>'1','observacao'=>'Teste'],$user),'T09 compra de material inativo rejeitada');
$buyInactive=null;
$pdo->exec("UPDATE materiais SET ativo=1 WHERE id=$material");
$pdo->exec("UPDATE idosos SET ativo=0 WHERE id=$idoso");
denied(fn()=>$stock->registrarUso($fresh,$user),'T07 idoso inativo rejeitado');
$pdo->exec("UPDATE idosos SET ativo=1 WHERE id=$idoso");
$logs=(int)$pdo->query('SELECT COUNT(*) FROM logs_auditoria')->fetchColumn();$tooMuch=$uso;$tooMuch['quantidade']='8';$tooMuch['chave_requisicao']=bin2hex(random_bytes(32));
denied(fn()=>$stock->registrarUso($tooMuch,$user),'T07 saldo insuficiente');
check((int)$pdo->query('SELECT COUNT(*) FROM logs_auditoria')->fetchColumn()===$logs,'T07 nenhuma auditoria parcial');
denied(fn()=>$stock->registrarEntrada($material,['quantidade'=>'1'],$user),'T08 origem obrigatória');
foreach(['0','1.0001','1000000000','1e3'] as $q) denied(fn()=>$stock->registrarEntrada($material,['quantidade'=>$q,'origem'=>'SUS'],$user),'T08 quantidade inválida '.$q);
$buy=new AsiloCompraService(new AsiloCompraRepository($pdo));$id=$buy->criar(['material_id'=>$material,'quantidade'=>'5','observacao'=>'Reposição'],$user);
$buy->alterarSituacao($id,'Pendente','Aprovada');
denied(fn()=>$buy->alterarSituacao($id,'Pendente','Recusada'),'T09 CAS obsoleto');
$buy->alterarSituacao($id,'Aprovada','Atendida');
denied(fn()=>$buy->alterarSituacao($id,'Atendida','Pendente'),'T09 terminal');
denied(fn()=>$buy->alterarSituacao(999999999,'Pendente','Aprovada'),'T09 inexistente');
$refused=$buy->criar(['material_id'=>$material,'quantidade'=>'1','observacao'=>'Outra'],$user);$buy->alterarSituacao($refused,'Pendente','Recusada');
check((float)$pdo->query("SELECT quantidade_atual FROM estoques WHERE material_id=$material")->fetchColumn()===7.875,'T09 estoque inalterado');
check((int)$pdo->query("SELECT COUNT(*) FROM logs_auditoria WHERE tabela='solicitacoes_compra' AND registro_id=$id AND usuario_id=$user")->fetchColumn()===3,'T09 auditoria com autoria');
if(!function_exists('pcntl_fork')) throw new RuntimeException('pcntl required for concurrency test');
$stock=null;$buy=null;$pdo=null;$pids=[];
for($i=0;$i<2;$i++) {
 $pid=pcntl_fork();if($pid===-1) throw new RuntimeException('fork failed');
 if($pid===0) { try { $child=connectTest();$child->exec("USE `$name`");$svc=new AsiloAlmoxarifadoService(new AsiloAlmoxarifadoRepository($child));$svc->registrarUso(['idoso_id'=>$idoso,'material_id'=>$material,'quantidade'=>'5','chave_requisicao'=>bin2hex(random_bytes(32))],$user);exit(0); }catch(DomainException $e){exit(10);}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(20);} }
 $pids[]=$pid;
}
$codes=[];foreach($pids as $pid){pcntl_waitpid($pid,$status);$codes[]=pcntl_wexitstatus($status);}sort($codes);
check($codes===[0,10],'T07 concorrência aceita só uma retirada');
$pdo=connectTest();$pdo->exec("USE `$name`");
check((float)$pdo->query("SELECT quantidade_atual FROM estoques WHERE material_id=$material")->fetchColumn()===2.875,'T07 saldo concorrente positivo');
check((int)$pdo->query("SELECT COUNT(*) FROM usos_materiais WHERE material_id=$material")->fetchColumn()===2,'T07 rollback sem consumo extra');
echo "PASS estoque/compras. Fixtures fictícias preservadas somente na base $name.\n";

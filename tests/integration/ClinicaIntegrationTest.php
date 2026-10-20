<?php
// Uso: ASILOSOFT_TEST_DB_HOST=127.0.0.1 ASILOSOFT_TEST_DB_PORTA=33317 ASILOSOFT_TEST_DB_NOME=asilosoft_test_clinica ASILOSOFT_TEST_DB_USUARIO=root ASILOSOFT_TEST_DB_SENHA='' php tests/integration/ClinicaIntegrationTest.php
$root = dirname(__DIR__, 2);
foreach (['HOST','PORTA','NOME','USUARIO','SENHA'] as $key) {
    if (getenv('ASILOSOFT_TEST_DB_'.$key) === false) throw new RuntimeException('Configure explicitamente ASILOSOFT_TEST_DB_'.$key);
}
$name = getenv('ASILOSOFT_TEST_DB_NOME');
if (!preg_match('/^asilosoft_test_[a-z0-9_]+$/D', $name)) throw new RuntimeException('Use uma base isolada asilosoft_test_*.');
$pdo = new PDO('mysql:host='.getenv('ASILOSOFT_TEST_DB_HOST').';port='.getenv('ASILOSOFT_TEST_DB_PORTA').';charset=utf8mb4', getenv('ASILOSOFT_TEST_DB_USUARIO'), getenv('ASILOSOFT_TEST_DB_SENHA'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
$pdo->exec('CREATE DATABASE IF NOT EXISTS `'.$name.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$pdo->exec('USE `'.$name.'`');
require_once $root.'/src/infra/Database/DatabaseMigrator.php';
(new DatabaseMigrator())->migrate($pdo, $root.'/src/infra/Database/migrations');
foreach (['idosos/repository/AsiloIdosoRepository.php','idosos/repository/AsiloSaudeRepository.php','idosos/services/AsiloSaudeService.php','zacompanhar/services/AsiloFichaService.php','zacompanhar/repository/AsiloFichaRepository.php','familiares/repository/AsiloFamiliarRepository.php'] as $file) require_once $root.'/src/modules/'.$file;
function check(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); echo "OK $message\n"; }
function denied(callable $action, string $message): void { try { $action(); } catch (DomainException $e) { check(true,$message); return; } throw new RuntimeException($message); }
$pdo->beginTransaction();
try {
    $pdo->exec("INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES ('Profissional fictício','clinica-prof@test.invalid','hash','Profissional de Saúde')");
    $prof = ['id'=>(int)$pdo->lastInsertId(),'perfil'=>'Profissional de Saúde','ativo'=>true];
    $pdo->exec("INSERT INTO quartos(codigo,capacidade) VALUES ('CLINICA-TEST',99)");
    $quarto = (int)$pdo->lastInsertId();
    $pdo->exec("INSERT INTO idosos(nome,cpf,identificador,quarto_id) VALUES ('Pessoa Fictícia Teste','12345678901','CLINICA-001',$quarto)");
    $id = (int)$pdo->lastInsertId();
    $idosos = new AsiloIdosoRepository($pdo); $saude = new AsiloSaudeRepository($pdo); $service = new AsiloSaudeService($saude,$idosos);
    foreach (['Fictícia','123.456.789-01','CLINICA-001'] as $termo) check(count($idosos->pesquisar($termo,true))===1, 'Busca '.$termo);
    check($idosos->buscarPorId($id)['quarto_codigo']==='CLINICA-TEST','Quarto na ficha');
    denied(fn()=> $service->registrar($id,['id'=>$prof['id'],'perfil'=>'Administrador','ativo'=>true],['descricao'=>'Negado']), 'Administrador não grava');
    denied(fn()=> $service->registrar($id,$prof,['descricao'=>'',]), 'Descrição vazia rejeitada');
    denied(fn()=> $service->registrar($id,$prof,['descricao'=>'Texto','visivel_para_familiar'=>'sim']), 'Visibilidade inválida rejeitada');
    denied(fn()=> $service->registrar(999999,$prof,['descricao'=>'Texto']), 'Idoso inexistente rejeitado');
    $service->registrar($id,$prof,['descricao'=>'Privado','usuario_id'=>999999]);
    $original = (int)$pdo->lastInsertId();
    set_error_handler(static function (int $severity, string $message): never { throw new ErrorException($message, 0, $severity); });
    try {
        foreach (['descricao','visivel_para_familiar','corrige_id'] as $campo) {
            denied(fn()=> $service->registrar($id,$prof,array_merge(['descricao'=>'Texto'],[$campo=>['forjado']])), 'Array rejeitado sem warning: '.$campo);
        }
    } finally { restore_error_handler(); }
    denied(fn()=> $service->registrar($id,$prof,['descricao'=>str_repeat('a',65000),'corrige_id'=>$original]), 'Limite considera referência da retificação');
    denied(fn()=> $service->registrar($id,$prof,['descricao'=>str_repeat('á',32501)]), 'Limite em bytes UTF-8');
    $service->registrar($id,$prof,['descricao'=>'Publicado','visivel_para_familiar'=>'1']);
    $service->registrar($id,$prof,['descricao'=>'Correção auditável','corrige_id'=>$original]);
    $registros = $saude->listarPorIdoso($id);
    check(count($registros)===3 && str_starts_with($registros[0]['descricao'],'Retificação do registro #'.$original), 'Retificação aditiva');
    check($registros[2]['descricao']==='Privado','Histórico original preservado');
    check((int)$pdo->query("SELECT COUNT(*) FROM registros_saude WHERE idoso_id=$id AND usuario_id=".$prof['id'])->fetchColumn()===3,'Autor real preservado');
    check((int)$pdo->query("SELECT COUNT(*) FROM logs_auditoria WHERE tabela='registros_saude' AND registro_id IN (SELECT id FROM registros_saude WHERE idoso_id=$id)")->fetchColumn()===3,'Auditoria de cada anotação');
    denied(fn()=> $service->registrar($id,$prof,['descricao'=>'Correção','corrige_id'=>999999]), 'Retificação inexistente rejeitada');
    $familiares = new AsiloFamiliarRepository($pdo);
    $ficha = (new AsiloFichaService($idosos,$familiares,$saude,new AsiloFichaRepository($pdo)))->consultar($id);
    check(count($ficha['registros'])===3 && $ficha['usos']===[], 'Ficha consolidada');
    check(count($idosos->pesquisar('',true,2))===0,'Paginação sem repetir resultados');
    echo "PASS clínica: integração MySQL concluída; fixtures revertidas.\n";
} finally { $pdo->rollBack(); }

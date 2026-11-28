<?php
// Base descartável local: não usa as credenciais nem a base do .env.
require_once __DIR__ . '/../../../scripts/popular-demonstracao.php';
$socket = getenv('ASILOSOFT_DEMO_TEST_SOCKET');
if (!$socket) {
    throw new RuntimeException('Defina ASILOSOFT_DEMO_TEST_SOCKET para um MySQL local descartável.');
}
$pdo = new PDO('mysql:unix_socket=' . $socket . ';charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function demoCheck(bool $ok, string $message): void {
    if (!$ok) throw new RuntimeException($message);
    echo "OK: $message\n";
}
$pdo->exec('CREATE DATABASE asilosoft_test_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
try {
    $pdo->exec('USE asilosoft_test_demo');
    (new DatabaseMigrator())->migrate($pdo, __DIR__ . '/../../../src/infra/Database/migrations');
    try {
        popularDemonstracao($pdo);
        throw new LogicException('Deveria exigir administrador');
    } catch (RuntimeException $e) {
        demoCheck(str_contains($e->getMessage(), 'Administrador padrão'), 'Sem administrador, carga aborta');
    }
    $pdo->exec("INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES ('Admin original','admin@asilosoft.local','hash-preservado','Administrador'),('Excluir','antigo@example.com','hash','Familiar')");
    $before = $pdo->query('SELECT * FROM usuarios WHERE id=1')->fetch();
    $pdo->exec('DROP TRIGGER bd_me_imutavel');
    $pdo->exec('DROP TRIGGER bd_um_imutavel');
    // Força falha após o DELETE e verifica rollback e restauração das triggers.
    $pdo->exec("CREATE TRIGGER demo_falhar BEFORE INSERT ON quartos FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Falha simulada'");
    try { popularDemonstracao($pdo); throw new LogicException('Deveria falhar'); }
    catch (PDOException $e) { demoCheck(str_contains($e->getMessage(),'Falha simulada'), 'Falha simulada alcançada'); }
    demoCheck((int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn()===2, 'Rollback preserva dados anteriores');
    $pdo->exec('DROP TRIGGER demo_falhar');
    $other = new PDO('mysql:unix_socket=' . $socket . ';dbname=asilosoft_test_demo;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $other->query("SELECT GET_LOCK('asilosoft_demo_migration', 0)")->closeCursor();
    try { popularDemonstracao($pdo); throw new LogicException('Deveria recusar execução concorrente'); }
    catch (RuntimeException $e) { demoCheck(str_contains($e->getMessage(), 'Outra carga'), 'Execução concorrente recusada imediatamente'); }
    $other->query("SELECT RELEASE_LOCK('asilosoft_demo_migration')")->closeCursor();
    $messages = [];
    demoCheck(popularDemonstracao($pdo, static function (string $message) use (&$messages): void { $messages[] = $message; }), 'Carga aplicada');
    demoCheck(count($messages) >= 5, 'Progresso informa as etapas da carga');
    demoCheck((int)$pdo->query("SELECT COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA=DATABASE() AND ROUTINE_NAME LIKE 'asilosoft_demo_%'")->fetchColumn() === 0, 'Procedimentos temporários removidos após sucesso e falha');
    demoCheck($pdo->query('SELECT * FROM usuarios WHERE id=1')->fetch()===$before, 'Administrador integralmente preservado');
    foreach (['usuarios'=>4,'idosos'=>100,'familiares'=>150,'quartos'=>28,'materiais'=>20,'registros_saude'=>300,'usos_materiais'=>300,'movimentacoes_estoque'=>343,'solicitacoes_compra'=>80] as $table=>$count) {
        demoCheck((int)$pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn()===$count, "$table: $count registros");
    }
    demoCheck((int)$pdo->query('SELECT COUNT(*) FROM vw_estoque_atual WHERE abaixo_minimo=1')->fetchColumn()===3, 'Três alertas de estoque');
    demoCheck((int)$pdo->query('SELECT COUNT(*) FROM estoques e WHERE quantidade_atual <> (SELECT SUM(IF(tipo="Entrada",quantidade,-quantidade)) FROM movimentacoes_estoque m WHERE m.material_id=e.material_id)')->fetchColumn()===0, 'Saldos conferem com extrato');
    demoCheck((int)$pdo->query('SELECT COUNT(*) FROM vw_acompanhamento_familiar a JOIN familiares f ON f.id=a.familiar_id WHERE f.usuario_id IS NOT NULL')->fetchColumn()===2, 'Conta familiar vê dois acompanhamentos publicados do idoso vinculado');
    foreach (['saude'=>'DemoSaude!2026','almoxarife'=>'DemoEstoque!2026','familiar'=>'DemoFamilia!2026'] as $account=>$password) {
        $stmt=$pdo->prepare('SELECT senha_hash FROM usuarios WHERE email=?');$stmt->execute(["$account@asilosoft.local"]);
        demoCheck(password_verify($password,$stmt->fetchColumn()), "Senha válida: $account");
    }
    $pdo->exec('DROP TRIGGER bd_me_imutavel');
    $pdo->exec('DROP TRIGGER bd_um_imutavel');
    demoCheck(!popularDemonstracao($pdo), 'Reexecução não apaga dados');
    foreach (['movimentacoes_estoque','usos_materiais'] as $table) {
        try { $pdo->exec("DELETE FROM $table LIMIT 1"); throw new LogicException('Proteção ausente'); }
        catch (PDOException $e) { demoCheck(str_contains($e->getMessage(),'imutável'), "Histórico protegido: $table"); }
    }
} finally {
    $pdo->exec('DROP DATABASE asilosoft_test_demo');
}

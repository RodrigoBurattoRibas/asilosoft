<?php
// Run: php tests/estoque_compras_rules.php (no database writes).
require_once dirname(__DIR__).'/src/modules/zcompras/services/AsiloCompraService.php';
$count = 0;
function check(bool $condition): void { global $count; if (!$condition) throw new RuntimeException('Assertion failed'); ++$count; }
function rejected(callable $call): void { try { $call(); } catch (DomainException $error) { check(true); return; } throw new RuntimeException('Expected domain rejection'); }
foreach (['0','-1','1e3','0.0001','1000000000','999999999.9999',[],null] as $invalid) {
    rejected(fn()=>AsiloAlmoxarifadoService::validarQuantidade($invalid));
}
check(AsiloAlmoxarifadoService::validarQuantidade('0,001') === '0.001');
check(AsiloAlmoxarifadoService::validarQuantidade('999999999.999') === '999999999.999');
$repository = new class {
    public array $writes = [];
    public function criar(...$args): int { $this->writes[] = $args; return 1; }
    public function alterarSituacao(...$args): void { $this->writes[] = $args; }
};
$service = new AsiloCompraService($repository);
$states = ['Pendente','Aprovada','Recusada','Atendida','Invalida'];
foreach ($states as $from) foreach ($states as $to) {
    if (in_array($to, AsiloCompraService::transicoes($from), true)) {
        $service->alterarSituacao(1,$from,$to); check(true);
    } else rejected(fn()=>$service->alterarSituacao(1,$from,$to));
}
rejected(fn()=>$service->alterarSituacao(0,'Pendente','Aprovada'));
rejected(fn()=>$service->criar(['material_id'=>1,'quantidade'=>'1','observacao'=>str_repeat('a',501)],1));
rejected(fn()=>$service->criar(['material_id'=>1,'quantidade'=>'1','observacao'=>[]],1));
check($service->criar(['material_id'=>1,'quantidade'=>'1,2','observacao'=>'Justificativa','situacao'=>'Atendida'],42)===1);
check(end($repository->writes)===[1,42,'1.200','Justificativa']);
echo "$count checks passed\n";

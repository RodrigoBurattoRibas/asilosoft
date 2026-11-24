<?php
// PHP puro: cada caso usa dublês novos, sem banco, rede ou sessão real.
$root = dirname(__DIR__, 2);
$coverage = in_array('--coverage', $argv, true);
$files = glob($root.'/src/modules/*/services/*.php');
$collector = null;
if ($coverage) {
    require_once $root.'/tests/support/LineCoverage.php';
    try {
        $collector = new LineCoverage($files, $root);
        $collector->start();
    } catch (RuntimeException $error) {
        fwrite(STDERR, $error->getMessage()."\n");
        exit(2);
    }
}
foreach ($files as $file) require_once $file;
final class DoubleRepository {
    public array $responses = [];
    public array $calls = [];
    public function __call(string $method, array $args): mixed {
        $this->calls[] = [$method, $args];
        return $this->responses[$method] ?? null;
    }
}
function same(mixed $actual, mixed $expected): void {
    if ($actual !== $expected) throw new RuntimeException('Esperado '.var_export($expected,true).'; obtido '.var_export($actual,true));
}
function rejects(callable $action): void {
    try { $action(); } catch (DomainException $e) { return; }
    throw new RuntimeException('Esperava DomainException');
}
$cases = [];
function scenario(string $id, string $name, array $data, string $expected, callable $run): void {
    global $cases;
    $cases[] = compact('id','name','data','expected','run');
}
foreach ([1,99,0,100,'1.5'] as $capacity) {
    scenario('CT-U'.sprintf('%02d',count($cases)+1), 'Quartos: limites da capacidade', ['capacidade'=>$capacity], in_array($capacity,[1,99],true)?'Criar quarto ativo':'Rejeitar sem gravar', function() use ($capacity) {
        $r=new DoubleRepository; $r->responses['criar']=['id'=>1]; $s=new AsiloQuartoService($r);
        if (in_array($capacity,[1,99],true)) { same($s->criar(['codigo'=>' A ','capacidade'=>$capacity]),['id'=>1]); same(end($r->calls),['criar',[['codigo'=>'A','capacidade'=>$capacity,'ativo'=>true]]]); }
        else { rejects(fn()=>$s->criar(['codigo'=>'A','capacidade'=>$capacity])); same($r->calls,[]); }
    });
}
scenario('CT-U06','Quarto ocupado não pode ser desativado',[], 'Rejeitar sem atualizar',function(){ $r=new DoubleRepository; $r->responses=['buscarPorId'=>['id'=>1],'contarOcupacao'=>1]; rejects(fn()=>(new AsiloQuartoService($r))->alterarStatus(1,false)); same(count($r->calls),2); });
scenario('CT-U07','Capacidade não pode ficar abaixo da ocupação',['capacidade'=>1], 'Rejeitar',function(){ $r=new DoubleRepository; $r->responses=['buscarPorId'=>['id'=>1],'contarOcupacao'=>2]; rejects(fn()=>(new AsiloQuartoService($r))->atualizar(1,['codigo'=>'A','capacidade'=>1])); });
foreach (['1234567','12345678'] as $password) scenario('CT-U'.sprintf('%02d',count($cases)+1),'Usuários: limite da senha',['senha'=>$password],strlen($password)===8?'Hash válido e e-mail normalizado':'Rejeitar sem gravar',function()use($password){$r=new DoubleRepository;$r->responses['criar']=['id'=>1];$s=new AsiloUsuarioService($r);$d=['nome'=>' Ana ','email'=>' ANA@EXAMPLE.COM ','senha'=>$password,'perfil'=>'Administrador'];if(strlen($password)<8){rejects(fn()=>$s->criar($d));same($r->calls,[]);}else{$s->criar($d);$payload=end($r->calls)[1][0];same($payload['email'],'ana@example.com');same(password_verify($password,$payload['senha_hash']),true);}});
scenario('CT-U10','Último administrador ativo',[], 'Rejeitar desativação',function(){$r=new DoubleRepository;$r->responses=['buscarPorId'=>['id'=>1,'perfil'=>'Administrador','ativo'=>true],'contarAdministradoresAtivos'=>1];rejects(fn()=>(new AsiloUsuarioService($r))->alterarStatus(1,false));same(count($r->calls),2);});
foreach (['0','0.001','999999999.999','1000000000','1e3','1.0001',[],null] as $q) scenario('CT-U'.sprintf('%02d',count($cases)+1),'Estoque: quantidade decimal',['quantidade'=>$q],in_array($q,['0.001','999999999.999'],true)?'Aceitar decimal exato':'Rejeitar',function()use($q){if(in_array($q,['0.001','999999999.999'],true))same(AsiloAlmoxarifadoService::validarQuantidade($q),$q);else rejects(fn()=>AsiloAlmoxarifadoService::validarQuantidade($q));});
scenario('CT-U19','Retirada sem finalidade',['quantidade'=>'1'],'Rejeitar sem retirar',function(){$r=new DoubleRepository;$r->responses['buscarItemPorId']=['quantidade_disponivel'=>10];rejects(fn()=>(new AsiloAlmoxarifadoService($r))->registrarRetirada(1,['quantidade'=>'1'],1));same(count($r->calls),1);});
scenario('CT-U20','Retirada superior ao saldo',['quantidade'=>'11','saldo'=>10],'Rejeitar sem retirar',function(){$r=new DoubleRepository;$r->responses['buscarItemPorId']=['quantidade_disponivel'=>10];rejects(fn()=>(new AsiloAlmoxarifadoService($r))->registrarRetirada(1,['quantidade'=>'11','observacao'=>'Uso'],1));same(count($r->calls),1);});
foreach (['Pendente','Aprovada','Recusada','Atendida','Inválida'] as $from) foreach (['Pendente','Aprovada','Recusada','Atendida'] as $to) scenario('CT-U'.sprintf('%02d',count($cases)+1),'Compras: transição de situação',['origem'=>$from,'destino'=>$to],in_array($to,AsiloCompraService::transicoes($from),true)?'Delegar uma alteração':'Rejeitar sem alterar',function()use($from,$to){$r=new DoubleRepository;$s=new AsiloCompraService($r);if(in_array($to,AsiloCompraService::transicoes($from),true)){$s->alterarSituacao(1,$from,$to);same($r->calls,[['alterarSituacao',[1,$from,$to]]]);}else{rejects(fn()=>$s->alterarSituacao(1,$from,$to));same($r->calls,[]);}});
foreach (['Administrador','Profissional de Saúde'] as $profile) scenario('CT-U'.sprintf('%02d',count($cases)+1),'Saúde: autorização e autoria',['perfil'=>$profile],'Somente profissional ativo grava com autor real',function()use($profile){$r=new DoubleRepository;$i=new DoubleRepository;$i->responses['buscarPorId']=['id'=>1];$s=new AsiloSaudeService($r,$i);$fn=fn()=>$s->registrar(1,['id'=>2,'perfil'=>$profile,'ativo'=>true],['descricao'=>' Nota ','usuario_id'=>999]);if($profile==='Administrador'){rejects($fn);same($r->calls,[]);}else{$fn();same($r->calls,[['registrar',[1,2,'Nota',false]]]);}});
foreach (['',str_repeat('a',65001),['x']] as $description) scenario('CT-U'.sprintf('%02d',count($cases)+1),'Saúde: descrição inválida',['descricao'=>$description],'Rejeitar sem gravar',function()use($description){$r=new DoubleRepository;$i=new DoubleRepository;$i->responses['buscarPorId']=['id'=>1];rejects(fn()=>(new AsiloSaudeService($r,$i))->registrar(1,['id'=>2,'perfil'=>'Profissional de Saúde','ativo'=>true],['descricao'=>$description]));same($r->calls,[]);});
foreach (['', 'inválido', 'ana@example.com'] as $email) scenario('CT-U'.sprintf('%02d',count($cases)+1),'Familiares: e-mail opcional',['email'=>$email],$email==='inválido'?'Rejeitar':'Salvar com campos opcionais normalizados',function()use($email){$r=new DoubleRepository;$r->responses['salvar']=['id'=>1];$s=new AsiloFamiliarService($r);$fn=fn()=>$s->salvar(['nome'=>'Ana','telefone'=>'123','email'=>$email]);if($email==='inválido'){rejects($fn);same($r->calls,[]);}else{same($fn(),['id'=>1]);same(end($r->calls)[1][0]['email'],$email===''?null:$email);}});
scenario('CT-U49','Idoso sem vaga',['capacidade'=>1,'ocupacao'=>1],'Rejeitar sem criar',function(){$r=new DoubleRepository;$q=new DoubleRepository;$q->responses=['buscarPorId'=>['ativo'=>true,'capacidade'=>1],'contarOcupacao'=>1];rejects(fn()=>(new AsiloIdosoService($r,$q))->criar(['nome'=>'Ana','cpf'=>'12345678901','identificador'=>'A','quarto_id'=>1]));same(count($r->calls),2);});
scenario('CT-U50','Desativar idoso libera quarto',[], 'Atualizar ativo e quarto juntos',function(){$r=new DoubleRepository;$r->responses=['buscarPorId'=>['id'=>1],'atualizar'=>['id'=>1]];(new AsiloIdosoService($r,new DoubleRepository))->alterarStatus(1,false);same(end($r->calls),['atualizar',[1,['ativo'=>false,'quarto_id'=>null]]]);});
require __DIR__.'/extended.php';
$results=[];$failed=0;
set_error_handler(static function(int $severity,string $message,string $file,int $line):never{throw new ErrorException($message,0,$severity,$file,$line);});
foreach($cases as $case){try{$case['run']();$status='Passou';$actual=$case['expected'];}catch(Throwable $e){$status='Falhou';$actual=$e->getMessage();$failed++;}unset($case['run']);$results[]=$case+['status'=>$status,'obtido'=>$actual];echo $case['id'].' '.$status.' '.$case['name']."\n";}
restore_error_handler();
$out = getenv('ASILOSOFT_TEST_REPORT_DIR') ?: $root.'/docs/testes/evidencias';
if (!is_dir($out) && !mkdir($out, 0775, true)) throw new RuntimeException('Não foi possível criar '.$out);
file_put_contents($out.'/unitarios.json',json_encode(['executado_em'=>date(DATE_ATOM),'php'=>PHP_VERSION,'total'=>count($results),'falharam'=>$failed,'casos'=>$results],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$coveragePassed = true;
if ($collector !== null) {
    $report = $collector->finish($out);
    $coveragePassed = $report['percentual'] >= $report['meta_percentual'];
    printf("Cobertura (%s): %.2f%% — %d/%d linhas; meta 60%%: %s\n",
        $report['driver'], $report['percentual'], $report['cobertas'], $report['linhas'],
        $coveragePassed ? 'atingida' : 'não atingida');
}
echo count($results)." casos; $failed falhas de teste\n";
exit($failed || !$coveragePassed ? 1 : 0);

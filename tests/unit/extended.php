<?php
// Cenários acrescentados após a primeira medição revelar serviços sem cobertura.
function extra(string $name, array $data, string $expected, callable $run): void
{
    global $cases;
    scenario('CT-U'.sprintf('%02d', count($cases) + 1), $name, $data, $expected, $run);
}
extra('Login válido normaliza e-mail e renova sessão', ['email'=>' ANA@EXAMPLE.COM ','senha'=>'senha123'], 'Autenticar usuário 7, renovar sessão antes de armazenar sua identidade', function() {
    $r=new DoubleRepository; $session=new DoubleRepository;
    $r->responses['buscarPorEmail']=['id'=>7,'ativo'=>true,'senha_hash'=>password_hash('senha123',PASSWORD_DEFAULT)];
    same((new AsiloAutenticacaoService($r,$session))->tentarLogin(' ANA@EXAMPLE.COM ','senha123'),true);
    same($r->calls,[['buscarPorEmail',['ana@example.com']]]);
    same($session->calls,[['regenerateId',[]],['set',['asilosoft_usuario_id',7]]]);
});
foreach (['inexistente','inativo','senha_errada'] as $condition) extra('Login recusado não altera sessão', ['condicao'=>$condition], 'DomainException e nenhuma alteração de sessão', function() use($condition) {
    $r=new DoubleRepository; $session=new DoubleRepository;
    if($condition!=='inexistente') $r->responses['buscarPorEmail']=['id'=>7,'ativo'=>$condition!=='inativo','senha_hash'=>password_hash('senha123',PASSWORD_DEFAULT)];
    rejects(fn()=>(new AsiloAutenticacaoService($r,$session))->tentarLogin('ana@example.com',$condition==='senha_errada'?'outra':'senha123'));
    same($session->calls,[]);
});
extra('Sessão com identidade inválida', ['identidade'=>'abc'], 'Não consultar usuário e retornar null', function(){
    $r=new DoubleRepository;$s=new DoubleRepository;$s->responses['get']='abc';
    same((new AsiloAutenticacaoService($r,$s))->usuarioAtual(),null);same($r->calls,[]);
});
extra('Sessão de usuário desativado é revogada', ['identidade'=>'7','ativo'=>false], 'Remover identidade da sessão e renovar seu ID',function(){
    $r=new DoubleRepository;$s=new DoubleRepository;$s->responses['get']='7';$r->responses['buscarPorId']=['id'=>7,'ativo'=>false];
    same((new AsiloAutenticacaoService($r,$s))->usuarioAtual(),null);
    same($s->calls,[['get',['asilosoft_usuario_id']],['remove',['asilosoft_usuario_id']],['regenerateId',[]]]);
});
extra('Sessão de usuário ativo', ['identidade'=>7,'ativo'=>true], 'Retornar usuário e preservar sessão',function(){
    $r=new DoubleRepository;$s=new DoubleRepository;$s->responses['get']=7;$r->responses['buscarPorId']=['id'=>7,'ativo'=>true];
    same((new AsiloAutenticacaoService($r,$s))->usuarioAtual(),['id'=>7,'ativo'=>true]);same(count($s->calls),1);
});
extra('Ficha de idoso inexistente', ['idoso_id'=>0], 'Retornar null sem consultar repositórios',function(){
    $r=new DoubleRepository;same((new AsiloFichaService($r,$r,$r,$r))->consultar(0),null);same($r->calls,[]);
});
extra('Ficha reúne informações do mesmo idoso', ['idoso_id'=>3], 'Consolidar residente, familiares, registros e usos consultando o mesmo ID',function(){
    $r=new DoubleRepository;$r->responses=['buscarPorId'=>['id'=>3],'porIdoso'=>[['nome'=>'Familiar']],'listarPorIdoso'=>[['descricao'=>'Nota']],'usos'=>[['quantidade'=>'1.000']]];
    same((new AsiloFichaService($r,$r,$r,$r))->consultar(3),['idoso'=>['id'=>3],'familiares'=>[['nome'=>'Familiar']],'registros'=>[['descricao'=>'Nota']],'usos'=>[['quantidade'=>'1.000']]]);
    same($r->calls,[['buscarPorId',[3]],['porIdoso',[3]],['listarPorIdoso',[3]],['usos',[3]]]);
});
extra('Filtro separa ativos e desativados', ['status'=>'inválido/desativados'], 'Status desconhecido assume ativos; resultados reindexados por situação',function(){
    $s=new AsiloFiltroSituacao;$rows=[4=>['id'=>1,'ativo'=>1],9=>['id'=>2,'ativo'=>0]];
    same($s->filtrar($rows,'qualquer'),[['id'=>1,'ativo'=>1]]);same($s->filtrar($rows,'desativados'),[['id'=>2,'ativo'=>0]]);
});
foreach (['','nova1234'] as $password) extra('Editar usuário com senha opcional', ['id'=>7,'senha'=>$password], $password===''?'Não sobrescrever hash quando senha estiver vazia':'Salvar novo hash verificável',function()use($password){
    $r=new DoubleRepository;$r->responses=['buscarPorId'=>['id'=>7],'buscarPorEmail'=>['id'=>7],'atualizar'=>['id'=>7]];
    (new AsiloUsuarioService($r))->atualizar(7,['nome'=>' Ana ','email'=>' ANA@EXAMPLE.COM ','perfil'=>'Familiar','senha'=>$password]);
    $payload=end($r->calls)[1][1];same($payload['nome'],'Ana');same($payload['email'],'ana@example.com');
    if($password==='')same(array_key_exists('senha_hash',$payload),false);else same(password_verify($password,$payload['senha_hash']),true);
});
extra('Material aceita estoque mínimo zero', ['nome'=>' Gaze ','tipo'=>'Material','unidade_medida'=>'un','estoque_minimo'=>'0'], 'Criar material normalizado com mínimo 0.000 e descrição null',function(){
    $r=new DoubleRepository;$r->responses['criarItem']=['id'=>1];
    same((new AsiloAlmoxarifadoService($r))->criarItem(['nome'=>' Gaze ','tipo'=>'Material','unidade_medida'=>'un','estoque_minimo'=>'0']),['id'=>1]);
    same(end($r->calls),['criarItem',[['nome'=>'Gaze','tipo'=>'Material','descricao'=>null,'unidade_medida'=>'un','estoque_minimo'=>'0.000']]]);
});
extra('Entrada registra origem quantidade e autoria', ['item_id'=>1,'quantidade'=>'1,2','origem'=>'SUS','usuario_id'=>7], 'Delegar entrada com quantidade 1.200 e autoria 7',function(){
    $r=new DoubleRepository;$r->responses=['buscarItemPorId'=>['id'=>1],'registrarEntrada'=>['id'=>2]];
    same((new AsiloAlmoxarifadoService($r))->registrarEntrada(1,['quantidade'=>'1,2','origem'=>'SUS','observacao'=>' Doação '],7),['id'=>2]);
    same(end($r->calls),['registrarEntrada',[1,'1.200','SUS',7,'Doação']]);
});
extra('Retirada admite exatamente o saldo disponível', ['quantidade'=>'10','saldo'=>10,'observacao'=>'Uso coletivo'], 'Aceitar retirada integral e preservar finalidade e autor',function(){
    $r=new DoubleRepository;$r->responses=['buscarItemPorId'=>['quantidade_disponivel'=>10],'registrarRetirada'=>['id'=>2]];
    same((new AsiloAlmoxarifadoService($r))->registrarRetirada(1,['quantidade'=>'10','observacao'=>'Uso coletivo'],7),['id'=>2]);
    same(end($r->calls),['registrarRetirada',[1,'10.000',7,'Uso coletivo']]);
});
extra('Consumo valida chave e preserva payload', ['idoso_id'=>3,'material_id'=>1,'quantidade'=>'0,001','chave'=>str_repeat('a',64)], 'Delegar consumo de 0.001 com chave original e autor 7',function(){
    $r=new DoubleRepository;$r->responses['registrarUso']=['id'=>9];$key=str_repeat('a',64);
    same((new AsiloAlmoxarifadoService($r))->registrarUso(['idoso_id'=>3,'material_id'=>1,'quantidade'=>'0,001','chave_requisicao'=>$key],7),['id'=>9]);
    same($r->calls,[['registrarUso',[3,1,'0.001',7,null,$key]]]);
});
extra('Vínculo familiar normaliza parentesco', ['idoso_id'=>3,'familiar_id'=>4,'parentesco'=>' Filha ','responsavel_principal'=>1], 'Delegar vínculo com parentesco sem espaços e responsável principal true',function(){
    $r=new DoubleRepository;(new AsiloFamiliarService($r))->vincular(3,4,['parentesco'=>' Filha ','responsavel_principal'=>1]);same($r->calls,[['vincular',[3,4,'Filha',true]]]);
});
extra('Vínculo familiar exige parentesco', ['idoso_id'=>3,'familiar_id'=>4,'parentesco'=>''], 'Rejeitar vínculo sem parentesco e não gravar',function(){
    $r=new DoubleRepository;rejects(fn()=>(new AsiloFamiliarService($r))->vincular(3,4,['parentesco'=>'']));same($r->calls,[]);
});
extra('Cadastro de idoso normaliza CPF com vaga livre', ['nome'=>' Ana ','cpf'=>'123.456.789-01','identificador'=>' A ','quarto_id'=>1,'capacidade'=>1,'ocupacao'=>0], 'Salvar CPF sem máscara e nome e identificador sem espaços',function(){
    $r=new DoubleRepository;$q=new DoubleRepository;$q->responses=['buscarPorId'=>['ativo'=>true,'capacidade'=>1],'contarOcupacao'=>0];$r->responses['criar']=['id'=>3];
    same((new AsiloIdosoService($r,$q))->criar(['nome'=>' Ana ','cpf'=>'123.456.789-01','identificador'=>' A ','quarto_id'=>1]),['id'=>3]);
    same(end($r->calls),['criar',[['nome'=>'Ana','cpf'=>'12345678901','identificador'=>'A','quarto_id'=>1]]]);
});
extra('Retificação de saúde inclui referência e mantém autoria', ['idoso_id'=>3,'autor_id'=>7,'corrige_id'=>8,'descricao'=>' Correção ','visivel_para_familiar'=>'1'], 'Gravar nota aditiva referente ao registro 8, autor 7 e visibilidade true',function(){
    $r=new DoubleRepository;$i=new DoubleRepository;$i->responses['buscarPorId']=['id'=>3];$r->responses['pertenceAoIdoso']=true;
    (new AsiloSaudeService($r,$i))->registrar(3,['id'=>7,'perfil'=>'Profissional de Saúde','ativo'=>true],['descricao'=>' Correção ','corrige_id'=>8,'visivel_para_familiar'=>'1']);
    same(end($r->calls),['registrar',[3,7,'Retificação do registro #8: Correção',true]]);
});
extra('Quarto vazio pode ser desativado', ['quarto_id'=>1,'ocupacao'=>0,'ativo'=>false], 'Atualizar quarto existente para ativo false',function(){
    $r=new DoubleRepository;$r->responses=['buscarPorId'=>['id'=>1],'contarOcupacao'=>0,'atualizar'=>['id'=>1,'ativo'=>false]];
    same((new AsiloQuartoService($r))->alterarStatus(1,false),['id'=>1,'ativo'=>false]);same(end($r->calls),['atualizar',[1,['ativo'=>false]]]);
});
// Limite de tentativas de login (DEF-S01, docs/testes/SEGURANCA.md).
extra('Login bloqueado após 5 falhas recentes', ['falhas_recentes'=>5,'senha'=>'correta'], 'DomainException sem consultar usuário nem alterar sessão',function(){
    $r=new DoubleRepository;$s=new DoubleRepository;$t=new DoubleRepository;$t->responses['falhasRecentes']=5;
    rejects(fn()=>(new AsiloAutenticacaoService($r,$s,$t))->tentarLogin('Ana@Example.com','senha123'));
    same($r->calls,[]);same($s->calls,[]);same($t->calls,[['falhasRecentes',[hash('sha256','ana@example.com'),15]]]);
});
extra('Falha de login registra tentativa, inclusive de e-mail inexistente', ['email'=>'ninguem@example.com','falhas_recentes'=>4], 'DomainException e uma falha registrada para o hash do e-mail',function(){
    $r=new DoubleRepository;$s=new DoubleRepository;$t=new DoubleRepository;$t->responses['falhasRecentes']=4;
    rejects(fn()=>(new AsiloAutenticacaoService($r,$s,$t))->tentarLogin('ninguem@example.com','qualquer'));
    same(end($t->calls),['registrarFalha',[hash('sha256','ninguem@example.com')]]);same($s->calls,[]);
});
extra('Login válido limpa as falhas anteriores', ['falhas_recentes'=>3], 'Autenticar e apagar as falhas do e-mail',function(){
    $r=new DoubleRepository;$s=new DoubleRepository;$t=new DoubleRepository;$t->responses['falhasRecentes']=3;
    $r->responses['buscarPorEmail']=['id'=>7,'ativo'=>true,'senha_hash'=>password_hash('senha123',PASSWORD_DEFAULT)];
    same((new AsiloAutenticacaoService($r,$s,$t))->tentarLogin('ana@example.com','senha123'),true);
    same(end($t->calls),['limpar',[hash('sha256','ana@example.com')]]);
});

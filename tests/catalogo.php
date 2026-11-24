<?php
// Gera a tabela a partir do resultado real, sem declarar casos não executados.
$root = dirname(__DIR__);
$reportDir = getenv('ASILOSOFT_TEST_REPORT_DIR') ?: $root.'/docs/testes/evidencias';
$reportFile = realpath($reportDir.'/unitarios.json');
if ($reportFile === false) throw new RuntimeException('Relatório unitário não encontrado em '.$reportDir);
$evidenceLink = str_starts_with($reportFile, $root.'/docs/testes/')
    ? substr($reportFile, strlen($root.'/docs/testes/')) : $reportFile;
$report = json_decode(file_get_contents($reportFile),true,512,JSON_THROW_ON_ERROR);
function cell(string $text): string { return str_replace(["\r","\n",'|'],['','<br>','\\|'],$text); }
function compactData(array $data): string {
    foreach($data as &$v) if(is_string($v) && strlen($v)>200) $v='str_repeat('.json_encode(substr($v,0,1)).', '.strlen($v).')';
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
}
$md="# Casos unitários executados\n\nGerado de `{$evidenceLink}`. Execução: {$report['executado_em']}.\n\nCada ID corresponde a um cenário independente em `tests/unit/run.php`. Os títulos da coluna funcionalidade são referências locais às regras existentes, sem inventar numeração de requisito. Dublês de repositório são recriados por caso; respostas, chamadas e argumentos esperados estão no script. Nenhum acesso real ao banco. Entradas longas usam notação `str_repeat` equivalente à entrada original, disponível no JSON.\n\n";
$headers=['ID','Requisito/Funcionalidade','Tipo de teste','Pré-condições','Passos','Dados de entrada','Resultado esperado','Resultado obtido','Status','Evidência'];
$md.='| '.implode(' | ',$headers)." |\n| ".implode(' | ',array_fill(0,10,'---'))." |\n";
foreach($report['casos'] as $case){$row=[$case['id'],$case['name'],'Unitário (regra de negócio)','PHP CLI com mbstring; dublês novos conforme fixture do ID','1. Executar php tests/unit/run.php; 2. Localizar '.$case['id'].'; 3. O script chama o serviço com a fixture e compara retorno, exceção e/ou chamadas',compactData($case['data']),$case['expected'],$case['obtido'],$case['status'],'[JSON por ID]('.$evidenceLink.')'];$md.='| '.implode(' | ',array_map('cell',$row))." |\n";}
file_put_contents($root.'/docs/testes/CASOS_UNITARIOS.md',$md);
echo "Catálogo atualizado: ".count($report['casos'])." casos reais.\n";

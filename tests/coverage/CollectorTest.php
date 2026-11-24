<?php
require_once dirname(__DIR__).'/support/LineCoverage.php';
if (PHP_SAPI !== 'phpdbg') { fwrite(STDERR,"Execute este controle com phpdbg -qrr.\n"); exit(2); }
$root = dirname(__DIR__, 2);
$fixture = __DIR__.'/fixture.php';
$collector = new LineCoverage([$fixture],$root);
$collector->start();
require $fixture;
if ((new CoverageFixture)->choose(true) !== '<tag>') throw new RuntimeException('Fixture inválida');
$out = sys_get_temp_dir().'/asilosoft-coverage-control-'.getmypid();
$report = $collector->finish($out);
if (count($report['arquivos']) !== 1) throw new RuntimeException('Arquivos externos no denominador');
$data = $report['arquivos']['tests/coverage/fixture.php'];
foreach (file($fixture) as $index => $line) {
    if (str_contains($line, '// covered branch') && !in_array($index+1,$data['linhas_cobertas'],true)) throw new RuntimeException('Ramo executado ausente');
    if ((str_contains($line,"return 'unvisited")) && !in_array($index+1,$data['linhas_nao_cobertas'],true)) throw new RuntimeException('Código nunca chamado excluído do denominador');
}
if ($report['percentual'] >= 100 || $report['percentual'] <= 0) throw new RuntimeException('Percentual de controle incorreto');
if (!str_contains(file_get_contents($out.'/cobertura.html'),'&lt;tag&gt;')) throw new RuntimeException('HTML não escapado');
foreach (glob($out.'/*') as $file) unlink($file);
rmdir($out);
echo "Controle aprovado: ramo executado, ramo não executado e método nunca chamado; escopo filtrado e HTML escapado.\n";

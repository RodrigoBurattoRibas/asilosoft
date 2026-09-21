<?php

declare(strict_types=1);

$arquivos = glob(__DIR__ . '/*Test.php');
$falhas = [];
$total = 0;

foreach ($arquivos as $arquivo) {
    require_once $arquivo;
}

foreach (get_defined_functions()['user'] as $funcao) {
    if (!str_starts_with($funcao, 'testar')) {
        continue;
    }

    $total++;
    try {
        $funcao();
        echo "OK: {$funcao}\n";
    } catch (Throwable $erro) {
        $falhas[] = "FALHOU: {$funcao} - {$erro->getMessage()}";
    }
}

foreach ($falhas as $falha) {
    echo $falha . "\n";
}

echo "\n{$total} teste(s), " . count($falhas) . " falha(s).\n";
exit($falhas === [] ? 0 : 1);

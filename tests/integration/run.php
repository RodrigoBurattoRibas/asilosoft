<?php
/**
 * Comando curto para o banco de testes configurado na aplicação.
 * Uso: php tests/integration/run.php
 * Documentação: docs/testes/README.md e docs/testes/COMANDO_INTEGRACAO.md.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}
if (($argv[1] ?? '') === '--help' && count($argv) === 2) {
    echo "Uso: php tests/integration/run.php\n";
    echo "Usa a conexão da aplicação (.env/ambiente), executa unitários e seis integrações em bases auxiliares e remove essas bases ao terminar.\n";
    exit(0);
}
if (count($argv) !== 1) {
    fwrite(STDERR, "Argumento inválido. Use php tests/integration/run.php --help\n");
    exit(2);
}

$root = dirname(__DIR__, 2);
require_once $root.'/src/infra/Database/Database.php';

try {
    // Reutiliza a resolução de aliases, precedência e valores vazios da aplicação,
    // sem duplicar o parser do .env nem executar seu conteúdo como shell.
    $read = new ReflectionMethod(PDOMysqli::class, 'envValue');
    $settings = [
        'HOST' => [['MYSQLHOST', 'ASILOSOFT_DB_HOST'], null],
        'PORTA' => [['MYSQLPORT', 'ASILOSOFT_DB_PORTA'], '3306'],
        'USUARIO' => [['MYSQLUSER', 'ASILOSOFT_DB_USUARIO'], null],
        'SENHA' => [['MYSQLPASSWORD', 'MYSQL_ROOT_PASSWORD', 'ASILOSOFT_DB_SENHA'], ''],
    ];
    $environment = getenv();
    $config = [];
    foreach ($settings as $name => [$keys, $default]) {
        $config[$name] = $read->invoke(null, $keys, $default);
        $environment['ASILOSOFT_TEST_DB_'.$name] = $config[$name];
    }

    $dsn = 'mysql:host='.$config['HOST'].';port='.$config['PORTA'].';charset=utf8mb4';
    $connect = static fn(): PDO => new PDO($dsn, $config['USUARIO'], $config['SENHA'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    // A conexão não seleciona a base da aplicação e não aplica migrations nela.
    $connect();
    echo "Executando testes no servidor configurado, em bases auxiliares isoladas.\n";
    $process = proc_open(['bash', 'tests/m3.sh'], [
        0 => ['file', '/dev/null', 'r'],
        1 => ['pipe', 'w'],
        2 => ['redirect', 1],
    ], $pipes, $root, $environment);
    if (!is_resource($process)) throw new RuntimeException('Não foi possível iniciar o runner.');

    $output = '';
    while (($line = fgets($pipes[1])) !== false) {
        $output .= $line;
        echo $line;
    }
    fclose($pipes[1]);
    $status = proc_close($process);

    // Somente nomes anunciados por ESTE processo, nunca a base MYSQLDATABASE.
    preg_match_all('/^CT-I\d+: [^\r\n]*banco (asilosoft_test_[a-z0-9_]+)$/m', $output, $matches);
    $databases = array_values(array_unique($matches[1]));
    $removed = [];
    $remaining = [];
    if ($databases !== []) {
        $pdo = $connect(); // Nova conexão: a execução remota pode durar vários minutos.
        foreach ($databases as $database) {
            $exists = $pdo->prepare('SELECT COUNT(*) FROM information_schema.schemata WHERE schema_name=?');
            $exists->execute([$database]);
            if ((int)$exists->fetchColumn() !== 0) {
                $pdo->exec('DROP DATABASE `'.$database.'`');
                $removed[] = $database;
            }
            $exists->execute([$database]);
            if ((int)$exists->fetchColumn() !== 0) $remaining[] = $database;
        }
    }
    $complete = $status === 0 && count($databases) === 6 && $remaining === [];
    $receipt = [
        'verificado_em' => (new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')))->format(DATE_ATOM),
        'codigo_suite' => $status,
        'bases_verificadas' => count($databases),
        'bases_retidas_removidas' => $removed,
        'bases_restantes' => $remaining,
        'resultado' => $complete ? 'Passou' : 'Falhou',
    ];
    if (preg_match('/^Evidências: (docs\/testes\/evidencias\/[0-9T_]+)$/m', $output, $path)) {
        if (file_put_contents($root.'/'.$path[1].'/limpeza.json', json_encode($receipt, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n") === false) {
            throw new RuntimeException('Não foi possível gravar a confirmação da limpeza.');
        }
    }
    echo 'Limpeza: '.count($databases).' bases verificadas; '.count($remaining)." bases restantes.\n";
    exit($complete ? 0 : ($status === 2 ? 2 : 1));
} catch (PDOException $error) {
    fwrite(STDERR, 'Falha no acesso/limpeza do banco. SQLSTATE '.$error->getCode().'. Confira conexão e permissões de CREATE/DROP DATABASE. Credenciais omitidas.'."\n");
    exit(2);
} catch (Throwable $error) {
    fwrite(STDERR, 'Não foi possível concluir o runner: '.$error->getMessage()."\n");
    exit(1);
}

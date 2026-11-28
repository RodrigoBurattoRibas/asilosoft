<?php
/**
 * Teste de carga e estresse do AsiloSoft (HTTP ponta a ponta).
 *
 * Usuários virtuais (processos pcntl) fazem login e repetem fluxos reais por perfil,
 * com tempo de leitura entre ações. As etapas aumentam a quantidade de usuários
 * simultâneos; cada etapa mede vazão, latência (média, p50, p90, p95, p99) e erros.
 * Ao final há uma etapa de recuperação e a verificação de integridade do banco.
 *
 * Uso: php tests/carga/CargaEstresse.php [--rapido] [--duracao=20] [--etapas=10,25,50,100,150,200,300]
 *                                          [--workers=1,4] [--conexao-da-aplicacao]
 * Documentação: docs/testes/CARGA_ESTRESSE.md
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}
foreach (['pcntl_fork', 'posix_kill'] as $funcao) {
    if (!function_exists($funcao)) {
        fwrite(STDERR, "IMPEDIMENTO: a função $funcao (extensões pcntl/posix) é necessária no PHP CLI.\n");
        exit(2);
    }
}
date_default_timezone_set('America/Sao_Paulo');
require_once __DIR__.'/../support/HttpCliente.php';
require_once __DIR__.'/../support/AmbienteHttp.php';
require_once __DIR__.'/../support/RelatorioCarga.php';

$opcoes = getopt('', ['rapido', 'duracao:', 'etapas:', 'workers:', 'conexao-da-aplicacao', 'help']);
if (isset($opcoes['help'])) {
    echo "Uso: php tests/carga/CargaEstresse.php [--rapido] [--duracao=S] [--etapas=N,N,...] [--workers=1,4] [--conexao-da-aplicacao]\n";
    exit(0);
}
$rapido = isset($opcoes['rapido']);
$duracao = (int) ($opcoes['duracao'] ?? ($rapido ? 8 : 20));
$etapas = array_map('intval', explode(',', $opcoes['etapas'] ?? ($rapido ? '5,20,60' : '10,25,50,100,150,200,300')));
$configWorkers = array_map('intval', explode(',', $opcoes['workers'] ?? ($rapido ? '1' : '1,4')));
$limiteP95 = 2000.0;     // RNF05: até 2 s em uso normal
$limiteErro = 0.01;      // RNF06: disponibilidade mínima de 99%
$limiteCarga = 50;       // até 50 usuários simultâneos = uso normal esperado; acima, estresse
$ruptura = 0.5;          // 50% de erros encerra a escalada
$timeoutCliente = 15.0;
$pensar = [0.5, 2.0];    // segundos de leitura entre ações do usuário

$raiz = dirname(__DIR__, 2);
$saida = $raiz.'/docs/testes/evidencias/carga_'.date('Ymd\THis');
mkdir($saida, 0775, true);
$tmp = sys_get_temp_dir().'/asilosoft-carga-'.getmypid();
mkdir($tmp, 0700, true);

$ambiente = null;
$interrompido = false;
pcntl_async_signals(true);
pcntl_signal(SIGINT, function () use (&$interrompido) { $interrompido = true; });

try {
    $ambiente = new AmbienteHttp('carga', isset($opcoes['conexao-da-aplicacao']));
    $local = in_array($ambiente->host(), ['127.0.0.1', 'localhost', '::1'], true);
    echo "Evidências: ".substr($saida, strlen($raiz) + 1)."\n";
    echo 'Preparando base '.$ambiente->base.' (MySQL '.($local ? 'local' : 'remoto').")...\n";
    $ambiente->preparar(20, 300, 20, 2);
    $versaoMysql = (string) $ambiente->pdo->query('SELECT VERSION()')->fetchColumn();
    $porta = (int) (getenv('ASILOSOFT_TEST_HTTP_PORT') ?: 18090);

    $resultado = [
        'gerado_em' => date(DATE_ATOM),
        'ambiente' => ambiente($versaoMysql, $local),
        'parametros' => [
            'duracao_etapa_s' => $duracao, 'etapas' => $etapas, 'workers' => $configWorkers,
            'limite_p95_ms' => $limiteP95, 'limite_erro' => $limiteErro, 'limite_carga_vus' => $limiteCarga,
            'timeout_cliente_s' => $timeoutCliente, 'tempo_leitura_s' => $pensar,
            'massa' => ['usuarios_por_perfil' => 20, 'idosos' => 300, 'materiais' => 20, 'registros_saude' => 600],
            'mistura_perfis' => ['Profissional de Saúde' => '40%', 'Administrador' => '30%', 'Almoxarife' => '30%'],
        ],
        'execucoes' => [],
    ];

    foreach ($configWorkers as $workers) {
        if ($interrompido) break;
        echo "\n== Servidor PHP embutido com ".($workers > 1 ? "$workers workers" : '1 processo (igual ao Railway)')." ==\n";
        $ambiente->iniciarServidor($porta, $workers, $saida."/servidor_w$workers.log");
        $antes = contagens($ambiente->pdo, $ambiente->base);
        $plano = [];
        foreach ($etapas as $vus) $plano[] = ['vus' => $vus, 'tipo' => $vus <= $limiteCarga ? 'carga' : 'estresse', 'duracao' => $duracao];
        $plano[] = ['vus' => min($etapas), 'tipo' => 'recuperacao', 'duracao' => max(8, intdiv($duracao, 2))];
        $rodada = executarConfiguracao($plano, $ambiente, $porta, $tmp, $timeoutCliente, $pensar, $ruptura);
        $execucao = ['workers' => $workers, 'etapas' => $rodada['etapas']];
        $ambiente->pararServidor();
        $ambiente->reconectar();
        $execucao['integridade'] = integridade($ambiente->pdo, $antes, $rodada['confirmados']);
        echo '   Integridade: '.$execucao['integridade']['resultado'].' - '.$execucao['integridade']['detalhe']."\n";
        $execucao['analise'] = analisar($execucao['etapas'], $limiteP95, $limiteErro);
        $resultado['execucoes'][] = $execucao;
    }

    $resultado['interrompido'] = $interrompido;
    file_put_contents($saida.'/resultado.json', json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");
    file_put_contents($saida.'/relatorio.md', RelatorioCarga::markdown($resultado));
    file_put_contents($saida.'/relatorio.html', RelatorioCarga::html($resultado));
    $rel = substr($saida, strlen($raiz) + 1);
    echo "\nRelatórios: $rel/relatorio.md e $rel/relatorio.html\n";

    $cargaOk = true;
    foreach ($resultado['execucoes'] as $execucao) {
        if ($execucao['workers'] !== 1) continue;
        foreach ($execucao['etapas'] as $etapa) if ($etapa['tipo'] === 'carga' && !$etapa['aprovada']) $cargaOk = false;
        if ($execucao['integridade']['resultado'] !== 'Passou') $cargaOk = false;
    }
    echo $cargaOk ? "Carga normal (configuração do Railway) dentro dos critérios.\n" : "Carga normal FORA dos critérios na configuração do Railway; veja o relatório.\n";
    $codigo = $interrompido ? 130 : ($cargaOk ? 0 : 1);
} catch (Throwable $erro) {
    fwrite(STDERR, 'Falha no teste de carga: '.$erro->getMessage()."\n");
    $codigo = $erro instanceof PDOException ? 2 : 1;
} finally {
    if ($ambiente) {
        try {
            $ambiente->encerrar();
            echo 'Base '.$ambiente->base." removida.\n";
        } catch (Throwable $erro) {
            fwrite(STDERR, 'Atenção: não foi possível remover a base '.$ambiente->base.": ".$erro->getMessage()."\n");
        }
    }
    array_map('unlink', glob($tmp.'/*') ?: []);
    @rmdir($tmp);
}
exit($codigo);

/* ------------------------------------------------------------------ */

/**
 * Executa as etapas em sequência com usuários persistentes: quem já entrou continua logado
 * na etapa seguinte; a cada aumento só os novos usuários fazem login (rampa de até 5 s).
 * Na recuperação os usuários excedentes saem e a medição começa 5 s depois.
 */
function executarConfiguracao(array $plano, AmbienteHttp $ambiente, int $porta, string $tmp, float $timeout, array $pensar, float $ruptura): array
{
    global $interrompido;
    $id = uniqid('cfg');
    $ativos = [];
    $janelas = [];
    $total = 0;
    $prazoFinal = microtime(true) + 3600;
    foreach ($plano as $k => $etapa) {
        if ($interrompido) break;
        while (count($ativos) > $etapa['vus']) {
            $pid = array_pop($ativos);
            posix_kill($pid, SIGTERM);
            pcntl_waitpid($pid, $status);
        }
        if ($etapa['tipo'] === 'recuperacao') usleep(5000000);
        $inicio = microtime(true);
        $novos = $etapa['vus'] - count($ativos);
        $rampa = $novos > 0 ? min(5.0, $etapa['duracao'] / 4) : 0.0;
        for ($n = 0; $n < $novos; $n++) {
            $i = $total++;
            $perfil = match (true) { $i % 10 < 4 => 'saude', $i % 10 < 7 => 'admin', default => 'estoque' };
            $contas = $ambiente->usuarios[$perfil];
            $conta = $contas[intdiv($i, 10) % count($contas)];
            $atraso = $novos > 1 ? $rampa * $n / ($novos - 1) : 0;
            $pid = pcntl_fork();
            if ($pid === -1) throw new RuntimeException('Falha ao criar usuário virtual (fork).');
            if ($pid === 0) {
                pcntl_signal(SIGINT, SIG_IGN);
                pcntl_signal(SIGTERM, SIG_DFL);
                usuarioVirtual($perfil, $conta['email'], $inicio + $atraso, $prazoFinal, "$tmp/$id-$i.jsonl", $porta, $ambiente->totalIdosos, $ambiente->totalMateriais, $timeout, $pensar);
                exit(0);
            }
            $ativos[] = $pid;
        }
        echo sprintf("-> %-11s %4d usuários simultâneos (%d novos) por %ds... ", $etapa['tipo'], $etapa['vus'], max(0, $novos), $etapa['duracao']);
        $fim = $inicio + $rampa + $etapa['duracao'];
        $amostras = [];
        $pids = $ambiente->pidsServidor();
        $cpuAnterior = cpuServidor($pids);
        $tAnterior = microtime(true);
        while (microtime(true) < $fim && !$interrompido) {
            usleep((int) (min(1.0, max(0.05, $fim - microtime(true))) * 1e6));
            $pids = $ambiente->pidsServidor();
            $cpu = cpuServidor($pids);
            $agora = microtime(true);
            $amostras[] = [
                't' => round($agora - $inicio, 1),
                'cpu_pct' => round(($cpu - $cpuAnterior) / 100 / max(0.05, $agora - $tAnterior) * 100, 1),
                'rss_mb' => round(rssServidor($pids) / 1048576, 1),
                'load1' => (float) explode(' ', (string) @file_get_contents('/proc/loadavg'))[0],
            ];
            $cpuAnterior = $cpu;
            $tAnterior = $agora;
        }
        $janelas[$k] = [$etapa, $inicio, $fim, $rampa, $amostras];
        $parcial = agregar($etapa, lerRegistros($tmp, $id, $inicio, $fim), $fim - $inicio, $rampa, $amostras);
        imprimirEtapa($parcial);
        if ($etapa['tipo'] !== 'recuperacao' && $parcial['taxa_erro'] >= $ruptura) {
            echo "   Ponto de ruptura atingido (>= 50% de erros); escalada encerrada, segue para recuperação.\n";
            $ultima = array_key_last($plano);
            if ($k < $ultima - 1) {
                // pula as etapas de estresse restantes e vai direto para a recuperação
                foreach (array_keys($plano) as $j) if ($j > $k && $j < $ultima) unset($plano[$j]);
            }
        }
    }
    // Aguarda respostas em andamento (inclusive tempos limite) antes de encerrar.
    usleep((int) (min($timeout, 5) * 1e6));
    foreach ($ativos as $pid) posix_kill($pid, SIGTERM);
    foreach ($ativos as $pid) pcntl_waitpid($pid, $status);

    $etapas = [];
    foreach ($janelas as [$etapa, $inicio, $fim, $rampa, $amostras]) {
        $etapas[] = agregar($etapa, lerRegistros($tmp, $id, $inicio, $fim), $fim - $inicio, $rampa, $amostras);
    }
    // Todas as respostas confirmadas, inclusive fora das janelas medidas (pausa e encerramento).
    $confirmados = [];
    foreach (lerRegistros($tmp, $id, 0, INF) as $r) if ($r[4]) $confirmados[$r[1]] = ($confirmados[$r[1]] ?? 0) + 1;
    array_map('unlink', glob("$tmp/$id-*.jsonl") ?: []);
    return ['etapas' => $etapas, 'confirmados' => $confirmados];
}

/** Registros cujo início da requisição caiu na janela [inicio, fim). */
function lerRegistros(string $tmp, string $id, float $inicio, float $fim): array
{
    $linhas = [];
    foreach (glob("$tmp/$id-*.jsonl") ?: [] as $arquivo) {
        foreach (file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $linha) {
            $r = json_decode($linha, true);
            if (!is_array($r)) continue;
            $comeco = $r[0] - $r[3] / 1000;
            if ($comeco >= $inicio && $comeco < $fim) {
                $r[0] = $comeco;
                $linhas[] = $r;
            }
        }
    }
    return $linhas;
}

/** Comportamento de um usuário: login e ações do perfil até o fim da etapa. */
function usuarioVirtual(string $perfil, string $email, float $comeco, float $fim, string $arquivo, int $porta, int $idosos, int $materiais, float $timeout, array $pensar): void
{
    mt_srand(getmypid() ^ (int) (microtime(true) * 1000));
    $saida = fopen($arquivo, 'w');
    $registrar = function (string $acao, array $r, bool $ok) use ($saida): void {
        fwrite($saida, json_encode([round(microtime(true), 3), $acao, $r['status'], round($r['ms'], 2), $ok ? 1 : 0, $r['erro'] ?? ($ok ? null : 'resposta')])."\n");
        fflush($saida);
    };
    $esperar = function () use ($pensar, $fim): void {
        $s = $pensar[0] + mt_rand() / mt_getrandmax() * ($pensar[1] - $pensar[0]);
        $s = min($s, max(0, $fim - microtime(true)));
        if ($s > 0) usleep((int) ($s * 1e6));
    };
    $get = function (HttpCliente $c, string $rota, string $acao) use ($registrar): array {
        $r = $c->request($rota);
        $ok = $r['erro'] === null && $r['status'] === 200 && !str_contains($r['body'], 'Banco de dados indisponível');
        $registrar($acao, $r, $ok);
        return $r;
    };
    $post = function (HttpCliente $c, string $rota, array $dados, string $acao) use ($registrar): void {
        $r = $c->request($rota, $dados);
        $destino = implode('', $r['headers']['location'] ?? []);
        $ok = $r['erro'] === null && $r['status'] === 303 && !str_contains($destino, 'route=login');
        $registrar($acao, $r, $ok);
    };

    if ($comeco > microtime(true)) usleep((int) (($comeco - microtime(true)) * 1e6));
    $c = new HttpCliente('127.0.0.1', $porta, $timeout);

    while (microtime(true) < $fim) {
        // Login: página + envio do formulário (bcrypt no servidor).
        $pagina = $c->request('login');
        $token = $pagina['erro'] === null ? HttpCliente::campo($pagina['body'], 'csrf_token') : null;
        $registrar('login (página)', $pagina, $token !== null);
        if ($token === null) { $esperar(); continue; }
        $r = $c->request('entrar', ['csrf_token' => $token, 'email' => $email, 'senha' => AmbienteHttp::SENHA]);
        $ok = $r['status'] === 303 && str_contains(implode('', $r['headers']['location'] ?? []), 'route=home');
        $registrar('login (envio)', $r, $ok);
        if ($ok) break;
        $esperar();
    }

    while (microtime(true) < $fim) {
        $idoso = mt_rand(1, $idosos);
        $material = mt_rand(1, $materiais);
        $acoes = match ($perfil) {
            'admin' => [
                fn() => $get($c, 'home', 'home'),
                fn() => $get($c, 'idosos', 'idosos (lista)'),
                fn() => $get($c, 'idosos&busca='.urlencode('Residente Teste 0'.mt_rand(0, 2)), 'idosos (busca)'),
                fn() => $get($c, 'idosos-ficha&id='.$idoso, 'ficha do idoso'),
                fn() => $get($c, 'usuarios', 'usuários'),
                fn() => $get($c, 'compras', 'compras'),
            ],
            'saude' => [
                fn() => $get($c, 'home', 'home'),
                fn() => $get($c, 'idosos&busca='.sprintf('RES-%04d', $idoso), 'idosos (busca)'),
                function () use ($c, $get, $post, $idoso, $esperar) {
                    $r = $get($c, 'idosos-saude&id='.$idoso, 'prontuário');
                    $token = HttpCliente::tokenFormulario($r['body']);
                    if ($r['status'] !== 200 || $token === null) return;
                    $esperar();
                    $post($c, 'idosos-saude-registrar&id='.$idoso, ['csrf_token' => $token, 'descricao' => 'Registro de carga fictício', 'visivel_para_familiar' => 0], 'registrar saúde (POST)');
                },
                fn() => $get($c, 'almoxarifado-uso', 'consumo (form)'),
            ],
            default => [
                fn() => $get($c, 'home', 'home'),
                fn() => $get($c, 'almoxarifado', 'almoxarifado'),
                function () use ($c, $get, $post, $material, $esperar) {
                    $r = $get($c, 'almoxarifado-entrada&id='.$material, 'entrada (form)');
                    $token = HttpCliente::tokenFormulario($r['body']);
                    if ($r['status'] !== 200 || $token === null) return;
                    $esperar();
                    $post($c, 'almoxarifado-registrar-entrada&id='.$material, ['csrf_token' => $token, 'quantidade' => '1', 'origem' => 'SUS'], 'entrada estoque (POST)');
                },
                fn() => $get($c, 'compras', 'compras'),
            ],
        };
        foreach ($acoes as $acao) {
            if (microtime(true) >= $fim) break;
            $acao();
            $esperar();
        }
    }
    fclose($saida);
}

function percentil(array $ordenados, float $p): float
{
    if ($ordenados === []) return 0.0;
    $indice = (int) ceil($p / 100 * count($ordenados)) - 1;
    return $ordenados[max(0, min(count($ordenados) - 1, $indice))];
}

function estatisticas(array $tempos): array
{
    sort($tempos);
    $n = count($tempos);
    return [
        'media' => $n ? round(array_sum($tempos) / $n, 1) : 0,
        'p50' => round(percentil($tempos, 50), 1),
        'p90' => round(percentil($tempos, 90), 1),
        'p95' => round(percentil($tempos, 95), 1),
        'p99' => round(percentil($tempos, 99), 1),
        'max' => $n ? round(max($tempos), 1) : 0,
    ];
}

function agregar(array $etapa, array $linhas, float $janela, float $rampa, array $amostras): array
{
    global $limiteP95, $limiteErro;
    $tempos = $porAcao = $erros = $porSegundo = [];
    $ok = 0;
    $base = $linhas ? min(array_column($linhas, 0)) : 0;
    foreach ($linhas as [$t, $acao, $status, $ms, $sucesso, $erro]) {
        $tempos[] = $ms;
        $porAcao[$acao]['tempos'][] = $ms;
        $porAcao[$acao]['erros'] = ($porAcao[$acao]['erros'] ?? 0) + ($sucesso ? 0 : 1);
        $segundo = (int) floor($t - $base);
        $porSegundo[$segundo]['n'] = ($porSegundo[$segundo]['n'] ?? 0) + 1;
        $porSegundo[$segundo]['erros'] = ($porSegundo[$segundo]['erros'] ?? 0) + ($sucesso ? 0 : 1);
        $porSegundo[$segundo]['tempos'][] = $ms;
        if ($sucesso) $ok++;
        else {
            $tipo = $erro === 'resposta' ? 'HTTP '.$status : $erro;
            $erros[$tipo] = ($erros[$tipo] ?? 0) + 1;
        }
    }
    $total = count($linhas);
    $acoes = [];
    ksort($porAcao);
    foreach ($porAcao as $nome => $d) {
        $acoes[$nome] = ['requisicoes' => count($d['tempos']), 'erros' => $d['erros']] + estatisticas($d['tempos']);
    }
    ksort($porSegundo);
    $linhaTempo = [];
    foreach ($porSegundo as $s => $d) {
        sort($d['tempos']);
        $linhaTempo[] = ['s' => $s, 'requisicoes' => $d['n'], 'erros' => $d['erros'], 'p95' => round(percentil($d['tempos'], 95), 1)];
    }
    $taxaErro = $total > 0 ? round(($total - $ok) / $total, 4) : 1.0;
    $stats = estatisticas($tempos);
    $cpus = array_column($amostras, 'cpu_pct');
    return [
        'tipo' => $etapa['tipo'],
        'vus' => $etapa['vus'],
        'duracao_s' => $etapa['duracao'],
        'rampa_s' => round($rampa, 1),
        'requisicoes' => $total,
        'sucesso' => $ok,
        'erros' => $total - $ok,
        'taxa_erro' => $taxaErro,
        'vazao_rps' => round($total / $janela, 2),
        'vazao_ok_rps' => round($ok / $janela, 2),
        'latencia_ms' => $stats,
        'tipos_erro' => $erros,
        'acoes' => $acoes,
        'servidor' => [
            'cpu_media_pct' => $cpus ? round(array_sum($cpus) / count($cpus), 1) : null,
            'cpu_max_pct' => $cpus ? max($cpus) : null,
            'rss_max_mb' => $amostras ? max(array_column($amostras, 'rss_mb')) : null,
            'load1_max' => $amostras ? max(array_column($amostras, 'load1')) : null,
        ],
        'linha_tempo' => $linhaTempo,
        'aprovada' => $total > 0 && $stats['p95'] <= $limiteP95 && $taxaErro < $limiteErro,
    ];
}

function imprimirEtapa(array $e): void
{
    echo sprintf(
        "%s\n   %d req | %.1f req/s | média %.0f ms | p95 %.0f ms | p99 %.0f ms | erros %.2f%%%s | CPU servidor %s%%\n",
        $e['aprovada'] ? 'dentro dos critérios' : 'FORA dos critérios',
        $e['requisicoes'], $e['vazao_rps'], $e['latencia_ms']['media'], $e['latencia_ms']['p95'], $e['latencia_ms']['p99'],
        $e['taxa_erro'] * 100,
        $e['tipos_erro'] ? ' ('.implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($e['tipos_erro']), $e['tipos_erro'])).')' : '',
        $e['servidor']['cpu_media_pct'] ?? '-'
    );
}

function analisar(array $etapas, float $limiteP95, float $limiteErro): array
{
    $degradacao = $ruptura = null;
    $maxVazao = 0;
    $vusMaxVazao = null;
    foreach ($etapas as $e) {
        if ($e['tipo'] === 'recuperacao') continue;
        if ($degradacao === null && !$e['aprovada']) $degradacao = $e['vus'];
        if ($ruptura === null && $e['taxa_erro'] >= 0.5) $ruptura = $e['vus'];
        if ($e['vazao_ok_rps'] > $maxVazao) { $maxVazao = $e['vazao_ok_rps']; $vusMaxVazao = $e['vus']; }
    }
    $rec = end($etapas);
    return [
        'ponto_degradacao_vus' => $degradacao,
        'ponto_ruptura_vus' => $ruptura,
        'vazao_maxima_ok_rps' => $maxVazao,
        'vus_na_vazao_maxima' => $vusMaxVazao,
        'recuperou' => $rec['tipo'] === 'recuperacao' && $rec['aprovada'],
    ];
}

function contagens(PDO $pdo, string $base): array
{
    $pdo->exec('USE `'.$base.'`');
    return [
        'registros_saude' => (int) $pdo->query('SELECT COUNT(*) FROM registros_saude')->fetchColumn(),
        'entradas' => (int) $pdo->query("SELECT COUNT(*) FROM movimentacoes_estoque WHERE tipo='Entrada'")->fetchColumn(),
    ];
}

function integridade(PDO $pdo, array $antes, array $confirmados): array
{
    $postSaude = $confirmados['registrar saúde (POST)'] ?? 0;
    $postEntrada = $confirmados['entrada estoque (POST)'] ?? 0;
    $depois = [
        'registros_saude' => (int) $pdo->query('SELECT COUNT(*) FROM registros_saude')->fetchColumn(),
        'entradas' => (int) $pdo->query("SELECT COUNT(*) FROM movimentacoes_estoque WHERE tipo='Entrada'")->fetchColumn(),
    ];
    $divergentes = (int) $pdo->query(
        "SELECT COUNT(*) FROM estoques e JOIN (
            SELECT material_id, SUM(CASE WHEN tipo='Entrada' THEN quantidade ELSE -quantidade END) saldo
            FROM movimentacoes_estoque GROUP BY material_id
         ) m ON m.material_id=e.material_id WHERE ABS(e.quantidade_atual - m.saldo) > 0.0005"
    )->fetchColumn();
    $novosSaude = $depois['registros_saude'] - $antes['registros_saude'];
    $novasEntradas = $depois['entradas'] - $antes['entradas'];
    // Um POST pode ser gravado e a resposta se perder (tempo limite do cliente ou usuário
    // encerrado no meio da requisição); por isso o banco pode ter mais linhas que respostas
    // confirmadas, mas nunca menos.
    $ok = $divergentes === 0 && $novosSaude >= $postSaude && $novasEntradas >= $postEntrada;
    return [
        'resultado' => $ok ? 'Passou' : 'Falhou',
        'posts_saude_confirmados' => $postSaude, 'registros_saude_gravados' => $novosSaude,
        'posts_entrada_confirmados' => $postEntrada, 'entradas_gravadas' => $novasEntradas,
        'saldos_divergentes' => $divergentes,
        'detalhe' => "$novosSaude registros de saúde para $postSaude confirmados; $novasEntradas entradas para $postEntrada confirmadas; $divergentes saldos divergentes",
    ];
}

function cpuServidor(array $pids): int
{
    $total = 0;
    foreach ($pids as $pid) {
        $stat = @file_get_contents("/proc/$pid/stat");
        if ($stat === false) continue;
        $campos = explode(' ', substr($stat, strrpos($stat, ')') + 2));
        $total += (int) $campos[11] + (int) $campos[12];
    }
    return $total;
}

function rssServidor(array $pids): int
{
    $total = 0;
    foreach ($pids as $pid) {
        $statm = @file_get_contents("/proc/$pid/statm");
        if ($statm !== false) $total += (int) explode(' ', $statm)[1] * 4096;
    }
    return $total;
}

function ambiente(string $versaoMysql, bool $local): array
{
    $cpu = '';
    if (preg_match('/model name\s*:\s*(.+)/', (string) @file_get_contents('/proc/cpuinfo'), $m)) $cpu = trim($m[1]);
    $mem = preg_match('/MemTotal:\s+(\d+)/', (string) @file_get_contents('/proc/meminfo'), $m) ? round($m[1] / 1048576, 1) : null;
    return [
        'php' => PHP_VERSION,
        'so' => php_uname('s').' '.php_uname('r').' '.php_uname('m'),
        'cpu' => $cpu,
        'cpus_logicas' => (int) trim((string) shell_exec('nproc')),
        'memoria_gib' => $mem,
        'mysql' => $versaoMysql.($local ? ' (local, mesma máquina)' : ' (remoto)'),
        'servidor_http' => 'PHP embutido (php -S) com router.php, mesmo comando do Railway',
        'gerador_carga' => 'Processos PHP (pcntl) na mesma máquina do servidor',
    ];
}

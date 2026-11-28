<?php
/** Relatórios Markdown e HTML (gráficos SVG embutidos) do teste de carga/estresse. */
final class RelatorioCarga
{
    private const NOMES_TIPO = ['carga' => 'Carga', 'estresse' => 'Estresse', 'recuperacao' => 'Recuperação'];

    public static function nomeConfig(int $workers): string
    {
        return $workers > 1 ? "$workers workers" : '1 processo (Railway)';
    }

    public static function markdown(array $r): string
    {
        $p = $r['parametros'];
        $md = "# Relatório de carga e estresse - AsiloSoft\n\n";
        $md .= 'Gerado em '.date('d/m/Y H:i:s', strtotime($r['gerado_em'])).' por `php tests/carga/CargaEstresse.php`.'.($r['interrompido'] ? ' **Execução interrompida pelo usuário.**' : '')."\n\n";
        $md .= "## Ambiente\n\n";
        foreach ($r['ambiente'] as $chave => $valor) $md .= '- '.self::rotulo($chave).': '.$valor."\n";
        $md .= "\n## Critérios\n\n";
        $md .= "- Carga (até {$p['limite_carga_vus']} usuários simultâneos): p95 até ".self::ms($p['limite_p95_ms'])." (RNF05) e erros abaixo de ".($p['limite_erro'] * 100)."% (RNF06).\n";
        $md .= "- Estresse (acima de {$p['limite_carga_vus']}): identificar o ponto de degradação (primeira etapa fora dos critérios) e o de ruptura (50% ou mais de erros).\n";
        $md .= "- Recuperação: após o estresse, voltar à menor carga e cumprir de novo os critérios.\n";
        $md .= "- Integridade: nenhum POST confirmado pode faltar no banco e os saldos de estoque devem bater com as movimentações.\n";
        $md .= "- Cada etapa dura {$p['duracao_etapa_s']} s mais rampa de entrada; cada usuário espera {$p['tempo_leitura_s'][0]} a {$p['tempo_leitura_s'][1]} s entre ações; tempo limite do cliente {$p['timeout_cliente_s']} s.\n";
        $md .= "- Massa fictícia: {$p['massa']['idosos']} idosos, {$p['massa']['registros_saude']} registros de saúde, {$p['massa']['materiais']} materiais, {$p['massa']['usuarios_por_perfil']} usuários por perfil. Mistura: ".implode(', ', array_map(fn($k, $v) => "$k $v", array_keys($p['mistura_perfis']), $p['mistura_perfis'])).".\n";

        foreach ($r['execucoes'] as $e) {
            $a = $e['analise'];
            $md .= "\n## Servidor com ".self::nomeConfig($e['workers'])."\n\n";
            $md .= "| Etapa | Usuários | Requisições | Vazão (req/s) | Média | p50 | p95 | p99 | Máx. | Erros | CPU servidor | Resultado |\n";
            $md .= "| --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | --- |\n";
            foreach ($e['etapas'] as $s) {
                $l = $s['latencia_ms'];
                $md .= sprintf("| %s | %d | %d | %s | %s | %s | %s | %s | %s | %s | %s | %s |\n",
                    self::NOMES_TIPO[$s['tipo']], $s['vus'], $s['requisicoes'], self::num($s['vazao_rps'], 1),
                    self::ms($l['media']), self::ms($l['p50']), self::ms($l['p95']), self::ms($l['p99']), self::ms($l['max']),
                    self::pct($s['taxa_erro']).self::tiposErro($s['tipos_erro']),
                    $s['servidor']['cpu_media_pct'] === null ? '-' : self::num($s['servidor']['cpu_media_pct'], 0).'%',
                    $s['aprovada'] ? 'Passou' : 'Falhou');
            }
            $md .= "\n- Ponto de degradação: ".($a['ponto_degradacao_vus'] ? $a['ponto_degradacao_vus'].' usuários simultâneos' : 'não atingido nas etapas executadas').".\n";
            $md .= "- Ponto de ruptura: ".($a['ponto_ruptura_vus'] ? $a['ponto_ruptura_vus'].' usuários simultâneos' : 'não atingido').".\n";
            $md .= '- Maior vazão com sucesso: '.self::num($a['vazao_maxima_ok_rps'], 1).' req/s com '.$a['vus_na_vazao_maxima']." usuários.\n";
            $md .= '- Recuperação após estresse: '.($a['recuperou'] ? 'sim, voltou aos critérios' : 'não voltou aos critérios').".\n";
            $md .= '- Integridade do banco: **'.$e['integridade']['resultado'].'** - '.$e['integridade']['detalhe'].".\n";

            $pior = self::etapaMaisPesadaAprovadaOuUltima($e['etapas']);
            if ($pior) {
                $md .= "\n### Tempo por ação na etapa de {$pior['vus']} usuários\n\n| Ação | Requisições | Erros | Média | p95 | Máx. |\n| --- | ---: | ---: | ---: | ---: | ---: |\n";
                foreach ($pior['acoes'] as $nome => $d) {
                    $md .= sprintf("| %s | %d | %d | %s | %s | %s |\n", $nome, $d['requisicoes'], $d['erros'], self::ms($d['media']), self::ms($d['p95']), self::ms($d['max']));
                }
            }
        }
        $md .= "\n## Arquivos\n\n- `resultado.json`: dados completos, inclusive linha do tempo por segundo e amostras de CPU/memória.\n- `relatorio.html`: gráficos de p95, vazão e erros por quantidade de usuários.\n- `servidor_w*.log`: log do servidor PHP de cada configuração.\n";
        return $md;
    }

    public static function html(array $r): string
    {
        $p = $r['parametros'];
        $cores = ['var(--series-1)', 'var(--series-2)', 'var(--series-3)'];
        $series = ['p95' => [], 'vazao' => [], 'erro' => []];
        foreach ($r['execucoes'] as $i => $e) {
            $pontos = array_values(array_filter($e['etapas'], fn($s) => $s['tipo'] !== 'recuperacao'));
            $base = ['nome' => self::nomeConfig($e['workers']), 'cor' => $cores[$i] ?? $cores[0]];
            $series['p95'][] = $base + ['pontos' => array_map(fn($s) => [$s['vus'], $s['latencia_ms']['p95']], $pontos)];
            $series['vazao'][] = $base + ['pontos' => array_map(fn($s) => [$s['vus'], $s['vazao_ok_rps']], $pontos)];
            $series['erro'][] = $base + ['pontos' => array_map(fn($s) => [$s['vus'], $s['taxa_erro'] * 100], $pontos)];
        }
        $graficos = self::svg('p95 da latência por usuários simultâneos', 'ms', $series['p95'], fn($v) => self::ms($v), [$p['limite_p95_ms'], 'Limite RNF05: 2 s'])
            .self::svg('Vazão com sucesso por usuários simultâneos', 'req/s', $series['vazao'], fn($v) => self::num($v, 1).' req/s')
            .self::svg('Taxa de erro por usuários simultâneos', '%', $series['erro'], fn($v) => self::num($v, 1).'%', [$p['limite_erro'] * 100, 'Limite RNF06: 1%']);

        $tiles = '';
        foreach ($r['execucoes'] as $e) {
            $a = $e['analise'];
            $tiles .= '<div class="tile"><p class="rot">'.self::e(self::nomeConfig($e['workers'])).'</p>'
                .'<p class="valor">'.self::e(self::num($a['vazao_maxima_ok_rps'], 1)).' <span>req/s</span></p>'
                .'<p class="sub">maior vazão, com '.(int) $a['vus_na_vazao_maxima'].' usuários</p>'
                .'<p class="sub">Degradação: '.self::e($a['ponto_degradacao_vus'] ? $a['ponto_degradacao_vus'].' usuários' : 'não atingida').'</p>'
                .'<p class="sub">Integridade: '.self::e($e['integridade']['resultado']).' · Recuperação: '.($a['recuperou'] ? 'sim' : 'não').'</p></div>';
        }

        $tabelas = '';
        foreach ($r['execucoes'] as $e) {
            $tabelas .= '<h2>Servidor com '.self::e(self::nomeConfig($e['workers'])).'</h2><div class="rolagem"><table><thead><tr><th>Etapa</th><th>Usuários</th><th>Requisições</th><th>Vazão</th><th>Média</th><th>p95</th><th>p99</th><th>Erros</th><th>CPU</th><th>Resultado</th></tr></thead><tbody>';
            foreach ($e['etapas'] as $s) {
                $l = $s['latencia_ms'];
                $tabelas .= '<tr><td>'.self::NOMES_TIPO[$s['tipo']].'</td><td>'.$s['vus'].'</td><td>'.$s['requisicoes'].'</td><td>'.self::num($s['vazao_rps'], 1).' req/s</td><td>'
                    .self::ms($l['media']).'</td><td>'.self::ms($l['p95']).'</td><td>'.self::ms($l['p99']).'</td><td>'.self::e(self::pct($s['taxa_erro']).self::tiposErro($s['tipos_erro'])).'</td><td>'
                    .($s['servidor']['cpu_media_pct'] === null ? '-' : self::num($s['servidor']['cpu_media_pct'], 0).'%').'</td><td>'
                    .($s['aprovada'] ? '<span class="st ok">✓ Passou</span>' : '<span class="st falha">✕ Falhou</span>').'</td></tr>';
            }
            $tabelas .= '</tbody></table></div><p class="nota">Integridade do banco: <strong>'.self::e($e['integridade']['resultado']).'</strong> - '.self::e($e['integridade']['detalhe']).'.</p>';
        }

        $amb = '';
        foreach ($r['ambiente'] as $chave => $valor) $amb .= '<dt>'.self::e(self::rotulo($chave)).'</dt><dd>'.self::e((string) $valor).'</dd>';
        $data = date('d/m/Y H:i', strtotime($r['gerado_em']));

        return <<<HTML
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Carga e Estresse</title>
<style>
:root{color-scheme:light;--surface-1:#ffffff;--surface-2:#f6f6f6;--borda:#e2e2e2;--grade:#ececec;--text-primary:#191919;--text-secondary:#666666;--series-1:#257de0;--series-2:#f47230;--series-3:#00b894;--ref:#8a8a8a;--good:#0a7d1d;--critical:#c42f30}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){color-scheme:dark;--surface-1:#191919;--surface-2:#222222;--borda:#333333;--grade:#2a2a2a;--text-primary:#efefef;--text-secondary:#9c9c9c;--series-1:#338ced;--series-2:#e76624;--series-3:#00a888;--ref:#8a8a8a;--good:#0aac23;--critical:#eb6263}}
:root[data-theme="dark"]{color-scheme:dark;--surface-1:#191919;--surface-2:#222222;--borda:#333333;--grade:#2a2a2a;--text-primary:#efefef;--text-secondary:#9c9c9c;--series-1:#338ced;--series-2:#e76624;--series-3:#00a888;--ref:#8a8a8a;--good:#0aac23;--critical:#eb6263}
*{box-sizing:border-box}body{margin:0;background:var(--surface-1);color:var(--text-primary);font:15px/1.5 system-ui,-apple-system,"Segoe UI",sans-serif}
main{max-width:1040px;margin:0 auto;padding:24px 16px 48px}h1{font-size:1.6rem;margin:0 0 4px}h2{font-size:1.15rem;margin:32px 0 12px}
.sub,.nota{color:var(--text-secondary);margin:2px 0}.tiles{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin:20px 0}
.tile{background:var(--surface-2);border:1px solid var(--borda);border-radius:10px;padding:14px 16px}.rot{margin:0;color:var(--text-secondary);font-size:.85rem}
.valor{font-size:1.8rem;font-weight:700;margin:2px 0}.valor span{font-size:.9rem;font-weight:500;color:var(--text-secondary)}
.graficos{display:grid;grid-template-columns:1fr;gap:16px}figure{margin:0;background:var(--surface-2);border:1px solid var(--borda);border-radius:10px;padding:12px;position:relative}
figcaption{font-weight:600;margin-bottom:4px}.legenda{display:flex;gap:14px;flex-wrap:wrap;font-size:.85rem;color:var(--text-secondary);margin-bottom:4px}
.legenda i{display:inline-block;width:14px;height:3px;border-radius:2px;vertical-align:middle;margin-right:6px}svg{width:100%;height:auto;display:block;overflow:visible}
svg text{fill:var(--text-secondary);font-size:12px}.eixo{stroke:var(--borda)}.grade{stroke:var(--grade)}.ref{stroke:var(--ref);stroke-dasharray:4 4}
.alvo{fill:transparent;cursor:crosshair}.dica{position:absolute;pointer-events:none;background:var(--surface-1);border:1px solid var(--borda);border-radius:6px;padding:6px 8px;font-size:.8rem;box-shadow:0 2px 8px rgba(0,0,0,.15);display:none;white-space:nowrap;z-index:2}
.rolagem{overflow-x:auto}table{border-collapse:collapse;width:100%;font-size:.88rem}th,td{padding:6px 8px;border-bottom:1px solid var(--borda);text-align:right}th:first-child,td:first-child{text-align:left}
th{color:var(--text-secondary);font-weight:600}.st{font-weight:600;white-space:nowrap}.st.ok{color:var(--good)}.st.falha{color:var(--critical)}
dl{display:grid;grid-template-columns:max-content 1fr;gap:4px 16px;margin:0}dt{color:var(--text-secondary)}dd{margin:0}
</style>
</head>
<body>
<main>
<h1>Carga e estresse do AsiloSoft</h1>
<p class="sub">Execução de {$data} · etapas de {$p['duracao_etapa_s']} s · usuários esperam {$p['tempo_leitura_s'][0]} a {$p['tempo_leitura_s'][1]} s entre ações · critérios: p95 até 2 s (RNF05) e erros abaixo de 1% (RNF06) em até {$p['limite_carga_vus']} usuários</p>
<div class="tiles">{$tiles}</div>
<div class="graficos">{$graficos}</div>
{$tabelas}
<h2>Ambiente</h2>
<dl>{$amb}</dl>
</main>
<script>
document.querySelectorAll('figure').forEach(function (fig) {
  var dica = fig.querySelector('.dica');
  fig.querySelectorAll('.alvo').forEach(function (alvo) {
    alvo.addEventListener('mouseenter', function () { dica.textContent = alvo.dataset.dica; dica.style.display = 'block'; });
    alvo.addEventListener('mousemove', function (ev) {
      var r = fig.getBoundingClientRect(); var x = ev.clientX - r.left + 12;
      if (x + dica.offsetWidth > r.width) x = ev.clientX - r.left - dica.offsetWidth - 12;
      dica.style.left = x + 'px'; dica.style.top = (ev.clientY - r.top - 36) + 'px';
    });
    alvo.addEventListener('mouseleave', function () { dica.style.display = 'none'; });
  });
});
</script>
</body>
</html>
HTML;
    }

    /** Gráfico de linhas com eixo y único, legenda, rótulo direto no fim de cada série e dica ao passar o mouse. */
    private static function svg(string $titulo, string $unidade, array $series, callable $formatar, ?array $referencia = null): string
    {
        $L = 60; $R = 150; $T = 12; $B = 40; $W = 900; $H = 270;
        $xs = $ys = [];
        foreach ($series as $s) foreach ($s['pontos'] as [$x, $y]) { $xs[] = $x; $ys[] = $y; }
        if ($xs === []) return '';
        if ($referencia) $ys[] = $referencia[0];
        $xMax = max($xs); $xMin = 0;
        $passo = self::teto((max($ys) ?: 1) / 4);
        $yMax = $passo * max(1, (int) ceil(max($ys) / $passo - 1e-9));
        $px = fn($x) => $L + ($x - $xMin) / max(1, $xMax - $xMin) * ($W - $L - $R);
        $py = fn($y) => $H - $B - $y / $yMax * ($H - $T - $B);

        $svg = '<svg viewBox="0 0 '.$W.' '.$H.'" role="img" aria-label="'.self::e($titulo).'">';
        for ($v = 0.0; $v <= $yMax + 1e-9; $v += $passo) {
            $y = round($py($v), 1);
            $svg .= '<line class="grade" x1="'.$L.'" x2="'.($W - $R).'" y1="'.$y.'" y2="'.$y.'"/>'
                .'<text x="'.($L - 6).'" y="'.($y + 4).'" text-anchor="end">'.self::e(self::curto($v, $unidade)).'</text>';
        }
        $ticks = array_values(array_unique($xs)); sort($ticks);
        foreach ($ticks as $i => $x) {
            if ($i > 0 && $px($x) - $px($ticks[$i - 1]) < 26) continue;
            $svg .= '<text x="'.round($px($x), 1).'" y="'.($H - $B + 16).'" text-anchor="middle">'.$x.'</text>';
        }
        $svg .= '<line class="eixo" x1="'.$L.'" x2="'.($W - $R).'" y1="'.($H - $B).'" y2="'.($H - $B).'"/>';
        $svg .= '<text x="'.(($L + $W - $R) / 2).'" y="'.($H - 4).'" text-anchor="middle">usuários simultâneos</text>';
        if ($referencia) {
            $y = round($py($referencia[0]), 1);
            $svg .= '<line class="ref" x1="'.$L.'" x2="'.($W - $R).'" y1="'.$y.'" y2="'.$y.'"/><text x="'.($W - $R + 6).'" y="'.($y + 4).'">'.self::e($referencia[1]).'</text>';
        }
        $finais = [];
        foreach ($series as $s) {
            $d = '';
            foreach ($s['pontos'] as $i => [$x, $y]) $d .= ($i ? 'L' : 'M').round($px($x), 1).' '.round($py($y), 1);
            $svg .= '<path d="'.$d.'" fill="none" stroke="'.$s['cor'].'" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>';
            foreach ($s['pontos'] as [$x, $y]) {
                $svg .= '<circle cx="'.round($px($x), 1).'" cy="'.round($py($y), 1).'" r="4" fill="'.$s['cor'].'" stroke="var(--surface-2)" stroke-width="2"/>';
            }
            [$xf, $yf] = end($s['pontos']);
            $finais[] = [round($py($yf), 1), round($px($xf), 1), $s['nome']];
        }
        usort($finais, fn($a, $b) => $a[0] <=> $b[0]);
        $ultimoY = -100;
        foreach ($finais as [$y, $x, $nome]) {
            $y = max($y, $ultimoY + 13); $ultimoY = $y;
            $svg .= '<text x="'.($x + 8).'" y="'.($y + 4).'" style="fill:var(--text-primary)">'.self::e($nome).'</text>';
        }
        // Alvos de hover maiores que o ponto.
        foreach ($series as $s) {
            foreach ($s['pontos'] as [$x, $y]) {
                $svg .= '<circle class="alvo" cx="'.round($px($x), 1).'" cy="'.round($py($y), 1).'" r="12" data-dica="'.self::e($s['nome'].' · '.$x.' usuários: '.$formatar($y)).'"/>';
            }
        }
        $svg .= '</svg>';
        $legenda = '';
        if (count($series) > 1) {
            $legenda = '<div class="legenda">';
            foreach ($series as $s) $legenda .= '<span><i style="background:'.$s['cor'].'"></i>'.self::e($s['nome']).'</span>';
            $legenda .= '</div>';
        }
        return '<figure><figcaption>'.self::e($titulo).'</figcaption>'.$legenda.$svg.'<div class="dica"></div></figure>';
    }

    private static function rotulo(string $chave): string
    {
        return ['php' => 'PHP', 'so' => 'Sistema operacional', 'cpu' => 'Processador', 'cpus_logicas' => 'CPUs lógicas',
            'memoria_gib' => 'Memória (GiB)', 'mysql' => 'Banco de dados', 'servidor_http' => 'Servidor HTTP',
            'gerador_carga' => 'Gerador de carga'][$chave] ?? $chave;
    }

    private static function etapaMaisPesadaAprovadaOuUltima(array $etapas): ?array
    {
        $escolhida = null;
        foreach ($etapas as $s) if ($s['tipo'] !== 'recuperacao' && ($escolhida === null || $s['vus'] > $escolhida['vus'])) $escolhida = $s;
        return $escolhida;
    }

    private static function teto(float $v): float
    {
        $ordem = 10 ** floor(log10($v));
        foreach ([1, 2, 2.5, 5, 10] as $m) if ($m * $ordem >= $v) return $m * $ordem;
        return 10 * $ordem;
    }

    private static function curto(float $v, string $unidade): string
    {
        if ($unidade === 'ms') return $v >= 1000 ? self::num($v / 1000, fmod($v, 1000) ? 1 : 0).' s' : self::num($v, 0).' ms';
        $casas = fmod($v, 1) == 0 ? 0 : (fmod($v * 10, 1) == 0 ? 1 : 2);
        return self::num($v, $casas).($unidade === '%' ? '%' : '');
    }

    private static function tiposErro(array $tipos): string
    {
        return $tipos ? ' ('.implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($tipos), $tipos)).')' : '';
    }

    public static function ms(float $v): string
    {
        return $v >= 1000 ? self::num($v / 1000, 2).' s' : self::num($v, 0).' ms';
    }

    private static function pct(float $v): string
    {
        return self::num($v * 100, 2).'%';
    }

    private static function num(float $v, int $casas): string
    {
        return number_format($v, $casas, ',', '.');
    }

    private static function e(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
    }
}

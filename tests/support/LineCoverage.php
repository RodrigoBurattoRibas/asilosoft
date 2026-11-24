<?php

/**
 * Coleta cobertura de linhas sem Composer ou framework de testes.
 * Escopo, cálculo, drivers e limitações: docs/testes/PHPDBG.md.
 */
final class LineCoverage
{
    private string $driver;

    public function __construct(private array $files, private string $root)
    {
        if (PHP_SAPI === 'phpdbg' && function_exists('phpdbg_start_oplog')) {
            $this->driver = 'phpdbg';
        } elseif (function_exists('xdebug_start_code_coverage')) {
            $this->driver = 'xdebug';
        } else {
            throw new RuntimeException('Cobertura indisponível. Execute bash tests/coverage.sh (phpdbg) ou use Xdebug em modo coverage.');
        }
        $this->files = array_map(static fn(string $file): string => realpath($file) ?: $file, $files);
        sort($this->files);
    }

    public function start(): void
    {
        if ($this->driver === 'phpdbg') {
            phpdbg_start_oplog();
        } else {
            xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
            if (!xdebug_code_coverage_started()) {
                throw new RuntimeException('Ative XDEBUG_MODE=coverage antes de iniciar o PHP.');
            }
        }
    }

    public function finish(string $out): array
    {
        if ($this->driver === 'phpdbg') {
            $executed = phpdbg_end_oplog();
            $executable = phpdbg_get_executable(['files' => $this->files]);
            if (!is_array($executed)) throw new RuntimeException('phpdbg não retornou a coleta.');
        } else {
            $raw = xdebug_get_code_coverage();
            xdebug_stop_code_coverage();
            $executable = $executed = [];
            foreach ($raw as $file => $lines) {
                $executable[$file] = array_filter($lines, static fn(int $hits): bool => $hits !== -2);
                $executed[$file] = array_filter($lines, static fn(int $hits): bool => $hits > 0);
            }
        }

        $total = $covered = 0;
        $details = [];
        foreach ($this->files as $file) {
            // Include every scoped file, even when none of its methods were called.
            if (!isset($executable[$file]) || $executable[$file] === []) {
                throw new RuntimeException('Sem linhas executáveis para '.$file.'. Carregue todos os arquivos do escopo antes de concluir a coleta.');
            }
            $lines = array_map('intval', array_keys($executable[$file]));
            sort($lines);
            $hits = array_values(array_filter($lines, static fn(int $line): bool => ($executed[$file][$line] ?? 0) > 0));
            $missed = array_values(array_diff($lines, $hits));
            $total += count($lines);
            $covered += count($hits);
            $details[substr($file, strlen($this->root) + 1)] = [
                'executaveis' => count($lines), 'cobertas' => count($hits),
                'percentual' => 100 * count($hits) / count($lines),
                'linhas_cobertas' => $hits, 'linhas_nao_cobertas' => $missed,
                'sha256' => hash_file('sha256', $file),
            ];
        }
        if ($total === 0) throw new RuntimeException('Escopo de cobertura vazio.');
        $report = [
            'executado_em' => (new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')))->format(DATE_ATOM),
            'driver' => $this->driver, 'php' => PHP_VERSION, 'sapi' => PHP_SAPI,
            'escopo' => 'Todos os src/modules/*/services/*.php; somente suíte unitária',
            'metrica' => 'Linhas executáveis reconhecidas pelo driver; não mede ramos ou caminhos',
            'meta_percentual' => 60, 'linhas' => $total, 'cobertas' => $covered,
            'percentual' => 100 * $covered / $total, 'arquivos' => $details,
        ];
        if (!is_dir($out) && !mkdir($out, 0775, true)) throw new RuntimeException('Não foi possível criar '.$out);
        $json = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (file_put_contents($out.'/cobertura.json', $json."\n") === false) throw new RuntimeException('Falha ao gravar JSON de cobertura.');
        $this->writeHtml($out.'/cobertura.html', $report);
        return $report;
    }

    private function writeHtml(string $path, array $report): void
    {
        $escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $html = '<!doctype html><html lang="pt-BR"><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Cobertura unitária AsiloSoft</title><style>body{font:16px system-ui;margin:32px auto;padding:0 20px;max-width:1150px;color:#172b36}table{border-collapse:collapse;width:100%}td,th{border:1px solid #ccd5da;padding:8px;text-align:left}.hit{background:#e0f3e5}.miss{background:#ffe2df}pre{overflow:auto;font-size:13px;line-height:1.6}pre span{display:block;min-width:max-content}a{color:#175f92}</style><h1>Cobertura unitária AsiloSoft</h1>';
        $html .= '<p>'.number_format($report['percentual'], 2, ',', '.').'% — '.$report['cobertas'].' / '.$report['linhas'].' linhas executáveis. Meta: 60%.</p>';
        $html .= '<p>Driver '.$escape($report['driver']).'; PHP '.$escape($report['php']).'; '.$escape($report['executado_em']).'.</p><p>'.$escape($report['escopo']).'. '.$escape($report['metrica']).'.</p><p>Verde: executada; vermelho: executável não coberta; sem cor: fora da contagem do driver.</p><table><tr><th>Serviço</th><th>Cobertas</th><th>Executáveis</th><th>Percentual</th></tr>';
        foreach ($report['arquivos'] as $file => $data) {
            $anchor = 'f'.hash('sha256', $file);
            $html .= '<tr><td><a href="#'.$anchor.'">'.$escape($file).'</a></td><td>'.$data['cobertas'].'</td><td>'.$data['executaveis'].'</td><td>'.number_format($data['percentual'], 2, ',', '.').'%</td></tr>';
        }
        $html .= '</table>';
        foreach ($report['arquivos'] as $file => $data) {
            $html .= '<h2 id="f'.hash('sha256', $file).'">'.$escape($file).'</h2><pre>';
            foreach (file($this->root.'/'.$file) as $index => $source) {
                $line = $index + 1;
                $class = in_array($line, $data['linhas_cobertas'], true) ? 'hit' : (in_array($line, $data['linhas_nao_cobertas'], true) ? 'miss' : '');
                $html .= '<span class="'.$class.'">'.str_pad((string)$line, 4, ' ', STR_PAD_LEFT).' '.$escape(rtrim($source, "\r\n")).'</span>';
            }
            $html .= '</pre>';
        }
        if (file_put_contents($path, $html.'</html>') === false) throw new RuntimeException('Falha ao gravar HTML de cobertura.');
    }
}

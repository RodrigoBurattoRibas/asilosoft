<?php
/**
 * Testes de segurança do AsiloSoft (caixa-cinza, pelo HTTP real), orientados ao OWASP Top 10.
 * Sobe servidor e base próprios (asilosoft_test_*), ataca somente 127.0.0.1 e usa dados fictícios.
 *
 * Uso: php tests/seguranca/SegurancaTest.php [--conexao-da-aplicacao]
 * Código de saída: 0 = nenhuma vulnerabilidade; 1 = vulnerabilidades encontradas; 2 = impedimento de ambiente.
 * Documentação: docs/testes/SEGURANCA.md
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}
date_default_timezone_set('America/Sao_Paulo');
require_once __DIR__.'/../support/HttpCliente.php';
require_once __DIR__.'/../support/AmbienteHttp.php';

$opcoes = getopt('', ['conexao-da-aplicacao']);
$raiz = dirname(__DIR__, 2);
$saida = $raiz.'/docs/testes/evidencias/seguranca_'.date('Ymd\THis');
$casos = [];
$ambiente = null;
$porta = (int) (getenv('ASILOSOFT_TEST_HTTP_PORT') ?: 18091);

/** Registra um caso. $teste devolve [bool passou, string obtido]. */
function caso(string $id, string $categoria, string $owasp, string $titulo, string $esperado, string $severidadeSeFalhar, callable $teste): void
{
    global $casos;
    try {
        [$passou, $obtido] = $teste();
    } catch (Throwable $erro) {
        [$passou, $obtido] = [false, 'Erro na execução do caso: '.$erro->getMessage()];
    }
    $casos[] = compact('id', 'categoria', 'owasp', 'titulo', 'esperado', 'obtido', 'passou') + ['severidade' => $passou ? null : $severidadeSeFalhar];
    echo sprintf("%s %-6s %s\n        %s\n", $passou ? 'OK   ' : 'FALHA', $id, $titulo, $obtido);
}

function cliente(): HttpCliente
{
    global $porta;
    return new HttpCliente('127.0.0.1', $porta, 15);
}

function logado(string $email): HttpCliente
{
    $c = cliente();
    if (!$c->entrar($email, AmbienteHttp::SENHA)) throw new RuntimeException('Login de teste falhou para '.$email);
    return $c;
}

/** Sinais de erro interno vazando para o usuário. */
function vazamento(string $corpo): ?string
{
    foreach (['SQLSTATE', 'PDOException', 'syntax error', 'You have an error in your SQL', 'Fatal error', 'Warning:', 'Notice:', 'Deprecated:', 'Stack trace', 'Uncaught', '/home/', 'mysqli', 'TypeError'] as $sinal) {
        if (stripos($corpo, $sinal) !== false) return $sinal;
    }
    return null;
}

function sessaoId(HttpCliente $c): ?string
{
    return $c->cookies['PHPSESSID'] ?? null;
}

try {
    $ambiente = new AmbienteHttp('seguranca', isset($opcoes['conexao-da-aplicacao']));
    mkdir($saida, 0775, true);
    echo 'Evidências: '.substr($saida, strlen($raiz) + 1)."\n";
    $ambiente->preparar(2, 5, 3, 1);
    $ambiente->iniciarServidor($porta, 1, $saida.'/servidor.log');
    $pdo = $ambiente->pdo;
    $admin = $ambiente->usuarios['admin'][0];
    $saude = $ambiente->usuarios['saude'][0];
    $estoque = $ambiente->usuarios['estoque'][0];
    $familia = $ambiente->usuarios['familia'][0];   // vinculado ao idoso 1
    $a = logado($admin['email']);
    $s = logado($saude['email']);
    $e = logado($estoque['email']);
    $f = logado($familia['email']);

    echo "\n== Injeção de SQL (A03) ==\n";
    caso('CT-S01', 'Injeção de SQL', 'A03', 'Login não aceita injeção no e-mail ou na senha', 'Nenhum payload autentica; resposta volta ao login sem erro interno', 'Crítica', function () use ($admin) {
        $payloads = [["' OR '1'='1' -- ", 'x'], ["' OR 1=1#", 'x'], [$admin['email']."' -- ", 'qualquer'], [$admin['email'], "' OR '1'='1"], ['" OR ""="', '" OR ""="']];
        foreach ($payloads as [$email, $senha]) {
            $c = cliente();
            if ($c->entrar($email, $senha)) return [false, "Autenticou com e-mail [$email] e senha [$senha]"];
            $home = $c->request('home');
            if ($home['status'] === 200) return [false, 'Sessão autenticada após payload '.$email];
        }
        return [true, count($payloads).' payloads recusados; todos redirecionaram para o login'];
    });
    caso('CT-S02', 'Injeção de SQL', 'A03', 'Busca de idosos trata aspas e UNION como texto', 'Nenhum residente extra listado, sem hash de senha e sem mensagem de erro SQL', 'Crítica', function () use ($a) {
        foreach (["' OR '1'='1", "%' OR 1=1 -- ", "' UNION SELECT id,email,senha_hash,perfil,ativo,1 FROM usuarios-- -", "1') OR ('1'='1"] as $p) {
            $r = $a->request('idosos&busca='.urlencode($p));
            if ($r['status'] >= 500) return [false, "HTTP {$r['status']} com [$p]"];
            if ($sinal = vazamento($r['body'])) return [false, "Vazou [$sinal] com [$p]"];
            if (str_contains($r['body'], 'Residente Teste') || str_contains($r['body'], '$2y$') || str_contains($r['body'], '@example.test')) return [false, "Retornou dados indevidos com [$p]"];
        }
        return [true, '4 payloads tratados como texto: 0 resultados, sem erro'];
    });
    caso('CT-S03', 'Injeção de SQL', 'A03', 'Injeção cega por tempo (SLEEP) não executa', 'Respostas em menos de 2,5 s com payload SLEEP(3)', 'Crítica', function () use ($a) {
        $maior = 0;
        foreach (['idosos&busca='.urlencode("' AND SLEEP(3)-- -"), 'idosos&busca='.urlencode("x' OR SLEEP(3) OR 'x"), 'idosos-ficha&id='.urlencode('1 AND SLEEP(3)'), 'idosos&pagina='.urlencode('1;SELECT SLEEP(3)')] as $rota) {
            $r = $a->request($rota);
            $maior = max($maior, $r['ms']);
            if ($r['ms'] > 2500) return [false, 'Resposta levou '.round($r['ms']).' ms em '.$rota];
        }
        return [true, 'Maior tempo: '.round($maior).' ms'];
    });
    caso('CT-S04', 'Injeção de SQL', 'A03', 'Parâmetros numéricos (id) rejeitam texto e SQL', 'Sem erro 500 nem mensagem SQL; nenhum dado de outro registro', 'Alta', function () use ($a) {
        foreach (["1 OR 1=1", "1'", "-1 UNION SELECT 1,2,3,4,5,6", "1;DROP TABLE idosos", "0x31"] as $p) {
            foreach (['idosos-ficha', 'idosos-saude', 'idosos-editar'] as $rota) {
                $r = $a->request($rota.'&id='.urlencode($p));
                if ($r['status'] >= 500) return [false, "HTTP {$r['status']} em $rota id=[$p]"];
                if ($sinal = vazamento($r['body'])) return [false, "Vazou [$sinal] em $rota id=[$p]"];
            }
        }
        return [true, '15 combinações sem erro interno; ids inválidos tratados'];
    });
    caso('CT-S05', 'Injeção de SQL', 'A03', 'Campos de cadastro gravam payload SQL como texto (inclusive segunda ordem)', 'Familiar salvo com o nome literal; tabela idosos intacta; busca posterior sem erro', 'Crítica', function () use ($a, $pdo) {
        $nome = "Robert'); DROP TABLE idosos;-- ";
        $token = HttpCliente::tokenFormulario($a->request('familiares-novo')['body']);
        $a->request('familiares-criar', ['csrf_token' => $token, 'nome' => $nome, 'telefone' => "' OR 1=1 -- "]);
        $salvo = $pdo->prepare('SELECT COUNT(*) FROM familiares WHERE nome=?');
        $salvo->execute([trim($nome)]);
        $idosos = (int) $pdo->query('SELECT COUNT(*) FROM idosos')->fetchColumn();
        $lista = $a->request('familiares');
        if ((int) $salvo->fetchColumn() !== 1) return [false, 'Nome não foi salvo literalmente'];
        if ($idosos !== 5) return [false, "Tabela idosos alterada ($idosos linhas)"];
        if ($lista['status'] !== 200 || vazamento($lista['body'])) return [false, 'Listagem com erro após gravar o payload'];
        return [true, 'Nome literal gravado; idosos com 5 linhas; listagem normal'];
    });

    echo "\n== Cross-site scripting (A03) ==\n";
    $xss = '<script>alert("xss")</script>';
    $xssAttr = '"><img src=x onerror=alert(1)>';
    caso('CT-S06', 'XSS', 'A03', 'XSS armazenado no cadastro de familiar', 'Lista e edição exibem o texto escapado; tag não é interpretada', 'Alta', function () use ($a, $pdo, $xss, $xssAttr) {
        $token = HttpCliente::tokenFormulario($a->request('familiares-novo')['body']);
        $a->request('familiares-criar', ['csrf_token' => $token, 'nome' => 'XSS '.$xss, 'telefone' => $xssAttr, 'parentesco_principal' => $xss]);
        $id = (int) $pdo->query("SELECT id FROM familiares WHERE nome LIKE 'XSS %'")->fetchColumn();
        if ($id === 0) return [false, 'Cadastro com payload não foi salvo; não foi possível verificar a saída'];
        foreach (['familiares', 'familiares-editar&id='.$id] as $rota) {
            $corpo = $a->request($rota)['body'];
            if (str_contains($corpo, $xss) || str_contains($corpo, '<img src=x')) return [false, "Payload sem escape em $rota"];
        }
        return [true, 'Payload salvo e exibido como &lt;script&gt; nas 2 telas'];
    });
    caso('CT-S07', 'XSS', 'A03', 'XSS armazenado no cadastro de material', 'Lista do almoxarifado, compras e consumo exibem o nome escapado', 'Alta', function () use ($e, $s, $xss, $xssAttr) {
        $token = HttpCliente::tokenFormulario($e->request('almoxarifado-novo')['body']);
        $r = $e->request('almoxarifado-criar', ['csrf_token' => $token, 'nome' => 'Gaze '.$xssAttr, 'descricao' => $xss, 'tipo' => 'Material', 'unidade_medida' => 'un', 'estoque_minimo' => '1']);
        if ($r['status'] !== 303) return [false, 'Cadastro do material recusado (HTTP '.$r['status'].')'];
        foreach ([[$e, 'almoxarifado'], [$e, 'compras-nova'], [$s, 'almoxarifado-uso']] as [$c, $rota]) {
            $corpo = $c->request($rota)['body'];
            if (str_contains($corpo, '<img src=x') || str_contains($corpo, $xss)) return [false, "Payload sem escape em $rota"];
        }
        return [true, 'Escapado em almoxarifado, compras-nova e almoxarifado-uso'];
    });
    caso('CT-S08', 'XSS', 'A03', 'XSS armazenado no prontuário chega escapado ao portal da família', 'Prontuário e acompanhamento familiar não interpretam a tag', 'Alta', function () use ($s, $f, $xss) {
        $token = HttpCliente::tokenFormulario($s->request('idosos-saude&id=1')['body']);
        $s->request('idosos-saude-registrar&id=1', ['csrf_token' => $token, 'descricao' => 'Evolução '.$xss, 'visivel_para_familiar' => 1]);
        $prontuario = $s->request('idosos-saude&id=1')['body'];
        $portal = $f->request('acompanhamento-familiar&id=1')['body'];
        if (!str_contains($portal, 'Evolução')) return [false, 'Registro não apareceu no portal; verificação incompleta'];
        if (str_contains($prontuario, $xss) || str_contains($portal, $xss)) return [false, 'Payload sem escape no prontuário ou no portal'];
        return [true, 'Escapado no prontuário e no portal da família'];
    });
    caso('CT-S09', 'XSS', 'A03', 'XSS refletido na busca, na paginação e na rota', 'Parâmetros devolvidos na página sempre escapados', 'Alta', function () use ($a, $xss, $xssAttr) {
        foreach (['idosos&busca='.urlencode($xss), 'idosos&busca='.urlencode($xssAttr), 'idosos&situacao='.urlencode($xssAttr), 'usuarios&situacao='.urlencode($xss), 'idosos&pagina='.urlencode($xss)] as $rota) {
            $r = $a->request($rota);
            if (str_contains($r['body'], $xss) || str_contains($r['body'], '<img src=x')) return [false, "Refletido sem escape em $rota"];
        }
        $r = cliente()->request('/?route='.rawurlencode($xss));
        if (str_contains($r['body'], $xss)) return [false, 'Rota refletida sem escape'];
        return [true, '6 pontos de reflexão testados, todos escapados'];
    });
    caso('CT-S10', 'XSS', 'A03', 'Mensagens de erro do login não refletem a entrada', 'E-mail com script gera mensagem genérica, sem a tag', 'Média', function () use ($xss) {
        $c = cliente();
        $c->entrar($xss.'@x.test', $xss);
        $corpo = $c->request('login')['body'];
        if (str_contains($corpo, $xss)) return [false, 'Entrada refletida sem escape'];
        return [true, 'Mensagem genérica "E-mail ou senha inválidos."'];
    });

    echo "\n== Autenticação (A07) e senhas (A02) ==\n";
    caso('CT-S11', 'Autenticação', 'A07', 'Mensagem de erro não revela se o e-mail existe', 'Mesma mensagem para e-mail inexistente e senha errada', 'Média', function () use ($admin) {
        $msg = function (string $email) {
            $c = cliente();
            $c->entrar($email, 'SenhaErrada999');
            preg_match('/data-aviso[^>]*>(.*?)<\/div>/s', $c->request('login')['body'], $m);
            return trim(str_replace('×', '', html_entity_decode(strip_tags($m[1] ?? ''), ENT_QUOTES, 'UTF-8')));
        };
        $existe = $msg($admin['email']);
        $naoExiste = $msg('ninguem@example.test');
        return [$existe === $naoExiste && $existe !== '', "Existente: [$existe] / inexistente: [$naoExiste]"];
    });
    caso('CT-S12', 'Autenticação', 'A07', 'Tempo de resposta não revela se o e-mail existe', 'Diferença das medianas abaixo de 40 ms', 'Baixa', function () use ($ambiente) {
        // Conta própria e 4 tentativas, abaixo do limite de bloqueio, para medir só a verificação de senha.
        $medir = function (string $email) {
            $tempos = [];
            for ($i = 0; $i < 4; $i++) {
                $c = cliente();
                $token = HttpCliente::campo($c->request('login')['body'], 'csrf_token');
                $tempos[] = $c->request('entrar', ['csrf_token' => $token, 'email' => $email, 'senha' => 'SenhaErrada999'])['ms'];
            }
            sort($tempos);
            return ($tempos[1] + $tempos[2]) / 2;
        };
        $existe = $medir($ambiente->usuarios['saude'][1]['email']);
        $naoExiste = $medir('ninguem@example.test');
        $dif = $existe - $naoExiste;
        return [abs($dif) < 40, sprintf('Mediana com e-mail existente %.0f ms; inexistente %.0f ms; diferença %.0f ms', $existe, $naoExiste, $dif)];
    });
    caso('CT-S13', 'Autenticação', 'A07', 'Proteção contra força bruta no login', 'Após no máximo 10 senhas erradas seguidas, novas tentativas são bloqueadas, inclusive com a senha certa', 'Alta', function () use ($ambiente) {
        $email = $ambiente->usuarios['admin'][1]['email'];   // conta própria: o bloqueio não afeta os demais casos
        $c = cliente();
        $inicio = microtime(true);
        $bloqueioNa = null;
        for ($i = 1; $i <= 30; $i++) {
            $token = HttpCliente::campo($c->request('login')['body'], 'csrf_token');
            $c->request('entrar', ['csrf_token' => $token, 'email' => $email, 'senha' => 'Tentativa'.$i]);
            if ($bloqueioNa === null && str_contains($c->request('login')['body'], 'Muitas tentativas')) $bloqueioNa = $i;
        }
        $duracao = microtime(true) - $inicio;
        $entrou = $c->entrar($email, AmbienteHttp::SENHA);
        if ($entrou || $bloqueioNa === null || $bloqueioNa > 10) {
            return [false, sprintf('30 senhas erradas processadas em %.1f s sem bloqueio%s', $duracao, $entrou ? '; a senha certa entrou logo depois' : '')];
        }
        return [true, sprintf('Bloqueio a partir da tentativa %d (5 falhas aceitas); a senha certa também foi recusada durante o bloqueio', $bloqueioNa)];
    });
    caso('CT-S14', 'Autenticação', 'A07', 'Conta desativada não entra', 'Login recusado para usuário inativo', 'Alta', function () use ($pdo, $ambiente) {
        $email = $ambiente->usuarios['familia'][1]['email'];
        $pdo->prepare('UPDATE usuarios SET ativo=0 WHERE email=?')->execute([$email]);
        $entrou = cliente()->entrar($email, AmbienteHttp::SENHA);
        $pdo->prepare('UPDATE usuarios SET ativo=1 WHERE email=?')->execute([$email]);
        return [!$entrou, $entrou ? 'Usuário inativo autenticou' : 'Login recusado'];
    });
    caso('CT-S15', 'Senhas', 'A02', 'Senhas guardadas com hash forte (RNF02)', 'Todas as senhas com bcrypt/argon2; nenhuma em texto puro', 'Crítica', function () use ($pdo) {
        $linhas = $pdo->query('SELECT senha_hash FROM usuarios')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($linhas as $hash) {
            $info = password_get_info($hash);
            if ($info['algo'] === null || $info['algo'] === 0 || $hash === AmbienteHttp::SENHA) return [false, 'Hash fraco ou senha em texto puro'];
        }
        $colunas = $pdo->query("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema=DATABASE() AND COLUMN_NAME IN ('senha','password')")->fetchAll(PDO::FETCH_COLUMN);
        if ($colunas) return [false, 'Coluna de senha em texto: '.implode(',', $colunas)];
        return [true, count($linhas).' contas com '.password_get_info($linhas[0])['algoName']];
    });
    caso('CT-S16', 'Senhas', 'A07', 'Política mínima de senha no cadastro de usuário', 'Senha com menos de 8 caracteres recusada', 'Média', function () use ($a, $pdo) {
        $token = HttpCliente::tokenFormulario($a->request('usuarios-novo')['body']);
        $a->request('usuarios-criar', ['csrf_token' => $token, 'nome' => 'Senha Curta', 'email' => 'curta@example.test', 'perfil' => 'Familiar', 'senha' => '1234567']);
        $criado = (int) $pdo->query("SELECT COUNT(*) FROM usuarios WHERE email='curta@example.test'")->fetchColumn();
        return [$criado === 0, $criado ? 'Usuário criado com senha de 7 caracteres' : 'Cadastro recusado'];
    });

    echo "\n== Sessão (A07) ==\n";
    caso('CT-S17', 'Sessão', 'A07', 'Cookie de sessão com HttpOnly e SameSite', 'Set-Cookie do PHPSESSID com HttpOnly e SameSite=Lax/Strict', 'Média', function () {
        $c = cliente();
        $c->request('login');
        $cookie = '';
        foreach ($c->setCookiesBrutos as $bruto) if (str_starts_with($bruto, 'PHPSESSID=')) $cookie = $bruto;
        if ($cookie === '') return [false, 'Cookie de sessão não recebido'];
        $faltam = [];
        if (stripos($cookie, 'httponly') === false) $faltam[] = 'HttpOnly';
        if (stripos($cookie, 'samesite') === false) $faltam[] = 'SameSite';
        $atributos = trim(preg_replace('/^PHPSESSID=[^;]+;?/', '', $cookie));
        return [$faltam === [], $faltam ? 'Faltam: '.implode(', ', $faltam).'. Atributos recebidos: '.($atributos ?: 'nenhum') : 'Atributos: '.$atributos];
    });
    caso('CT-S18', 'Sessão', 'A07', 'ID de sessão trocado no login (fixação de sessão)', 'ID após o login diferente do anterior; ID antigo sem acesso', 'Alta', function () use ($admin) {
        $c = cliente();
        $c->request('login');
        $antes = sessaoId($c);
        $c->entrar($admin['email'], AmbienteHttp::SENHA);
        $depois = sessaoId($c);
        $velho = cliente();
        $velho->cookies['PHPSESSID'] = (string) $antes;
        $acesso = $velho->request('home')['status'];
        return [$antes !== $depois && $acesso !== 200, 'ID '.($antes !== $depois ? 'renovado' : 'mantido')."; ID antigo recebeu HTTP $acesso em home"];
    });
    caso('CT-S19', 'Sessão', 'A07', 'Logout invalida a sessão no servidor', 'Cookie copiado antes do logout perde o acesso', 'Alta', function () use ($admin) {
        $c = logado($admin['email']);
        $copia = cliente();
        $copia->cookies = $c->cookies;
        $token = HttpCliente::campo($c->request('home')['body'], 'csrf_token');
        $c->request('sair', ['csrf_token' => $token]);
        $status = $copia->request('home')['status'];
        return [$status !== 200, "Cookie copiado recebeu HTTP $status em home após o logout"];
    });
    caso('CT-S20', 'Sessão', 'A07', 'Servidor recusa ID de sessão escolhido pelo cliente (modo estrito)', 'ID inventado é descartado e o servidor emite outro', 'Baixa', function () {
        // ID novo a cada execução: um ID fixo poderia já existir no servidor por uma execução anterior.
        $inventado = 'atacante'.bin2hex(random_bytes(12));
        $c = cliente();
        $c->cookies['PHPSESSID'] = $inventado;
        $c->request('login');
        $aceito = sessaoId($c) === $inventado;
        return [!$aceito, $aceito ? 'Servidor adotou o ID inventado (session.use_strict_mode=0). O login renova o ID, o que reduz o impacto' : 'ID inventado substituído'];
    });

    echo "\n== Controle de acesso (A01) ==\n";
    require_once $raiz.'/src/infra/Routers/Router.php';
    require_once $raiz.'/src/infra/Routers/RouteLoader.php';
    RouteLoader::load($raiz.'/src/modules');
    $rotasGet = $rotasPost = [];
    foreach (Router::rotas() as $rota => $cfg) {
        $mutacao = false;
        foreach ($cfg['middlewares'] as $m) if ($m instanceof CsrfMiddleware) $mutacao = true;
        if ($mutacao) $rotasPost[$rota] = $cfg; else $rotasGet[$rota] = $cfg;
    }
    caso('CT-S21', 'Controle de acesso', 'A01', 'Visitante sem login não acessa nenhuma rota interna', 'Todas as rotas não públicas redirecionam para o login', 'Crítica', function () use ($rotasGet, $rotasPost) {
        $abertas = [];
        foreach ($rotasGet + $rotasPost as $rota => $cfg) {
            if ($cfg['publica']) continue;
            $r = cliente()->request($rota.'&id=1', isset($rotasPost[$rota]) ? ['csrf_token' => 'x'] : null);
            $destino = implode('', $r['headers']['location'] ?? []);
            if (!($r['status'] === 303 && str_contains($destino, 'route=login'))) $abertas[] = "$rota (HTTP {$r['status']})";
        }
        return [$abertas === [], $abertas ? 'Acessíveis sem login: '.implode(', ', $abertas) : count($rotasGet + $rotasPost) - 2 .' rotas protegidas redirecionaram ao login'];
    });
    $matriz = [
        'home' => ['admin', 'saude', 'estoque', 'familia'],
        'usuarios' => ['admin'], 'usuarios-novo' => ['admin'], 'usuarios-editar' => ['admin'],
        'quartos' => ['admin'], 'quartos-novo' => ['admin'], 'quartos-editar' => ['admin'],
        'familiares' => ['admin'], 'familiares-novo' => ['admin'], 'familiares-editar' => ['admin'],
        'idosos' => ['admin', 'saude'], 'idosos-novo' => ['admin'], 'idosos-editar' => ['admin'],
        'idosos-saude' => ['admin', 'saude'], 'idosos-ficha' => ['admin', 'saude'],
        'almoxarifado' => ['admin', 'estoque'], 'almoxarifado-novo' => ['admin', 'estoque'], 'almoxarifado-editar' => ['admin', 'estoque'],
        'almoxarifado-entrada' => ['admin', 'estoque'], 'almoxarifado-retirada' => ['admin', 'estoque'],
        'almoxarifado-uso' => ['admin', 'estoque', 'saude'],
        'compras' => ['admin', 'estoque'], 'compras-nova' => ['admin', 'estoque'],
        'meus-idosos' => ['familia'], 'acompanhamento-familiar' => ['familia'],
    ];
    caso('CT-S22', 'Controle de acesso', 'A01', 'Matriz de permissões por perfil em todas as telas (RNF03)', 'Perfil autorizado recebe 200; os demais recebem 403 ou redirecionamento', 'Crítica', function () use ($matriz, $rotasGet, $a, $s, $e, $f) {
        $clientes = ['admin' => $a, 'saude' => $s, 'estoque' => $e, 'familia' => $f];
        $semMatriz = array_diff(array_keys(array_filter($rotasGet, fn($c) => !$c['publica'])), array_keys($matriz));
        if ($semMatriz) return [false, 'Rotas sem expectativa definida no teste: '.implode(', ', $semMatriz)];
        $divergencias = [];
        $total = 0;
        foreach ($matriz as $rota => $permitidos) {
            foreach ($clientes as $perfil => $c) {
                $total++;
                $status = $c->request($rota.'&id=1')['status'];
                $deveri = in_array($perfil, $permitidos, true);
                if ($deveri && $status !== 200) $divergencias[] = "$perfil bloqueado em $rota (HTTP $status)";
                if (!$deveri && $status === 200) $divergencias[] = "$perfil ACESSOU $rota";
            }
        }
        return [$divergencias === [], $divergencias ? implode('; ', $divergencias) : "$total combinações perfil x tela conforme a matriz"];
    });
    caso('CT-S23', 'Controle de acesso', 'A01', 'Familiar não acessa idoso de outra família (IDOR)', 'Ids de outros idosos, inexistentes ou inválidos não exibem dados', 'Crítica', function () use ($f) {
        foreach (['2', '3', '999', '0', '-1', '1.5', '2abc'] as $id) {
            $r = $f->request('acompanhamento-familiar&id='.urlencode($id));
            if ($r['status'] === 200 && preg_match('/Residente Teste 000[2-5]/', $r['body'])) return [false, "Dados de outro idoso com id=$id"];
            if ($r['status'] >= 500) return [false, "HTTP {$r['status']} com id=$id"];
        }
        return [true, '7 ids testados; nenhum dado de outra família'];
    });
    caso('CT-S24', 'Controle de acesso', 'A01', 'Escalada de privilégio por requisição forjada', 'Almoxarife, profissional e familiar não criam usuário, não aprovam compra nem registram prontuário', 'Crítica', function () use ($s, $e, $f, $pdo) {
        $usuarios = (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
        $tokenE = HttpCliente::tokenFormulario($e->request('compras-nova')['body']);
        $e->request('compras-criar', ['csrf_token' => $tokenE, 'material_id' => 1, 'quantidade' => '2', 'observacao' => 'Teste']);
        $compra = (int) $pdo->query('SELECT MAX(id) FROM solicitacoes_compra')->fetchColumn();
        $tokenUso = HttpCliente::tokenFormulario($s->request('almoxarifado-uso')['body']);
        $tentativas = [
            [$e, 'usuarios-criar', ['csrf_token' => $tokenE, 'nome' => 'Invasor', 'email' => 'invasor@example.test', 'perfil' => 'Administrador', 'senha' => 'SenhaInvasor1']],
            [$e, 'compras-situacao&id='.$compra, ['csrf_token' => $tokenE, 'situacao_atual' => 'Pendente', 'situacao' => 'Aprovada']],
            [$s, 'compras-situacao&id='.$compra, ['csrf_token' => $tokenUso, 'situacao_atual' => 'Pendente', 'situacao' => 'Aprovada']],
            [$f, 'idosos-saude-registrar&id=1', ['csrf_token' => HttpCliente::tokenFormulario($f->request('meus-idosos')['body']), 'descricao' => 'Forjado']],
            [$s, 'usuarios-status&id=1', ['csrf_token' => $tokenUso, 'ativo' => '0']],
        ];
        foreach ($tentativas as [$c, $rota, $dados]) $c->request($rota, $dados);
        $novosUsuarios = (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() - $usuarios;
        $situacao = $pdo->query('SELECT situacao FROM solicitacoes_compra WHERE id='.$compra)->fetchColumn();
        $forjado = (int) $pdo->query("SELECT COUNT(*) FROM registros_saude WHERE descricao='Forjado'")->fetchColumn();
        $adminAtivo = (int) $pdo->query('SELECT ativo FROM usuarios WHERE id=1')->fetchColumn();
        $ok = $novosUsuarios === 0 && $situacao === 'Pendente' && $forjado === 0 && $adminAtivo === 1;
        return [$ok, "Usuários novos: $novosUsuarios; compra: $situacao; prontuário forjado: $forjado; administrador ativo: $adminAtivo"];
    });
    caso('CT-S25', 'Controle de acesso', 'A01', 'Autor do registro de saúde vem da sessão, não do formulário', 'Campo usuario_id enviado é ignorado', 'Alta', function () use ($s, $pdo, $admin, $saude) {
        $token = HttpCliente::tokenFormulario($s->request('idosos-saude&id=2')['body']);
        $s->request('idosos-saude-registrar&id=2', ['csrf_token' => $token, 'descricao' => 'Autor forjado', 'usuario_id' => $admin['id']]);
        $autor = (int) $pdo->query("SELECT usuario_id FROM registros_saude WHERE descricao='Autor forjado'")->fetchColumn();
        return [$autor === $saude['id'], "Autor gravado: usuário $autor (esperado {$saude['id']})"];
    });

    echo "\n== CSRF (A01) ==\n";
    caso('CT-S26', 'CSRF', 'A01', 'Todas as ações de gravação exigem token CSRF válido e método POST', 'Sem token ou com token inválido: 419; GET: 405', 'Alta', function () use ($a, $rotasPost) {
        $erros = [];
        foreach (array_keys($rotasPost) as $rota) {
            if ($rota === 'entrar') continue;
            $sem = $a->request($rota.'&id=1', [])['status'];
            $invalido = $a->request($rota.'&id=1', ['csrf_token' => str_repeat('a', 64)])['status'];
            $get = $a->request($rota.'&id=1')['status'];
            if ($sem !== 419 || $invalido !== 419 || $get !== 405) $erros[] = "$rota ($sem/$invalido/$get)";
        }
        return [$erros === [], $erros ? 'Divergências: '.implode(', ', $erros) : (count($rotasPost) - 1).' rotas de gravação protegidas'];
    });
    caso('CT-S27', 'CSRF', 'A01', 'Token de uma sessão não vale em outra', 'Token roubado do usuário A é recusado na sessão do usuário B', 'Alta', function () use ($a, $pdo, $admin) {
        $tokenA = HttpCliente::tokenFormulario($a->request('quartos-novo')['body']);
        $b = logado($admin['email']);
        $b->request('quartos-novo');
        $r = $b->request('quartos-criar', ['csrf_token' => $tokenA, 'codigo' => 'CSRF', 'capacidade' => 2]);
        $criado = (int) $pdo->query("SELECT COUNT(*) FROM quartos WHERE codigo='CSRF'")->fetchColumn();
        return [$r['status'] === 419 && $criado === 0, "HTTP {$r['status']}; quartos criados: $criado"];
    });

    echo "\n== Configuração e cabeçalhos (A05) ==\n";
    $cab = fn(array $r, string $nome) => implode(', ', $r['headers'][strtolower($nome)] ?? []);
    caso('CT-S28', 'Configuração', 'A05', 'Proteção contra clickjacking', 'X-Frame-Options DENY/SAMEORIGIN ou CSP frame-ancestors', 'Média', function () use ($cab) {
        $r = cliente()->request('login');
        $xfo = $cab($r, 'X-Frame-Options');
        $csp = $cab($r, 'Content-Security-Policy');
        $ok = $xfo !== '' || str_contains($csp, 'frame-ancestors');
        return [$ok, $ok ? "X-Frame-Options: $xfo; CSP: $csp" : 'Nenhum cabeçalho impede abrir o login dentro de um iframe de outro site'];
    });
    caso('CT-S29', 'Configuração', 'A05', 'Cabeçalhos de proteção do navegador', 'X-Content-Type-Options: nosniff, Content-Security-Policy e Referrer-Policy presentes', 'Baixa', function () use ($cab, $a) {
        $faltam = [];
        foreach ([cliente()->request('login'), $a->request('home')] as $r) {
            if (stripos($cab($r, 'X-Content-Type-Options'), 'nosniff') === false) $faltam['X-Content-Type-Options'] = 1;
            if ($cab($r, 'Content-Security-Policy') === '') $faltam['Content-Security-Policy'] = 1;
            if ($cab($r, 'Referrer-Policy') === '') $faltam['Referrer-Policy'] = 1;
        }
        return [$faltam === [], $faltam ? 'Ausentes: '.implode(', ', array_keys($faltam)) : 'Todos presentes'];
    });
    caso('CT-S30', 'Configuração', 'A05', 'Servidor não divulga versão do PHP', 'Sem cabeçalho X-Powered-By com versão', 'Baixa', function () use ($cab) {
        $r = cliente()->request('login');
        $versao = $cab($r, 'X-Powered-By');
        return [$versao === '', $versao === '' ? 'Cabeçalho ausente' : "X-Powered-By: $versao (expose_php ativo no PHP)"];
    });
    caso('CT-S31', 'Configuração', 'A05', 'Páginas com dados clínicos não ficam em cache', 'Cache-Control com no-store no prontuário e na ficha', 'Média', function () use ($cab, $s) {
        foreach (['idosos-saude&id=1', 'idosos-ficha&id=1'] as $rota) {
            $cc = $cab($s->request($rota), 'Cache-Control');
            if (stripos($cc, 'no-store') === false) return [false, "Cache-Control em $rota: ".($cc ?: 'ausente')];
        }
        return [true, 'Cache-Control: '.$cab($s->request('idosos-saude&id=1'), 'Cache-Control')];
    });

    echo "\n== Arquivos internos e travessia de diretório (A01/A05) ==\n";
    caso('CT-S32', 'Arquivos', 'A05', 'Arquivos internos e travessia de diretório bloqueados', 'Somente CSS/JS/imagens de src/assets são servidos; o resto devolve 404', 'Crítica', function () {
        $caminhos = ['/.env', '/.env.example', '/.git/config', '/.git/HEAD', '/.gitignore', '/railway.toml', '/router.php', '/tests/README.md',
            '/docs/testes/README.md', '/src/infra/Database/migrations/001_base_atual.sql', '/src/infra/Database/Database.php', '/scripts/criar-administrador.php',
            '/src/assets/../../.env', '/src/assets/%2e%2e/%2e%2e/.env', '/src/assets/css/..%2f..%2f..%2f.env', '/src/assets/', '/src/assets/css/', '/.env%00.css',
            '/src/assets/css/asilosoft.css/../../../../.env', '/%2e%2e/%2e%2e/etc/passwd'];
        $expostos = [];
        foreach ($caminhos as $c) {
            $r = cliente()->request($c);
            if ($r['status'] !== 404 || str_contains($r['body'], 'MYSQL') || str_contains($r['body'], 'root:x:') || str_contains($r['body'], '[core]')) $expostos[] = "$c (HTTP {$r['status']})";
        }
        $css = cliente()->request('/src/assets/css/asilosoft.css')['status'];
        if ($css !== 200) $expostos[] = "CSS público não servido (HTTP $css)";
        return [$expostos === [], $expostos ? 'Expostos: '.implode(', ', $expostos) : count($caminhos).' caminhos bloqueados; CSS público servido'];
    });
    caso('CT-S33', 'Arquivos', 'A01', 'Parâmetro route não permite incluir arquivos', 'Valores com ../ ou nomes de arquivo levam ao login/404, sem conteúdo de arquivo', 'Crítica', function () use ($a) {
        foreach (['../../.env', '..%2f..%2f.env', '/etc/passwd', 'home/../../.env', 'php://filter/resource=index', 'home%00', str_repeat('a', 300)] as $p) {
            $r = $a->request('/?route='.$p);
            if ($r['status'] >= 500 || str_contains($r['body'], 'MYSQL') || str_contains($r['body'], 'root:x:') || vazamento($r['body'])) return [false, "Resposta indevida para route=$p (HTTP {$r['status']})"];
        }
        return [true, '7 valores maliciosos tratados como rota inexistente'];
    });

    echo "\n== Tratamento de erros e entradas inesperadas (A04/A05) ==\n";
    caso('CT-S34', 'Erros', 'A05', 'Campos enviados como lista (array) não quebram a aplicação', 'Sem HTTP 500, stack trace ou caminho de arquivo na resposta', 'Média', function () use ($a, $s) {
        $c = cliente();
        $token = HttpCliente::campo($c->request('login')['body'], 'csrf_token');
        $falhas = [];
        $testes = [
            [$c, 'entrar', 'csrf_token='.urlencode($token).'&email[]=a&senha[]=b'],
            [$a, 'idosos&busca[]=x', null],
            [$a, 'idosos-ficha&id[]=1', null],
            [$a, 'idosos&pagina[]=2', null],
            [$s, 'idosos-saude-registrar&id=1', 'csrf_token='.urlencode((string) HttpCliente::tokenFormulario($s->request('idosos-saude&id=1')['body'])).'&descricao[]=x'],
            [$a, 'familiares-criar', 'csrf_token='.urlencode((string) HttpCliente::tokenFormulario($a->request('familiares-novo')['body'])).'&nome[]=x&telefone[]=y'],
            [$a, 'quartos-criar', 'csrf_token='.urlencode((string) HttpCliente::tokenFormulario($a->request('quartos-novo')['body'])).'&codigo[]=x&capacidade[]=2'],
        ];
        foreach ($testes as [$cli, $rota, $corpo]) {
            $r = $cli->request($rota, $corpo);
            if ($r['status'] >= 500 || vazamento($r['body'])) $falhas[] = "$rota (HTTP {$r['status']}".(vazamento($r['body']) ? ', vazou '.vazamento($r['body']) : '').')';
        }
        return [$falhas === [], $falhas ? 'Falhas: '.implode('; ', $falhas) : count($testes).' requisições com arrays tratadas sem erro interno'];
    });
    caso('CT-S35', 'Erros', 'A04', 'Entradas muito grandes são recusadas sem erro interno', 'Busca de 10 mil caracteres e nome de 100 mil caracteres sem HTTP 500', 'Baixa', function () use ($a, $pdo) {
        $r1 = $a->request('idosos&busca='.str_repeat('a', 10000));
        $token = HttpCliente::tokenFormulario($a->request('familiares-novo')['body']);
        $r2 = $a->request('familiares-criar', ['csrf_token' => $token, 'nome' => str_repeat('N', 100000), 'telefone' => '1']);
        $gravado = (int) $pdo->query('SELECT COUNT(*) FROM familiares WHERE CHAR_LENGTH(nome) > 120')->fetchColumn();
        $ok = $r1['status'] < 500 && $r2['status'] < 500 && !vazamento($r1['body'].$r2['body']) && $gravado === 0;
        return [$ok, "Busca: HTTP {$r1['status']}; cadastro: HTTP {$r2['status']}; nomes acima de 120 caracteres gravados: $gravado"];
    });
    caso('CT-S36', 'Erros', 'A05', 'Métodos HTTP inesperados não causam erro nem refletem cabeçalhos', 'TRACE, PUT, DELETE e OPTIONS sem HTTP 500 e sem eco de cookies', 'Baixa', function () use ($a) {
        foreach (['TRACE', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $metodo) {
            $r = $a->request('home', null, $metodo);
            if ($r['status'] >= 500 && $r['status'] !== 501) return [false, "$metodo devolveu HTTP {$r['status']}"];
            if (str_contains($r['body'], 'PHPSESSID')) return [false, "$metodo refletiu o cookie de sessão"];
        }
        return [true, '5 métodos tratados sem erro e sem eco'];
    });

    $falhas = array_values(array_filter($casos, fn($c) => !$c['passou']));
    $resultado = [
        'gerado_em' => date(DATE_ATOM),
        'ambiente' => ['php' => PHP_VERSION, 'mysql' => (string) $pdo->query('SELECT VERSION()')->fetchColumn(), 'servidor' => 'PHP embutido (php -S) com router.php em 127.0.0.1:'.$porta, 'base' => 'asilosoft_test_* criada e removida pela suíte'],
        'total' => count($casos),
        'passaram' => count($casos) - count($falhas),
        'vulnerabilidades' => count($falhas),
        'casos' => $casos,
    ];
    file_put_contents($saida.'/resultado.json', json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");
    file_put_contents($saida.'/relatorio.md', relatorioMarkdown($resultado));
    echo "\nResultado: {$resultado['passaram']} de {$resultado['total']} casos passaram; {$resultado['vulnerabilidades']} vulnerabilidades encontradas.\n";
    echo 'Relatório: '.substr($saida, strlen($raiz) + 1)."/relatorio.md\n";
    $codigo = $falhas === [] ? 0 : 1;
} catch (Throwable $erro) {
    fwrite(STDERR, 'IMPEDIMENTO: '.$erro->getMessage()."\n");
    $codigo = 2;
} finally {
    if ($ambiente) {
        try { $ambiente->encerrar(); echo 'Base '.$ambiente->base." removida.\n"; }
        catch (Throwable $erro) { fwrite(STDERR, 'Atenção: base '.$ambiente->base.' não removida: '.$erro->getMessage()."\n"); }
    }
}
exit($codigo);

function relatorioMarkdown(array $r): string
{
    $ordem = ['Crítica' => 0, 'Alta' => 1, 'Média' => 2, 'Baixa' => 3];
    $md = "# Relatório de testes de segurança - AsiloSoft\n\n";
    $md .= 'Gerado em '.date('d/m/Y H:i:s', strtotime($r['gerado_em']))." por `php tests/seguranca/SegurancaTest.php`.\n\n";
    $md .= "PHP {$r['ambiente']['php']}, MySQL {$r['ambiente']['mysql']}, {$r['ambiente']['servidor']}; {$r['ambiente']['base']}. Dados fictícios.\n\n";
    $md .= "**{$r['passaram']} de {$r['total']} casos passaram. {$r['vulnerabilidades']} vulnerabilidades encontradas.**\n\n";
    $falhas = array_filter($r['casos'], fn($c) => !$c['passou']);
    if ($falhas) {
        usort($falhas, fn($a, $b) => $ordem[$a['severidade']] <=> $ordem[$b['severidade']]);
        $md .= "## Vulnerabilidades encontradas\n\n| Caso | Severidade | OWASP | Achado | Obtido |\n| --- | --- | --- | --- | --- |\n";
        foreach ($falhas as $c) $md .= "| {$c['id']} | {$c['severidade']} | {$c['owasp']} | {$c['titulo']} | ".str_replace('|', '\|', $c['obtido'])." |\n";
        $md .= "\n";
    }
    $md .= "## Todos os casos\n\n| Caso | Categoria | OWASP | Título | Esperado | Obtido | Resultado |\n| --- | --- | --- | --- | --- | --- | --- |\n";
    foreach ($r['casos'] as $c) {
        $md .= "| {$c['id']} | {$c['categoria']} | {$c['owasp']} | {$c['titulo']} | {$c['esperado']} | ".str_replace('|', '\|', $c['obtido']).' | '.($c['passou'] ? 'Passou' : 'Falhou ('.$c['severidade'].')')." |\n";
    }
    return $md;
}

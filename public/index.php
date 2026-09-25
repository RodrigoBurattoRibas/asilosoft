<?php

declare(strict_types=1);

session_start();

spl_autoload_register(static function (string $classe): void {
    foreach (['Core', 'Repositories', 'Services', 'Controllers'] as $pasta) {
        $arquivo = __DIR__ . '/../app/' . $pasta . '/' . $classe . '.php';
        if (is_file($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});

function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function renderizar(string $view, array $dados = []): void
{
    $dados['mensagemErro'] = $GLOBALS['sessao']->mensagem('erro');
    $dados['mensagemSucesso'] = $GLOBALS['sessao']->mensagem('sucesso');
    $dados['modoDemonstracao'] = $GLOBALS['modoDemonstracao'];
    extract($dados, EXTR_SKIP);
    $arquivoDaView = __DIR__ . '/../app/Views/' . $view . '.php';
    require __DIR__ . '/../app/Views/layout.php';
}

function redirecionar(string $caminho): never
{
    header('Location: ' . $caminho);
    exit;
}

function validarCsrf(Csrf $csrf): void
{
    if (!$csrf->validar($_POST['csrf'] ?? null)) {
        http_response_code(419);
        exit('Não foi possível validar o formulário. Atualize a página e tente novamente.');
    }
}

function respostaNaoEncontrada(): never
{
    http_response_code(404);
    renderizar('nao_encontrado', ['titulo' => 'Página não encontrada']);
    exit;
}

$GLOBALS['sessao'] = new Sessao();
$GLOBALS['modoDemonstracao'] = false;
$configuracao = require __DIR__ . '/../config/database.php';

try {
    $pdo = Database::conectar($configuracao);
    $repositorioUsuarios = new UsuarioRepository($pdo);
    $repositorioIdosos = new IdosoRepository($pdo);
    $repositorioQuartos = new QuartoRepository($pdo);
} catch (PDOException) {
    $GLOBALS['modoDemonstracao'] = true;
    $repositorioUsuarios = new RepositorioDeSessao($GLOBALS['sessao']);
    $repositorioIdosos = new RepositorioIdososDeSessao($GLOBALS['sessao']);
    $repositorioQuartos = $repositorioIdosos;
}

$autenticacao = new AutenticacaoService($repositorioUsuarios, $GLOBALS['sessao']);
$csrf = new Csrf($GLOBALS['sessao']);
$controleDeLogin = new ControladorAutenticacao($autenticacao, $GLOBALS['sessao'], $csrf);
$controleDeUsuarios = new ControladorUsuarios($repositorioUsuarios, new UsuarioService($repositorioUsuarios), $autenticacao, $GLOBALS['sessao'], $csrf);
$controleDeIdosos = new ControladorIdosos($repositorioIdosos, $repositorioQuartos, new IdosoService($repositorioIdosos), $autenticacao, $GLOBALS['sessao'], $csrf);

$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$caminho = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($metodo === 'GET' && $caminho === '/') {
    redirecionar('/login');
}
if ($metodo === 'GET' && $caminho === '/login') {
    $controleDeLogin->formulario();
    exit;
}
if ($metodo === 'POST' && $caminho === '/login') {
    $controleDeLogin->entrar();
}
if ($metodo === 'POST' && $caminho === '/sair') {
    $controleDeLogin->sair();
}
if ($metodo === 'GET' && $caminho === '/usuarios') {
    $controleDeUsuarios->listar();
    exit;
}
if ($metodo === 'GET' && $caminho === '/usuarios/novo') {
    $controleDeUsuarios->novo();
    exit;
}
if ($metodo === 'POST' && $caminho === '/usuarios') {
    $controleDeUsuarios->criar();
}
if ($metodo === 'GET' && $caminho === '/idosos') {
    $controleDeIdosos->listar();
    exit;
}
if ($metodo === 'GET' && $caminho === '/idosos/novo') {
    $controleDeIdosos->novo();
    exit;
}
if ($metodo === 'POST' && $caminho === '/idosos') {
    $controleDeIdosos->criar();
}

if (preg_match('#^/usuarios/(\d+)/editar$#', $caminho, $coincidencias) && $metodo === 'GET') {
    $controleDeUsuarios->editar((int) $coincidencias[1]);
    exit;
}
if (preg_match('#^/usuarios/(\d+)$#', $caminho, $coincidencias) && $metodo === 'POST') {
    $controleDeUsuarios->atualizar((int) $coincidencias[1]);
}
if (preg_match('#^/usuarios/(\d+)/status$#', $caminho, $coincidencias) && $metodo === 'POST') {
    $controleDeUsuarios->alterarStatus((int) $coincidencias[1]);
}

respostaNaoEncontrada();

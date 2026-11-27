<?php

// Cabeçalhos de proteção do navegador em todas as respostas da aplicação.
header_remove('X-Powered-By');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; frame-ancestors 'none'; form-action 'self'; base-uri 'self'; object-src 'none'");

if (session_status() === PHP_SESSION_NONE) {
    // Recusa IDs de sessão inventados pelo cliente e protege o cookie contra leitura por
    // scripts e envio em requisições de outros sites. Secure quando servido em HTTPS
    // (o Railway informa o protocolo original em X-Forwarded-Proto).
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => ($_SERVER['HTTPS'] ?? '') === 'on'
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https',
    ]);
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', getenv('ASILOSOFT_DEBUG') === '1' ? '1' : '0');
ini_set('log_errors', '1');

require_once __DIR__ . '/src/core/Config/App.php';
require_once __DIR__ . '/src/core/Helpers/RouteSanitizer.php';
require_once __DIR__ . '/src/infra/Middleware/AsiloAuthMiddleware.php';
require_once __DIR__ . '/src/infra/Routers/Router.php';
require_once __DIR__ . '/src/infra/Routers/RouteLoader.php';

App::init();
RouteLoader::load(__DIR__ . '/src/modules');

$_GET = RouteSanitizer::sanitizarEntrada($_GET);
$_POST = RouteSanitizer::sanitizarEntrada($_POST);

$rota = isset($_GET['route']) ? RouteSanitizer::validar($_GET['route']) : '';

if (Router::requerAutenticacao($rota)) {
    AsiloAuthMiddleware::validarSessao();
}

Router::dispatch($rota);

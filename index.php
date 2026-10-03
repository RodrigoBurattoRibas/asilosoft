<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

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

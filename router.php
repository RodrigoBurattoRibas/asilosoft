<?php
/** Roteador para o servidor PHP de desenvolvimento/homologação. */
if (PHP_SAPI !== 'cli-server') {
    http_response_code(404);
    exit;
}

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
if ($path === '/' || $path === '/index.php') {
    require __DIR__ . '/index.php';
    return true;
}

$assets = __DIR__ . '/src/assets/';
$file = str_contains($path, "\0") ? false : realpath(__DIR__ . $path);
if ($file !== false && str_starts_with($file, $assets) && is_file($file)
    && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['css', 'js', 'png', 'jpg', 'jpeg', 'svg', 'ico', 'webp', 'woff', 'woff2'], true)) {
    return false;
}
http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo 'Página não encontrada.';
return true;

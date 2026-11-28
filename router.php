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

// Assets e 404 não passam pelo index.php; recebem aqui os mesmos cabeçalhos de proteção.
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');

$assets = __DIR__ . '/src/assets/';
$tipos = [
    'css' => 'text/css; charset=utf-8', 'js' => 'application/javascript; charset=utf-8',
    'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'svg' => 'image/svg+xml',
    'ico' => 'image/x-icon', 'webp' => 'image/webp', 'woff' => 'font/woff', 'woff2' => 'font/woff2',
];
$file = str_contains($path, "\0") ? false : realpath(__DIR__ . $path);
$extensao = $file === false ? '' : strtolower(pathinfo($file, PATHINFO_EXTENSION));
if ($file !== false && str_starts_with($file, $assets) && is_file($file) && isset($tipos[$extensao])) {
    // Servido pelo PHP (e não com "return false") para que os cabeçalhos acima valham também aqui.
    header('Content-Type: ' . $tipos[$extensao]);
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: public, max-age=3600');
    readfile($file);
    return true;
}
http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo 'Página não encontrada.';
return true;

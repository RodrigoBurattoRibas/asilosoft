<?php

class Router
{
    private static $routes = [];

    public static function add(
        $rota,
        $modulo,
        $controller,
        $metodo,
        $publica = false,
        $middlewares = []
    ) {
        self::$routes[strtolower($rota)] = [
            'modulo' => $modulo,
            'controller' => $controller,
            'metodo' => $metodo,
            'publica' => (bool) $publica,
            'middlewares' => self::normalizarMiddlewares($middlewares),
        ];
    }

    public static function addPublic($rota, $modulo, $controller, $metodo, $middlewares = [])
    {
        self::add($rota, $modulo, $controller, $metodo, true, $middlewares);
    }

    public static function requerAutenticacao($rota)
    {
        $rota = strtolower(trim((string) $rota, '/'));

        return !isset(self::$routes[$rota])
            || empty(self::$routes[$rota]['publica']);
    }

    public static function dispatch($rota)
    {
        $rota = strtolower(trim((string) $rota, '/'));

        if ($rota === '') {
            header('Location: ?route=login');
            exit;
        }

        if (!isset(self::$routes[$rota])) {
            self::erro404();
            return;
        }

        $config = self::$routes[$rota];

        if (!self::executarMiddlewares($config['middlewares'])) {
            return;
        }

        $controllerFile = dirname(dirname(__DIR__))
            . '/modules/' . $config['modulo']
            . '/controller/' . $config['controller'] . '.php';

        if (!is_file($controllerFile)) {
            http_response_code(500);
            echo 'Controller não encontrado.';
            return;
        }

        require_once $controllerFile;

        $controller = new $config['controller']();
        call_user_func([$controller, $config['metodo']]);
    }

    public static function configuracao($rota)
    {
        $rota = strtolower(trim((string) $rota, '/'));

        return self::$routes[$rota] ?? null;
    }

    public static function rotas()
    {
        return self::$routes;
    }

    private static function executarMiddlewares(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            if (is_object($middleware) && method_exists($middleware, 'handle')) {
                $resultado = $middleware->handle();
            } elseif (is_callable($middleware)) {
                $resultado = call_user_func($middleware);
            } else {
                http_response_code(500);
                echo 'Middleware de rota inválido.';
                return false;
            }

            if ($resultado === false) {
                return false;
            }
        }

        return true;
    }

    private static function normalizarMiddlewares($middlewares)
    {
        if ($middlewares === null || $middlewares === []) {
            return [];
        }

        if (is_object($middlewares) || is_callable($middlewares)) {
            return [$middlewares];
        }

        return is_array($middlewares) ? $middlewares : [$middlewares];
    }

    private static function erro404()
    {
        http_response_code(404);
        require_once dirname(dirname(__DIR__)) . '/presentation/layout/AsiloView.php';

        AsiloView::render(
            dirname(dirname(__DIR__)) . '/presentation/components/asiloNaoEncontrado.php',
            ['titulo' => 'Página não encontrada']
        );
    }
}

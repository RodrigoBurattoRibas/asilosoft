<?php

require_once dirname(__DIR__, 3) . '/infra/Middleware/CsrfMiddleware.php';
require_once dirname(__DIR__, 3) . '/infra/Middleware/RequestMethodMiddleware.php';

Router::addPublic(
    'login',
    'autenticacao',
    'AsiloAutenticacaoController',
    'formulario',
    [new RequestMethodMiddleware('GET')]
);
Router::addPublic(
    'entrar',
    'autenticacao',
    'AsiloAutenticacaoController',
    'entrar',
    [new CsrfMiddleware('asilosoft_login')]
);
Router::add(
    'sair',
    'autenticacao',
    'AsiloAutenticacaoController',
    'sair',
    false,
    [new CsrfMiddleware('asilosoft_sair')]
);

<?php

require_once dirname(__DIR__, 3) . '/infra/Middleware/CsrfMiddleware.php';
require_once dirname(__DIR__, 3) . '/infra/Middleware/RequestMethodMiddleware.php';

$usuariosGet = [new RequestMethodMiddleware('GET')];
$usuariosPost = [new CsrfMiddleware('asilosoft_usuarios')];

Router::add('usuarios', 'usuarios', 'AsiloUsuarioController', 'listar', false, $usuariosGet);
Router::add('usuarios-novo', 'usuarios', 'AsiloUsuarioController', 'novo', false, $usuariosGet);
Router::add('usuarios-criar', 'usuarios', 'AsiloUsuarioController', 'criar', false, $usuariosPost);
Router::add('usuarios-editar', 'usuarios', 'AsiloUsuarioController', 'editar', false, $usuariosGet);
Router::add('usuarios-atualizar', 'usuarios', 'AsiloUsuarioController', 'atualizar', false, $usuariosPost);
Router::add('usuarios-status', 'usuarios', 'AsiloUsuarioController', 'alterarStatus', false, $usuariosPost);

<?php

require_once dirname(__DIR__, 3) . '/infra/Middleware/CsrfMiddleware.php';
require_once dirname(__DIR__, 3) . '/infra/Middleware/RequestMethodMiddleware.php';

$almoxarifadoGet = [new RequestMethodMiddleware('GET')];
$almoxarifadoPost = [new CsrfMiddleware('asilosoft_almoxarifado')];

Router::add('almoxarifado', 'almoxarifado', 'AsiloAlmoxarifadoController', 'listar', false, $almoxarifadoGet);
Router::add('almoxarifado-novo', 'almoxarifado', 'AsiloAlmoxarifadoController', 'novo', false, $almoxarifadoGet);
Router::add('almoxarifado-criar', 'almoxarifado', 'AsiloAlmoxarifadoController', 'criar', false, $almoxarifadoPost);
Router::add('almoxarifado-editar', 'almoxarifado', 'AsiloAlmoxarifadoController', 'editar', false, $almoxarifadoGet);
Router::add('almoxarifado-atualizar', 'almoxarifado', 'AsiloAlmoxarifadoController', 'atualizar', false, $almoxarifadoPost);
Router::add('almoxarifado-entrada', 'almoxarifado', 'AsiloAlmoxarifadoController', 'entrada', false, $almoxarifadoGet);
Router::add('almoxarifado-registrar-entrada', 'almoxarifado', 'AsiloAlmoxarifadoController', 'registrarEntrada', false, $almoxarifadoPost);
Router::add('almoxarifado-retirada', 'almoxarifado', 'AsiloAlmoxarifadoController', 'retirada', false, $almoxarifadoGet);
Router::add('almoxarifado-registrar-retirada', 'almoxarifado', 'AsiloAlmoxarifadoController', 'registrarRetirada', false, $almoxarifadoPost);

Router::add('almoxarifado-uso', 'almoxarifado', 'AsiloAlmoxarifadoController', 'uso', false, $almoxarifadoGet);
Router::add('almoxarifado-registrar-uso', 'almoxarifado', 'AsiloAlmoxarifadoController', 'registrarUso', false, $almoxarifadoPost);

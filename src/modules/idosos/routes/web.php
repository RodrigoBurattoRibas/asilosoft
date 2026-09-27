<?php

require_once dirname(__DIR__, 3) . '/infra/Middleware/CsrfMiddleware.php';
require_once dirname(__DIR__, 3) . '/infra/Middleware/RequestMethodMiddleware.php';

$idososGet = [new RequestMethodMiddleware('GET')];
$idososPost = [new CsrfMiddleware('asilosoft_idosos')];

Router::add('idosos', 'idosos', 'AsiloIdosoController', 'listar', false, $idososGet);
Router::add('idosos-novo', 'idosos', 'AsiloIdosoController', 'novo', false, $idososGet);
Router::add('idosos-criar', 'idosos', 'AsiloIdosoController', 'criar', false, $idososPost);
Router::add('idosos-editar', 'idosos', 'AsiloIdosoController', 'editar', false, $idososGet);
Router::add('idosos-atualizar', 'idosos', 'AsiloIdosoController', 'atualizar', false, $idososPost);
Router::add('idosos-retirar-quarto', 'idosos', 'AsiloIdosoController', 'retirarDoQuarto', false, $idososPost);
Router::add('idosos-status', 'idosos', 'AsiloIdosoController', 'alterarStatus', false, $idososPost);

<?php

require_once dirname(__DIR__, 3) . '/infra/Middleware/CsrfMiddleware.php';
require_once dirname(__DIR__, 3) . '/infra/Middleware/RequestMethodMiddleware.php';

$quartosGet = [new RequestMethodMiddleware('GET')];
$quartosPost = [new CsrfMiddleware('asilosoft_quartos')];

Router::add('quartos', 'quartos', 'AsiloQuartoController', 'listar', false, $quartosGet);
Router::add('quartos-novo', 'quartos', 'AsiloQuartoController', 'novo', false, $quartosGet);
Router::add('quartos-criar', 'quartos', 'AsiloQuartoController', 'criar', false, $quartosPost);
Router::add('quartos-editar', 'quartos', 'AsiloQuartoController', 'editar', false, $quartosGet);
Router::add('quartos-atualizar', 'quartos', 'AsiloQuartoController', 'atualizar', false, $quartosPost);
Router::add('quartos-status', 'quartos', 'AsiloQuartoController', 'alterarStatus', false, $quartosPost);

<?php
require_once dirname(__DIR__,3).'/infra/Middleware/RequestMethodMiddleware.php'; require_once dirname(__DIR__,3).'/infra/Middleware/CsrfMiddleware.php';
Router::add('compras','zcompras','AsiloCompraController','listar',false,[new RequestMethodMiddleware('GET')]);
Router::add('compras-nova','zcompras','AsiloCompraController','nova',false,[new RequestMethodMiddleware('GET')]);
Router::add('compras-criar','zcompras','AsiloCompraController','criar',false,[new CsrfMiddleware('asilosoft_compras')]);
Router::add('compras-situacao','zcompras','AsiloCompraController','situacao',false,[new CsrfMiddleware('asilosoft_compras')]);

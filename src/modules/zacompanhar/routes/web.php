<?php
require_once dirname(__DIR__,3).'/infra/Middleware/RequestMethodMiddleware.php';
Router::add('idosos-ficha','zacompanhar','AsiloFichaIdosoController','mostrar',false,[new RequestMethodMiddleware('GET')]);

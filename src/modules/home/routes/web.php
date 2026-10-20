<?php

require_once dirname(__DIR__, 3) . '/infra/Middleware/RequestMethodMiddleware.php';

Router::add('home', 'home', 'AsiloHomeController', 'index', false, [new RequestMethodMiddleware('GET')]);

<?php

declare(strict_types=1);

require_once __DIR__ . '/ambiente.php';

return [
    'host' => valorDoAmbiente('ASILOSOFT_DB_HOST', '127.0.0.1'),
    'porta' => valorDoAmbiente('ASILOSOFT_DB_PORTA', '3306'),
    'banco' => valorDoAmbiente('ASILOSOFT_DB_NOME', 'asilosoft'),
    'usuario' => valorDoAmbiente('ASILOSOFT_DB_USUARIO', 'root'),
    'senha' => valorDoAmbiente('ASILOSOFT_DB_SENHA'),
];

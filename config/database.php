<?php

declare(strict_types=1);

return [
    'host' => getenv('ASILOSOFT_DB_HOST') ?: '127.0.0.1',
    'porta' => getenv('ASILOSOFT_DB_PORTA') ?: '3306',
    'banco' => getenv('ASILOSOFT_DB_NOME') ?: 'asilosoft',
    'usuario' => getenv('ASILOSOFT_DB_USUARIO') ?: 'root',
    'senha' => getenv('ASILOSOFT_DB_SENHA') ?: '',
];

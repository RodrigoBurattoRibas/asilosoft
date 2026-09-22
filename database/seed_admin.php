<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Repositories/UsuarioRepository.php';
require_once __DIR__ . '/../app/Services/UsuarioService.php';
require_once __DIR__ . '/../config/ambiente.php';

$email = valorDoAmbiente('ASILOSOFT_ADMIN_EMAIL');
$nome = valorDoAmbiente('ASILOSOFT_ADMIN_NOME');
$senha = valorDoAmbiente('ASILOSOFT_ADMIN_SENHA');

if ($email === '' || $nome === '' || $senha === '') {
    fwrite(STDERR, "Defina ASILOSOFT_ADMIN_EMAIL, ASILOSOFT_ADMIN_NOME e ASILOSOFT_ADMIN_SENHA antes de executar.\n");
    exit(1);
}

$repositorio = new UsuarioRepository(Database::conectar(require __DIR__ . '/../config/database.php'));
$servico = new UsuarioService($repositorio);

try {
    $servico->criar(['nome' => $nome, 'email' => $email, 'senha' => $senha, 'perfil' => 'Administrador']);
    echo "Administrador inicial criado.\n";
} catch (DomainException $erro) {
    fwrite(STDERR, $erro->getMessage() . "\n");
    exit(1);
}

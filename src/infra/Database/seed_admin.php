<?php
declare(strict_types=1);
require_once __DIR__ . '/Database.php';
$email = strtolower(trim(Environment::get('ASILOSOFT_ADMIN_EMAIL')));
$nome = trim(Environment::get('ASILOSOFT_ADMIN_NOME'));
$senha = Environment::get('ASILOSOFT_ADMIN_SENHA');
if ($email === '' || $nome === '' || $senha === '') { fwrite(STDERR, "Defina ASILOSOFT_ADMIN_EMAIL, ASILOSOFT_ADMIN_NOME e ASILOSOFT_ADMIN_SENHA antes de executar.\n"); exit(1); }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { fwrite(STDERR, "E-mail de administrador inválido.\n"); exit(1); }
try {
 $pdo=PDOMysqli::connection(); $pdo->beginTransaction();
 $find=$pdo->prepare('SELECT id FROM usuarios WHERE email=?'); $find->execute([$email]); $id=$find->fetchColumn(); $hash=password_hash($senha,PASSWORD_DEFAULT);
 if ($id === false) $pdo->prepare("INSERT INTO usuarios(nome,email,senha_hash,perfil,ativo) VALUES(?,?,?,'Administrador',1)")->execute([$nome,$email,$hash]);
 else $pdo->prepare("UPDATE usuarios SET nome=?,senha_hash=?,perfil='Administrador',ativo=1 WHERE id=?")->execute([$nome,$hash,$id]);
 $pdo->commit(); echo "Administrador configurado.\n";
} catch(Throwable $e) { if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack(); fwrite(STDERR,"Seed administrativo falhou: {$e->getMessage()}\n"); exit(1); }

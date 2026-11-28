INSERT INTO usuarios (nome, email, senha_hash, perfil, ativo)
VALUES (@cadastro_admin_nome, @cadastro_admin_email, @cadastro_admin_hash, 'Administrador', 1);

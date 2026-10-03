CREATE TABLE IF NOT EXISTS materiais (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(160) NOT NULL UNIQUE, descricao VARCHAR(500) NULL,
 unidade_medida VARCHAR(30) NOT NULL, estoque_minimo DECIMAL(12,3) NOT NULL DEFAULT 0, ativo TINYINT(1) NOT NULL DEFAULT 1,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT chk_materiais_minimo CHECK (estoque_minimo >= 0), INDEX idx_materiais_ativos_nome (ativo,nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS estoques (
 material_id INT UNSIGNED PRIMARY KEY, quantidade_atual DECIMAL(12,3) NOT NULL DEFAULT 0,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_estoque_material FOREIGN KEY (material_id) REFERENCES materiais(id) ON DELETE CASCADE,
 CONSTRAINT chk_estoque_nao_negativo CHECK (quantidade_atual >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS usos_materiais (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, idoso_id INT UNSIGNED NOT NULL, material_id INT UNSIGNED NOT NULL,
 usuario_id INT UNSIGNED NOT NULL, quantidade DECIMAL(12,3) NOT NULL, observacao VARCHAR(500) NULL,
 usado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_um_idoso FOREIGN KEY (idoso_id) REFERENCES idosos(id), CONSTRAINT fk_um_material FOREIGN KEY (material_id) REFERENCES materiais(id),
 CONSTRAINT fk_um_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id), CONSTRAINT chk_um_quantidade CHECK (quantidade > 0),
 INDEX idx_um_idoso_data (idoso_id,usado_em,id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS movimentacoes_estoque (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, material_id INT UNSIGNED NOT NULL, tipo ENUM('Entrada','Saída') NOT NULL,
 quantidade DECIMAL(12,3) NOT NULL, origem ENUM('SUS','Plano de Saúde','Instituição') NULL, uso_material_id BIGINT UNSIGNED NULL UNIQUE,
 usuario_id INT UNSIGNED NULL, observacao VARCHAR(500) NULL, movimentado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CONSTRAINT fk_me_material FOREIGN KEY (material_id) REFERENCES materiais(id),
 CONSTRAINT fk_me_uso FOREIGN KEY (uso_material_id) REFERENCES usos_materiais(id), CONSTRAINT fk_me_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
 CONSTRAINT chk_me_quantidade CHECK (quantidade > 0),
 CONSTRAINT chk_me_origem CHECK ((tipo = 'Entrada' AND origem IS NOT NULL) OR (tipo = 'Saída' AND origem IS NULL)),
 INDEX idx_me_material_data (material_id,movimentado_em,id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS solicitacoes_compra (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, material_id INT UNSIGNED NOT NULL, usuario_solicitante_id INT UNSIGNED NOT NULL,
 quantidade DECIMAL(12,3) NOT NULL, situacao ENUM('Pendente','Aprovada','Recusada','Atendida') NOT NULL DEFAULT 'Pendente',
 observacao VARCHAR(500) NULL, solicitada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_sc_material FOREIGN KEY (material_id) REFERENCES materiais(id), CONSTRAINT fk_sc_usuario FOREIGN KEY (usuario_solicitante_id) REFERENCES usuarios(id),
 CONSTRAINT chk_sc_quantidade CHECK (quantidade > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS asilosoft CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE asilosoft;

CREATE TABLE IF NOT EXISTS quartos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    capacidade TINYINT UNSIGNED NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_quartos_capacidade CHECK (capacidade > 0)
);

CREATE TABLE IF NOT EXISTS idosos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    cpf CHAR(11) NOT NULL UNIQUE,
    identificador VARCHAR(60) NOT NULL UNIQUE,
    quarto_id INT UNSIGNED NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_idosos_quarto FOREIGN KEY (quarto_id) REFERENCES quartos(id)
);

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('Administrador', 'Profissional de Saúde', 'Almoxarife', 'Familiar') NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Módulo completo de cadastro, saúde, estoque e auditoria (MySQL 8).
CREATE TABLE IF NOT EXISTS familiares (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, usuario_id INT UNSIGNED NULL UNIQUE,
    nome VARCHAR(120) NOT NULL, cpf CHAR(11) NULL UNIQUE, telefone VARCHAR(30) NOT NULL,
    email VARCHAR(160) NULL, parentesco_principal VARCHAR(60) NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_familiares_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS idosos_familiares (
    idoso_id INT UNSIGNED NOT NULL, familiar_id INT UNSIGNED NOT NULL, parentesco VARCHAR(60) NOT NULL,
    responsavel_principal TINYINT(1) NOT NULL DEFAULT 0, criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idoso_id, familiar_id),
    CONSTRAINT fk_if_idoso FOREIGN KEY (idoso_id) REFERENCES idosos(id) ON DELETE CASCADE,
    CONSTRAINT fk_if_familiar FOREIGN KEY (familiar_id) REFERENCES familiares(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS registros_saude (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, idoso_id INT UNSIGNED NOT NULL, usuario_id INT UNSIGNED NOT NULL,
    descricao TEXT NOT NULL, visivel_para_familiar TINYINT(1) NOT NULL DEFAULT 0,
    registrado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rs_idoso FOREIGN KEY (idoso_id) REFERENCES idosos(id), CONSTRAINT fk_rs_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_rs_idoso_data (idoso_id, registrado_em)
);
CREATE TABLE IF NOT EXISTS materiais (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(160) NOT NULL UNIQUE, descricao VARCHAR(500) NULL,
    unidade_medida VARCHAR(30) NOT NULL, estoque_minimo DECIMAL(12,3) NOT NULL DEFAULT 0, ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_materiais_minimo CHECK (estoque_minimo >= 0)
);
CREATE TABLE IF NOT EXISTS estoques (
    material_id INT UNSIGNED PRIMARY KEY, quantidade_atual DECIMAL(12,3) NOT NULL DEFAULT 0,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_estoque_material FOREIGN KEY (material_id) REFERENCES materiais(id) ON DELETE CASCADE,
    CONSTRAINT chk_estoque_nao_negativo CHECK (quantidade_atual >= 0)
);
CREATE TABLE IF NOT EXISTS usos_materiais (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, idoso_id INT UNSIGNED NOT NULL, material_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL, quantidade DECIMAL(12,3) NOT NULL, usado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observacao VARCHAR(500) NULL, criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_um_idoso FOREIGN KEY (idoso_id) REFERENCES idosos(id), CONSTRAINT fk_um_material FOREIGN KEY (material_id) REFERENCES materiais(id),
    CONSTRAINT fk_um_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id), CONSTRAINT chk_um_quantidade CHECK (quantidade > 0)
);
CREATE TABLE IF NOT EXISTS movimentacoes_estoque (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, material_id INT UNSIGNED NOT NULL,
    tipo ENUM('Entrada','Saída') NOT NULL, quantidade DECIMAL(12,3) NOT NULL,
    origem ENUM('SUS','Plano de Saúde','Instituição') NULL, uso_material_id BIGINT UNSIGNED NULL UNIQUE,
    usuario_id INT UNSIGNED NULL, observacao VARCHAR(500) NULL, movimentado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_me_material FOREIGN KEY (material_id) REFERENCES materiais(id), CONSTRAINT fk_me_uso FOREIGN KEY (uso_material_id) REFERENCES usos_materiais(id),
    CONSTRAINT fk_me_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    CONSTRAINT chk_me_quantidade CHECK (quantidade > 0),
    CONSTRAINT chk_me_origem CHECK ((tipo = 'Entrada' AND origem IS NOT NULL) OR (tipo = 'Saída' AND origem IS NULL)),
    INDEX idx_me_material_data (material_id, movimentado_em)
);
CREATE TABLE IF NOT EXISTS solicitacoes_compra (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, material_id INT UNSIGNED NOT NULL, usuario_solicitante_id INT UNSIGNED NOT NULL,
    quantidade DECIMAL(12,3) NOT NULL, situacao ENUM('Pendente','Aprovada','Recusada','Atendida') NOT NULL DEFAULT 'Pendente',
    observacao VARCHAR(500) NULL, solicitada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sc_material FOREIGN KEY (material_id) REFERENCES materiais(id), CONSTRAINT fk_sc_usuario FOREIGN KEY (usuario_solicitante_id) REFERENCES usuarios(id),
    CONSTRAINT chk_sc_quantidade CHECK (quantidade > 0)
);
CREATE TABLE IF NOT EXISTS logs_auditoria (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, operacao ENUM('INSERT','UPDATE','DELETE') NOT NULL,
    tabela VARCHAR(64) NOT NULL, registro_id BIGINT UNSIGNED NOT NULL, usuario_id INT UNSIGNED NULL,
    valores_anteriores JSON NULL, valores_novos JSON NULL, criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_la_tabela_registro (tabela, registro_id), INDEX idx_la_data (criado_em)
);

DELIMITER $$
DROP TRIGGER IF EXISTS bi_idosos_validar_ocupacao$$
DROP TRIGGER IF EXISTS bu_idosos_validar_ocupacao$$
DROP TRIGGER IF EXISTS ai_materiais_criar_estoque$$
DROP TRIGGER IF EXISTS bi_me_validar_saldo$$
DROP TRIGGER IF EXISTS ai_me_projetar_estoque$$
DROP TRIGGER IF EXISTS ai_um_registrar_saida$$
DROP TRIGGER IF EXISTS ai_materiais_auditar$$
DROP TRIGGER IF EXISTS au_materiais_auditar$$
DROP TRIGGER IF EXISTS ad_materiais_auditar$$
DROP TRIGGER IF EXISTS ai_rs_auditar$$
CREATE TRIGGER bi_idosos_validar_ocupacao BEFORE INSERT ON idosos FOR EACH ROW
BEGIN
  DECLARE ocupacao INT; DECLARE capacidade_quarto INT; DECLARE quarto_ativo TINYINT;
  IF NEW.ativo = 1 AND NEW.quarto_id IS NOT NULL THEN
    SELECT capacidade, ativo INTO capacidade_quarto, quarto_ativo FROM quartos WHERE id = NEW.quarto_id;
    IF quarto_ativo IS NULL OR quarto_ativo = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quarto indisponível.'; END IF;
    SELECT COUNT(*) INTO ocupacao FROM idosos WHERE quarto_id = NEW.quarto_id AND ativo = 1;
    IF ocupacao >= capacidade_quarto THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quarto sem vaga disponível.'; END IF;
  END IF;
END$$
CREATE TRIGGER bu_idosos_validar_ocupacao BEFORE UPDATE ON idosos FOR EACH ROW
BEGIN
  DECLARE ocupacao INT; DECLARE capacidade_quarto INT; DECLARE quarto_ativo TINYINT;
  IF NEW.ativo = 1 AND NEW.quarto_id IS NOT NULL AND (OLD.ativo = 0 OR OLD.quarto_id IS NULL OR OLD.quarto_id <> NEW.quarto_id) THEN
    SELECT capacidade, ativo INTO capacidade_quarto, quarto_ativo FROM quartos WHERE id = NEW.quarto_id;
    IF quarto_ativo IS NULL OR quarto_ativo = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quarto indisponível.'; END IF;
    SELECT COUNT(*) INTO ocupacao FROM idosos WHERE quarto_id = NEW.quarto_id AND ativo = 1;
    IF ocupacao >= capacidade_quarto THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quarto sem vaga disponível.'; END IF;
  END IF;
END$$
CREATE TRIGGER ai_materiais_criar_estoque AFTER INSERT ON materiais FOR EACH ROW BEGIN INSERT INTO estoques (material_id) VALUES (NEW.id); END$$
CREATE TRIGGER bi_me_validar_saldo BEFORE INSERT ON movimentacoes_estoque FOR EACH ROW
BEGIN
  DECLARE saldo DECIMAL(12,3);
  IF NEW.tipo = 'Saída' THEN SELECT quantidade_atual INTO saldo FROM estoques WHERE material_id = NEW.material_id FOR UPDATE;
    IF saldo IS NULL OR saldo < NEW.quantidade THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Estoque insuficiente.'; END IF;
  END IF;
END$$
CREATE TRIGGER ai_me_projetar_estoque AFTER INSERT ON movimentacoes_estoque FOR EACH ROW
BEGIN UPDATE estoques SET quantidade_atual = quantidade_atual + IF(NEW.tipo = 'Entrada', NEW.quantidade, -NEW.quantidade) WHERE material_id = NEW.material_id; END$$
CREATE TRIGGER ai_um_registrar_saida AFTER INSERT ON usos_materiais FOR EACH ROW
BEGIN INSERT INTO movimentacoes_estoque (material_id,tipo,quantidade,uso_material_id,usuario_id,observacao,movimentado_em) VALUES (NEW.material_id,'Saída',NEW.quantidade,NEW.id,NEW.usuario_id,NEW.observacao,NEW.usado_em); END$$
CREATE TRIGGER ai_materiais_auditar AFTER INSERT ON materiais FOR EACH ROW
BEGIN INSERT INTO logs_auditoria (operacao,tabela,registro_id,valores_novos) VALUES ('INSERT','materiais',NEW.id,JSON_OBJECT('nome',NEW.nome,'ativo',NEW.ativo)); END$$
CREATE TRIGGER au_materiais_auditar AFTER UPDATE ON materiais FOR EACH ROW
BEGIN INSERT INTO logs_auditoria (operacao,tabela,registro_id,valores_anteriores,valores_novos) VALUES ('UPDATE','materiais',NEW.id,JSON_OBJECT('nome',OLD.nome,'ativo',OLD.ativo),JSON_OBJECT('nome',NEW.nome,'ativo',NEW.ativo)); END$$
CREATE TRIGGER ad_materiais_auditar AFTER DELETE ON materiais FOR EACH ROW
BEGIN INSERT INTO logs_auditoria (operacao,tabela,registro_id,valores_anteriores) VALUES ('DELETE','materiais',OLD.id,JSON_OBJECT('nome',OLD.nome,'ativo',OLD.ativo)); END$$
CREATE TRIGGER ai_rs_auditar AFTER INSERT ON registros_saude FOR EACH ROW
BEGIN INSERT INTO logs_auditoria (operacao,tabela,registro_id,usuario_id,valores_novos) VALUES ('INSERT','registros_saude',NEW.id,NEW.usuario_id,JSON_OBJECT('idoso_id',NEW.idoso_id,'registrado_em',NEW.registrado_em)); END$$
DELIMITER ;

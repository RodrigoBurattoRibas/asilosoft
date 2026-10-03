CREATE TABLE IF NOT EXISTS logs_auditoria (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, operacao ENUM('INSERT','UPDATE','DELETE') NOT NULL,
 tabela VARCHAR(64) NOT NULL, registro_id BIGINT UNSIGNED NOT NULL, usuario_id INT UNSIGNED NULL,
 valores_anteriores JSON NULL, valores_novos JSON NULL, criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_la_tabela_registro (tabela,registro_id), INDEX idx_la_data (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER bi_idosos_validar_ocupacao BEFORE INSERT ON idosos FOR EACH ROW
BEGIN
 DECLARE ocupacao INT DEFAULT 0; DECLARE capacidade_quarto INT DEFAULT NULL; DECLARE quarto_ativo TINYINT DEFAULT NULL;
 IF NEW.ativo = 1 AND NEW.quarto_id IS NOT NULL THEN
  SELECT capacidade,ativo INTO capacidade_quarto,quarto_ativo FROM quartos WHERE id=NEW.quarto_id;
  IF quarto_ativo IS NULL OR quarto_ativo=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Quarto indisponível'; END IF;
  SELECT COUNT(*) INTO ocupacao FROM idosos WHERE quarto_id=NEW.quarto_id AND ativo=1;
  IF ocupacao >= capacidade_quarto THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Quarto sem vaga disponível'; END IF;
 END IF;
END$$
CREATE TRIGGER bu_idosos_validar_ocupacao BEFORE UPDATE ON idosos FOR EACH ROW
BEGIN
 DECLARE ocupacao INT DEFAULT 0; DECLARE capacidade_quarto INT DEFAULT NULL; DECLARE quarto_ativo TINYINT DEFAULT NULL;
 IF NEW.ativo=1 AND NEW.quarto_id IS NOT NULL AND (OLD.ativo=0 OR OLD.quarto_id IS NULL OR OLD.quarto_id<>NEW.quarto_id) THEN
  SELECT capacidade,ativo INTO capacidade_quarto,quarto_ativo FROM quartos WHERE id=NEW.quarto_id;
  IF quarto_ativo IS NULL OR quarto_ativo=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Quarto indisponível'; END IF;
  SELECT COUNT(*) INTO ocupacao FROM idosos WHERE quarto_id=NEW.quarto_id AND ativo=1;
  IF ocupacao >= capacidade_quarto THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Quarto sem vaga disponível'; END IF;
 END IF;
END$$
CREATE TRIGGER bi_familiares_perfil BEFORE INSERT ON familiares FOR EACH ROW
BEGIN
 DECLARE perfil_usuario VARCHAR(40) DEFAULT NULL;
 IF NEW.usuario_id IS NOT NULL THEN SELECT perfil INTO perfil_usuario FROM usuarios WHERE id=NEW.usuario_id; IF perfil_usuario <> 'Familiar' OR perfil_usuario IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuário deve ter perfil Familiar'; END IF; END IF;
END$$
CREATE TRIGGER bu_familiares_perfil BEFORE UPDATE ON familiares FOR EACH ROW
BEGIN
 DECLARE perfil_usuario VARCHAR(40) DEFAULT NULL;
 IF NEW.usuario_id IS NOT NULL THEN SELECT perfil INTO perfil_usuario FROM usuarios WHERE id=NEW.usuario_id; IF perfil_usuario <> 'Familiar' OR perfil_usuario IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuário deve ter perfil Familiar'; END IF; END IF;
END$$
CREATE TRIGGER bi_rs_perfil BEFORE INSERT ON registros_saude FOR EACH ROW
BEGIN
 DECLARE perfil_usuario VARCHAR(40) DEFAULT NULL; SELECT perfil INTO perfil_usuario FROM usuarios WHERE id=NEW.usuario_id;
 IF perfil_usuario <> 'Profissional de Saúde' OR perfil_usuario IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Autor deve ter perfil Profissional de Saúde'; END IF;
END$$
CREATE TRIGGER bu_rs_perfil BEFORE UPDATE ON registros_saude FOR EACH ROW
BEGIN
 DECLARE perfil_usuario VARCHAR(40) DEFAULT NULL; SELECT perfil INTO perfil_usuario FROM usuarios WHERE id=NEW.usuario_id;
 IF perfil_usuario <> 'Profissional de Saúde' OR perfil_usuario IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Autor deve ter perfil Profissional de Saúde'; END IF;
END$$
CREATE TRIGGER ai_materiais_criar_estoque AFTER INSERT ON materiais FOR EACH ROW BEGIN INSERT INTO estoques(material_id) VALUES(NEW.id); END$$
CREATE TRIGGER bi_me_validar BEFORE INSERT ON movimentacoes_estoque FOR EACH ROW
BEGIN
 DECLARE saldo DECIMAL(12,3) DEFAULT NULL; DECLARE uso_material INT DEFAULT NULL; DECLARE uso_quantidade DECIMAL(12,3) DEFAULT NULL;
 IF NEW.uso_material_id IS NOT NULL THEN
  SELECT material_id,quantidade INTO uso_material,uso_quantidade FROM usos_materiais WHERE id=NEW.uso_material_id;
  IF NEW.tipo <> 'Saída' OR uso_material <> NEW.material_id OR uso_quantidade <> NEW.quantidade THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Movimentação não corresponde ao uso'; END IF;
 END IF;
 IF NEW.tipo='Saída' THEN SELECT quantidade_atual INTO saldo FROM estoques WHERE material_id=NEW.material_id FOR UPDATE; IF saldo IS NULL OR saldo<NEW.quantidade THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Estoque insuficiente'; END IF; END IF;
END$$
CREATE TRIGGER ai_me_projetar AFTER INSERT ON movimentacoes_estoque FOR EACH ROW
BEGIN UPDATE estoques SET quantidade_atual=quantidade_atual + IF(NEW.tipo='Entrada',NEW.quantidade,-NEW.quantidade) WHERE material_id=NEW.material_id; END$$
CREATE TRIGGER bu_me_imutavel BEFORE UPDATE ON movimentacoes_estoque FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Movimentação de estoque é imutável'; END$$
CREATE TRIGGER bd_me_imutavel BEFORE DELETE ON movimentacoes_estoque FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Movimentação de estoque é imutável'; END$$
CREATE TRIGGER ai_um_registrar_saida AFTER INSERT ON usos_materiais FOR EACH ROW
BEGIN INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,uso_material_id,usuario_id,observacao,movimentado_em) VALUES(NEW.material_id,'Saída',NEW.quantidade,NULL,NEW.id,NEW.usuario_id,NEW.observacao,NEW.usado_em); END$$
CREATE TRIGGER bu_um_imutavel BEFORE UPDATE ON usos_materiais FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Uso de material é imutável'; END$$
CREATE TRIGGER bd_um_imutavel BEFORE DELETE ON usos_materiais FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Uso de material é imutável'; END$$
CREATE TRIGGER ai_familiares_auditar AFTER INSERT ON familiares FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_novos) VALUES('INSERT','familiares',NEW.id,@asilosoft_usuario_id,JSON_OBJECT('usuario_id',NEW.usuario_id,'nome',NEW.nome)); END$$
CREATE TRIGGER au_familiares_auditar AFTER UPDATE ON familiares FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores,valores_novos) VALUES('UPDATE','familiares',NEW.id,@asilosoft_usuario_id,JSON_OBJECT('usuario_id',OLD.usuario_id,'nome',OLD.nome),JSON_OBJECT('usuario_id',NEW.usuario_id,'nome',NEW.nome)); END$$
CREATE TRIGGER ad_familiares_auditar AFTER DELETE ON familiares FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores) VALUES('DELETE','familiares',OLD.id,@asilosoft_usuario_id,JSON_OBJECT('usuario_id',OLD.usuario_id,'nome',OLD.nome)); END$$
CREATE TRIGGER ai_if_auditar AFTER INSERT ON idosos_familiares FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_novos) VALUES('INSERT','idosos_familiares',NEW.familiar_id,@asilosoft_usuario_id,JSON_OBJECT('idoso_id',NEW.idoso_id,'familiar_id',NEW.familiar_id,'parentesco',NEW.parentesco)); END$$
CREATE TRIGGER au_if_auditar AFTER UPDATE ON idosos_familiares FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores,valores_novos) VALUES('UPDATE','idosos_familiares',NEW.familiar_id,@asilosoft_usuario_id,JSON_OBJECT('idoso_id',OLD.idoso_id,'parentesco',OLD.parentesco),JSON_OBJECT('idoso_id',NEW.idoso_id,'parentesco',NEW.parentesco)); END$$
CREATE TRIGGER ad_if_auditar AFTER DELETE ON idosos_familiares FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores) VALUES('DELETE','idosos_familiares',OLD.familiar_id,@asilosoft_usuario_id,JSON_OBJECT('idoso_id',OLD.idoso_id,'familiar_id',OLD.familiar_id)); END$$
CREATE TRIGGER ai_rs_auditar AFTER INSERT ON registros_saude FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_novos) VALUES('INSERT','registros_saude',NEW.id,COALESCE(@asilosoft_usuario_id,NEW.usuario_id),JSON_OBJECT('idoso_id',NEW.idoso_id,'registrado_em',NEW.registrado_em,'visivel_para_familiar',NEW.visivel_para_familiar)); END$$
CREATE TRIGGER au_rs_auditar AFTER UPDATE ON registros_saude FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores,valores_novos) VALUES('UPDATE','registros_saude',NEW.id,COALESCE(@asilosoft_usuario_id,NEW.usuario_id),JSON_OBJECT('idoso_id',OLD.idoso_id,'registrado_em',OLD.registrado_em,'visivel_para_familiar',OLD.visivel_para_familiar),JSON_OBJECT('idoso_id',NEW.idoso_id,'registrado_em',NEW.registrado_em,'visivel_para_familiar',NEW.visivel_para_familiar)); END$$
CREATE TRIGGER ad_rs_auditar AFTER DELETE ON registros_saude FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores) VALUES('DELETE','registros_saude',OLD.id,COALESCE(@asilosoft_usuario_id,OLD.usuario_id),JSON_OBJECT('idoso_id',OLD.idoso_id,'registrado_em',OLD.registrado_em,'visivel_para_familiar',OLD.visivel_para_familiar)); END$$
CREATE TRIGGER ai_materiais_auditar AFTER INSERT ON materiais FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_novos) VALUES('INSERT','materiais',NEW.id,@asilosoft_usuario_id,JSON_OBJECT('nome',NEW.nome,'ativo',NEW.ativo)); END$$
CREATE TRIGGER au_materiais_auditar AFTER UPDATE ON materiais FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores,valores_novos) VALUES('UPDATE','materiais',NEW.id,@asilosoft_usuario_id,JSON_OBJECT('nome',OLD.nome,'ativo',OLD.ativo),JSON_OBJECT('nome',NEW.nome,'ativo',NEW.ativo)); END$$
CREATE TRIGGER ad_materiais_auditar AFTER DELETE ON materiais FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores) VALUES('DELETE','materiais',OLD.id,@asilosoft_usuario_id,JSON_OBJECT('nome',OLD.nome,'ativo',OLD.ativo)); END$$
CREATE TRIGGER ai_sc_auditar AFTER INSERT ON solicitacoes_compra FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_novos) VALUES('INSERT','solicitacoes_compra',NEW.id,@asilosoft_usuario_id,JSON_OBJECT('material_id',NEW.material_id,'quantidade',NEW.quantidade,'situacao',NEW.situacao)); END$$
CREATE TRIGGER au_sc_auditar AFTER UPDATE ON solicitacoes_compra FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores,valores_novos) VALUES('UPDATE','solicitacoes_compra',NEW.id,@asilosoft_usuario_id,JSON_OBJECT('quantidade',OLD.quantidade,'situacao',OLD.situacao),JSON_OBJECT('quantidade',NEW.quantidade,'situacao',NEW.situacao)); END$$
CREATE TRIGGER ad_sc_auditar AFTER DELETE ON solicitacoes_compra FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_anteriores) VALUES('DELETE','solicitacoes_compra',OLD.id,@asilosoft_usuario_id,JSON_OBJECT('material_id',OLD.material_id,'quantidade',OLD.quantidade)); END$$
CREATE TRIGGER ai_um_auditar AFTER INSERT ON usos_materiais FOR EACH ROW BEGIN INSERT INTO logs_auditoria(operacao,tabela,registro_id,usuario_id,valores_novos) VALUES('INSERT','usos_materiais',NEW.id,COALESCE(@asilosoft_usuario_id,NEW.usuario_id),JSON_OBJECT('idoso_id',NEW.idoso_id,'material_id',NEW.material_id,'quantidade',NEW.quantidade,'usado_em',NEW.usado_em)); END$$
DELIMITER ;

CREATE TABLE IF NOT EXISTS controle_administradores (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  quantidade INT UNSIGNED NOT NULL,
  CONSTRAINT chk_controle_admin_id CHECK (id = 1)
) ENGINE=InnoDB;

INSERT IGNORE INTO controle_administradores (id, quantidade)
SELECT 1, COUNT(*) FROM usuarios WHERE perfil='Administrador' AND ativo=1;

DROP TRIGGER IF EXISTS ai_usuarios_contar_admin;
DROP TRIGGER IF EXISTS bu_usuarios_preservar_admin;
DROP TRIGGER IF EXISTS bd_usuarios_preservar_admin;

DELIMITER $$
CREATE TRIGGER ai_usuarios_contar_admin AFTER INSERT ON usuarios FOR EACH ROW
BEGIN
  IF NEW.perfil='Administrador' AND NEW.ativo=1 THEN
    UPDATE controle_administradores SET quantidade=quantidade+1 WHERE id=1;
  END IF;
END $$

CREATE TRIGGER bu_usuarios_preservar_admin BEFORE UPDATE ON usuarios FOR EACH ROW
BEGIN
  IF OLD.perfil='Administrador' AND OLD.ativo=1
     AND NOT (NEW.perfil='Administrador' AND NEW.ativo=1) THEN
    UPDATE controle_administradores SET quantidade=quantidade-1
    WHERE id=1 AND quantidade>1;
    IF ROW_COUNT()=0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Mantenha ao menos um administrador ativo.';
    END IF;
  ELSEIF NOT (OLD.perfil='Administrador' AND OLD.ativo=1)
     AND NEW.perfil='Administrador' AND NEW.ativo=1 THEN
    UPDATE controle_administradores SET quantidade=quantidade+1 WHERE id=1;
  END IF;
END $$

CREATE TRIGGER bd_usuarios_preservar_admin BEFORE DELETE ON usuarios FOR EACH ROW
BEGIN
  IF OLD.perfil='Administrador' AND OLD.ativo=1 THEN
    UPDATE controle_administradores SET quantidade=quantidade-1
    WHERE id=1 AND quantidade>1;
    IF ROW_COUNT()=0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Mantenha ao menos um administrador ativo.';
    END IF;
  END IF;
END $$
DELIMITER ;

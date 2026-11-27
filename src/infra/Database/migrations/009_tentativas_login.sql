CREATE TABLE IF NOT EXISTS tentativas_login (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  chave CHAR(64) NOT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_tentativas_chave_data (chave, criado_em)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

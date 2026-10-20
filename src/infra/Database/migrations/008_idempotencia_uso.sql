ALTER TABLE usos_materiais ADD COLUMN chave_requisicao CHAR(64) NULL, ADD UNIQUE KEY uq_uso_requisicao (chave_requisicao);

ALTER TABLE materiais
  ADD COLUMN tipo ENUM('Material', 'Insumo', 'Medicamento') NOT NULL DEFAULT 'Material' AFTER nome;

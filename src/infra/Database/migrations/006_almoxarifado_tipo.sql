SET @asilo_tipo_sql = IF(
    EXISTS (SELECT 1 FROM information_schema.columns
            WHERE table_schema = DATABASE()
              AND table_name = 'materiais' AND column_name = 'tipo'),
    'SELECT 1',
    'ALTER TABLE materiais ADD COLUMN tipo ENUM(''Material'', ''Insumo'', ''Medicamento'') NOT NULL DEFAULT ''Material'' AFTER nome'
);
PREPARE asilo_tipo_stmt FROM @asilo_tipo_sql;
EXECUTE asilo_tipo_stmt;
DEALLOCATE PREPARE asilo_tipo_stmt;

CREATE OR REPLACE VIEW vw_ficha_idoso AS
SELECT i.id AS idoso_id,i.nome,i.cpf,i.identificador,i.ativo,q.id AS quarto_id,q.codigo AS quarto_codigo,q.capacidade AS quarto_capacidade,
 f.id AS familiar_id,f.nome AS familiar_nome,f.telefone AS familiar_telefone,f.email AS familiar_email,ifm.parentesco,ifm.responsavel_principal
FROM idosos i LEFT JOIN quartos q ON q.id=i.quarto_id LEFT JOIN idosos_familiares ifm ON ifm.idoso_id=i.id LEFT JOIN familiares f ON f.id=ifm.familiar_id;
CREATE OR REPLACE VIEW vw_estoque_atual AS
SELECT m.id AS material_id,m.nome,m.descricao,m.unidade_medida,m.estoque_minimo,m.ativo,e.quantidade_atual,
 (e.quantidade_atual < m.estoque_minimo) AS abaixo_minimo FROM materiais m JOIN estoques e ON e.material_id=m.id;
CREATE OR REPLACE VIEW vw_extrato_estoque AS
SELECT me.id,me.material_id,m.nome AS material_nome,me.tipo,me.quantidade,me.origem,me.uso_material_id,me.usuario_id,u.nome AS usuario_nome,me.observacao,me.movimentado_em
FROM movimentacoes_estoque me JOIN materiais m ON m.id=me.material_id LEFT JOIN usuarios u ON u.id=me.usuario_id;
CREATE OR REPLACE VIEW vw_acompanhamento_familiar AS
SELECT ifm.familiar_id,rs.id,rs.idoso_id,rs.usuario_id,rs.descricao,rs.visivel_para_familiar,rs.registrado_em
FROM idosos_familiares ifm JOIN registros_saude rs ON rs.idoso_id=ifm.idoso_id WHERE rs.visivel_para_familiar=1;

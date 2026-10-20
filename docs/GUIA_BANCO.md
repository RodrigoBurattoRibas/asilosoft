# Guia do banco AsiloSoft

O banco usa PHP/PDO e MySQL 8.0.16+; não requer framework ou ORM. A fonte de verdade é `src/infra/Database/migrations/`. Execute `php src/infra/Database/bootstrap.php` para criar/atualizar a base configurada. Faça backup antes de migrar; uma reversão é sempre uma nova migração compensatória, nunca a edição de uma versão aplicada.

Após configurar o `.env`, execute `php src/infra/Database/bootstrap.php`. O comando aplica as migrations pendentes de forma idempotente. O sistema não cria dados fictícios nem usa repositórios alternativos em sessão.

## Relações e regras

`idosos -> quartos`; `familiares <-> idosos` por `idosos_familiares`; `registros_saude` pertence a idoso e autor; `materiais -> estoques`; usos geram uma saída imutável e solicitações pertencem a material e solicitante. Entradas exigem origem `SUS`, `Plano de Saúde` ou `Instituição`; saídas têm origem nula. `estoques.quantidade_atual` é projeção do livro `movimentacoes_estoque` e não pode ser editado pela aplicação.

A tabela `logs_auditoria` registra metadados de alterações, sem descrições clínicas nem senhas. Os controllers de familiares e compras configuram `@asilosoft_usuario_id` após autenticação para as triggers; saúde e uso também registram o autor no próprio registro. Os repositories abrem transações PDO onde há múltiplas escritas/bloqueios. Não existe helper `PDOMysqli::transactional` nesta implementação.

## Consultas publicadas

Use PDO e parâmetros, nunca acesso direto do navegador. `vw_ficha_idoso` suporta busca por nome, CPF ou identificador. `vw_estoque_atual` suporta filtro por `abaixo_minimo`; `vw_extrato_estoque` por material e intervalo; `vw_acompanhamento_familiar` por familiar e idoso. Histórico clínico deve consultar `registros_saude` por `(idoso_id, registrado_em DESC, id DESC)`.

As views não concedem autorização. As classes PHP devem associar conta apenas ao perfil `Familiar`, permitir escrita clínica apenas a `Profissional de Saúde` autorizado e filtrar acompanhamento familiar pela view visível.

## Teste

Defina explicitamente `ASILOSOFT_TEST_DB_HOST`, `ASILOSOFT_TEST_DB_PORTA`, `ASILOSOFT_TEST_DB_NOME`, `ASILOSOFT_TEST_DB_USUARIO` e `ASILOSOFT_TEST_DB_SENHA`, com nome iniciado por `asilosoft_test_`, e execute `php tests/integration/database/DatabaseIntegrationTest.php`. O runner recria somente essa base de teste.

## Atualização de 20/10/2026

As migrations 001–006 permanecem inalteradas. A 007 converge `materiais.tipo` para VARCHAR(30), preservando dados, independentemente da ordem histórica das duas 006. A 008 adiciona `usos_materiais.chave_requisicao` CHAR(64) nullable/única. Usos existentes continuam com NULL e novas submissões web usam chave aleatória persistida.

O migrator consome o cursor de instruções condicionais antes da próxima instrução. Instalação nova, atualização com tipo ENUM histórico e segunda execução foram testadas com saldo/histórico preservados. As transações de consumo não gravam saída manual: a trigger existente é responsável pela baixa única.

Consulte `tests/README.md` para os runners e cuidados com bases descartáveis.

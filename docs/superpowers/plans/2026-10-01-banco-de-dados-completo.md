# Banco de Dados Completo do AsiloSoft Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Entregar uma base MySQL versionada, íntegra, testável e documentada que suporte todos os requisitos de familiares, saúde, estoque, uso de materiais, compras, auditoria e consultas do AsiloSoft, pronta para os módulos PHP próprios já adotados pelo projeto.

**Architecture:** O banco será a fonte de verdade para entidades e invariantes transacionais; o saldo de estoque será uma projeção atualizada exclusivamente pelo livro de movimentações. As regras dependentes da identidade autenticada continuarão nas classes PHP próprias da aplicação, mas o schema persistirá autor, vínculo e visibilidade suficientes para que elas apliquem a autorização sem ambiguidade. A entrega preserva as tabelas, colunas e contratos já usados por `usuarios`, `idosos`, `quartos`, `PDOMysqli` e seus repositórios; migrações só acrescentam mecanismos compatíveis e o `schema.sql` continua sendo o bootstrap para instalações novas.

**Tech Stack:** PHP 8 nativo, `Environment`, `PDOMysqli`, repositórios e serviços próprios já presentes, PDO com `mysql`, MySQL 8.0.16+ (CHECK constraints aplicadas) e scripts SQL/PHP sem Composer.

**Spec:** `GUIA_TECNICO.md`; `OBJETIVOS_E_REQUISITOS.md`

## Global Constraints

- Preservar PHP sem framework, PDO com prepared statements nativos e codificação `utf8mb4`.
- Não adicionar Spring Boot, Java, Maven, Gradle, Composer, bibliotecas de migração, ORM, framework PHP ou qualquer outra dependência externa; usar somente PHP nativo, PDO/MySQL e os arquivos/contratos já existentes em `src/`.
- Não implementar rotas, controllers, services, repositories, views ou endpoints dos módulos futuros nesta entrega.
- Não renomear, remover ou alterar a semântica de colunas já consumidas por `AsiloUsuarioRepository`, `AsiloIdosoRepository`, `AsiloQuartoRepository` ou pelos repositórios de sessão.
- Não criar módulos de faturamento, RH, chat, videochamada, integrações externas, aplicativo móvel, prescrição, anexos clínicos, multi-instituição ou novos perfis de usuário.
- A única tabela de logs do escopo é `logs_auditoria`; logs técnicos de PHP, sessão, acesso ou erro continuam responsabilidade da infraestrutura e não serão persistidos no domínio.
- Não gravar senhas, dados reais de pessoas ou dados clínicos completos nos logs de auditoria ou nos seeders.
- Tratar `movimentacoes_estoque` como histórico imutável e `estoques.quantidade_atual` como projeção não editável pela aplicação.
- Toda entrada exige uma origem entre `SUS`, `Plano de Saúde` e `Instituição`; toda saída deve ter origem nula.
- Os scripts de teste só podem apagar/recriar o banco cujo nome vier explicitamente de `ASILOSOFT_TEST_DB_NOME`; nunca a base operacional.
- Continuar compatível com os contratos atuais de `usuarios`, `idosos`, `quartos`, `PDOMysqli` e repositórios existentes.
- Documentar que permissões por perfil e a filtragem do familiar serão aplicadas pelas classes PHP próprias da aplicação, ainda que o banco forneça os vínculos e consultas necessárias.

## Review Focus

- Concorrência: duas saídas simultâneas para o último saldo disponível devem resultar em no máximo uma operação bem-sucedida; teste na Task 4.
- Privacidade: uma consulta destinada a familiar nunca pode retornar registro de saúde com `visivel_para_familiar = 0`; teste na Task 5.
- Idempotência: executar bootstrap e seed duas vezes não pode duplicar vínculos, movimentações nem alterar o saldo; teste na Task 6.
- Integridade cruzada: uma movimentação ligada a uso precisa ter o mesmo material, quantidade e tipo de saída do uso; teste na Task 4.
- Evolução: aplicar as migrações em banco já criado e em banco vazio deve produzir o mesmo conjunto de tabelas, índices, views e triggers; teste na Task 7.

## File Structure

- Create: `src/infra/Database/migrations/001_base_atual.sql` — baseline idempotente das três entidades já consumidas pela aplicação.
- Create: `src/infra/Database/migrations/002_dominio_assistencial.sql` — familiares, vínculos, saúde e índices de consulta.
- Create: `src/infra/Database/migrations/003_estoque_e_compras.sql` — catálogo, projeção de saldo, movimentações, usos e solicitações.
- Create: `src/infra/Database/migrations/004_auditoria_e_invariantes.sql` — triggers de integridade, imutabilidade e auditoria.
- Create: `src/infra/Database/migrations/005_consultas_publicadas.sql` — views de leitura para ficha, estoque e acompanhamento visível.
- Create: `src/infra/Database/DatabaseMigrator.php` — aplica migrações em ordem e registra versões.
- Create: `src/infra/Database/bootstrap.php` — CLI para criar/atualizar uma base configurada e executar migrações.
- Modify: `src/infra/Database/schema.sql` — torná-lo apenas o bootstrap para banco vazio que chama/concatena as mesmas migrações, ou substituí-lo por instrução de compatibilidade que aponta para `bootstrap.php`; não manter duas fontes de verdade.
- Modify: `src/infra/Database/Database.php` — expor helper transacional que define `@asilosoft_usuario_id` antes de escritas auditáveis, sem alterar o contrato de `PDOMysqli::connection()`.
- Modify: `src/infra/Database/seed_admin.php` — usar `Environment` e `PDOMysqli` atuais, criar ou atualizar somente o administrador configurado.
- Modify: `src/infra/Database/seed_dados_ficticios.php` — usar a infraestrutura atual e semear dados determinísticos, transacionais e idempotentes.
- Create: `tests/integration/database/DatabaseIntegrationTest.php` — runner PHP/PDO sem dependência externa para DDL, seed e invariantes.
- Create: `docs/GUIA_BANCO.md` — modelo relacional, contratos de leitura/escrita, variáveis, comandos e operação.
- Modify: `README.md`, `.env.example` — comandos e variáveis de desenvolvimento/admin/teste coerentes com os scripts reais.

### Task 1: Definir o ciclo de vida versionado do banco

**Files:**
- Create: `src/infra/Database/migrations/001_base_atual.sql`
- Create: `src/infra/Database/DatabaseMigrator.php`
- Create: `src/infra/Database/bootstrap.php`
- Modify: `src/infra/Database/schema.sql`
- Test: `tests/integration/database/DatabaseIntegrationTest.php`

**Interfaces:**
- Consumes: `PDOMysqli::connection(): PDO` e as variáveis `ASILOSOFT_DB_*` existentes.
- Produces: `DatabaseMigrator::migrate(PDO $pdo, string $migrationDirectory): array` e CLI `php src/infra/Database/bootstrap.php`.

- [ ] **Step 1: Escrever o teste de banco vazio e de segunda execução**

```php
assertSame(['001_base_atual'], $migrator->migrate($pdo, $migrationDirectory));
assertSame([], $migrator->migrate($pdo, $migrationDirectory));
assertTableExists($pdo, 'schema_migrations');
```

- [ ] **Step 2: Executar o runner para confirmar que falha sem o migrador**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=migrations`
Expected: FAIL informando que `DatabaseMigrator` ou `schema_migrations` não existe.

- [ ] **Step 3: Implementar `DatabaseMigrator::migrate(PDO $pdo, string $migrationDirectory): array`**

Criar `schema_migrations` com `versao VARCHAR(100) PRIMARY KEY` e `aplicada_em`; ordenar arquivos `NNN_*.sql`, executar cada arquivo ainda não registrado em transação quando o tipo de DDL permitir, registrar a versão somente após sucesso e retornar as versões aplicadas. O executor deve reconhecer `DELIMITER` para criar triggers e recusar arquivos fora do diretório de migrações.

- [ ] **Step 4: Extrair o baseline de `quartos`, `idosos` e `usuarios` para `001_base_atual.sql`**

Manter nomes, chaves, defaults e colunas utilizados por `AsiloUsuarioRepository`, `AsiloIdosoRepository` e `AsiloQuartoRepository`. Remover `CREATE DATABASE` e `USE` das migrações; a CLI recebe a conexão já apontada à base alvo. Fazer de `schema.sql` um adaptador documentado que não duplique o DDL.

- [ ] **Step 5: Implementar a CLI de bootstrap e reexecutar o teste**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=migrations`
Expected: PASS; uma segunda chamada não reaplica nenhuma versão.

- [ ] **Step 6: Commit**

```bash
git add src/infra/Database/migrations src/infra/Database/DatabaseMigrator.php src/infra/Database/bootstrap.php src/infra/Database/schema.sql tests/integration/database/DatabaseIntegrationTest.php
git commit -m "feat(database): versiona bootstrap e migrações"
```

### Task 2: Consolidar cadastros assistenciais e contratos de identidade

**Files:**
- Create: `src/infra/Database/migrations/002_dominio_assistencial.sql`
- Modify: `docs/GUIA_BANCO.md`
- Test: `tests/integration/database/DatabaseIntegrationTest.php`

**Interfaces:**
- Consumes: `usuarios(id, perfil, ativo)`, `idosos(id, ativo)` da Task 1.
- Produces: tabelas `familiares`, `idosos_familiares`, `registros_saude` e consultas indexadas por `idoso_id`/data.

- [ ] **Step 1: Escrever testes para vínculo familiar e autoria clínica**

```php
assertPrimaryKey($pdo, 'idosos_familiares', ['idoso_id', 'familiar_id']);
assertForeignKey($pdo, 'familiares', 'usuario_id', 'usuarios', 'id');
assertIndexExists($pdo, 'registros_saude', 'idx_rs_idoso_data');
assertQueryCount($pdo, 'SELECT COUNT(*) FROM idosos_familiares WHERE idoso_id = ? AND familiar_id = ?', 1);
```

- [ ] **Step 2: Executar o caso assistencial e confirmar a falha inicial**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=assistencial`
Expected: FAIL por ausência das tabelas ou constraints assistenciais.

- [ ] **Step 3: Criar `familiares` e `idosos_familiares` com a cardinalidade prevista**

`familiares` terá `id`, `usuario_id` opcional e único, nome, CPF opcional único, telefone obrigatório, e-mail opcional, parentesco principal opcional e timestamps. `idosos_familiares` terá a PK composta, `parentesco`, `responsavel_principal` e timestamp; seus FKs terão `ON DELETE CASCADE`. Permitir um familiar em muitos idosos (RN03) e preservar um familiar sem conta de login.

- [ ] **Step 4: Criar `registros_saude` editável e rastreável**

Incluir `id`, `idoso_id`, `usuario_id`, `descricao`, `visivel_para_familiar`, `registrado_em`, `criado_em` e `atualizado_em`. Criar o índice `(idoso_id, registrado_em DESC, id DESC)` e índice de autor quando suportado pela consulta. O registro não pode expor conteúdo clínico via auditoria; visibilidade é atributo obrigatório para as classes PHP aplicarem RN08/RNF04.

- [ ] **Step 5: Registrar no guia os contratos futuros de escrita e consulta**

Documentar payloads/linhas retornadas para `familiar`, `vinculo_familiar` e `registro_saude`, as buscas da ficha por `nome`, `cpf` ou `identificador`, e a regra: as classes PHP só associam `usuario_id` de perfil `Familiar`; só permitem escrita clínica a perfil `Profissional de Saúde` autorizado.

- [ ] **Step 6: Executar o caso assistencial**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=assistencial`
Expected: PASS para vínculo muitos-para-muitos, unicidade do vínculo, FK de usuário e ordenação de histórico.

- [ ] **Step 7: Commit**

```bash
git add src/infra/Database/migrations/002_dominio_assistencial.sql docs/GUIA_BANCO.md tests/integration/database/DatabaseIntegrationTest.php
git commit -m "feat(database): adiciona domínio familiar e clínico"
```

### Task 3: Modelar catálogo, compras e o livro de estoque

**Files:**
- Create: `src/infra/Database/migrations/003_estoque_e_compras.sql`
- Modify: `docs/GUIA_BANCO.md`
- Test: `tests/integration/database/DatabaseIntegrationTest.php`

**Interfaces:**
- Consumes: `usuarios`, `idosos` (Task 1) e o autor de saúde (Task 2).
- Produces: `materiais`, `estoques`, `movimentacoes_estoque`, `usos_materiais`, `solicitacoes_compra` e seus índices/FKs.

- [ ] **Step 1: Escrever testes de DDL de estoque e compra**

```php
assertSame(0.000, saldoDoMaterial($pdo, $materialId));
assertRejects($pdo, 'INSERT INTO movimentacoes_estoque (...) VALUES ("Entrada", 1, NULL, ...)', 'origem');
assertRejects($pdo, 'INSERT INTO movimentacoes_estoque (...) VALUES ("Saída", 1, "SUS", ...)', 'origem');
assertRejects($pdo, 'INSERT INTO solicitacoes_compra (...) VALUES (..., 0, ...)', 'quantidade');
```

- [ ] **Step 2: Executar o caso estrutural de estoque para confirmar a falha**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=estoque-schema`
Expected: FAIL por inexistência das tabelas de estoque/compras.

- [ ] **Step 3: Criar o catálogo e a projeção de saldo**

`materiais` preservará as colunas existentes: nome único, descrição, unidade de medida, estoque mínimo não negativo, ativo e timestamps. Materiais, insumos e medicamentos usam o mesmo catálogo já previsto; não adicionar categoria, SKU, lote, validade ou prescrição, pois não são requisitos do projeto. `estoques` terá `material_id` como PK, `quantidade_atual DECIMAL(12,3)` não negativa e timestamp; ela não receberá campo de edição funcional. Indexar materiais ativos por nome e itens abaixo do mínimo pela consulta de saldo.

- [ ] **Step 4: Criar usos e movimentações com semântica fechada**

`movimentacoes_estoque` preservará suas colunas planejadas: material, `tipo ENUM('Entrada','Saída')`, quantidade positiva, origem condicional, referência única/opcional ao uso, usuário, observação e data. `usos_materiais` terá idoso, material, usuário, quantidade positiva, observação, data e timestamps. Indexar histórico por `(material_id, movimentado_em, id)` e uso por `(idoso_id, usado_em, id)`.

- [ ] **Step 5: Preservar o ciclo de solicitações de compra planejado**

Manter `solicitacoes_compra` com material, solicitante, quantidade positiva, situação `Pendente`, `Aprovada`, `Recusada` ou `Atendida`, observação e timestamps. A tabela registra a solicitação institucional (RF08), sem introduzir aprovação financeira, fornecedores, orçamento, pedido de compra ou fluxo de pagamento, que estão fora do escopo.

- [ ] **Step 6: Atualizar o dicionário de dados**

Explicar que materiais, insumos e medicamentos compartilham o catálogo, que entradas são a única origem de saldo positivo, que o uso gera saída vinculada ao idoso e que uma saída avulsa não deve ter `uso_material_id`. Publicar os filtros e ordenações esperados para telas de catálogo, baixo estoque, extrato e compras.

- [ ] **Step 7: Executar o caso estrutural**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=estoque-schema`
Expected: PASS para checks de quantidade/origem, FKs e saldo inicial zero.

- [ ] **Step 8: Commit**

```bash
git add src/infra/Database/migrations/003_estoque_e_compras.sql docs/GUIA_BANCO.md tests/integration/database/DatabaseIntegrationTest.php
git commit -m "feat(database): estrutura estoque, usos e compras"
```

### Task 4: Aplicar invariantes transacionais e auditoria minimizada

**Files:**
- Create: `src/infra/Database/migrations/004_auditoria_e_invariantes.sql`
- Modify: `src/infra/Database/Database.php`
- Modify: `docs/GUIA_BANCO.md`
- Test: `tests/integration/database/DatabaseIntegrationTest.php`

**Interfaces:**
- Consumes: tabelas de Tasks 1–3.
- Produces: `PDOMysqli::transactional(int $usuarioId, callable $operation): mixed`, `logs_auditoria` e triggers de ocupação, saldo, projeção, uso e auditoria.

- [ ] **Step 1: Escrever testes de regras de domínio e trilha**

```php
assertRejects($pdo, $saidaAcimaDoSaldo, 'Estoque insuficiente');
assertSame(9.000, saldoDoMaterial($pdo, $materialId));
assertMovementMatchesUse($pdo, $usoId, $materialId, 1.000, 'Saída');
assertAuditDoesNotContain($pdo, 'registro clínico confidencial');
assertRejects($pdo, 'UPDATE movimentacoes_estoque SET quantidade = 99 WHERE id = ?', 'imutável');
```

- [ ] **Step 2: Executar o caso de invariantes e confirmar que falha**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=invariantes`
Expected: FAIL porque os triggers e o contexto de auditoria ainda não existem.

- [ ] **Step 3: Implementar os triggers de quarto e de vínculo de identidade**

Portar as validações de quarto ativo/capacidade para INSERT e UPDATE de idoso, incluindo reativação. Adicionar triggers que rejeitam `familiares.usuario_id` cujo usuário não tenha perfil `Familiar`, e `registros_saude.usuario_id` cujo autor não tenha perfil `Profissional de Saúde`; não impedir a leitura histórica se esses usuários forem posteriormente desativados.

- [ ] **Step 4: Implementar o livro de estoque e sua imutabilidade**

Após criar material, inserir saldo zero. Antes de inserir saída, bloquear saldo insuficiente com bloqueio da linha de `estoques`; após toda movimentação, projetar o delta no saldo. Após inserir uso, criar exatamente uma saída ligada ao uso; antes de aceitar movimento com `uso_material_id`, validar correspondência de material, quantidade e tipo. Rejeitar UPDATE e DELETE de movimentos e usos para preservar rastreabilidade; correções serão novas movimentações compensatórias pelas classes PHP próprias.

- [ ] **Step 5: Implementar a auditoria sem vazar conteúdo clínico**

Criar e manter `logs_auditoria` como a tabela de log de domínio, com operação, tabela, registro, usuário, JSON anterior/novo e índices por registro/data. Auditar INSERT/UPDATE/DELETE de familiares, vínculos, materiais, solicitações, usos e registros de saúde; para saúde, gravar apenas ids, datas e o marcador de visibilidade. Nos triggers, obter o ator de `@asilosoft_usuario_id`, podendo ficar nulo apenas em bootstrap/seed administrativo. Não criar log de acesso, log de sessão ou log de erro nesta base.

- [ ] **Step 6: Implementar `PDOMysqli::transactional(int $usuarioId, callable $operation): mixed`**

Abrir transação, executar `SET @asilosoft_usuario_id = :id`, invocar a operação com o PDO, confirmar em sucesso e desfazer em qualquer `Throwable`; limpar a variável da conexão após commit/rollback. Documentar que todo serviço PHP próprio futuro que altere dados auditáveis deve usar esse método, sem introduzir framework ou biblioteca.

- [ ] **Step 7: Executar testes de invariantes, incluindo concorrência**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=invariantes --case=concorrencia`
Expected: PASS; saída acima do saldo, alteração/exclusão de livro e vínculos incompatíveis falham; a soma do saldo permanece correta.

- [ ] **Step 8: Commit**

```bash
git add src/infra/Database/migrations/004_auditoria_e_invariantes.sql src/infra/Database/Database.php docs/GUIA_BANCO.md tests/integration/database/DatabaseIntegrationTest.php
git commit -m "feat(database): protege saldo e adiciona auditoria"
```

### Task 5: Publicar consultas estáveis para os módulos PHP próprios

**Files:**
- Create: `src/infra/Database/migrations/005_consultas_publicadas.sql`
- Modify: `docs/GUIA_BANCO.md`
- Test: `tests/integration/database/DatabaseIntegrationTest.php`

**Interfaces:**
- Consumes: domínio e invariantes de Tasks 1–4.
- Produces: views `vw_ficha_idoso`, `vw_estoque_atual`, `vw_extrato_estoque` e `vw_acompanhamento_familiar`, além de contratos de filtros parametrizados.

- [ ] **Step 1: Escrever testes dos resultados publicados**

```php
assertSame($idosoId, fetchOne($pdo, 'SELECT idoso_id FROM vw_ficha_idoso WHERE idoso_id = ?', [$idosoId])['idoso_id']);
assertSame(9.000, fetchOne($pdo, 'SELECT quantidade_atual FROM vw_estoque_atual WHERE material_id = ?', [$materialId])['quantidade_atual']);
assertSame(0, countRows($pdo, 'SELECT * FROM vw_acompanhamento_familiar WHERE familiar_id = ? AND id = ?', [$familiarId, $registroPrivadoId]));
```

- [ ] **Step 2: Executar o caso de consultas e confirmar a falha**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=consultas`
Expected: FAIL por ausência das views.

- [ ] **Step 3: Criar as views somente de leitura**

`vw_ficha_idoso` reunirá campos não clínicos do idoso, quarto e contatos/vínculos familiares sem duplicar o histórico; `vw_estoque_atual` exporá catálogo, saldo, mínimo e indicador `abaixo_minimo`; `vw_extrato_estoque` unirá movimento, origem, autor e referência de uso/compra; `vw_acompanhamento_familiar` unirá vínculo e registros de saúde exclusivamente onde `visivel_para_familiar = 1`.

- [ ] **Step 4: Documentar consultas parametrizadas que os repositórios PHP próprios devem usar**

Definir nomes e parâmetros: `buscarFicha(string $termo)`, `listarHistoricoSaude(int $idosoId, ?DateTimeInterface $de, ?DateTimeInterface $ate)`, `listarAcompanhamentoFamiliar(int $familiarId, int $idosoId)`, `listarEstoque(bool $somenteAbaixoMinimo)`, e `listarExtrato(int $materialId, ?DateTimeInterface $de, ?DateTimeInterface $ate)`. Esclarecer que as views não substituem autorização nos serviços PHP próprios e que as views são acessadas somente por PDO, nunca pelo navegador.

- [ ] **Step 5: Executar o caso de consultas**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=consultas`
Expected: PASS; ficha e extrato retornam dados esperados e a view familiar exclui registros privados.

- [ ] **Step 6: Commit**

```bash
git add src/infra/Database/migrations/005_consultas_publicadas.sql docs/GUIA_BANCO.md tests/integration/database/DatabaseIntegrationTest.php
git commit -m "feat(database): publica consultas de domínio"
```

### Task 6: Corrigir e tornar reproduzíveis os dados operacionais e de demonstração

**Files:**
- Modify: `src/infra/Database/seed_admin.php`
- Modify: `src/infra/Database/seed_dados_ficticios.php`
- Modify: `.env.example`
- Modify: `docs/GUIA_BANCO.md`
- Test: `tests/integration/database/DatabaseIntegrationTest.php`

**Interfaces:**
- Consumes: `Environment::get()`, `PDOMysqli::connection()`, migrações e `PDOMysqli::transactional()`.
- Produces: CLI `php src/infra/Database/seed_admin.php` e CLI `php src/infra/Database/seed_dados_ficticios.php` idempotentes.

- [ ] **Step 1: Escrever o teste de seed determinístico**

```php
runSeed($pdo);
$primeiroSaldo = saldoDoMaterial($pdo, $materialId);
runSeed($pdo);
assertSame($primeiroSaldo, saldoDoMaterial($pdo, $materialId));
assertSame(1, countRows($pdo, 'SELECT * FROM usos_materiais WHERE observacao = ?', ['Uso fictício']));
```

- [ ] **Step 2: Executar o caso de seed para confirmar a falha atual**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=seed`
Expected: FAIL porque os scripts atuais dependem de `app/` e `config/` que não existem nesta estrutura.

- [ ] **Step 3: Reescrever o seed administrativo na infraestrutura atual**

Ler `ASILOSOFT_ADMIN_EMAIL`, `ASILOSOFT_ADMIN_NOME` e `ASILOSOFT_ADMIN_SENHA`; falhar sem expor senha se estiverem ausentes; normalizar e-mail; criar ou atualizar o usuário administrador de forma explícita com `password_hash`, preservando as demais contas.

- [ ] **Step 4: Reescrever o seed fictício com chaves naturais estáveis**

Usar `PDOMysqli`, transação e buscas por e-mail/código/CPF/nome de material; criar usuários de cada perfil, quarto, idosos, familiar/vínculo, registro visível, materiais fictícios, entradas das três origens, um uso e uma solicitação. Não usar `INSERT IGNORE` para esconder erro de integridade: consultar antes e inserir apenas o dado ausente. Cada nova linha deve obedecer triggers e a referência de usuário auditável.

- [ ] **Step 5: Atualizar variáveis e documentação operacional**

Adicionar apenas nomes de variáveis, sem valores secretos, para admin e base de teste. Documentar a sequência `bootstrap -> seed_admin opcional -> seed_dados_ficticios opcional` e que os dados são exclusivamente fictícios.

- [ ] **Step 6: Executar o caso de seed**

Run: `php tests/integration/database/DatabaseIntegrationTest.php --case=seed`
Expected: PASS; segunda execução preserva contagens e saldo, e todas as entidades previstas recebem dados de demonstração.

- [ ] **Step 7: Commit**

```bash
git add src/infra/Database/seed_admin.php src/infra/Database/seed_dados_ficticios.php .env.example docs/GUIA_BANCO.md tests/integration/database/DatabaseIntegrationTest.php
git commit -m "fix(database): torna seeders reproduzíveis"
```

### Task 7: Validar integração e concluir a documentação de handoff

**Files:**
- Modify: `tests/integration/database/DatabaseIntegrationTest.php`
- Modify: `docs/GUIA_BANCO.md`
- Modify: `README.md`
- Modify: `GUIA_TECNICO.md`

**Interfaces:**
- Consumes: todas as entregas anteriores.
- Produces: comando único de verificação e documentação suficiente para infraestrutura e para os módulos PHP próprios do projeto.

- [ ] **Step 1: Completar o runner com proteção de alvo e matriz de casos**

```php
assertTrue(str_starts_with($testDatabaseName, 'asilosoft_test_'));
assertThrows(fn () => runTestsWithDatabaseName('asilosoft'), RuntimeException::class);
runAllCases();
```

- [ ] **Step 2: Executar o runner contra uma base MySQL de teste**

Run: `ASILOSOFT_TEST_DB_HOST=127.0.0.1 ASILOSOFT_TEST_DB_PORTA=3306 ASILOSOFT_TEST_DB_NOME=asilosoft_test_local ASILOSOFT_TEST_DB_USUARIO=asilosoft_test ASILOSOFT_TEST_DB_SENHA=... php tests/integration/database/DatabaseIntegrationTest.php`
Expected: PASS para migrations, assistencial, estoque-schema, invariantes, concorrência, consultas e seed.

- [ ] **Step 3: Validar bootstrap em dois cenários**

Executar o bootstrap em uma base nova e numa cópia que contenha o baseline atual, depois comparar `information_schema` para tabelas, colunas, FKs, índices, views e triggers previstos. Registrar no guia o procedimento de backup e rollback: backup antes de migrar; rollback é uma nova migração compensatória, nunca edição de versão aplicada.

- [ ] **Step 4: Finalizar o guia de handoff**

Incluir diagrama textual das relações, dicionário de colunas, política de retenção/privacidade, matriz de quem lê/escreve cada domínio, formato de erros SQL que os serviços PHP próprios devem traduzir e a lista de consultas/views. Atualizar `GUIA_TECNICO.md` para trocar “planejada no banco” por “disponível no banco, sem módulo web” somente após os testes passarem. Registrar explicitamente que a operação usa PHP/PDO/MySQL nativos e não requer Spring Boot nem outro framework.

- [ ] **Step 5: Executar a verificação completa e inspecionar estado do repositório**

Run: `php tests/integration/database/DatabaseIntegrationTest.php && git status --short`
Expected: PASS em todos os casos; somente os arquivos desta entrega aparecem como modificados ou prontos para commit.

- [ ] **Step 6: Commit**

```bash
git add tests/integration/database/DatabaseIntegrationTest.php docs/GUIA_BANCO.md README.md GUIA_TECNICO.md
git commit -m "docs(database): documenta operação e contratos"
```

## Self-Review

**Cobertura da especificação:** RF02 é atendido pela Task 2; RF03 e RF09 pelas Tasks 2 e 5; RF04, RF06, RF07, RF08 e RF12 pelas Tasks 3–4; RF05 pela Task 5; RN01/RN02 pela Task 4; RN03 pela Task 2; RN05/RN08 pelo contrato e pelas constraints/consultas das Tasks 2, 4 e 5; RN06/RN07 pela Task 4; RNF02, RNF03, RNF04 e RNF05 recebem suporte por hashes existentes, contratos, privacidade, índices e testes. `logs_auditoria` é coberta explicitamente pela Task 4 e pelos testes de integração. RF01, RF10 e RF11 permanecem preservados no modelo atual e fora da implementação de módulos web deste plano.

**Decisões verificadas:** `schema.sql` continua o bootstrap e as migrações são somente incrementais; os contratos de `usuarios`, `idosos`, `quartos`, PDO e repositórios atuais não são alterados; o seed deixa de depender dos diretórios inexistentes `app/` e `config/`; saldo não pode divergir do histórico; `logs_auditoria` registra metadados necessários sem copiar descrição clínica ou senha; o banco não concede acesso ao familiar por conta própria.

**Limites assumidos:** este plano não introduz multi-instituição, categoria/SKU/lote/validade de materiais, prescrição médica, anexos, fornecedores, orçamento, pagamento, faturamento, RH, chat/videochamada, integração externa, aplicativo nativo, Spring Boot, Java ou qualquer framework/biblioteca adicional, pois não constam do escopo. A autorização HTTP e as telas serão implementadas depois pelos módulos PHP próprios sobre os contratos aqui publicados.

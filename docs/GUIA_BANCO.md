# Guia do banco AsiloSoft

O banco usa PHP/PDO e MySQL 8.0.16+; não requer framework ou ORM. A fonte de verdade é `src/infra/Database/migrations/`. Execute `php src/infra/Database/bootstrap.php` para criar/atualizar a base configurada. Faça backup antes de migrar; uma reversão é sempre uma nova migração compensatória, nunca a edição de uma versão aplicada.

Após configurar o `.env`, execute `php src/infra/Database/bootstrap.php`. O comando aplica as migrations pendentes de forma idempotente. O sistema não cria dados fictícios nem usa repositórios alternativos em sessão.

## Relações e regras

`idosos -> quartos`; `familiares <-> idosos` por `idosos_familiares`; `registros_saude` pertence a idoso e autor; `materiais -> estoques`; usos geram uma saída imutável e solicitações pertencem a material e solicitante. Entradas exigem origem `SUS`, `Plano de Saúde` ou `Instituição`; saídas têm origem nula. `estoques.quantidade_atual` é projeção do livro `movimentacoes_estoque` e não pode ser editado pela aplicação.

A tabela `logs_auditoria` registra metadados de alterações, sem descrições clínicas nem senhas. Todo serviço futuro que altere domínio auditável deve chamar `PDOMysqli::transactional($usuarioId, fn(PDO $pdo) => ...)`.

## Consultas publicadas

Use PDO e parâmetros, nunca acesso direto do navegador. `vw_ficha_idoso` suporta busca por nome, CPF ou identificador. `vw_estoque_atual` suporta filtro por `abaixo_minimo`; `vw_extrato_estoque` por material e intervalo; `vw_acompanhamento_familiar` por familiar e idoso. Histórico clínico deve consultar `registros_saude` por `(idoso_id, registrado_em DESC, id DESC)`.

As views não concedem autorização. As classes PHP devem associar conta apenas ao perfil `Familiar`, permitir escrita clínica apenas a `Profissional de Saúde` autorizado e filtrar acompanhamento familiar pela view visível.

## Teste

Defina explicitamente `ASILOSOFT_TEST_DB_HOST`, `ASILOSOFT_TEST_DB_PORTA`, `ASILOSOFT_TEST_DB_NOME`, `ASILOSOFT_TEST_DB_USUARIO` e `ASILOSOFT_TEST_DB_SENHA`, com nome iniciado por `asilosoft_test_`, e execute `php tests/integration/database/DatabaseIntegrationTest.php`. O runner recria somente essa base de teste.

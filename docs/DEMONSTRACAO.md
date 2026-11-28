# Base de demonstração

A migration opcional `src/infra/Database/demo/001_reset_demonstracao.sql` apaga os dados de negócio e os usuários, preservando integralmente a conta ativa `admin@asilosoft.local` (inclusive ID e senha). Preserva também a estrutura e o controle de migrations. Os dados inseridos são fictícios.

Com a aplicação parada e um backup do banco realizado, execute na raiz:

```bash
php scripts/popular-demonstracao.php
```

O terminal informa cada etapa com o tempo decorrido desde a conexão. Esperas por bloqueios de tabela/linha têm limite de 15 segundos; a resposta de uma consulta tem limite de 120 segundos. Uma segunda carga simultânea é recusada imediatamente por um lock exclusivo.

O comando usa o banco configurado no `.env`/ambiente, aplica primeiro as migrations estruturais e executa a limpeza e a carga em uma transação. Não execute o SQL diretamente: o comando fornece IDs, hashes de senha e trata as proteções do histórico. As duas triggers que bloqueiam exclusão de movimentações e consumos são temporariamente removidas e restauradas em `finally`, inclusive em caso de rollback. Não interrompa o processo durante essa janela; alterações de triggers não são transacionais no MySQL. A conta do banco precisa poder criar e remover triggers e procedimentos (`CREATE ROUTINE`/`ALTER ROUTINE`). A carga usa um procedimento temporário, removido ao terminar, para executar as instruções no servidor sem milhares de viagens de rede.

Antes de verificar se a carga já foi aplicada, o comando recria as triggers `bd_me_imutavel` e `bd_um_imutavel` caso estejam ausentes, usando as definições da migration 004. Proteções existentes são preservadas.

O administrador precisa existir, estar ativo e ter perfil Administrador; caso contrário a carga é recusada antes de remover dados. A migration é registrada como `demo_001_reset_demonstracao` em `schema_migrations`. Uma segunda execução não altera os dados. O bootstrap normal nunca aplica esta carga de demonstração.

## Acessos

| Perfil | E-mail | Senha |
| --- | --- | --- |
| Administrador | admin@asilosoft.local | A senha existente é preservada |
| Profissional de Saúde | saude@asilosoft.local | DemoSaude!2026 |
| Almoxarife | almoxarife@asilosoft.local | DemoEstoque!2026 |
| Familiar | familiar@asilosoft.local | DemoFamilia!2026 |

Estas são credenciais públicas de demonstração, armazenadas no banco com `password_hash`. Use esta base apenas para demonstração/homologação.

## Cenários disponíveis

- 100 idosos, sendo 96 ativos e quatro inativos; CPFs fictícios com dígitos verificadores.
- 28 quartos de quatro vagas: 24 ocupados pelos ativos, três com vagas e um inativo.
- 150 familiares e vínculos, com um responsável principal por idoso. Os primeiros 50 idosos têm dois contatos. Somente Ana Oliveira tem conta de acesso; os demais são contatos cadastrais.
- A conta Familiar está vinculada a Maria Oliveira (`DEMO-001`) e vê seus dois acompanhamentos publicados. O registro interno não é publicado.
- 300 registros de saúde com datas relativas à execução: 200 publicados e 100 internos.
- 20 itens entre materiais, insumos e medicamentos; 40 entradas, 300 consumos individuais e três retiradas internas. As triggers geram as baixas e os saldos, sem duplicação. Três itens ficam abaixo do mínimo.
- 80 compras: 20 pendentes, 20 aprovadas, 20 recusadas e 20 atendidas. Aprovações e atendimentos percorrem as transições de situação para gerar auditoria. Compras atendidas têm abastecimento lançado separadamente; a mudança de situação não movimenta estoque.
- Auditoria antiga removida; novos eventos são gerados pelas triggers durante a carga.

## Validação isolada

O teste exige um MySQL local descartável com autenticação root sem senha por socket e permissão de criar bases. Não utiliza o banco do `.env`. Cria e remove apenas `asilosoft_test_demo` e falha se a base já existir.

```bash
ASILOSOFT_DEMO_TEST_SOCKET=/caminho/mysql.sock php tests/integration/database/DemonstracaoIntegrationTest.php
```

Confere preservação integral do administrador, rollback em falha simulada, quantidades, saldos, acesso familiar, senhas, reexecução e restauração das proteções de histórico.

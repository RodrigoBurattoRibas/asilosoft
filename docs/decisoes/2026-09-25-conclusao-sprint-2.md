# Conclusão da Sprint 2: familiares, acesso e preparação do banco

## Objetivo

Concluir a Sprint 2 com o vínculo de familiares aos idosos, corrigir a ativação e desativação de cadastros e preparar o banco de dados para os módulos das Sprints 3 a 7.

## Escopo desta entrega

- Corrigir a persistência de `ativo` para usuários e idosos, enviando `0` ou `1` ao MySQL.
- Permitir que administradores cadastrem, editem, ativem e desativem familiares.
- Permitir vincular um familiar a um ou mais idosos e listar esses vínculos.
- Limitar as telas administrativas de usuários, idosos e familiares ao perfil `Administrador`.
- Preparar as tabelas futuras de saúde, estoque, movimentação, utilização e compras, sem criar interfaces fora da Sprint 2.
- Criar uma migração para instalações que já possuem o banco `asilosoft`.

## Fora do escopo

- Login de familiar e consulta de acompanhamento: previstos para a Sprint 7.
- Cadastro de prontuário e telas de saúde: previstos para a Sprint 3.
- Interfaces de materiais, estoque, uso e compras: previstas para as Sprints 4 a 6.
- Exclusão definitiva de usuários, idosos ou familiares.

## Regras de negócio

- Um familiar pode estar vinculado a vários idosos; um idoso pode ter vários familiares.
- Um vínculo repetido entre o mesmo familiar e o mesmo idoso não é permitido.
- Somente administradores gerenciam usuários, idosos e familiares.
- Usuários e familiares desativados permanecem registrados e podem ser reativados.
- Idosos inativos ou sem quarto não ocupam vaga.
- Entradas de estoque registram origem `SUS`, `Plano de Saúde` ou `Instituição`.
- Nenhuma movimentação de saída pode deixar a quantidade disponível negativa quando o módulo de estoque for implementado.

## Dados

### Familiares e vínculo

- `familiares`: nome, e-mail, telefone, parentesco e situação ativa.
- `idoso_familiar`: chaves estrangeiras para `idosos` e `familiares`, com combinação única.

### Estruturas preparadas para próximas sprints

- `registros_saude`: idoso, profissional responsável, descrição e data.
- `itens_estoque`: nome, tipo (material ou medicamento), unidade e quantidade disponível.
- `movimentacoes_estoque`: item, usuário, tipo de movimentação, quantidade e origem na entrada.
- `utilizacoes_itens`: item, idoso, usuário, quantidade e data de uso.
- `solicitacoes_compra`: item solicitado, solicitante, quantidade, situação e observação.

## Fluxo de familiares

1. Administrador abre a área de familiares.
2. Cadastra ou edita os dados de contato.
3. Seleciona um ou mais idosos ativos para vínculo.
4. O serviço valida os dados e evita vínculos duplicados.
5. A listagem exibe familiares ativos ou desativados e os idosos associados.

## Verificação

- Testes para ativação/desativação persistida como inteiro no MySQL.
- Testes para cadastro de familiar e vínculo com mais de um idoso.
- Testes para rejeição de vínculo duplicado.
- Testes para a restrição administrativa das ações da Sprint 2.
- Migração aplicada em banco existente e schema atualizado para instalação nova.

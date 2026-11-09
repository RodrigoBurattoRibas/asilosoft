# Checklist de implementação do AsiloSoft

Atualizado em 09/11/2026 após regressão completa. Status funcional apurado a partir dos requisitos do projeto e do código atual. `[x]` indica item implementado; `[ ]` indica item ainda pendente, inclusive quando há apenas modelagem no banco sem recurso disponível na aplicação.

## Acesso e usuários

- [x] Login por credenciais.
- [x] Senhas armazenadas com hash.
- [x] Cadastro, edição, ativação e desativação de usuários.
- [x] Restrição de gestão de usuários ao Administrador.
- [x] Bloqueio de acesso para usuário desativado.
- [x] Aplicar, validar e testar as permissões de todos os perfis em todos os módulos: Administrador, Profissional de Saúde, Almoxarife e Familiar.

## Idosos e quartos

- [x] Cadastrar idoso com nome, CPF, identificador e quarto.
- [x] Editar os dados cadastrais do idoso.
- [x] Impedir CPF e identificador duplicados.
- [x] Listar idosos ativos e desativados.
- [x] Ativar e desativar idoso.
- [x] Retirar idoso do quarto.
- [x] Impedir ocupação acima da capacidade do quarto.
- [x] Impedir alocação em quarto inativo.
- [x] Cadastrar, editar, ativar/desativar e administrar quartos.
- [x] Buscar idoso por nome, CPF ou identificador.
- [x] Exibir ficha consolidada do idoso com dados pessoais, quarto, familiares e saúde.

## Familiares e acompanhamento clínico

- [x] Cadastrar familiares ou responsáveis com dados de contato.
- [x] Vincular um ou mais familiares a um idoso.
- [x] Permitir que um familiar seja vinculado a mais de um idoso.
- [x] Definir responsável principal e parentesco no vínculo.
- [x] Registrar e atualizar informações de saúde do idoso por profissional autorizado.
- [x] Definir quais registros de saúde podem ser vistos pelo familiar.
- [x] Permitir que o familiar consulte apenas o acompanhamento dos idosos vinculados a ele.

## Materiais, insumos e medicamentos

- [x] Cadastrar materiais, insumos e medicamentos.
- [x] Editar nome, tipo e unidade de medida do item.
- [x] Registrar entrada de estoque.
- [x] Informar a origem da entrada: SUS, Plano de Saúde ou Instituição.
- [x] Registrar retirada de estoque.
- [x] Atualizar saldo após entrada e retirada.
- [x] Impedir retirada que deixe o estoque negativo.
- [x] Consultar histórico básico de movimentações.
- [x] Registrar, em toda saída, para o que o item foi utilizado por meio de finalidade ou observação obrigatória.
- [x] Permitir referenciar um idoso na saída quando o item for destinado ao seu atendimento.
- [x] Registrar uso individual do item com idoso, quantidade, data/hora, profissional responsável e observação.
- [x] Registrar o usuário responsável por cada entrada e retirada no histórico.
- [x] Cadastrar e controlar estoque mínimo por item.
- [x] Identificar itens abaixo do estoque mínimo e alertar a necessidade de reposição.
- [x] Criar solicitações de compra de materiais ou medicamentos.
- [x] Acompanhar situação da solicitação: Pendente, Aprovada, Recusada e Atendida.
- [x] Unificar o módulo de almoxarifado com o schema versionado (`materiais`, `estoques`, `movimentacoes_estoque`, `usos_materiais` e `solicitacoes_compra`).

## Integridade, privacidade e qualidade

- [x] Proteger formulários existentes contra CSRF.
- [x] Usar transação e bloqueio no repositório atual de estoque para evitar saldo negativo em retiradas concorrentes.
- [x] Integrar o módulo web às regras, triggers e views do schema versionado.
- [x] Garantir que somente profissionais autorizados possam criar ou alterar registros de saúde.
- [x] Garantir que dados pessoais e clínicos sejam acessados somente por usuários autorizados.

## Entrega e aceite

- [x] Roteiro de homologação, matriz de acesso, instalação e primeiro administrador sem senha padrão.
- [x] Runner único com seis suítes, benchmark e validação de sintaxe.
- [x] Bloqueio HTTP de arquivos internos no servidor PHP com router.php.
- [ ] Aceite da equipe e validação em Chrome, Edge e Firefox no ambiente de destino.
- [ ] Medição representativa de desempenho e disponibilidade operacional.

Saídas genéricas exigem finalidade; saídas para atendimento individual referenciam o idoso pelo fluxo Consumo, com observação opcional. A evidência atual está em docs/PRONTIDAO_HOMOLOGACAO.md; o aceite está em docs/HOMOLOGACAO.md.

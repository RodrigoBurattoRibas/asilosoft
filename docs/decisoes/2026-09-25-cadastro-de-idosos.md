# Sprint 2 - Cadastro de idosos

## Objetivo

Entregar o primeiro recorte da Sprint 2: cadastro e listagem de idosos com associação a quartos disponíveis. O recorte atende ao RF01 e às regras de negócio RNO1 e RNO2, sem incluir familiares, prontuário, edição de idosos ou gestão visual de quartos.

## Escopo desta entrega

- Criar a estrutura de quartos, com código, capacidade e situação ativa.
- Criar a estrutura de idosos, com nome, CPF, identificador e quarto associado.
- Listar idosos cadastrados com seu quarto.
- Exibir um formulário para cadastro de idoso.
- Exibir somente quartos ativos com vagas no formulário.
- Impedir CPF duplicado, identificador duplicado e alocação em quarto lotado.
- Suportar MySQL e o modo demonstração em sessão.

## Fora do escopo

- Cadastro, edição ou exclusão de quartos pela interface.
- Edição ou desativação de idosos.
- Vínculo com familiares e contatos.
- Prontuário, condições de saúde e consulta familiar.
- Permissões específicas da Sprint 2 além da proteção administrativa já existente.

## Dados

### Quarto

| Campo | Regra |
| --- | --- |
| id | Identificador interno. |
| codigo | Texto único visível, por exemplo `A-101`. |
| capacidade | Inteiro positivo que limita a ocupação. |
| ativo | Apenas quartos ativos aparecem no cadastro. |

### Idoso

| Campo | Regra |
| --- | --- |
| id | Identificador interno. |
| nome | Obrigatório. |
| cpf | Obrigatório e único; salvo apenas com dígitos. |
| identificador | Obrigatório e único. |
| quarto_id | Obrigatório; aponta para um quarto com vaga. |

## Fluxo

1. Administrador acessa a listagem de idosos.
2. Seleciona "Cadastrar idoso".
3. Informa nome, CPF, identificador e quarto.
4. O serviço valida os campos e verifica duplicidade e capacidade.
5. O repositório grava o idoso e a listagem exibe a nova alocação.

## Testes previstos

- Cadastro válido associa o idoso ao quarto escolhido.
- CPF duplicado é rejeitado.
- Identificador duplicado é rejeitado.
- Quarto sem vaga é rejeitado.
- Quarto inativo não é oferecido para cadastro.
- Dados funcionam no repositório temporário do modo demonstração.

## Critério de pronto

O item estará pronto quando o cadastro e a listagem funcionarem no modo demonstração e no MySQL, as regras de ocupação forem respeitadas e todos os testes previstos passarem.

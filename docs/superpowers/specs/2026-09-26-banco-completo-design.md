# Banco Completo do AsiloSoft - Design

## Objetivo

Completar o banco MySQL do AsiloSoft com as entidades previstas no documento do projeto, regras de integridade no banco, trilha de auditoria, dados ficticios reproduziveis e um teste de integracao que valide o schema e o seeder de ponta a ponta.

## Fonte de verdade e limites

Esta especificacao implementa somente o escopo e as regras RN01 a RN09 do PDF `AsiloSoft_Engenharia_de_Software_II (1).pdf`. Ela nao inclui financeiro, RH, chat/videochamada, integracoes externas ou aplicativo nativo.

O banco permanece em MySQL 8, acessado por PHP 8/PDO, no monolito MVC existente. As alteracoes nao modificam os controllers atualmente alterados pelo usuario.

## Modelo de dados

As tabelas existentes `usuarios`, `quartos` e `idosos` serao preservadas. O schema sera ampliado com:

- `familiares`: dados de contato do familiar ou responsavel e referencia opcional ao usuario de perfil `Familiar`.
- `idosos_familiares`: vinculo muitos-para-muitos entre idosos e familiares, permitindo que um familiar acompanhe mais de um idoso (RN03).
- `registros_saude`: historico clinico do idoso, registrado por um usuario profissional de saude; `visivel_para_familiar` determina o que pode ser disponibilizado ao familiar.
- `materiais`: catalogo de materiais, insumos e medicamentos, incluindo unidade de medida e estoque minimo.
- `estoques`: saldo atual de cada material, mantido exclusivamente pelas movimentacoes.
- `movimentacoes_estoque`: historico imutavel de entradas e saidas; toda entrada possui origem `SUS`, `Plano de Saude` ou `Instituicao` (RN07).
- `usos_materiais`: uso ou retirada destinado a um idoso, com material, quantidade, profissional responsavel e data.
- `solicitacoes_compra`: solicitacao institucional de compra de um material, com quantidade e situacao.
- `logs_auditoria`: registro de operacao, tabela, chave do registro, usuario responsavel quando existir, valores anterior/novo em JSON e data.

`movimentacoes_estoque` e a fonte do historico de estoque. `estoques.quantidade_atual` e uma projeção para consulta rapida, nao uma segunda fonte editavel pela aplicacao.

## Regras aplicadas pelo banco

- RN01/RN02: ao inserir ou alterar um idoso ativo com quarto, trigger valida que o quarto esta ativo e que a ocupacao ativa nao excedera a capacidade. Um idoso pode continuar sem quarto apenas nas operacoes ja suportadas pelo sistema.
- RN03: a chave primaria composta de `idosos_familiares` impede duplicacao do mesmo vinculo e permite o mesmo familiar em varios idosos.
- RN05: `registros_saude.usuario_id` referencia um usuario. A verificacao de perfil autorizado e feita pela camada de acesso do sistema; o banco preserva o autor para rastreabilidade.
- RN06: uma trigger anterior a cada saida bloqueia quantidade insuficiente. Uma trigger posterior atualiza o saldo, mantendo-o nunca negativo.
- RN07: a coluna `origem` e obrigatoria em entrada e proibida em saida por `CHECK`; a origem fica na movimentacao, nao no material, pois o mesmo material pode ter entradas de fontes diferentes.
- RN08: o relacionamento idoso-familiar fica persistido em `idosos_familiares`; a autorizacao de consulta sera feita pela camada de aplicacao com base nesse vinculo.
- RN09: o campo existente `usuarios.ativo` permanece a fonte para impedir autenticacao de usuarios desativados na aplicacao.

RN04 (somente administradores gerenciam usuarios), RN05, RN08 e RN09 envolvem identidade autenticada e serao mantidas nas services/middlewares. O banco registra os vinculos e autores necessarios, mas nao substitui controle de acesso da aplicacao.

## Triggers e auditoria

As triggers de dominio tratam capacidade, entrada/saida e saldo. Inserir um uso de material gera uma movimentacao de saida ligada ao uso; se nao houver saldo, a operacao inteira falha e nenhum uso fica gravado.

Triggers de auditoria registram `INSERT`, `UPDATE` e `DELETE` das tabelas de negocio, exceto da propria tabela de logs para evitar recursao. Os dados de saude terao no log apenas metadados de operacao e identificadores; o conteudo clinico nao sera copiado para a auditoria, reduzindo exposicao de dados sensiveis.

## Seeder

O seeder sera um script PHP idempotente, com transacao. Ele cria dados inteiramente ficticios e deterministas: perfis de usuario, quartos, idosos, familiares e seus vinculos, registros de saude, materiais, entradas de estoque com as tres origens, um uso de material e uma solicitacao de compra. Senhas sao geradas com `password_hash`; nenhum segredo real e gravado.

Executar o seeder novamente nao duplica os dados de demonstracao nem altera saldos indevidamente.

## Testes

O teste sera de integracao MySQL, pois verifica DDL, chaves, triggers, seeder e transacoes reais. Ele usa uma base de teste dedicada definida por variaveis `ASILOSOFT_TEST_DB_*`, recria somente essa base, executa `schema.sql` e o seeder, e comprova:

1. todas as tabelas previstas existem e o seeder preenche cada uma;
2. os relacionamentos de idoso/familiar e autoria de saude existem;
3. entradas das tres origens formam saldo correto;
4. uso de material cria saida e reduz saldo;
5. saida acima do saldo falha;
6. quarto acima da capacidade falha;
7. entrada sem origem falha;
8. logs de auditoria sao gerados;
9. nova execucao do seeder preserva as quantidades esperadas.

O runner atual continuara sem exigir banco. O teste de integracao sera opt-in por variaveis de ambiente, para que os testes unitarios em memoria continuem executaveis em qualquer maquina.

## Operacao

`README.md` e `docs/GUIA_BANCO.md` receberao os comandos de criacao, seeder e teste. A documentacao exigira uma conta MySQL local com permissao somente na base de testes e nao incluira senha real.

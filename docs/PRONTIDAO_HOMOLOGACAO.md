# Prontidão para homologação — 09/11/2026

Entrega funcional preparada para a equipe testar. Este registro descreve validações locais executadas; o resultado da homologação deve ser preenchido em [HOMOLOGACAO.md](HOMOLOGACAO.md).

## Alterações entregues

- Consolidação das alterações locais de login: formulário anônimo sem conexão antecipada, timeout MySQL e mensagem de indisponibilidade na autenticação; apresentação dos familiares na ficha com confirmação ao remover vínculo.
- Finalidade obrigatória em retirada genérica, com regressão que confirma ausência de movimentação e de alteração de saldo quando vazia. Autor e finalidade conferidos na retirada válida.
- Roteador do servidor PHP com acesso apenas à aplicação e assets. Testes HTTP confirmam 404 para `.env`, `.git/config`, documentação, SQL e script administrativo; CSS continua acessível.
- Primeiro administrador por CLI, sem senha padrão, hash seguro, validação de entrada e bloqueio para inicialização concorrente. Segunda inicialização recusada quando já existe administrador ativo.
- Runner de regressão, checklist atualizado, instruções de início corrigidas e roteiro de aceite com os quatro perfis.

## Validação executada

MySQL 8.0.46 isolado em 127.0.0.1:33317, datadir temporário; bases com prefixo `asilosoft_test_`. Nenhuma migration ou fixture foi aplicada ao banco configurado no `.env`. O runner usa configurações de teste explícitas e nomes exclusivos por execução.

Comando: `bash tests/run.sh`, com as quatro variáveis obrigatórias de teste definidas conforme `tests/README.md`. Resultado: **todas as suítes passaram**.

| Validação | Resultado |
| --- | --- |
| Regras de estoque e compras | 40 verificações aprovadas |
| DatabaseIntegrationTest | Instalação nova, atualização histórica, preservação, bootstrap repetido, invariantes e contratos de rotas aprovados |
| FamiliaresIntegrationTest | Contas, vínculos, isolamento do portal, rollback e concorrência de principal aprovados |
| ClinicaIntegrationTest | Busca, autorização, validação, retificação, auditoria, ficha e paginação aprovados |
| EstoqueComprasIntegrationTest | Baixa única, idempotência, rollback, saldo concorrente, origem e transições de compra aprovados |
| InstalacaoIntegrationTest | Senha curta rejeitada sem gravação, hash válido, primeiro administrador e recusa de repetição aprovados |
| HttpIntegrationTest | Login/perfis, fluxos integrados, CSRF/métodos, consumo com correção, revogação, finalidade de retirada e bloqueio de arquivos internos aprovados |
| Sintaxe PHP | `src`, `tests`, `scripts`, `index.php` e `router.php` aprovados |
| Shell e diff | `bash -n tests/run.sh` e `git diff --check` aprovados |

Benchmark da mesma execução: 1.001 idosos, 100 consultas por critério, PDO/MySQL local sem carga simultânea.

| Critério | Mediana | p95 | Máximo |
| --- | --- | --- | --- |
| Nome | 0,784 ms | 1,071 ms | 2,048 ms |
| CPF | 0,554 ms | 0,597 ms | 0,684 ms |
| Identificador | 0,595 ms | 0,683 ms | 0,791 ms |

O benchmark mede consulta e mapeamento no repository. O aceite de RNF05 exige medição ponta a ponta com carga representativa no destino. A revisão visual anterior está em `VALIDACAO_VISUAL.md`; a revisão atual foi por código e HTTP, sem nova inspeção visual em navegadores.

## Atividades da homologação

Executar H01–H14 do roteiro, registrar evidências e defeitos, validar Chrome/Edge/Firefox e dispositivos da equipe, medir desempenho no destino e configurar/acompanhar disponibilidade. O requisito de 99% demanda uma janela de operação acordada. Não foram realizados deploy, aprovação humana ou alteração de documentos externos. Os artefatos preexistentes de `output/` foram preservados e não incorporados aos commits desta entrega.

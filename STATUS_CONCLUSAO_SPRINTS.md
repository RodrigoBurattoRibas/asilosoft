# Conclusão das sprints AsiloSoft — 20/10/2026

## Partida e escopo

Branch `feat/add-nova-estrutura`, commit `b9e33b869a29aa81e00f0de76285958d722599cc`. Alterações realizadas no diretório de trabalho, sem commit, reset, troca de branch ou deploy. Os arquivos preexistentes não rastreados de planejamento e prompt foram preservados. PHP/JavaScript/MySQL, arquitetura modular, layout e integração PDO mantidos; nenhuma dependência ou framework adicionado. Banco real e `.env` não foram alterados.

## Checklist e diagnóstico revalidado

- [x] Inspeção de requisitos, rotas, migrations, código e Git.
- [x] A: autorização de escrita clínica, rotas sem colisão, navegação, migrations convergentes.
- [x] B/C: associação conta-familiar, vínculos atômicos, ficha pesquisável e retificação auditável.
- [x] D/E: consumo individual idempotente e compras com estados/atualização concorrente.
- [x] F: portal filtrado por identidade, vínculo, publicação e situação ativa.
- [x] Integrações MySQL/HTTP, concorrência, regressões, sintaxe e revisão responsiva local.
- [x] Documentação e limites de validação registrados.

Na partida, A1/A2/B/C/D/E/F eram parciais; foram completadas. A3 era uma divergência real de tipo dependente da ordem histórica de aplicação, não colisão da chave de versão. A integração PDO, triggers de consumo, revalidação de conta ativa e módulos web já existiam e foram reutilizados. Também foi reproduzida e corrigida falha do bootstrap: `EXECUTE` retornando `SELECT 1` deixava cursor aberto e bloqueava a instrução seguinte. Não havia motivo para fallback em sessão ou migração de tabelas legadas.

## Estado por sprint e requisito

“Concluído e validado” abaixo significa fluxo funcional validado localmente em MySQL e HTTP com dados fictícios, e não homologação em produção.

| Sprint | RF | Estado e evidência |
| --- | --- | --- |
| S1 | RF10, RF11 | Regressões concluídas e validadas: login dos quatro perfis, hash, edição de usuário, proteção do último administrador, logout/login |
| S2 | RF01, RF02 | Concluído e validado: associação/preservação/remoção de conta, múltiplos vínculos, principal atômico, ficha e regressões de quartos/idosos |
| S3 | RF03, RF05 | Concluído e validado: busca pelos três critérios, paginação, autorização clínica, anotação privada/publicada e retificação preservando original |
| S4 | RF04, RF06 | Regressões concluídas e validadas: material, entrada, retirada, capacidade decimal, saldo e concorrência; sem reescrita do estoque |
| S5 | RF07, RF12 | Concluído e validado: origem obrigatória, uso individual com uma baixa, rollback e reenvio idempotente |
| S6 | RF08 | Concluído e validado: solicitação, aprovação, recusa, atendimento, transição obsoleta/ilegal rejeitada e estoque inalterado |
| S7 | RF09 | Concluído e validado: portal publicado, isolamento por vínculo, estados vazios e revogação na próxima requisição |
| S8–S11 | Qualidade/homologação/entrega | Parcial: os testes e documentos desta entrega não concluem integralmente essas sprints |

## Arquivos e decisões

- `src/modules/familiares/{controller,services,repository,view}`: conta Familiar ativa opcional, unicidade, limites/e-mail, campo ausente preservado sob bloqueio e vínculos transacionais. Regra de principal: **no máximo um**, podendo não haver principal.
- `src/modules/idosos/` e `src/modules/zacompanhar/`: busca por nome parcial, CPF normalizado e identificador; 30 resultados por página, ordenação nome/id; services/repositories para clínica/ficha. Administrador consulta, somente profissional registra. Retificação é nova anotação com referência ao original e auditoria INSERT; não apaga conteúdo anterior.
- `src/modules/almoxarifado/`: formulário/POST de consumo, DECIMAL(12,3), idoso/material ativos, bloqueios e idempotência persistida. A trigger existente gera a movimentação; nenhuma baixa manual duplicada. Profissional pode consumir pela necessidade assistencial descrita nos requisitos, sem gerir estoque/compras.
- `src/modules/zcompras/{controller,services,repository,view}`: decisão de estados Pendente → Aprovada/Recusada; Aprovada → Atendida; terminais Recusada/Atendida. Compare-and-set impede atualização sobre estado obsoleto. Somente administrador muda situação; compra não movimenta estoque.
- Portal: idosos inativos ficam fora tanto da lista quanto da URL direta, mantendo a regra preexistente da listagem. Identidade e publicação são verificadas a cada consulta.
- `src/modules/home/`, `src/presentation/layout/asilo.php`, `src/assets/css/asilosoft.css`: navegação por perfil, quebra de menu, formulários, contraste do logout e checkboxes.
- Rotas embutidas de quartos renomeadas para `idosos-quartos-criar/atualizar`; rotas do módulo quartos mantidas com seu escopo CSRF. Arquivo vazio `zacompanhar/routes/compras.php` preservado, não carregado pelo RouteLoader.
- `index.php`: erros ocultos por padrão, logs habilitados; debug explícito por `ASILOSOFT_DEBUG=1`.

Migrations novas: **007_convergir_tipo_material.sql** converge para VARCHAR(30) sem perder valores; **008_idempotencia_uso.sql** adiciona chave nullable/única ao consumo, preservando registros antigos. Migrations 001–006 intactas. Aplicar pelo bootstrap antes de utilizar a versão atualizada; nenhuma implantação real foi realizada.

## Validações realmente executadas

Ambiente: Linux, PHP CLI 8.4.25, PDO/MySQL, MySQL 8.0.46 em instância isolada `/tmp/asilosoft-sprints-mysql`, host 127.0.0.1, porta 33317. Bases com prefixo `asilosoft_test_`; dados fictícios. Comandos completos e regras de descarte em `tests/README.md`.

| Teste executável | Resultado / cobertura |
| --- | --- |
| `tests/estoque_compras_rules.php` | 40 verificações de quantidades, estados e regras passaram |
| `tests/integration/FamiliaresIntegrationTest.php` | T01/T02/T05/T06 passaram: contas, limites, vínculos, rollback, portal, principal concorrente (2 processos, 24 alterações) |
| `tests/integration/ClinicaIntegrationTest.php` | 20 verificações passaram: busca, ficha, autoria, admin negado, validações, retificação, auditoria e paginação |
| `tests/integration/EstoqueComprasIntegrationTest.php` | 23 verificações passaram: T07/T08/T09, reenvio, payload divergente, inativos, rollback, dois consumidores concorrentes, estados e auditoria |
| `tests/integration/database/DatabaseIntegrationTest.php` | T11/T12/T14 passaram: novo schema, atualização simulando ENUM histórico, bootstrap repetido, dados/saldo/histórico preservados, capacidade e handlers |
| `tests/integration/HttpIntegrationTest.php` | 146 verificações HTTP passaram: T01–T11/T13/T14 nos fluxos aplicáveis; CSRF ausente/inválido e método incorreto em todas as rotas; login, usuários, quartos por ambas entradas, estoque genérico, publicação, consumo, compras e revogação |
| Sintaxe | 81 arquivos PHP de `src/` e `tests/`, mais `index.php`, passaram em `php -l` |
| Diff | `git diff --check` passou |

A atualização histórica do teste T12 reproduz o schema ENUM resultante da ordem alternativa, mantendo dados, em vez de usar uma cópia do banco real. A concorrência de compras foi validada pela rejeição do estado obsoleto com compare-and-set; consumo e principal também foram executados em processos concorrentes reais.

Revisão visual: navegador integrado do Codex, snapshots HTML gerados pelo servidor PHP durante o teste HTTP, com dados fictícios. Ficha revisada em 390×844, 768×1024 e 1366×900; familiares, prontuário, compras, portal, home e consumo tiveram dimensões/layout inspecionados nesses tamanhos. Sem transbordamento global da página; tabelas usam rolagem interna. Essa revisão visual não substitui testes de interação: estes foram feitos via HTTP. **Chrome, Edge e Firefox externos não foram testados.**

Benchmark `tests/integration/BuscaBenchmark.php`: 1.001 idosos, 100 consultas por critério, PDO/MySQL local sem carga simultânea. Medianas nome/CPF/identificador: **0,824 / 0,582 / 0,632 ms**; p95: **1,575 / 1,105 / 1,170 ms**. Somente consulta e mapeamento do repository; não inclui rede externa nem renderização. **RNF05 e RNF06 não são declarados atendidos por esse ensaio.**

## Pendências reais e atualização do relatório M2

Restam homologação com usuários, compatibilidade em Chrome/Edge/Firefox e dispositivos reais, medição representativa de ponta a ponta/carga (RNF05), monitoramento de disponibilidade (RNF06) e atividades completas S8–S11. As migrations precisam ser aplicadas no ambiente de implantação pela equipe, após os procedimentos normais de backup. Não há bloqueio funcional conhecido nos cenários executados.

Para M2, atualizar mapa de RF/sprints, matriz de acesso, fluxo conta→familiar→vínculo→publicação, retificação auditável, consumo/trigger/idempotência, estados de compras e resultados/limitações dos testes. Separar conclusão funcional local de homologação e qualidade operacional. Nenhum Google Docs, relatório externo, evidência acadêmica ou contribuição individual foi editado automaticamente.

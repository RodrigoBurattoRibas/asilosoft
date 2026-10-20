# Planejamento de implementacao - acompanhamento, familiares e aquisicoes

## 1. Objetivo e limites

Implementar os requisitos ainda pendentes do AsiloSoft, sem adicionar bibliotecas, frameworks ou tecnologias. A solucao permanecera um monolito modular em PHP 8, JavaScript puro, PDO/MySQL, rotas `?route=...`, controllers, services, repositories e views.

Escopo desta entrega:

1. cadastro e vinculo de familiares/responsaveis aos idosos;
2. prontuario e acompanhamento de saude do idoso;
3. ficha consolidada pesquisavel do idoso;
4. registro de uso de material/medicamento destinado a um idoso;
5. necessidades de aquisicao (solicitacoes de compra) para administrador e almoxarife;
6. consulta, pelo familiar, apenas dos acompanhamentos explicitamente liberados;
7. controle de acesso coerente em todas essas operacoes;
8. convergencia do almoxarifado para o schema de migrations ja existente.

Nao pertencem a este plano: pagamentos, recursos humanos, chat/videochamada, integracoes com sistemas externos e aplicativo nativo.

## 2. Perfis e diferenca de acesso

Os documentos nao definem uma categoria generica de "pessoa normal". Para evitar acesso indevido a dados clinicos, ela nao deve ser criada. O usuario externo comum do sistema e o perfil **Familiar**, sempre associado a um cadastro em `familiares` e a pelo menos um idoso em `idosos_familiares`.

| Acao | Administrador | Profissional de Saude | Almoxarife | Familiar |
| --- | --- | --- | --- | --- |
| Gerenciar usuarios, idosos, quartos e familiares | Sim | Nao | Nao | Nao |
| Consultar ficha completa do idoso | Sim | Sim | Nao | Somente acompanhamento liberado do idoso vinculado |
| Registrar/alterar acompanhamento de saude | Nao, salvo se tambem possuir credencial de saude | Sim | Nao | Nao |
| Cadastrar item, entrada, retirada e consultar estoque | Sim | Nao | Sim | Nao |
| Registrar uso de item para idoso | Sim | Sim, se a instituicao permitir o fluxo de consumo assistencial | Sim | Nao |
| Registrar solicitacao de compra | Sim | Nao | Sim | Nao |
| Alterar a situacao de uma solicitacao | Administrador | Nao | Nao nesta entrega | Nao |

Decisao de privacidade: Administrador pode consultar dados administrativos e a ficha consolidada; escrita clinica fica restrita ao perfil `Profissional de Saude`, como exigem as regras RN05 e os gatilhos do banco. Caso a instituicao precise que um administrador tambem registre saude, deve criar uma conta separada com esse perfil, mantendo autoria auditavel.

## 3. Diagnostico do ponto de partida

- O modulo `idosos` ja tem cadastro, quartos e uma tela inicial de saude; ela precisa ser integrada a uma ficha consolidada e a familiares.
- As tabelas `familiares`, `idosos_familiares`, `registros_saude`, `usos_materiais` e `solicitacoes_compra`, mais as views de consulta, ja existem nas migrations `002`, `003` e `005`.
- O modulo `almoxarifado` em uso ainda cria e consulta `itens_estoque` e `almoxarifado_movimentacoes` diretamente. Isso conflita com o modelo versionado `materiais`, `estoques` e `movimentacoes_estoque`.
- O controle de perfil esta repetido nos controllers. As novas rotas devem centralizar a verificacao reutilizavel, sem liberar uma rota apenas por estar autenticada.
- O modo demonstracao precisa continuar funcional. Os novos repositorios de sessao devem espelhar os contratos usados pelas telas, ou as telas devem indicar de modo claro a indisponibilidade de dados persistentes; a preferencia e cobrir o fluxo completo tambem em sessao.

## 4. Modelo de dados e migracao

### 4.1 Fonte de verdade

Manter `src/infra/Database/migrations/` como fonte de verdade. Criar uma migration posterior, por exemplo `006_alinhar_almoxarifado_modulos.sql`, sem editar migrations ja aplicaveis. Ela devera:

1. complementar `materiais` com o tipo ja usado pela interface (`Material`, `Insumo` ou `Medicamento`), caso o campo ainda nao exista;
2. migrar itens e saldos das tabelas legadas para `materiais` e `estoques` somente quando nao houver registro equivalente;
3. migrar o historico legado para `movimentacoes_estoque`, preservando data, tipo, quantidade e origem quando possivel;
4. manter as tabelas antigas apenas durante a transicao, sem que o codigo novo volte a escrever nelas;
5. validar, antes de aposentar estruturas legadas em outra mudanca controlada, a contagem de itens, saldos e movimentacoes migrados.

Nao realizar `DROP TABLE` nesta entrega. A remocao posterior depende de backup validado e homologacao da migracao.

### 4.2 Entidades e regras

| Entidade | Uso no fluxo | Regras principais |
| --- | --- | --- |
| `familiares` | Contato e possivel conta de acesso do responsavel | Um familiar pode estar ligado a varios idosos; `usuario_id`, quando preenchido, deve ser de perfil Familiar. |
| `idosos_familiares` | Vinculo idoso-familiar | Registrar parentesco e responsavel principal; chave composta impede duplicidade. |
| `registros_saude` | Eventos do prontuario | Texto obrigatorio, autor profissional de saude, data, e opcao `visivel_para_familiar`. Registros nao sao apagados; correcoes geram novo registro ou mecanismo de retificacao auditavel. |
| `usos_materiais` | Consumo individual | Idoso, item, quantidade positiva, responsavel, data/hora e observacao/finalidade obrigatoria. A insercao gera a saida de estoque pelo gatilho existente. |
| `solicitacoes_compra` | Necessidade de aquisicao | Item, quantidade positiva, solicitante, data e observacao; nasce em `Pendente`. Administrador pode mudar para Aprovada, Recusada ou Atendida apenas quando esse subfluxo for implementado. |

Usar as views `vw_ficha_idoso`, `vw_estoque_atual`, `vw_extrato_estoque` e `vw_acompanhamento_familiar` para leitura onde elas cobrem o caso. A ficha devera buscar os registros de saude separadamente, para preservar a ordenacao e evitar repeticao causada por varios familiares.

## 5. Desenho por modulo

### 5.1 Autorizacao

Criar um servico ou helper interno de autorizacao reutilizavel, sem dependencia externa, que receba usuario atual e perfis permitidos. Controllers continuam responsaveis por redirecionar usuarios nao autenticados e retornar 403 para perfil invalido.

- Todas as rotas com dados de idoso exigem autenticacao e autorizacao especifica.
- A consulta familiar deve ainda confirmar no repositorio o vinculo entre `usuario_id -> familiar -> idoso` e filtrar `visivel_para_familiar = 1`; nunca confiar apenas no `id` recebido por query string.
- Formularios `POST` mantem o middleware e token CSRF ja utilizados.
- Consultas e mutacoes usam prepared statements e as validacoes de dominio ficam nos services.

### 5.2 Modulo `idosos`

Adicionar, sem desmontar as telas atuais:

1. busca por nome, CPF ou identificador na lista de idosos;
2. rota e view de **ficha completa**, com dados pessoais, quarto, familiares/responsaveis, ultimos registros de saude e historico de uso de materiais;
3. subfluxo de familiares: listar, criar/editar contato, vincular/desvincular ao idoso e marcar responsavel principal;
4. evolucao do prontuario existente: registrar acompanhamento com indicacao de visibilidade familiar, listar por data e exibir autor; manter escrita apenas para Profissional de Saude;
5. contrato de repositorio para carregar ficha, familiares, vinculos, registros e usos sem consultas N+1.

Rotas propostas, seguindo a convencao existente: `idosos-ficha`, `familiares`, `familiares-novo`, `familiares-criar`, `familiares-editar`, `familiares-atualizar`, `idosos-familiares-vincular` e `idosos-familiares-desvincular`. Os nomes finais devem ser registrados em `src/modules/idosos/routes/web.php` e cobertos por middleware de metodo/CSRF.

### 5.3 Modulo `almoxarifado`

Refatorar o repositorio para usar o modelo migrado, preservando a camada `AsiloAlmoxarifadoService` e as telas como pontos de extensao:

1. listar saldo e alerta de estoque minimo a partir de `vw_estoque_atual`;
2. registrar entrada com origem obrigatoria;
3. separar retirada geral de **registro de uso para idoso**. Este ultimo seleciona idoso ativo, exige finalidade/observacao e grava em `usos_materiais`; o gatilho cria a saida imutavel e impede saldo negativo;
4. criar lista e formulario de solicitacoes de compra para Administrador e Almoxarife, com item, quantidade e justificativa/observacao;
5. apresentar solicitacoes pendentes e, somente para Administrador, controles de situacao quando a transicao de compra estiver pronta;
6. exibir no extrato o autor, a origem e o vinculo de uso quando existente.

Rotas propostas: `almoxarifado-uso`, `almoxarifado-registrar-uso`, `compras`, `compras-nova`, `compras-criar` e, para administracao posterior do ciclo, `compras-situacao`.

### 5.4 Modulo de consulta do familiar

Criar `src/modules/familiares/` com a mesma organizacao de `controller`, `services`, `repository`, `routes` e `view`. Ele e deliberadamente pequeno: pagina inicial com os idosos vinculados e pagina de acompanhamento exibindo somente registros publicados. O familiar nao recebe navegacao para idosos, usuarios, ficha completa ou almoxarifado.

Rotas propostas: `meus-idosos` e `acompanhamento-familiar`. A navegacao em `src/presentation/layout/asilo.php` passa a derivar os links do perfil atual, incluindo uma entrada exclusiva para Familiar.

## 6. Sequencia de implementacao

### Etapa 0 - preparacao e seguranca

1. Levantar as estruturas existentes no banco de homologacao e executar o bootstrap/migrator em uma copia segura.
2. Implementar autorizacao reutilizavel e testes de 401/403 por perfil antes de novas telas.
3. Criar fixtures de usuarios: Administrador, Profissional de Saude, Almoxarife e Familiar vinculado, alem de um familiar sem vinculo.

**Pronto quando:** nenhuma rota nova puder ser acessada com perfil incorreto, inclusive por URL direta.

### Etapa 1 - familiares e ficha do idoso

1. Criar repository/service/controller/views de familiares e vinculos.
2. Acrescentar pesquisa e ficha consolidada no modulo de idosos.
3. Integrar a ficha a familiares, quarto e historico assistencial disponivel.

**Pronto quando:** administrador cadastra um familiar, o vincula a um ou mais idosos e um usuario autorizado encontra a ficha por nome, CPF ou identificador.

### Etapa 2 - prontuario

1. Extrair o acesso e a persistencia de saude do controller para service/repository do dominio.
2. Concluir os campos de registro, autor, data e publicacao ao familiar.
3. Disponibilizar a ficha para consulta de Administrador e Profissional de Saude, sem conceder escrita clinica ao administrador.

**Pronto quando:** profissional registra um acompanhamento e ele aparece em ordem cronologica na ficha; apenas o registro marcado como publico pode aparecer no portal familiar.

### Etapa 3 - convergencia do estoque e uso individual

1. Criar a migration de alinhamento e verificar os dados migrados.
2. Alterar o repositorio de almoxarifado para o schema versionado; remover criacao de DDL em tempo de execucao.
3. Implementar registro de uso para idoso e historico na ficha.
4. Exibir estoque minimo e manter o bloqueio de saldo negativo no service e no banco.

**Pronto quando:** cada uso individual gera uma unica saida vinculada, reduz o saldo uma unica vez e nao pode deixar o estoque negativo.

### Etapa 4 - solicitacoes de compra

1. Implementar lista e formulario para Administrador/Almoxarife.
2. Validar item ativo, quantidade decimal positiva e solicitante autenticado.
3. Gravar a solicitacao como `Pendente`, com rastreabilidade de autor e data.
4. Implementar a mudanca de situacao apenas para Administrador, caso seja incluida na sprint; caso contrario, manter a lista como controle de necessidades pendentes.

**Pronto quando:** administrador e almoxarife conseguem registrar e consultar necessidades de compra, sem alterar estoque automaticamente.

### Etapa 5 - portal do familiar e integracao

1. Associar, na gestao de usuario, conta Familiar ao cadastro de familiar, respeitando a constraint ja prevista.
2. Entregar `meus-idosos` e `acompanhamento-familiar` usando a view publicada e verificacao de vinculo.
3. Revisar navegacao, responsividade e mensagens de acesso negado.

**Pronto quando:** um familiar autenticado ve somente idosos vinculados e somente acompanhamentos liberados; tentativas de trocar o identificador na URL retornam 403/404 sem expor dados.

## 7. Arquivos previstos para alteracao/criacao

| Area | Arquivos principais |
| --- | --- |
| Banco | `src/infra/Database/migrations/006_alinhar_almoxarifado_modulos.sql` e, se necessario, novas views versionadas |
| Idosos | `src/modules/idosos/{controller,services,repository,view,routes}/...` |
| Familiares | novo `src/modules/familiares/{controller,services,repository,view,routes}/...` |
| Almoxarifado | `src/modules/almoxarifado/{controller,services,repository,view,routes}/...` |
| Acesso e navegacao | componente/servico interno de autorizacao e `src/presentation/layout/asilo.php` |
| Demonstracao | repositorios de sessao correspondentes aos contratos novos |
| Testes | testes unitarios de services/repositorios de sessao e testes de integracao com banco para triggers e permissoes |

Os nomes de classes devem manter o prefixo `Asilo` e o padrao de arquivos atual. Nenhuma dependencia de Composer, framework, API externa ou biblioteca de frontend sera acrescentada.

## 8. Cenarios obrigatorios de validacao

1. Administrador cria familiar, vincula-o a dois idosos e o mesmo familiar aparece corretamente nas duas fichas.
2. Profissional de Saude registra dois acompanhamentos; somente o marcado como visivel e exibido para o familiar vinculado.
3. Almoxarife tenta abrir ficha/prontuario: recebe 403. Familiar tenta abrir almoxarifado ou ficha completa: recebe 403.
4. Busca por nome parcial, CPF e identificador retorna o idoso correto sem duplicar registros por familiar.
5. Registro de uso com saldo suficiente reduz estoque e cria movimentacao; com saldo insuficiente falha e nao grava uso nem movimentacao.
6. Entrada sem origem e solicitacao com quantidade zero/negativa falham na validacao.
7. Administrador e Almoxarife criam solicitacao de compra pendente; Familiar e Profissional de Saude nao conseguem chamar a rota.
8. Banco ja populado com tabelas legadas e banco novo com migrations produzem a mesma lista de itens, saldo e historico apos a transicao validada.
9. Formularios mutaveis rejeitam token CSRF ausente/invalido e usuario desativado perde acesso na proxima requisicao.
10. Revisao manual em Chrome, Edge e Firefox, em larguras de celular, tablet e desktop, confirma navegacao e tabelas utilizaveis.

## 9. Riscos e criterios de aceite

| Risco | Mitigacao |
| --- | --- |
| Duas estruturas de estoque divergirem | Tornar migrations a fonte de verdade, migrar com verificacao de contagens/saldos e parar escritas no legado. |
| Exposicao de prontuario a familiar nao autorizado | Verificar perfil, associacao usuario-familiar, vinculo familiar-idoso e flag de visibilidade em toda consulta. |
| Saida duplicada em uso de item | Inserir somente em `usos_materiais`; deixar o gatilho criar a movimentacao. Cobrir com teste de transacao. |
| Registros clinicos alterados sem rastreabilidade | Manter historico imutavel; usar novo registro/retificacao, nunca sobrescrever a anotacao original. |
| Funcionalidades indisponiveis em demonstracao | Implementar contratos de repositorio equivalentes em sessao e exercitar os cenarios principais nos dois modos. |

A entrega sera aceita quando todos os cenarios da secao 8 passarem, as regras RN03, RN05, RN06, RN07 e RN08 forem aplicadas no service e no banco quando cabivel, nao houver novas dependencias, e a navegacao respeitar a matriz de perfis da secao 2.

## 10. Ordem sugerida no backlog

1. Controle de acesso reutilizavel e fixtures.
2. Familiares e vinculos (RF02).
3. Ficha pesquisavel e consolidada (RF05).
4. Prontuario e liberacao de registros (RF03).
5. Convergencia do almoxarifado e uso para idoso (RF12, RF06, RF07).
6. Solicitacoes de compra (RF08).
7. Portal do familiar (RF09).
8. Testes integrados, desempenho da ficha, responsividade e homologacao.

Essa ordem respeita as dependencias: o familiar precisa existir antes do acesso externo, e a ficha/prontuario precisa existir antes de ser publicada. Compras dependem do cadastro de itens, mas nao devem movimentar o estoque sem uma entrada registrada.

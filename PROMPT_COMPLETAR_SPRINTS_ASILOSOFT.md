# Prompt para concluir as sprints parcialmente implementadas do AsiloSoft

> Copie este arquivo para outro chat com acesso ao repositório. A solicitação abaixo é de implementação: analisar o estado atual, corrigir as lacunas, validar os fluxos e atualizar a documentação. Não parar apenas no planejamento.

## 1. Objetivo e contexto

Continue o desenvolvimento do AsiloSoft e conclua as partes incompletas das sprints S2, S3, S5, S6 e S7, preservando as funcionalidades existentes. Inclua os testes de integração necessários para demonstrar essas entregas; não trate toda a S8–S11 como automaticamente concluída.

- Repositório: https://github.com/RodrigoBurattoRibas/asilosoft.git
- Branch de referência: `feat/add-nova-estrutura`.
- Diagnóstico deste prompt: 20/10/2026, commit `b9e33b8`.
- Stack: PHP 8, JavaScript puro, HTML/CSS, MySQL via PDO, sem framework.
- Arquitetura: monólito modular com rotas `?route=...`, Controller, Service, Repository e View.
- **A integração do sistema com o banco já foi implementada. Não recriar essa integração, não substituir MySQL por dados de sessão e não confundir ausência de um teste executado com ausência de implementação.**

O planejamento previa que o grupo estivesse entre a quinta e a sexta sprint, mas atividades das sprints 6 e 7 foram adiantadas. O código evoluiu desde a revisão `7226ee4` analisada anteriormente: agora existem módulos web de familiares, acompanhamento/ficha e compras. Não repetir o diagnóstico antigo de que essas áreas existem apenas no banco.

Os pontos 1 a 7 de revisão documental da M1 foram informados como conferidos e corrigidos. Isso não equivale a afirmar que as sprints S1 a S7 estão todas concluídas.

## 2. Antes de alterar

1. Leia as instruções aplicáveis do repositório, `README.md`, `GUIA_TECNICO.md`, `OBJETIVOS_E_REQUISITOS.md`, `docs/GUIA_BANCO.md`, migrations, rotas e implementações atuais.
2. Verifique branch, commit, alterações locais e evolução posterior a este diagnóstico. Não sobrescreva trabalho do usuário nem execute reset/clean destrutivo. Não troque de branch com alterações locais sem preservar o trabalho.
3. Existe o arquivo `PLANEJAMENTO_IMPLEMENTACAO_ACOMPANHAMENTO_FAMILIARES_COMPRAS.md`, que estava não rastreado nesta revisão. Preserve-o. Use-o como contexto, não como retrato atualizado do sistema.
4. Revalide cada lacuna abaixo e classifique-a como existente, parcial, corrigida ou não reproduzida. Não refaça o que já funciona.
5. Use banco local/de teste isolado e dados fictícios para testes de escrita. Não execute fixtures, testes destrutivos ou migrations experimentais no banco real. Não exiba nem copie segredos do `.env`.
6. Registre um checklist curto, execute as tarefas em ordem de dependência e prossiga até completar o escopo. Faça perguntas apenas quando uma decisão de negócio não puder ser resolvida pelo código/requisitos disponíveis.

### Informações antigas que precisam ser desconsideradas

- O projeto atual exige PDO/MySQL; não implementar o fallback em sessão sugerido no planejamento antigo.
- Já existem `src/core/Security/AsiloAuthorization.php` e `src/presentation/components/asiloAcessoNegado.php`. Reutilize-os; não crie duplicatas.
- Já existem `familiares`, `zacompanhar` e `zcompras`. Os nomes com prefixo `z` não justificam reescrever ou renomear os módulos sem necessidade.
- Não supor que ainda há tabelas legadas de estoque em uso. Verificar o código e um ambiente de teste antes de propor migração de dados antigos.

## 3. Mapa de requisitos e situação de partida

| Sprint | Requisitos / histórias / casos de uso | Implementação observada | Trabalho restante |
| --- | --- | --- | --- |
| S1 | RF11/US01/UC13 e RF10/US02/UC12 | Login, usuários e hash de senha | Preservar e testar regressões de acesso |
| S2 | RF01/US03/UC01 e RF02/US04/UC02 | Idosos, quartos, módulo de familiares e rotas de vínculo | Completar associação conta-familiar e vínculo na interface; validar integridade |
| S3 | RF03/US05/UC03 e RF05/US06/UC06 | Prontuário, repository de saúde e rota `idosos-ficha` | Corrigir autorização de escrita, busca, navegação e separação de camadas |
| S4 | RF04/US07/UC04 e RF06/US08/UC07 | Materiais, entradas, retiradas e histórico | Preservar; testar saldo e concorrência ao integrar consumo individual |
| S5 | RF07/US09/UC09 e RF12/US10/UC05 | Origem de entradas; tabela/triggers de uso individual | Implementar fluxo web completo de uso por idoso sem saída duplicada |
| S6 | RF08/US11/UC10 | Módulo `zcompras`, lista, formulário e mudança de situação | Corrigir ciclo de estados, validação e camadas; testar ponta a ponta |
| S7 | RF09/US12/UC11 | `meus-idosos`, `acompanhamento-familiar` e consulta filtrada por vínculo | Conectar conta/vínculo/navegação e validar isolamento dos familiares |

## 4. Etapa A — resolver problemas de integração antes das novas telas

### A1. Autorizações coerentes entre PHP e banco

O método `exigirAcessoSaude()` de `AsiloIdosoController` aceita Administrador e Profissional de Saúde tanto para leitura quanto para registro. Entretanto, as triggers de `registros_saude` exigem autor com perfil Profissional de Saúde.

- Separar autorização de leitura e escrita: Administrador e Profissional de Saúde podem consultar a ficha/prontuário; somente Profissional de Saúde registra anotações, conforme a regra atual do banco.
- Ocultar controles de escrita para quem só consulta e bloquear POST direto, antes de tentar inserir no banco.
- Reutilizar a autorização existente nos módulos novos e manter a verificação no servidor, independentemente dos menus.
- Não ampliar a permissão clínica do Administrador silenciosamente nem remover a trigger para contornar o problema.

**Aceite:** Administrador consulta, mas não grava saúde; Profissional de Saúde grava; Familiar e Almoxarife não acessam a ficha clínica completa. Nenhuma tentativa negada gera erro SQL exposto.

### A2. Rotas duplicadas e navegação

- `quartos-criar` e `quartos-atualizar` aparecem no módulo `idosos` e no módulo `quartos`. Inspecionar `RouteLoader`, ordenação e comportamento de `Router::add` para identificar qual handler e escopo CSRF prevalecem.
- Definir um fluxo coerente, com nomes de rota sem colisão ou reaproveitamento explícito do mesmo handler. Conferir os formulários e os tokens de ambos os pontos de entrada.
- O carregador atual busca `*/routes/web.php`; não presumir que `zacompanhar/routes/compras.php` é carregado. Verificar referências antes de remover ou consolidar arquivos.
- Conferir home, layout e links para familiares, ficha, prontuário, compras e portal familiar. O profissional deve encontrar seus idosos autorizados sem ganhar permissão para alterar cadastros administrativos.

**Aceite:** navegação por perfil chega às rotas corretas, sem URL manual obrigatória, sem 404 inesperado e sem incompatibilidade de CSRF.

### A3. Migrations existentes

Há dois arquivos com prefixo `006`: `006_alinhar_almoxarifado_modulos.sql` e `006_almoxarifado_tipo.sql`. Ambos verificam/adicionam `materiais.tipo`, mas um usa VARCHAR e outro ENUM. O migrator registra o nome completo, não apenas o prefixo; portanto, não concluir que existe colisão de chave de versão somente pelo número.

- Comparar instalação nova com atualização de instalação existente e verificar se a ordem resulta em schemas diferentes.
- Preservar migrations já aplicadas. Se for necessário convergir o schema, criar uma migration posterior com versão disponível, compatível com os dados existentes.
- Verificar aplicação e segunda execução do bootstrap, sem perda de dados e sem alteração inesperada de saldo/histórico.

**Aceite:** instalação nova e atualização convergem para o contrato esperado pela aplicação; segunda execução não reaplica alterações indevidas.

## 5. Etapa B — familiares e vínculos (S2 / RF02)

Arquivos principais: `src/modules/familiares/{controller,services,repository,view,routes}` e ficha em `src/modules/zacompanhar/view/ficha.php`.

Lacunas observadas:

- Service/repository aceitam `usuario_id`, mas `familiares/view/formulario.php` não oferece associação à conta. Na edição, a ausência do campo pode resultar em perda de vínculo de acesso.
- Existem rotas de vincular/desvincular, mas a ficha inspecionada apenas lista familiares, sem controles para gerenciar esses vínculos.
- A troca do responsável principal faz UPDATE seguido de INSERT/UPDATE sem transação explícita.

Tarefas:

- [ ] Oferecer seleção de conta ativa de perfil Familiar, permitindo contato sem conta quando compatível com o schema.
- [ ] Validar associação no backend, unicidade prevista no banco e conflitos entre contas/familiares. Preservar o vínculo existente quando a edição não solicitar sua alteração; distinguir campo ausente de remoção explícita.
- [ ] Completar UI para vincular/desvincular familiar e idoso, registrar parentesco e indicar responsável principal.
- [ ] Permitir vários familiares por idoso e um familiar vinculado a vários idosos, sem duplicidade.
- [ ] Validar IDs positivos, existência dos registros, limites de campos, e-mail e tratamento de conflitos antes de expor erro de banco.
- [ ] Tornar a alteração do responsável principal atômica; validar o comportamento concorrente conforme a regra de um principal adotada pelo fluxo atual.

**Aceite:** administrador cadastra contato, associa conta, vincula a dois idosos, edita sem perder acesso, altera principal e remove vínculo; dados persistem após nova sessão. Familiar não pode gerenciar vínculos.

## 6. Etapa C — prontuário e ficha pesquisável (S3 / RF03 e RF05)

Arquivos principais: `AsiloIdosoController`, `AsiloIdosoService`, `AsiloIdosoRepository`, `AsiloSaudeRepository`, `zacompanhar/controller/AsiloFichaIdosoController.php` e views correspondentes.

- [ ] Manter a ficha existente; completar acesso por busca de nome parcial, CPF normalizado e identificador. Não considerar métodos internos de unicidade como substitutos de uma busca utilizável pela interface.
- [ ] Evitar duplicação de idosos na busca quando houver vários familiares. Implementar ordenação estável e paginação/limites quando necessário.
- [ ] Exibir dados pessoais, quarto, contatos e parentesco, prontuário por data/autor e histórico de consumo individual.
- [ ] Disponibilizar consulta ao profissional sem liberar botões administrativos de cadastro, edição ou desativação.
- [ ] Extrair regras clínicas do controller para service; manter SQL nos repositories. A ficha também contém consulta direta de uso de materiais que deve ser organizada sem mudar o comportamento correto.
- [ ] Validar descrição e visibilidade do registro; derivar autor da sessão, nunca de um campo enviado pelo cliente.
- [ ] Definir um fluxo auditável de correção/atualização do acompanhamento, atendendo RF03 sem apagar silenciosamente o histórico. Reutilizar auditoria existente e adicionar migration apenas se necessária.
- [ ] Manter registros privados fora do portal familiar. Verificar autorização e publicação em cada requisição.

**Aceite:** usuário autorizado encontra a ficha pelos três critérios; profissional registra acompanhamento privado e publicado; a ficha exibe ambos, mas o familiar vinculado vê apenas o publicado. IDs inválidos e inexistentes têm resposta controlada.

## 7. Etapa D — uso de material por idoso (S5 / RF12)

A tabela `usos_materiais`, as triggers de saída e a leitura de histórico na ficha já existem. As rotas inspecionadas do almoxarifado ainda cobrem somente entrada e retirada genérica.

- [ ] Criar formulário e POST para consumo individual, seguindo as camadas existentes. Nomes possíveis: `almoxarifado-uso` e `almoxarifado-registrar-uso`, se ainda estiverem disponíveis.
- [ ] Selecionar idoso e material válidos/ativos; validar quantidade decimal positiva, precisão e limite compatíveis com DECIMAL do schema, finalidade/observação e usuário autenticado.
- [ ] Definir explicitamente os perfis autorizados a consumir: preservar Administrador/Almoxarife; verificar se o fluxo assistencial do Profissional de Saúde é requerido e documentar a decisão sem conceder acesso geral ao estoque ou à gestão de compras.
- [ ] Inserir o consumo em `usos_materiais`. Inspecionar a trigger `ai_um_registrar_saida`: não inserir uma segunda saída nem subtrair saldo manualmente se o banco já faz isso.
- [ ] Garantir atomicidade: saldo insuficiente não deixa uso, movimentação ou auditoria parcial confirmados.
- [ ] Conferir concorrência com transações/bloqueios apropriados; validação de saldo somente antes da gravação não é garantia contra duas retiradas simultâneas.
- [ ] Fazer o consumo aparecer na ficha e no extrato com autor, quantidade, data e vínculo, sem alterar o contrato da retirada genérica.
- [ ] Evitar duplicação por reenvio acidental de formulário, com mecanismo proporcional ao fluxo; POST/Redirect/GET sozinho não impede toda repetição.

**Aceite:** um uso reduz saldo exatamente uma vez e gera uma movimentação vinculada. Operação acima do saldo não grava nada. Duas operações concorrentes não geram saldo negativo. Reenvio do mesmo consumo não duplica a baixa.

## 8. Etapa E — completar solicitações de compra (S6 / RF08)

O módulo `zcompras` já lista, cria e altera situação. Não o recriar. O controller atual concentra SQL/regras; a view só mostra mudança de situação para Pendente, enquanto o endpoint aceita estados sem conferir a transição de origem nem a existência do registro.

- [ ] Separar Controller, Service e Repository, mantendo rotas e views existentes quando possível.
- [ ] Validar material ativo, quantidade com precisão/limite do banco, justificativa e solicitante derivado da sessão.
- [ ] Criar sempre em Pendente. Verificar existência antes de alterar; nunca anunciar sucesso para ID inexistente.
- [ ] Definir máquina de estados coerente. Na ausência de regra mais específica, usar Pendente → Aprovada ou Recusada e Aprovada → Atendida; Recusada/Atendida terminais. Registrar essa decisão como regra de implementação, não como exigência literal original da M1.
- [ ] Aplicar transições no backend e refletir exatamente as mesmas opções na interface. Testar concorrência de atualização para impedir transição sobre estado obsoleto.
- [ ] Administrador/Almoxarife criam e consultam; somente Administrador altera situação, conforme o fluxo atual.
- [ ] Solicitação/aprovação/atendimento não devem movimentar estoque implicitamente. A entrada de material continua sendo registrada no fluxo de estoque com sua origem.
- [ ] Preservar autoria, data e auditoria existente das alterações.

**Aceite:** criação, aprovação, recusa e atendimento são navegáveis e persistentes. POST forjado, estado inválido, ID inexistente e perfil sem permissão são rejeitados sem alteração de dados.

## 9. Etapa F — completar acompanhamento familiar (S7 / RF09)

As rotas `meus-idosos` e `acompanhamento-familiar`, as views e a consulta por vínculo já existem. Concluir e validar o encadeamento inteiro.

- [ ] Garantir conta Familiar → cadastro em `familiares` → vínculo em `idosos_familiares` → acompanhamento publicado.
- [ ] Usar a identidade autenticada em todas as consultas; nunca confiar em `familiar_id` ou `usuario_id` informado na URL/formulário.
- [ ] Validar conta sem cadastro, familiar sem vínculos, idoso sem registros públicos e acesso após remoção do vínculo.
- [ ] Conferir regra para idosos desativados de forma consistente entre listagem e URL direta, sem inventar uma obrigação que não esteja nos requisitos.
- [ ] Testar troca do ID na URL: familiar A não vê o idoso exclusivo do familiar B, nomes, saúde privada nem outros dados da ficha administrativa.
- [ ] Conferir navegação da home/menu e mensagens de estado vazio sem expor dados de terceiros.
- [ ] Desativação da conta deve bloquear o acesso na requisição seguinte.

**Aceite:** a jornada pode ser concluída pela interface, usando dados persistentes, e o isolamento continua válido mesmo acessando endpoints diretamente.

## 10. Validação obrigatória e regressões

Criar testes úteis para as regras e falhas corrigidas, usando o mecanismo existente ou uma estrutura simples compatível com a stack. Não introduzir framework/dependências apenas para reestruturar o projeto. Não declarar teste aprovado sem executá-lo.

| ID | Cenário | Resultado esperado |
| --- | --- | --- |
| T01 | Cadastro/edição do familiar com conta | Associação preservada, sem conta de perfil incorreto |
| T02 | Um familiar em dois idosos e vários familiares no mesmo idoso | Vínculos corretos, sem duplicação; principal consistente |
| T03 | Busca por nome, CPF e identificador | Resultado correto e ficha acessível ao perfil permitido |
| T04 | Administrador tenta POST clínico; profissional grava | Primeiro negado, segundo persistido com autor real |
| T05 | Registro privado e publicado | Só o publicado aparece para familiar vinculado |
| T06 | Familiar troca IDs, perde vínculo ou é desativado | Dados protegidos e acesso revogado |
| T07 | Uso individual válido, sem saldo e concorrente | Uma baixa por uso; rollback em falha; saldo nunca negativo |
| T08 | Entrada sem origem e quantidades inválidas | Rejeição controlada sem escrita parcial |
| T09 | Compras e ciclo de estados | Transições permitidas funcionam; inválidas negadas; estoque inalterado |
| T10 | CSRF ausente/inválido e método HTTP incorreto | Mutação rejeitada em todas as rotas afetadas |
| T11 | Quartos pelas duas entradas da interface | Handler/token coerentes; capacidade e ocupação preservadas |
| T12 | Banco novo, banco existente de teste e bootstrap repetido | Schema convergente e dados preservados |
| T13 | Logout/login e recarregamento | Dados persistentes; nenhum fallback de sessão |
| T14 | Regressão de login, usuários, quartos e estoque | Fluxos anteriores continuam operacionais |

Também executar análise de sintaxe PHP e revisão da interface em tamanhos de celular, tablet e desktop. Registrar quais navegadores foram realmente testados. Medir consultas relevantes quando houver ambiente adequado, informando massa de dados e condições. Não afirmar RNF05 (2 segundos) ou RNF06 (99% de disponibilidade) sem medição correspondente.

## 11. Restrições e qualidade

- Não reescrever o sistema, trocar stack, adicionar framework nem alterar a integração já existente sem necessidade demonstrada.
- Não remover dados, migrations aplicadas ou auditoria para fazer testes passarem.
- Não fazer DDL de criação de tabelas dentro de requisições normais da aplicação.
- Manter prepared statements, escape de saída, CSRF e validação de método HTTP.
- Tratar erros de domínio e de integridade com mensagens compreensíveis, sem SQL/credenciais para o usuário.
- Revisar `display_errors` antes de uso em produção; não alterar implantação real como parte deste prompt.
- Evitar limpeza cosmética extensa; formatar e separar responsabilidades dos trechos alterados para torná-los legíveis.
- Fora do escopo: financeiro, boletos, folha/ponto, chat/videochamada, integração externa com governo/hospitais e aplicativo nativo.
- Não editar o relatório do Google Docs, evidências ou contribuição individual nesta tarefa. Apenas indicar ao final o que o relatório precisará atualizar.

## 12. Entregáveis e definição de pronto

Entregar código integrado, migrations adicionais quando necessárias, testes executáveis e documentação atualizada. Atualizar README, guia técnico e mapa de requisitos com o estado real; corrigir textos antigos que dizem que os módulos web não existem.

Produzir `STATUS_CONCLUSAO_SPRINTS.md` contendo:

1. Commit/branch de partida e resumo das mudanças.
2. Tabela por sprint e RF com status: concluído e validado, implementado mas não validado, parcial ou bloqueado.
3. Arquivos relevantes, decisões de negócio adotadas e migrations necessárias.
4. Comandos e cenários realmente executados, resultado e limitações.
5. Pendências reais, caso ainda existam, sem preencher com sucesso presumido.
6. Ajustes recomendados no relatório M2, sem alterá-lo automaticamente.

Uma sprint só será marcada como concluída quando o fluxo correspondente estiver acessível pela interface, persistir no MySQL, respeitar perfis e regras, tratar erros e passar pelos testes aplicáveis. Existência de tabela, rota ou tela isolada não basta.

Comece pela inspeção e pelo checklist, depois implemente as etapas A → B → C → D → E → F, reaproveitando tudo que já estiver correto. Se uma etapa já estiver concluída na branch mais recente, valide-a e avance. Continue até concluir o escopo; se houver impedimento externo, conclua as tarefas independentes e descreva precisamente o bloqueio.

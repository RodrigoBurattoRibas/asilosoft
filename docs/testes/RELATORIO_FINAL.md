# Relatório de Testes - resultado desta etapa

AsiloSoft - 23/11/2026. Este relatório retrata a etapa de unitários e integração; não afirma conclusão de todas as categorias do M3.

## 1. Resumo de execução

Foram documentados e executados 77 casos/cenários: 71 unitários e seis cenários compostos das suítes de integração existentes. Resultado final: 77 passaram, zero falharam, zero bloqueados entre esses casos na execução final. Além deles, o script legado de estoque/compras passou em 40 verificações; elas não são somadas aos 77. A verificação de sintaxe dos arquivos PHP de src, tests e scripts também passou.

Evidência final: evidencias/20261123T205106_29124/execucao.log; resumo das integrações em resumo.txt; resultados unitários por ID em unitarios.json; seis logs CT-I01 a CT-I06. Início 20:51:06 e fim 20:51:15, horário de São Paulo, em 23/11/2026. A tabela dos casos referencia os IDs dessas evidências.

Cobertura de linhas medida com phpdbg: 224 de 329 linhas executáveis reconhecidas pelo driver nos dez arquivos de serviços, ou 68,09%. A meta global de 60% foi atingida nesse escopo. A primeira coleta foi 39,82% com os 50 casos anteriores; foram acrescentados 21 casos relevantes, sem alterar a aplicação ou reduzir o denominador. O relatório detalhado, instalação, comandos e limitações estão em PHPDBG.md. A ausência anterior de driver de cobertura fica registrada como histórico; a solução usa phpdbg 8.4.28 extraído em /tmp, sem instalação global. Não é cobertura da aplicação inteira nem de ramos/caminhos.

## 2. Achados de integração e segurança parcial

As migrations preservaram dados e saldos e foram idempotentes. A clínica preservou autoria e histórico, acrescentando retificações sem sobrescrever registros. Os testes recusaram dados inválidos e aplicaram limites. O consumo repetido com a mesma chave baixou estoque uma única vez; duas retiradas concorrentes de 5, após saldo de 7.875, admitiram somente uma e resultaram em 2.875. Vínculos familiares passaram também pelas operações concorrentes.

Os testes HTTP confirmaram bloqueio de arquivos internos, controle por perfil, proteção CSRF e método HTTP, revogação de acesso familiar e logout. Esses resultados são evidências parciais de autenticação/sessão e controle de acesso. Não houve varredura completa de SQL Injection ou XSS, teste de todas as superfícies nem certificação de segurança. Nenhuma nova vulnerabilidade ou defeito funcional foi encontrado e corrigido nesta etapa; a aplicação não precisou ser alterada.

## 3. Performance, carga, usabilidade e E2E

Não foram executadas novas medições de performance/carga nesta etapa. Existe um benchmark simples de busca no repositório e no runner original, mas ele não integra esta evidência final nem comprova média/p95/throughput e carga simultânea exigidos no M3. Não há gráficos nem conclusão de capacidade ou gargalo.

Os testes HTTP não substituem três fluxos completos com navegador, JavaScript e evidência visual. Não foram avaliados três usuários, acessibilidade ou usabilidade; não existem resultados fictícios de feedback. Estas categorias são aplicáveis e pendentes.

## 4. Ocorrências e defeitos

A primeira execução de integração não conseguiu abrir conexão porque o ambiente restringia sockets. A tentativa foi preservada em evidencias/20261123T201656_2/execucao.log. A instância MySQL descartável e os testes foram posteriormente executados com acesso local permitido, e as seis suítes passaram. Trata-se de impedimento do ambiente, não de bug funcional demonstrado. A execução intermediária aprovada também foi preservada.

Não há comparação de correção antes/depois nem defeito funcional novo a apresentar. O registro de defeitos e pendências descreve isso explicitamente. Se a equipe encontrar um problema durante as categorias restantes, deverá registrar reprodução, resultado esperado/obtido, causa, correção e reexecução, sem modificar evidências antigas.

## 5. Lições e melhorias futuras

Dublês permitem exercitar limites rapidamente, mas não comprovam transações nem travas. MySQL real é necessário para esses contratos. Separar servidor e nomes de teste evita misturar massa de testes com dados reais. Quantidade de testes e aprovação não demonstram cobertura estrutural ou ausência de defeitos.

Próximos passos: manter a cobertura acima de 60% e ampliar os serviços ainda pouco cobertos, especialmente idosos e compras; expandir validações de campos e caminhos negativos dos demais serviços; concluir E2E visual, performance/carga, segurança e feedback; coletar prints datados; preparar vídeo e revisar documentação antes do envio. Os PDFs representam a versão atual e devem ser atualizados se novos resultados forem incluídos.

## 6. Uso de IA

Foi utilizado OpenAI Codex neste chat para analisar os materiais da professora e o repositório, implementar os novos testes unitários e runner de evidências, executar/verificar testes e elaborar tabelas, plano, relatório e PDFs. As integrações já existentes foram reaproveitadas. Os resultados apresentados foram obtidos por execução real dos scripts; nenhuma evidência foi inventada. A equipe deve revisar código, expectativas, documentação e saber explicar as decisões. Outras IAs eventualmente utilizadas anteriormente pelos integrantes devem ser declaradas pela equipe; não foram inferidas neste relatório.

## 7. Situação para entrega

Concluídos nesta etapa: testes unitários, cobertura phpdbg de 68,09% nos serviços, seis integrações, tabelas acima do mínimo de 20 casos, plano PDF, relatório parcial das categorias solicitadas e logs rastreáveis. Pendentes: três E2E, relatórios/gráficos de performance e carga, segurança completa, avaliação com três usuários, prints/vídeos e uploads. A entrega acadêmica integral não está concluída.

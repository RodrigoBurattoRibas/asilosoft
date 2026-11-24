# Validação antes dos commits

Em 23/11/2026, a versão revisada dos testes do AsiloSoft foi executada com sucesso entre 21:33:34 e 21:33:43, no horário de São Paulo. Os resultados desta revalidação estão em [evidencias/20261123T213334_34296](evidencias/20261123T213334_34296/execucao.log).

| Verificação | Resultado | Evidência |
| --- | --- | --- |
| Unitários | 71 casos aprovados, nenhuma falha | [unitarios.json](evidencias/20261123T213334_34296/unitarios.json) |
| Cobertura phpdbg | 224/329 linhas nos dez serviços, 68,09%, meta de 60% atingida | [JSON](evidencias/20261123T213334_34296/cobertura.json) e [HTML](evidencias/20261123T213334_34296/cobertura.html) |
| Integrações reais com MySQL | Seis suítes aprovadas | [resumo.txt](evidencias/20261123T213334_34296/resumo.txt), com logs CT-I01 a CT-I06 na mesma pasta |
| Verificações legadas de estoque e compras | 40 verificações aprovadas, sem somar como novos casos | [execucao.log](evidencias/20261123T213334_34296/execucao.log) |
| Sintaxe PHP | Arquivos de src, tests e scripts aprovados; index.php e router.php também verificados | [sintaxe.log](evidencias/20261123T213334_34296/sintaxe.log) |
| Controle do coletor | Ramo executado, ramo não executado e método nunca chamado classificados corretamente; escopo filtrado e HTML escapado | tests/coverage/CollectorTest.php, executado via phpdbg |
| Isolamento de saídas | Duas execuções unitárias simultâneas em pastas distintas, ambas com 71 casos aprovados | Controle local com ASILOSOFT_TEST_REPORT_DIR; arquivos temporários em /tmp |
| Ausência do driver | Código 2, sem alegar medição de cobertura | php tests/unit/run.php --coverage no PHP CLI sem driver |
| Falha de inicialização | Código 1 e nenhum relatório de cobertura reaproveitado | Controle local do wrapper com PHPDBG_BIN=false e pasta de saída própria |
| Sintaxe Bash e whitespace | bash -n e git diff --check aprovados | Executados antes dos commits |

## Ajustes da revisão

Os runners agora gravam diretamente na pasta da execução definida por ASILOSOFT_TEST_REPORT_DIR. A execução M3 utiliza a subpasta cobertura para o coletor, preservando o log geral e copiando seus relatórios para o diretório principal. A geração do catálogo lê o JSON dessa mesma execução. Isso evita misturar resultados simultâneos ou copiar relatórios antigos.

A documentação técnica versionada é Markdown. Exportações locais em Word/PDF não entram nos commits. Nenhum arquivo de src foi alterado nesta entrega de testes. As seis integrações já existiam e foram reaproveitadas, documentadas e reexecutadas; os 71 cenários unitários e o coletor phpdbg foram adicionados.

A validação utilizou phpdbg 8.4.28 isolado em /tmp, PHP CLI 8.4.25 e MySQL 8.0.46 descartável, separado do banco da aplicação. A configuração de reprodução e a instalação permanente do phpdbg estão em [PHPDBG.md](PHPDBG.md).

A aprovação refere-se ao escopo implementado. E2E visual, performance/carga com gráficos, segurança completa, avaliação com três usuários e vídeo continuam pendentes para a entrega M3 integral, conforme [DEFEITOS_E_PENDENCIAS.md](DEFEITOS_E_PENDENCIAS.md).

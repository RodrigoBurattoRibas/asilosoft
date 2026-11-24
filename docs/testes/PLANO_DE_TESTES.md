# Plano de Testes - AsiloSoft

Versão 1.1 - 23/11/2026 - entrega M3 de Engenharia de Software.

## 1. Objetivo e referências

Verificar regras de negócio e integração entre camadas do AsiloSoft com testes repetíveis e dados fictícios, documentando entradas, expectativas, resultados e limitações. Referência principal: M3 - Documento final do Projeto - Testes, seções 3.1 a 3.5 e checklist. Referências de apoio: aulas 10 (níveis e estratégia), 11 (anatomia do caso) e 12 (automação e regressão), da Profa. Cláudia Zambon.

O M3 solicita entrega em 30/11/2026 até 20h pelo AVA e apresentação em 30/11 ou 07/12. A equipe deve conferir sua data. O requisito de integração diverge entre tabela (3 cenários) e checklist (5); esta etapa adota pelo menos 5. Ferramentas da tabela são sugestões; a escolha de PHP puro utiliza a linguagem e os recursos já presentes no projeto.

## 2. Visão geral e escopo incluído

O AsiloSoft centraliza cadastros de idosos, quartos, usuários e familiares; prontuário e consulta familiar autorizada; materiais, estoque, consumo e solicitações de compra. A aplicação é PHP com persistência MySQL, organizada em serviços, repositórios, controladores e rotas.

Entram nesta etapa: limites e validações de quartos, senha de usuário e último administrador; quantidades decimais e restrições de retirada; transições de compras; autoria/perfil e entradas de saúde; e-mail opcional de familiares; vaga e liberação de quarto do idoso. São 71 cenários unitários, com dublês de repositório e sem banco.

Também entram as seis suítes existentes de integração: migrations e invariantes do banco; familiares e vínculos concorrentes; prontuário e auditoria; estoque/compras e concorrência; inicialização do administrador; HTTP, sessão, CSRF e permissões. Cada suíte contém várias verificações; a tabela registra seis cenários compostos sem inflar o total de casos.

## 3. Fora desta etapa e aplicabilidade

A coleta via phpdbg foi acrescentada e atingiu 68,09% (224/329 linhas) nos dez serviços. A meta global de 60% foi atingida nesse escopo. Foram acrescentados testes dedicados à autenticação, sessão, ficha consolidada e outras regras. O procedimento e as limitações estão em PHPDBG.md. Não há comprovação de cobertura total de métodos, ramos, regras ou vulnerabilidades.

Os três fluxos E2E em navegador, testes de performance com média/p95/throughput, carga/estresse com usuários simultâneos e gráficos, varredura de SQL Injection/XSS, avaliação de usabilidade/acessibilidade com três pessoas, prints/vídeo e publicação das evidências ficam pendentes. Todos se aplicam ao sistema. Não devem ser justificados como “não se aplica” apenas por falta de ferramenta ou tempo. Os scripts HTTP exercitam fluxos pelo protocolo, mas não executam JavaScript nem validam layout e acessibilidade visual. A concorrência de duas retiradas e dos vínculos valida consistência; não equivale à carga de 50/100/200 usuários.

## 4. Estratégia, técnicas e ferramentas

Unitários: PHP CLI e dublês escritos em PHP. O teste chama o serviço e compara valores, hash de senha, exceções esperadas e/ou argumentos das chamadas ao repositório. Negativos verificam que não houve gravação quando aplicável. Asserções são funções explícitas, independentes da configuração zend.assertions.

Integração: PHP PDO com MySQL real, migrations oficiais, transações, fixtures fictícias, pcntl para processos concorrentes e curl/proc_open para HTTP. Nenhuma credencial real é lida do .env pelos runners. Os nomes aceitos de banco precisam começar por asilosoft_test_. O banco precisa ser um servidor reservado para teste.

Técnicas: partição de equivalência (quantidade válida/inválida, perfil autorizado/não autorizado), valores-limite (capacidade 0/1/99/100; senha 7/8; decimal mínimo/máximo), tabela de decisão (20 combinações de situação de compra) e regressão de invariantes (idempotência, saldo e autoria). Casos e resultados estão nas tabelas CASOS_UNITARIOS.md e CASOS_INTEGRACAO.md.

Cobertura: phpdbg por meio de tests/coverage.sh, com Xdebug mantido como alternativa; todos os serviços como denominador, linhas sem execução incluídas; relatório JSON com total, linhas cobertas e detalhamento por arquivo. A métrica é de linhas, não de ramos nem percentual de regras. Os arquivos são carregados após iniciar a coleta. Sem driver, nenhum percentual é emitido.

## 5. Ambiente e massa

Execução verificada em 23/11/2026: Linux x86_64, PHP CLI 8.4.25, MySQL 8.0.46, Intel Core i5-13450HX, 16 CPUs lógicas visíveis, aproximadamente 15,3 GiB de memória visível ao ambiente. Recursos são compartilhados; estes dados não caracterizam benchmark controlado. Extensões: pdo_mysql, mbstring, curl e pcntl; proc_open disponível. Xdebug e PCOV ausentes. A coleta adicional usa phpdbg 8.4.28 extraído em /tmp, com extensões compatíveis com PHP 8.4; o PHP CLI das integrações permanece 8.4.25.

MySQL descartável inicializado em /tmp/asilosoft-m3-mysql, acesso somente em 127.0.0.1:33318. Servidor HTTP de teste em localhost, porta padrão 18087. Estes valores descrevem a execução realizada, não são obrigatórios na máquina do aluno. Não foram usados dados reais de residentes ou credenciais de produção. Senha vazia somente na instância descartável local; não é recomendação de configuração da aplicação.

Fixtures e ciclo de vida: database recria schema; familiares, instalação e HTTP criam/removem bases próprias; clínica mantém schema e reverte as fixtures; estoque mantém massa fictícia para inspeção. Encerrar e remover apenas a instância/bases descartáveis após a validação. Para reprodução, configurar MySQL próprio de testes conforme README.

## 6. Procedimento e critérios

Entrada: código disponível, PHP CLI e extensões instalados, MySQL exclusivo de testes, permissões de criação de bases/triggers, porta HTTP livre. Executar php tests/unit/run.php para unidade ou bash tests/m3.sh para o conjunto com evidências, após exportar as quatro variáveis explícitas do banco.

Um caso negativo passa quando a entrada inválida é recusada como previsto; ele não é uma falha da suíte. Uma falha é divergência entre expectativa e comportamento, ou erro inesperado. Falta de ambiente é impedimento e deve ser descrita como tal, sem culpar a regra de negócio. O runner interrompe as integrações na primeira falha, e as seguintes ficam não executadas.

Saída desta etapa: 71 unitários e seis cenários compostos de integração aprovados, sem falhas inesperadas, sintaxe válida e evidências rastreáveis. Saída da entrega M3 completa exige ainda concluir categorias restantes e preparar apresentação/vídeo. Não liberar o pacote acadêmico como integralmente concluído com base apenas neste plano.

## 7. Evidências e responsabilidades

O runner gera diretório com data/hora, log geral, JSON unitário, sintaxe, resumo e um log CT-I por suíte. Cada linha unitária traz CT-U e status. A execução final com cobertura aprovada está em evidencias/20261123T205106_29124/. A execução anterior em evidencias/20261123T201841_16280/ é preservada como histórico. Logs anteriores são preservados, inclusive a tentativa bloqueada por rede do ambiente.

A equipe deve revisar os documentos, executar no ambiente de apresentação, reexecutar a cobertura e concluir pendências, coletar feedback de três usuários, registrar defeitos reais, gravar contingência e fazer os envios ao AVA/Drive. A assistência automatizada é declarada no relatório final. Não foram enviados arquivos nem mensagens a serviços externos.

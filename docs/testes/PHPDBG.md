# Cobertura de testes com phpdbg

Em 23/11/2026, a suíte unitária do AsiloSoft atingiu **68,09% de cobertura de linhas**, com **224 de 329 linhas executáveis** reconhecidas pelo phpdbg em todos os dez arquivos de serviços. Os **71 casos unitários passaram**, e as seis suítes de integração também passaram na execução final. A meta global de 60% foi atingida sem adicionar framework, Composer ou dependência de produção à aplicação.

## O que foi adicionado

- `tests/support/LineCoverage.php`: coleta com phpdbg, filtro do escopo, cálculo e geração de JSON e HTML. O suporte anterior a Xdebug é mantido como alternativa.
- `tests/coverage.sh`: comando de cobertura com logs datados e cópias dos relatórios produzidos na execução. Retorna falha se houver teste reprovado ou cobertura inferior a 60%.
- `tests/unit/extended.php`: 21 cenários adicionais, CT-U51 a CT-U71, cobrindo lacunas apontadas pela primeira medição.
- `tests/coverage/CollectorTest.php` e `fixture.php`: controle independente que verifica ramo executado, ramo não executado, método nunca chamado, filtro dos arquivos e escape do HTML.
- `tests/m3.sh --coverage`: opção para executar a cobertura antes das seis integrações, mantendo os relatórios no diretório da entrega. Sem a opção, o comando anterior continua disponível.

A primeira medição dos 50 casos anteriores foi 39,82%, ou 131/329 linhas. Após acrescentar casos de autenticação, sessão, ficha, filtros, edição de usuários, estoque, vínculos, cadastro de idoso, retificação clínica e quarto vazio, o resultado foi 68,09%. Não foram removidos serviços do denominador nem somados testes de integração à cobertura unitária. Nenhum arquivo da aplicação em `src` foi alterado nesta adição.

## Instalação no ambiente de desenvolvimento

O phpdbg é um executável separado do ecossistema PHP. Não é uma extensão que precise ser habilitada no servidor web. Instale a versão correspondente à versão PHP da sua máquina; o projeto foi validado com PHP 8.4.

Exemplo para Ubuntu ou distribuição compatível, quando o pacote estiver disponível nos repositórios configurados:

```bash
sudo apt update
sudo apt install php8.4-phpdbg
command -v phpdbg8.4
export PHPDBG_BIN=phpdbg8.4
bash tests/coverage.sh
```

Se o pacote instalar o comando genérico `phpdbg`, basta executar `bash tests/coverage.sh`. O nome e a disponibilidade do pacote dependem da distribuição. Não configure uma versão com API incompatível com suas extensões. O runner de testes usa mbstring; as integrações continuam exigindo pdo_mysql, curl, pcntl e MySQL de testes, conforme o guia principal.

`PHPDBG_BIN` deve conter apenas o nome ou o caminho do executável. Não coloque argumentos nessa variável. As opções necessárias já estão no script. A variável vale para o terminal atual; não altera a aplicação, o `.env`, o Apache ou o PHP-FPM.

## Comandos de uso

Somente os casos unitários, sem cobertura e sem banco:

```bash
php tests/unit/run.php
```

Unitários e cobertura, com evidências datadas:

```bash
bash tests/coverage.sh
```

Coleta direta, com relatórios no diretório principal de evidências:

```bash
phpdbg -qrr -d opcache.enable_cli=0 -d opcache.jit=0 tests/unit/run.php --coverage
```

Substitua `phpdbg` por `phpdbg8.4` se esse for o comando instalado. `-q` reduz mensagens do debugger; `-rr` executa o script e encerra, sem deixar o prompt interativo aberto. OPCache para CLI e JIT ficam desabilitados nesta coleta para evitar otimizações dos opcodes analisados.

Para a entrega completa, configure explicitamente um MySQL exclusivo de testes:

```bash
export ASILOSOFT_TEST_DB_HOST=127.0.0.1
export ASILOSOFT_TEST_DB_PORTA=3306
export ASILOSOFT_TEST_DB_USUARIO=usuario_de_testes
export ASILOSOFT_TEST_DB_SENHA='senha_do_servidor_de_testes'
bash tests/m3.sh --coverage
```

O comando interrompe se a meta de cobertura ou os unitários falharem, antes de iniciar as integrações. As bases devem começar por `asilosoft_test_`, e os nomes distintos são gerados pelo runner. Nunca utilize servidor ou credenciais de produção.

Controle da implementação da coleta:

```bash
phpdbg -qrr -d opcache.enable_cli=0 tests/coverage/CollectorTest.php
```

Esse controle usa uma fixture isolada e não aumenta o percentual da suíte de negócio. Seus arquivos temporários são removidos após a aprovação.

## Como a medição funciona

Antes de carregar os serviços, o runner inicia `phpdbg_start_oplog()`. Todos os arquivos de `src/modules/*/services/*.php` são carregados, inclusive os serviços cujos métodos não forem chamados. Após a suíte, `phpdbg_end_oplog()` retorna as linhas executadas, e `phpdbg_get_executable()` identifica as linhas executáveis dos arquivos definidos no escopo.

O relatório cruza os dois conjuntos e calcula:

```text
cobertura = linhas executáveis cobertas / linhas executáveis totais × 100
resultado = 224 / 329 × 100 = 68,09%
```

Comentários e espaços em branco não são estimados manualmente. Linhas reconhecidas pelo driver são a unidade da contagem; uma linha é coberta quando houver execução registrada nela. O mesmo arquivo ou linha não é contado repetidamente por ter sido executado várias vezes. Métodos nunca chamados permanecem no denominador. O relatório guarda números de linha cobertos e não cobertos e SHA-256 de cada arquivo para vincular o resultado ao código analisado.

A validação da meta compara o valor completo, antes do arredondamento usado para exibição. A coleta não incorpora as integrações, controladores, views, repositórios, migrations, helpers de segurança ou scripts de teste ao denominador. Assim, o resultado é cobertura de linhas da camada de serviços, e não cobertura da aplicação inteira nem percentual de requisitos atendidos.

## Resultado por serviço

| Serviço | Linhas cobertas | Linhas executáveis | Cobertura |
| --- | --- | --- | --- |
| Almoxarifado | 64 | 87 | 73,56% |
| Autenticação | 18 | 18 | 100,00% |
| Familiares | 23 | 32 | 71,88% |
| Idosos | 22 | 64 | 34,38% |
| Saúde | 18 | 22 | 81,82% |
| Quartos | 23 | 31 | 74,19% |
| Filtro de situação | 6 | 6 | 100,00% |
| Usuários | 36 | 45 | 80,00% |
| Ficha consolidada | 5 | 5 | 100,00% |
| Compras | 9 | 19 | 47,37% |
| Total | 224 | 329 | 68,09% |

A meta foi aplicada ao total ponderado por linhas, não à média dos percentuais nem a cada arquivo. Idosos e compras são prioridades para ampliar os testes. Mesmo 100% de linhas em um serviço não significa que todos os valores, decisões ou caminhos tenham sido testados.

## Relatórios e evidências

O comando `bash tests/coverage.sh` salva uma pasta `docs/testes/evidencias/cobertura_<data>_<pid>/` contendo:

- `execucao.log`: IDs dos casos, status, percentual, meta e código de saída.
- `unitarios.json`: resultado individual dos 71 casos.
- `cobertura.json`: totais, driver, versão PHP, data, escopo, hashes e linhas por arquivo.
- `cobertura.html`: resumo por serviço e código fonte com linhas cobertas em verde e não cobertas em vermelho. Abra esse arquivo no navegador; ele não executa PHP nem precisa de servidor.

Os arquivos no diretório principal de evidências representam a última coleta direta e podem ser substituídos. As pastas datadas são snapshots preservados. O wrapper grava os artefatos diretamente na pasta da própria execução, evitando reutilizar relatórios antigos ou misturar resultados de execuções simultâneas. A variável opcional `ASILOSOFT_TEST_REPORT_DIR` define uma pasta de saída específica para os runners e para a geração do catálogo. No comando M3, a coleta fica na subpasta `cobertura/`, com cópias dos relatórios na pasta principal da execução.

A execução final com cobertura e integrações está em `evidencias/20261123T205106_29124/`. A coleta associada está em `evidencias/cobertura_20261123T205106_29132/`. As seis integrações passaram; o total atual da entrega é 77 casos/cenários documentados, sendo 71 unitários e seis cenários compostos de integração. As 40 verificações legadas complementares também passaram e não são somadas como 40 novos casos.

O controle do coletor foi aprovado e está em `evidencias/controle-coletor-phpdbg.log`. A primeira coleta, ainda abaixo da meta, foi preservada em `evidencias/cobertura_20261123T204806_2/`. Ela demonstra uma reprovação real da meta, mesmo com os 50 testes passando. Uma tentativa do comando completo sem variáveis de banco foi bloqueada antes das integrações e preservada em `evidencias/20261123T205031_28743/`.

## Códigos de saída e solução de problemas

| Código ou sintoma | Interpretação | Ação |
| --- | --- | --- |
| 0 | Testes passaram e a meta foi atingida | Consultar os relatórios e preservar a pasta datada |
| 1 | Algum caso falhou ou a cobertura ficou abaixo de 60% | Ler o log; corrigir defeito ou acrescentar cenários relevantes; executar novamente |
| 2 | Instrumentação não disponível ou configuração de banco ausente | Instalar/localizar phpdbg e configurar as variáveis exigidas |
| Outros códigos | Erro de inicialização, configuração ou falha inesperada | Ler stderr/log; não classificar como aprovação |
| phpdbg não encontrado | Executável ausente no PATH | Definir PHPDBG_BIN com caminho real ou instalar o pacote correspondente |
| mb_strtolower ou mb_strlen indefinida | mbstring ausente na configuração do phpdbg | Conferir o arquivo INI e a pasta de extensões usados por essa SAPI |
| PHP puro com --coverage retorna 2 | PHP CLI não contém driver de cobertura | Usar o wrapper phpdbg; PHP comum continua válido sem --coverage |
| Código 1 com todos os casos passando | A meta de cobertura não foi atingida | Abrir o HTML e cobrir regras ainda não exercitadas; não reduzir o escopo para inflar o resultado |

## Ambiente usado nesta validação

Para não alterar o PHP instalado, o pacote `php8.4-phpdbg` foi obtido pelo APT com índice isolado e extraído em `/tmp/asilosoft-phpdbg/runtime`. Não foi instalado globalmente. A versão executada foi PHP 8.4.28 SAPI phpdbg, usando mbstring da mesma API de PHP 8.4 disponível na máquina. O PHP CLI da aplicação e das integrações permaneceu em 8.4.25. A diferença de patch fica registrada no relatório; prefira versões alinhadas no ambiente de apresentação.

Comando usado neste ambiente temporário:

```bash
PHP_INI_SCAN_DIR=/etc/php/8.4/cli/conf.d \
PHPDBG_BIN=/tmp/asilosoft-phpdbg/runtime/usr/bin/phpdbg8.4 \
bash tests/coverage.sh
```

Esse caminho em `/tmp` é temporário e pode desaparecer após limpeza ou reinicialização. Para uso permanente, instale o pacote no ambiente de desenvolvimento ou disponibilize o executável em um caminho estável. `PHP_INI_SCAN_DIR` foi necessário porque o pacote foi apenas extraído, sem criar sua configuração de SAPI. Ele não é obrigatório em uma instalação normal e deve apontar para extensões compatíveis, sem carregar Xdebug/PCOV na mesma coleta.

## Limitações e manutenção

O phpdbg mede linhas, não ramos, condições ou caminhos completos. Linhas com várias expressões podem aparecer cobertas mesmo quando nem todas as alternativas foram executadas. A identificação das linhas executáveis depende do motor e pode divergir da contagem de Xdebug ou PCOV; compare resultados históricos usando o mesmo driver e versão.

O denominador desta entrega inclui todos os dez arquivos de serviços, incluindo métodos simples de delegação. Algumas regras transacionais também estão em repositórios e triggers, validadas por integração, mas não estão incluídas neste percentual. A meta de 60% foi operacionalizada para a camada de serviços e deve ser apresentada com esse escopo explícito à professora.

Para manutenção, acrescente casos para novas regras e execute a coleta novamente. Não edite os relatórios para alterar percentuais. Se novos serviços forem criados sob o caminho definido, entram automaticamente no escopo pelo glob. Arquivos cujo código não puder ser analisado não são tratados como cobertura zero silenciosa: a coleta falha para evitar um denominador incompleto.

E2E visual, performance/carga com gráficos, segurança completa, avaliação com três usuários e vídeo seguem pendentes. A cobertura agora medida resolve somente a pendência estrutural desta etapa. Não houve novo defeito funcional corrigido na aplicação; o resultado anterior de 39,82% foi uma insuficiência dos testes existentes, corrigida com novos cenários.

## Referências técnicas

- PHP Manual, [phpdbg](https://www.php.net/phpdbg).
- PHP Manual, [phpdbg_start_oplog](https://www.php.net/manual/en/function.phpdbg-start-oplog.php), [phpdbg_end_oplog](https://www.php.net/manual/en/function.phpdbg-end-oplog.php) e [phpdbg_get_executable](https://www.php.net/manual/en/function.phpdbg-get-executable.php).
- [Implementação das funções no código fonte oficial PHP 8.4](https://github.com/php/php-src/blob/PHP-8.4/sapi/phpdbg/phpdbg.c), consultada porque as páginas individuais do manual documentam principalmente as assinaturas.

Assistência de IA: OpenAI Codex foi usado para implementar o coletor e o wrapper, acrescentar cenários, executar a validação e elaborar a documentação. Os percentuais e resultados foram obtidos por execução real; a equipe deve revisar e saber explicar o escopo e as limitações.

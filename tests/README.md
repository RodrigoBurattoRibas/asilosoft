# Testes do AsiloSoft

PHP puro, sem framework nem instalação de dependências. Integrações exigem extensões já usadas pela aplicação (`pdo_mysql`, `mbstring`); HTTP usa `curl` e `proc_open`, concorrência usa `pcntl` do PHP CLI local. MySQL 8.0.16+ deve estar disponível **exclusivamente para testes**.

Não carregue `.env` de produção. Todos os runners exigem variáveis explícitas e nome iniciado por `asilosoft_test_`. O runner `database/DatabaseIntegrationTest.php` **recria** a base indicada. Familiares e HTTP criam base nova e a removem ao terminar, recusando nome já existente. Clínica mantém schema e reverte fixtures; estoque/compras preserva fixtures fictícias. Use nomes distintos como abaixo.

```bash
export ASILOSOFT_TEST_DB_HOST=127.0.0.1
export ASILOSOFT_TEST_DB_PORTA=33317
export ASILOSOFT_TEST_DB_USUARIO=root
export ASILOSOFT_TEST_DB_SENHA=''

php tests/estoque_compras_rules.php
ASILOSOFT_TEST_DB_NOME=asilosoft_test_database php tests/integration/database/DatabaseIntegrationTest.php
ASILOSOFT_TEST_DB_NOME=asilosoft_test_familiares php tests/integration/FamiliaresIntegrationTest.php
ASILOSOFT_TEST_DB_NOME=asilosoft_test_clinica php tests/integration/ClinicaIntegrationTest.php
ASILOSOFT_TEST_DB_NOME=asilosoft_test_estoque php tests/integration/EstoqueComprasIntegrationTest.php
ASILOSOFT_TEST_DB_NOME=asilosoft_test_http php tests/integration/HttpIntegrationTest.php
ASILOSOFT_TEST_DB_NOME=asilosoft_test_database php tests/integration/BuscaBenchmark.php
find src tests -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
git diff --check
```

A porta 33317 foi usada na instância descartável desta validação; substitua pelos valores do seu servidor **de teste**. O HTTP inicia seu próprio servidor em 127.0.0.1:18087 e o encerra ao terminar; `ASILOSOFT_TEST_HTTP_PORT` permite escolher outra porta livre. Não aponte para um servidor da aplicação já iniciado com banco real. A senha vazia de teste é definida dentro das requisições, evitando fallback acidental ao `.env` quando o processo filho omite variáveis vazias.

`ASILOSOFT_TEST_CAPTURE=1` salva cópias HTML com dados fictícios em `/tmp/asilosoft-ui`, para inspeção visual. São snapshots: navegação e POST devem ser validados pelo runner HTTP, não pelas cópias estáticas. Para visualizar com estilo, copie `src/assets` para o mesmo caminho relativo no diretório de captura.

O benchmark adiciona 1.000 residentes em transação, executa 100 buscas por critério e reverte a massa; requer base já migrada. Não mede rede externa, renderização, carga simultânea nem disponibilidade.

## Executar toda a entrega

Após definir explicitamente HOST, PORTA, USUARIO e SENHA de teste conforme acima:

```bash
bash tests/run.sh
```

O runner interrompe na primeira falha, gera nomes exclusivos `asilosoft_test_<data>_<pid>_<suite>`, verifica sintaxe e executa regras, migrations, familiares, clínica, estoque/compras, instalação inicial, HTTP e benchmark. Não carrega o `.env` para escolher o banco dos testes. As bases de database, clínica e estoque permanecem exclusivamente no servidor de testes para inspeção; familiares, instalação e HTTP removem suas bases no encerramento normal. Remova as bases retidas após analisar as evidências.

`InstalacaoIntegrationTest.php` verifica senha inválida sem gravação, primeiro administrador com hash e recusa de uma segunda inicialização. O teste HTTP usa `router.php`, verifica bloqueio de arquivos internos, assets públicos e retirada sem finalidade sem alterar saldo/histórico.

## Entrega M3 (novembro/2026)

- `php tests/unit/run.php`: 71 cenários isolados, sem MySQL; gera JSON com resultado por ID.
- `bash tests/m3.sh`: unitários, catálogo, sintaxe e seis integrações existentes; gera logs com horário em `docs/testes/evidencias/<execução>/`.
- `php tests/catalogo.php`: atualiza a tabela a partir do último JSON unitário.
- `XDEBUG_MODE=coverage php tests/unit/run.php --coverage`: se Xdebug já estiver disponível no PHP CLI, mede linhas em **todos** os serviços e falha abaixo de 60%. Sem a extensão, retorna código 2 e não inventa cobertura. Não adicionamos dependências ao projeto.

Plano, casos, resultados reais, limitações e roteiro de apresentação: [docs/testes/README.md](../docs/testes/README.md).

### Cobertura com phpdbg

`bash tests/coverage.sh` executa os unitários sob phpdbg e gera relatórios JSON/HTML e log datado. Defina `PHPDBG_BIN=phpdbg8.4` se esse for o comando instalado. `bash tests/m3.sh --coverage` inclui a medição antes das integrações. Resultado validado: 71 casos aprovados, 224/329 linhas nos dez serviços, 68,09%. Instalação, ambiente temporário usado, escopo, códigos de saída e limitações: [docs/testes/PHPDBG.md](../docs/testes/PHPDBG.md).

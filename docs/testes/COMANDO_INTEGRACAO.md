# Comando curto para executar as integrações

Na raiz do projeto, execute:

```bash
php tests/integration/run.php
```

Esse comando substitui o bloco PHP longo que configurava variáveis manualmente. Ele utiliza o servidor e as credenciais da aplicação, resolvidos pelo `.env` ou pelas variáveis de ambiente `MYSQL*`/`ASILOSOFT_DB_*`. Não é preciso copiar a senha para o terminal nem exportar as quatro variáveis de teste.

## O que o comando faz

1. Confere a conexão ao servidor configurado, sem selecionar ou migrar a base da aplicação.
2. Inicia `tests/m3.sh`, que executa os 71 unitários, a verificação de sintaxe, as 40 verificações legadas e as seis suítes de integração.
3. Cada suíte recebe uma base auxiliar exclusiva com prefixo `asilosoft_test_`, no mesmo servidor.
4. Exibe os resultados no terminal e salva os logs na pasta anunciada por `Evidências:`.
5. Ao terminar, inclusive após uma suíte falhar normalmente, remove as bases auxiliares retidas pelos testes. A limpeza considera somente os nomes anunciados pela execução atual.
6. Confere que essas bases não permanecem no servidor e salva `limpeza.json` junto das evidências.

O banco indicado por `MYSQLDATABASE`, `MYSQL_DATABASE` ou `ASILOSOFT_DB_NOME` não é usado para os dados fictícios dos testes. O comando exige que a conta configurada possa criar/remover bases e executar migrations e triggers. Utilize a conexão de testes já autorizada para o projeto; a base da aplicação é preservada, mas a criação de schemas ainda utiliza recursos e privilégios do servidor configurado.

## Unitários separados

Para executar somente os testes unitários, sem conexão ao MySQL:

```bash
php tests/unit/run.php
```

## Pré-requisitos e configuração

PHP CLI com pdo_mysql, mbstring, curl e pcntl; `proc_open` disponível; Bash; servidor MySQL acessível. A suíte HTTP utiliza a porta local 18087. Se ela estiver ocupada, é possível escolher outra:

```bash
ASILOSOFT_TEST_HTTP_PORT=18088 php tests/integration/run.php
```

A resolução da configuração é a mesma da aplicação, incluindo precedência das variáveis de ambiente sobre o arquivo e suporte a senha vazia. Valores de `ASILOSOFT_TEST_DB_*` eventualmente exportados em outro terminal não substituem a conexão escolhida por esse comando: o lançador preenche os valores a partir da configuração da aplicação. Os runners antigos continuam disponíveis para conexão explícita a outro servidor, conforme [README.md](README.md).

Para reutilizar a resolução existente sem duplicar o parser nem alterar código de produção, o lançador acessa `PDOMysqli::envValue` por reflexão. Se essa rotina interna for renomeada, o lançador também precisará ser atualizado. As credenciais são transmitidas ao processo por ambiente e não são incluídas em argumentos, mensagens ou no comprovante de limpeza.

## Resultados e erros

- Código 0: unitários e seis integrações aprovados, com limpeza concluída.
- Código 1: algum teste, inicialização do processo, gravação do comprovante ou verificação de completude falhou.
- Código 2: argumento inválido, conexão indisponível ou problema de permissão/acesso ao banco.

O runner para na primeira suíte reprovada e realiza a limpeza das bases anunciadas até esse ponto. Consulte `execucao.log`, `resumo.txt`, os logs `CT-I*.log` e `limpeza.json` na pasta da execução. Em caso de interrupção forçada do terminal/processo, a limpeza pode não ser executada; os nomes das bases criadas ficam registrados no log para remoção posterior.

A ajuda não abre conexão ao banco:

```bash
php tests/integration/run.php --help
```

Esse comando não mede cobertura de linhas e não faz deploy. Para cobertura via phpdbg, continue usando `bash tests/coverage.sh`, conforme [PHPDBG.md](PHPDBG.md).

## Validação realizada

O próprio comando `php tests/integration/run.php` foi validado no Railway em 24/11/2026. A suíte executou das 18:43:05 às 18:51:07, no horário de São Paulo, com 71 unitários, seis integrações e 40 verificações legadas aprovados. Às 18:51:13, o lançador confirmou seis bases auxiliares verificadas e nenhuma restante. Não houve alteração em arquivos de produção sob src.

- [Log da execução](evidencias/20261124T184305_23972/execucao.log).
- [Resumo das integrações](evidencias/20261124T184305_23972/resumo.txt).
- [Comprovante da limpeza automática](evidencias/20261124T184305_23972/limpeza.json).

Também foram verificados: ajuda sem conexão, argumento inválido com código 2, conexão indisponível com código 2 e ausência da senha fictícia na mensagem de erro. A verificação de sintaxe PHP/Bash e git diff --check passou.

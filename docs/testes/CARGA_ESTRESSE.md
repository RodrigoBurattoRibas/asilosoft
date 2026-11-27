# Testes de carga e estresse - AsiloSoft

Atualizado em 27/11/2026. Script: [tests/carga/CargaEstresse.php](../../tests/carga/CargaEstresse.php). PHP puro, sem dependências novas (não usa nem a extensão `curl`).

## 1. Objetivo

Verificar se o AsiloSoft atende ao RNF05 (consultas principais em até 2 segundos em uso normal) e ao RNF06 (disponibilidade mínima de 99%) com vários usuários ao mesmo tempo, e descobrir a partir de quantos usuários simultâneos o sistema degrada e quando ele deixa de responder.

- **Carga**: uso normal esperado, até 50 usuários simultâneos (equipe de uma instituição de longa permanência em horário de pico).
- **Estresse**: acima do uso normal (100, 150, 200 e 300 usuários) até encontrar o ponto de degradação e, se houver, o de ruptura.
- **Recuperação**: depois do estresse, voltar a 10 usuários e confirmar que o sistema volta aos critérios.
- **Integridade**: confirmar que nada se perdeu ou duplicou no banco durante a carga.

## 2. Como funciona

1. Cria uma base própria `asilosoft_test_carga_<data>_<pid>` no MySQL de testes, aplica as migrations oficiais e insere massa fictícia: 300 idosos em 4 quartos, 600 registros de saúde, 20 materiais com estoque, 20 usuários por perfil e vínculos familiares.
2. Sobe o servidor com o **mesmo comando usado no Railway** (`php -S ... router.php`) em 127.0.0.1, apontando para essa base.
3. Cada usuário virtual é um processo separado (pcntl). Ele faz login uma vez e repete as ações do seu perfil, esperando de 0,5 a 2 segundos entre uma ação e outra (tempo de leitura de uma pessoa):
   - **Profissional de Saúde (40%)**: início, busca de idoso por identificador, prontuário, **registro de acompanhamento (POST)**, tela de consumo.
   - **Administrador (30%)**: início, lista de idosos, busca por nome, ficha do idoso, usuários, compras.
   - **Almoxarife (30%)**: início, almoxarifado, formulário de entrada, **entrada de estoque (POST)**, compras.
4. As etapas aumentam a quantidade de usuários. Quem já entrou continua logado; só os novos fazem login, espalhados em até 5 segundos. Cada etapa é medida por 20 segundos mais essa rampa.
5. A configuração é executada duas vezes: com o servidor em **1 processo** (como está no Railway hoje) e com **4 workers** (`PHP_CLI_SERVER_WORKERS=4`), para comparar.
6. Ao final, remove a base criada.

Uma requisição conta como sucesso quando recebe a resposta esperada: HTTP 200 nas telas (sem a mensagem de banco indisponível) e HTTP 303 nos envios de formulário (sem voltar ao login). Tempo limite do cliente: 15 segundos. Requisição sem resposta nesse prazo, conexão recusada ou resposta diferente da esperada conta como erro.

## 3. Critérios de aceite

| Item | Critério | Origem |
| --- | --- | --- |
| Latência em carga normal (até 50 usuários) | p95 até 2 s | RNF05 |
| Erros em carga normal | menos de 1% | RNF06 |
| Ponto de degradação | primeira etapa fora dos critérios acima | - |
| Ponto de ruptura | primeira etapa com 50% ou mais de erros; interrompe a escalada | - |
| Recuperação | etapa final com 10 usuários volta aos critérios | - |
| Integridade | nenhum POST confirmado falta no banco; saldos de estoque batem com as movimentações | regras de estoque e prontuário |

O código de saída é 0 quando todas as etapas de **carga** e a integridade passam na configuração do Railway (1 processo), 1 quando algo falha e 2 em impedimento de ambiente.

## 4. Como executar

Pré-requisitos: PHP CLI com `pdo_mysql`, `mbstring`, `pcntl` e `posix`; MySQL/MariaDB **exclusivo de testes** com permissão para criar e remover bases.

```bash
export ASILOSOFT_TEST_DB_HOST=127.0.0.1
export ASILOSOFT_TEST_DB_PORTA=3306
export ASILOSOFT_TEST_DB_USUARIO=usuario_de_testes
export ASILOSOFT_TEST_DB_SENHA='senha_de_testes'

php tests/carga/CargaEstresse.php            # completo, cerca de 7 minutos
php tests/carga/CargaEstresse.php --rapido   # ensaio de 1 minuto (5, 20 e 60 usuários, 1 processo)
```

Opções: `--duracao=20` (segundos por etapa), `--etapas=10,25,50,100,150,200,300`, `--workers=1,4`. A porta padrão é 18090; use `ASILOSOFT_TEST_HTTP_PORT` para trocar. `--conexao-da-aplicacao` reutiliza o servidor MySQL do `.env` (ainda em base separada), mas **não é recomendado para carga**: com banco remoto o teste mede a internet, não a aplicação, e sobrecarrega um banco que pode estar atendendo o sistema publicado.

Evidências geradas em `docs/testes/evidencias/carga_<data>/`:

- `relatorio.html`: gráficos de p95, vazão e taxa de erro por quantidade de usuários, nas duas configurações.
- `relatorio.md`: tabelas por etapa e tempo por ação.
- `resultado.json`: todos os números, inclusive linha do tempo por segundo e amostras de CPU e memória do servidor.
- `servidor_w1.log` e `servidor_w4.log`: log do servidor PHP.

## 5. Resultados da execução de 27/11/2026

Evidências: [evidencias/carga_20261127T203047](evidencias/carga_20261127T203047/relatorio.md) (abra o [relatório HTML](evidencias/carga_20261127T203047/relatorio.html) para ver os gráficos). Código de saída 0.

Ambiente: PHP 8.5.4, AMD Ryzen 5 3400G (8 CPUs lógicas), 13,1 GiB, Linux 7.0. Banco: MariaDB 10.4.32 descartável em 127.0.0.1 (veja limitações). Servidor e gerador de carga na mesma máquina.

### Servidor com 1 processo (igual ao Railway hoje)

| Etapa | Usuários | Requisições | Vazão | Média | p95 | p99 | Erros | CPU do servidor | Resultado |
| --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | --- |
| Carga | 10 | 201 | 8,0 req/s | 17 ms | 110 ms | 228 ms | 0% | 10% | Passou |
| Carga | 25 | 505 | 20,2 req/s | 17 ms | 119 ms | 214 ms | 0% | 17% | Passou |
| Carga | 50 | 981 | 39,2 req/s | 22 ms | 154 ms | 193 ms | 0% | 27% | Passou |
| Estresse | 100 | 1.479 | 59,2 req/s | 452 ms | 2,71 s | 3,44 s | 0% | 52% | Falhou (p95) |
| Estresse | 150 | 2.293 | 91,7 req/s | 417 ms | 2,47 s | 2,78 s | 0% | 56% | Falhou (p95) |
| Estresse | 200 | 2.891 | 115,6 req/s | 496 ms | 3,57 s | 4,01 s | 0% | 61% | Falhou (p95) |
| Estresse | 300 | 1.687 | 67,5 req/s | 3,26 s | 11,93 s | 13,02 s | 0% | 89% | Falhou (p95) |
| Recuperação | 10 | 78 | 7,8 req/s | 6 ms | 17 ms | 23 ms | 0% | 3% | Passou |

Integridade: **Passou**. 763 registros de saúde gravados para 763 confirmados; 539 entradas de estoque para 539 confirmadas; nenhum saldo divergente.

### Servidor com 4 workers (`PHP_CLI_SERVER_WORKERS=4`)

| Etapa | Usuários | Requisições | Vazão | Média | p95 | p99 | Erros | CPU do servidor | Resultado |
| --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | --- |
| Carga | 10 | 203 | 8,1 req/s | 16 ms | 23 ms | 220 ms | 0% | 11% | Passou |
| Carga | 25 | 507 | 20,3 req/s | 11 ms | 20 ms | 224 ms | 0% | 19% | Passou |
| Carga | 50 | 998 | 39,9 req/s | 10 ms | 22 ms | 218 ms | 0% | 33% | Passou |
| Estresse | 100 | 1.952 | 78,1 req/s | 21 ms | 194 ms | 372 ms | 0% | 65% | Passou |
| Estresse | 150 | 2.949 | 118,0 req/s | 14 ms | 30 ms | 286 ms | 0% | 78% | Passou |
| Estresse | 200 | 3.991 | 159,6 req/s | 9 ms | 32 ms | 198 ms | 0% | 93% | Passou |
| Estresse | 300 | 5.891 | 235,6 req/s | 26 ms | 199 ms | 506 ms | 0% | 146% | Passou |
| Recuperação | 10 | 77 | 7,7 req/s | 6 ms | 8 ms | 51 ms | 0% | 3% | Passou |

Integridade: **Passou**. 1.282 registros de saúde para 1.282 confirmados; 940 entradas para 940 confirmadas; nenhum saldo divergente.

CPU acima de 100% significa mais de um núcleo em uso (4 workers).

### Análise

- **Carga normal aprovada (RNF05 e RNF06).** Até 50 usuários simultâneos, mesmo com 1 processo, o p95 ficou em no máximo 154 ms (limite 2 s) e não houve nenhum erro.
- **Ponto de degradação com 1 processo: 100 usuários.** A partir daí o p95 passa de 2 s em todas as telas, não só em uma. Com 300 usuários o p95 chega a 11,9 s e a vazão **cai** de 115,6 para 67,5 req/s: o servidor saturou.
- **Causa: o servidor embutido do PHP atende uma requisição por vez** e cada login gasta cerca de 195 ms de CPU com o hash bcrypt (custo 12, padrão do PHP 8.4 em diante). Quando 50 pessoas entram ao mesmo tempo, são quase 10 s de trabalho em fila, e todas as outras telas esperam atrás dos logins. Com 50 usuários a busca de idosos levou em média 22 ms; com 100, 418 ms; com 300, 2,8 s, sem nenhuma mudança na consulta.
- **Não houve ruptura.** Nenhuma requisição falhou em nenhuma etapa, mas com 300 usuários o p99 (13 s) ficou perto do tempo limite do cliente (15 s). Acima disso, as requisições começariam a expirar.
- **Com 4 workers, nenhuma etapa saiu dos critérios** até 300 usuários (p95 máximo 199 ms) e a vazão cresceu junto com a carga. O gargalo é a configuração do servidor, não o código nem o banco.
- **Recuperação:** nas duas configurações, ao voltar para 10 usuários, o sistema respondeu normalmente (p95 de 17 ms e 8 ms).
- **Integridade:** sob carga e estresse, nenhum registro de saúde ou entrada de estoque se perdeu ou duplicou, e os saldos bateram com as movimentações (os gatilhos e travas do banco funcionaram com concorrência real).

### Recomendação

No Railway, iniciar o servidor com workers. Em **Settings > Variables** do serviço do site, criar `PHP_CLI_SERVER_WORKERS=4` (o comando de início continua o mesmo). Para produção definitiva, o ideal é PHP-FPM com Nginx ou Apache, que são feitos para atender muitas requisições ao mesmo tempo; o próprio manual do PHP não recomenda o servidor embutido para produção.

## 6. Limitações

- O banco usado foi **MariaDB 10.4** (do XAMPP já instalado na máquina), porque não havia MySQL 8 disponível no momento: o Docker não estava rodando e não há MySQL instalado. Todas as migrations, gatilhos e travas funcionaram nele, mas o Railway usa MySQL. Para repetir com MySQL 8, basta apontar as quatro variáveis para ele.
- Servidor, banco e gerador de carga na mesma máquina: disputam CPU entre si, e não há latência de rede. No Railway, cada requisição ainda soma o tempo de internet entre o usuário e o servidor.
- Os números valem para esta máquina. Servem para comparar configurações e achar o gargalo, não como garantia de capacidade em outro hardware.
- Não foram medidos tempo de renderização no navegador nem download de CSS/JS.
- Não foi feito teste de longa duração (horas) para detectar vazamento de memória; a memória do servidor ficou estável em cerca de 25 MB (1 processo) e 100 MB (4 workers) durante toda a execução.

# Relatório de carga e estresse - AsiloSoft

Gerado em 27/11/2026 20:30:47 por `php tests/carga/CargaEstresse.php`.

## Ambiente

- PHP: 8.5.4
- Sistema operacional: Linux 7.0.0-43-generic x86_64
- Processador: AMD Ryzen 5 3400G with Radeon Vega Graphics
- CPUs lógicas: 8
- Memória (GiB): 13.1
- Banco de dados: 10.4.32-MariaDB (local, mesma máquina)
- Servidor HTTP: PHP embutido (php -S) com router.php, mesmo comando do Railway
- Gerador de carga: Processos PHP (pcntl) na mesma máquina do servidor

## Critérios

- Carga (até 50 usuários simultâneos): p95 até 2,00 s (RNF05) e erros abaixo de 1% (RNF06).
- Estresse (acima de 50): identificar o ponto de degradação (primeira etapa fora dos critérios) e o de ruptura (50% ou mais de erros).
- Recuperação: após o estresse, voltar à menor carga e cumprir de novo os critérios.
- Integridade: nenhum POST confirmado pode faltar no banco e os saldos de estoque devem bater com as movimentações.
- Cada etapa dura 20 s mais rampa de entrada; cada usuário espera 0.5 a 2 s entre ações; tempo limite do cliente 15 s.
- Massa fictícia: 300 idosos, 600 registros de saúde, 20 materiais, 20 usuários por perfil. Mistura: Profissional de Saúde 40%, Administrador 30%, Almoxarife 30%.

## Servidor com 1 processo (Railway)

| Etapa | Usuários | Requisições | Vazão (req/s) | Média | p50 | p95 | p99 | Máx. | Erros | CPU servidor | Resultado |
| --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | --- |
| Carga | 10 | 201 | 8,0 | 17 ms | 5 ms | 111 ms | 229 ms | 234 ms | 0,00% | 10% | Passou |
| Carga | 25 | 505 | 20,2 | 17 ms | 5 ms | 119 ms | 214 ms | 226 ms | 0,00% | 17% | Passou |
| Carga | 50 | 981 | 39,2 | 22 ms | 5 ms | 154 ms | 193 ms | 210 ms | 0,00% | 27% | Passou |
| Estresse | 100 | 1479 | 59,2 | 452 ms | 6 ms | 2,71 s | 3,44 s | 3,84 s | 0,00% | 52% | Falhou |
| Estresse | 150 | 2293 | 91,7 | 417 ms | 7 ms | 2,47 s | 2,78 s | 3,13 s | 0,00% | 56% | Falhou |
| Estresse | 200 | 2891 | 115,6 | 496 ms | 10 ms | 3,57 s | 4,01 s | 4,15 s | 0,00% | 61% | Falhou |
| Estresse | 300 | 1687 | 67,5 | 3,26 s | 1,38 s | 11,92 s | 13,02 s | 13,26 s | 0,00% | 89% | Falhou |
| Recuperação | 10 | 78 | 7,8 | 6 ms | 5 ms | 17 ms | 23 ms | 23 ms | 0,00% | 3% | Passou |

- Ponto de degradação: 100 usuários simultâneos.
- Ponto de ruptura: não atingido.
- Maior vazão com sucesso: 115,6 req/s com 200 usuários.
- Recuperação após estresse: sim, voltou aos critérios.
- Integridade do banco: **Passou** - 763 registros de saúde para 763 confirmados; 539 entradas para 539 confirmadas; 0 saldos divergentes.

### Tempo por ação na etapa de 300 usuários

| Ação | Requisições | Erros | Média | p95 | Máx. |
| --- | ---: | ---: | ---: | ---: | ---: |
| almoxarifado | 97 | 0 | 2,64 s | 11,74 s | 13,14 s |
| compras | 127 | 0 | 3,79 s | 12,30 s | 13,22 s |
| consumo (form) | 98 | 0 | 3,97 s | 12,07 s | 13,02 s |
| entrada (form) | 91 | 0 | 2,21 s | 11,80 s | 13,09 s |
| entrada estoque (POST) | 75 | 0 | 2,82 s | 11,38 s | 12,80 s |
| ficha do idoso | 75 | 0 | 2,57 s | 11,94 s | 13,08 s |
| home | 317 | 0 | 4,26 s | 12,09 s | 13,21 s |
| idosos (busca) | 209 | 0 | 2,77 s | 12,61 s | 13,21 s |
| idosos (lista) | 93 | 0 | 2,92 s | 11,81 s | 12,21 s |
| login (envio) | 100 | 0 | 6,06 s | 12,30 s | 13,26 s |
| login (página) | 100 | 0 | 2,69 s | 3,95 s | 4,17 s |
| prontuário | 132 | 0 | 1,73 s | 9,17 s | 12,51 s |
| registrar saúde (POST) | 112 | 0 | 2,86 s | 11,83 s | 12,81 s |
| usuários | 61 | 0 | 2,34 s | 8,70 s | 11,84 s |

## Servidor com 4 workers

| Etapa | Usuários | Requisições | Vazão (req/s) | Média | p50 | p95 | p99 | Máx. | Erros | CPU servidor | Resultado |
| --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | --- |
| Carga | 10 | 203 | 8,1 | 16 ms | 5 ms | 23 ms | 221 ms | 232 ms | 0,00% | 11% | Passou |
| Carga | 25 | 507 | 20,3 | 11 ms | 5 ms | 20 ms | 224 ms | 229 ms | 0,00% | 19% | Passou |
| Carga | 50 | 998 | 39,9 | 11 ms | 4 ms | 22 ms | 218 ms | 228 ms | 0,00% | 33% | Passou |
| Estresse | 100 | 1952 | 78,1 | 21 ms | 5 ms | 194 ms | 372 ms | 673 ms | 0,00% | 65% | Passou |
| Estresse | 150 | 2949 | 118,0 | 14 ms | 4 ms | 30 ms | 286 ms | 516 ms | 0,00% | 78% | Passou |
| Estresse | 200 | 3991 | 159,6 | 9 ms | 4 ms | 32 ms | 198 ms | 256 ms | 0,00% | 93% | Passou |
| Estresse | 300 | 5891 | 235,6 | 27 ms | 4 ms | 199 ms | 507 ms | 915 ms | 0,00% | 146% | Passou |
| Recuperação | 10 | 77 | 7,7 | 6 ms | 5 ms | 8 ms | 51 ms | 51 ms | 0,00% | 3% | Passou |

- Ponto de degradação: não atingido nas etapas executadas.
- Ponto de ruptura: não atingido.
- Maior vazão com sucesso: 235,6 req/s com 300 usuários.
- Recuperação após estresse: sim, voltou aos critérios.
- Integridade do banco: **Passou** - 1282 registros de saúde para 1282 confirmados; 940 entradas para 940 confirmadas; 0 saldos divergentes.

### Tempo por ação na etapa de 300 usuários

| Ação | Requisições | Erros | Média | p95 | Máx. |
| --- | ---: | ---: | ---: | ---: | ---: |
| almoxarifado | 348 | 0 | 45 ms | 93 ms | 721 ms |
| compras | 604 | 0 | 16 ms | 31 ms | 700 ms |
| consumo (form) | 435 | 0 | 29 ms | 170 ms | 733 ms |
| entrada (form) | 344 | 0 | 26 ms | 174 ms | 592 ms |
| entrada estoque (POST) | 334 | 0 | 26 ms | 126 ms | 674 ms |
| ficha do idoso | 275 | 0 | 29 ms | 91 ms | 903 ms |
| home | 1131 | 0 | 17 ms | 59 ms | 694 ms |
| idosos (busca) | 744 | 0 | 19 ms | 71 ms | 672 ms |
| idosos (lista) | 287 | 0 | 17 ms | 57 ms | 500 ms |
| login (envio) | 100 | 0 | 248 ms | 479 ms | 915 ms |
| login (página) | 100 | 0 | 61 ms | 485 ms | 698 ms |
| prontuário | 456 | 0 | 21 ms | 70 ms | 603 ms |
| registrar saúde (POST) | 455 | 0 | 22 ms | 73 ms | 710 ms |
| usuários | 278 | 0 | 14 ms | 36 ms | 690 ms |

## Arquivos

- `resultado.json`: dados completos, inclusive linha do tempo por segundo e amostras de CPU/memória.
- `relatorio.html`: gráficos de p95, vazão e erros por quantidade de usuários.
- `servidor_w*.log`: log do servidor PHP de cada configuração.

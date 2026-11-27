# Registro de defeitos e pendências

Adição posterior validada: 71 unitários, seis integrações e cobertura phpdbg de 68,09% (224/329 linhas de serviços). Detalhes e evidências em [PHPDBG.md](PHPDBG.md). Os registros abaixo anteriores à medição são históricos; a situação atual da meta é atendida.

## Ocorrências verificadas

| ID | Natureza | Reprodução / esperado / obtido | Tratamento | Situação e evidência |
| --- | --- | --- | --- | --- |
| AMB-01 | Ambiente, não defeito da aplicação | Iniciar MySQL em ambiente sem permissão de socket e executar integração. Esperado: conectar ao banco de teste. Obtido: conexão recusada/erro PDO 2002 | Executar instância descartável e testes com rede local permitida | Resolvido para a execução final; log inicial em evidencias/20261123T201656_2 e final em evidencias/20261123T201841_16280 |
| AMB-02 | Medição indisponível | php tests/unit/run.php --coverage. Esperado: medir linhas com driver. Obtido: ausência de Xdebug, código 2 | Disponibilizar Xdebug no PHP CLI de desenvolvimento, medir e ampliar testes até 60% | Pendente; evidencias/cobertura-indisponivel.log |
| DEF-C01 | Desempenho/configuração | Carga com 100 ou mais usuários simultâneos no servidor de 1 processo. Esperado: p95 até 2 s. Obtido: 2,71 s com 100 e 11,93 s com 300; vazão cai de 115,6 para 67,5 req/s | Servidor embutido atende uma requisição por vez; logins (bcrypt ~195 ms) bloqueiam a fila. Com PHP_CLI_SERVER_WORKERS=4 todas as etapas até 300 usuários passaram (p95 máximo 199 ms) | Aberto: aplicar a variável no Railway; evidencias/carga_20261127T203047 |
| DEF-S01 a DEF-S07 | Segurança | 1 alta (sem limite de tentativas de login), 2 médias (cookie sem HttpOnly/SameSite, clickjacking) e 4 baixas (cabeçalhos, tempo de login, modo estrito de sessão, versão do PHP exposta) | Limite de 5 falhas por e-mail em 15 min (migration 009), `password_verify` com hash fictício, sessão estrita com cookie HttpOnly/SameSite e cabeçalhos de proteção em index.php | Corrigidos e retestados: antes evidencias/seguranca_20261127T203744 (29/36), depois evidencias/seguranca_20261127T204403 (36/36) |

Até 23/11/2026 não havia sido encontrado defeito funcional novo. Em 27/11/2026 os testes de carga e segurança encontraram os itens DEF-C01 e DEF-S01 a DEF-S07 acima; os de segurança já foram corrigidos e retestados. Não há correção de código da aplicação a alegar. Casos que rejeitam entradas inválidas são testes negativos aprovados, não bugs.

## Pendências do M3

| Exigência | Situação | Ação necessária / motivo |
| --- | --- | --- |
| Pelo menos 20 casos | Atendida | 71 unitários e seis cenários compostos de integração com campos solicitados |
| Unitários com cobertura de 60% | Atendida no escopo de serviços | phpdbg: 224/329 linhas, 68,09%, com 71 casos unitários aprovados; veja PHPDBG.md |
| Pelo menos cinco integrações | Atendida | Seis suítes existentes reexecutadas em MySQL real, com logs e identificação |
| Coleção exportada e relatório | Alternativa implementada | Scripts PHP versionados e logs substituem coleção de ferramenta sugerida; confirmar aceitação com professora na revisão da entrega |
| Pelo menos três E2E | Pendente | HTTP não renderiza navegador; gravar três fluxos completos com UI e evidências |
| Performance | Pendente | Medir funcionalidades principais em uso normal e gerar gráficos de média, p95 e throughput |
| Carga/estresse | Atendida (27/11/2026) | tests/carga/CargaEstresse.php: 10 a 300 usuários simultâneos, recuperação, integridade e gráficos. Carga normal aprovada; degradação a partir de 100 usuários com 1 processo (configuração do Railway). Veja [CARGA_ESTRESSE.md](CARGA_ESTRESSE.md) e DEF-C01 |
| Segurança | Atendida (27/11/2026) | tests/seguranca/SegurancaTest.php: 36 casos OWASP Top 10. Antes: 29 passaram e 7 vulnerabilidades (DEF-S01 a DEF-S07). Corrigidas e retestadas: 36 de 36. Veja [SEGURANCA.md](SEGURANCA.md) |
| Usabilidade/acessibilidade | Pendente | Avaliar pelo menos três pessoas, coletar feedback e propor melhorias; IA não substitui usuários |
| Regressão | Parcial | Scripts reexecutados e aprovados; sem novo defeito corrigido para comparativo e sem pipeline novo |
| Plano e relatório | Disponíveis para esta etapa | Markdown editável e PDF; atualizar após categorias restantes |
| Defeitos e antes/depois | Atendida para segurança | DEF-S01 a DEF-S07 com caso, severidade, correção e reteste (29/36 antes, 36/36 depois). DEF-C01 depende de configurar o Railway |
| Prints, vídeos, Drive e AVA | Pendente | Logs possuem horário; falta material visual com data/hora e organização/upload final |
| Apresentação e vídeo de contingência | Pendente | Seguir roteiro do README, ensaiar e gravar antes da apresentação |

Nada foi classificado como tecnicamente inaplicável: as categorias restantes fazem sentido para uma aplicação web como o AsiloSoft. A restrição de ferramentas e a ausência de participantes/ambiente não eliminam os requisitos da professora.

## Modelo para um defeito real futuro

Registrar: ID DEF-xxx; caso CT relacionado; versão/data/ambiente; pré-condições; passos mínimos e dados fictícios; esperado; obtido; evidência datada; severidade e prioridade; causa; arquivos/commit da correção; evidência da reexecução; responsável e status. Preservar tanto o log de falha quanto o log do reteste.

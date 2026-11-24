# Registro de defeitos e pendências

Adição posterior validada: 71 unitários, seis integrações e cobertura phpdbg de 68,09% (224/329 linhas de serviços). Detalhes e evidências em [PHPDBG.md](PHPDBG.md). Os registros abaixo anteriores à medição são históricos; a situação atual da meta é atendida.

## Ocorrências verificadas

| ID | Natureza | Reprodução / esperado / obtido | Tratamento | Situação e evidência |
| --- | --- | --- | --- | --- |
| AMB-01 | Ambiente, não defeito da aplicação | Iniciar MySQL em ambiente sem permissão de socket e executar integração. Esperado: conectar ao banco de teste. Obtido: conexão recusada/erro PDO 2002 | Executar instância descartável e testes com rede local permitida | Resolvido para a execução final; log inicial em evidencias/20261123T201656_2 e final em evidencias/20261123T201841_16280 |
| AMB-02 | Medição indisponível | php tests/unit/run.php --coverage. Esperado: medir linhas com driver. Obtido: ausência de Xdebug, código 2 | Disponibilizar Xdebug no PHP CLI de desenvolvimento, medir e ampliar testes até 60% | Pendente; evidencias/cobertura-indisponivel.log |

Não foi encontrado um novo defeito funcional da aplicação durante esta etapa. Não há correção de código da aplicação a alegar. Casos que rejeitam entradas inválidas são testes negativos aprovados, não bugs.

## Pendências do M3

| Exigência | Situação | Ação necessária / motivo |
| --- | --- | --- |
| Pelo menos 20 casos | Atendida | 71 unitários e seis cenários compostos de integração com campos solicitados |
| Unitários com cobertura de 60% | Atendida no escopo de serviços | phpdbg: 224/329 linhas, 68,09%, com 71 casos unitários aprovados; veja PHPDBG.md |
| Pelo menos cinco integrações | Atendida | Seis suítes existentes reexecutadas em MySQL real, com logs e identificação |
| Coleção exportada e relatório | Alternativa implementada | Scripts PHP versionados e logs substituem coleção de ferramenta sugerida; confirmar aceitação com professora na revisão da entrega |
| Pelo menos três E2E | Pendente | HTTP não renderiza navegador; gravar três fluxos completos com UI e evidências |
| Performance | Pendente | Medir funcionalidades principais em uso normal e gerar gráficos de média, p95 e throughput |
| Carga/estresse | Pendente | Ensaiar concorrência progressiva em ambiente reservado, taxa de erro, gráficos e ponto de degradação; duas retiradas concorrentes não bastam |
| Segurança | Parcial | HTTP cobre sessão, acesso, CSRF e arquivos internos; falta SQL Injection, XSS e relatório completo de vulnerabilidades/correções |
| Usabilidade/acessibilidade | Pendente | Avaliar pelo menos três pessoas, coletar feedback e propor melhorias; IA não substitui usuários |
| Regressão | Parcial | Scripts reexecutados e aprovados; sem novo defeito corrigido para comparativo e sem pipeline novo |
| Plano e relatório | Disponíveis para esta etapa | Markdown editável e PDF; atualizar após categorias restantes |
| Defeitos e antes/depois | Sem novo bug funcional | Registrar somente falhas reais encontradas futuramente, com caso, severidade, correção e reteste |
| Prints, vídeos, Drive e AVA | Pendente | Logs possuem horário; falta material visual com data/hora e organização/upload final |
| Apresentação e vídeo de contingência | Pendente | Seguir roteiro do README, ensaiar e gravar antes da apresentação |

Nada foi classificado como tecnicamente inaplicável: as categorias restantes fazem sentido para uma aplicação web como o AsiloSoft. A restrição de ferramentas e a ausência de participantes/ambiente não eliminam os requisitos da professora.

## Modelo para um defeito real futuro

Registrar: ID DEF-xxx; caso CT relacionado; versão/data/ambiente; pré-condições; passos mínimos e dados fictícios; esperado; obtido; evidência datada; severidade e prioridade; causa; arquivos/commit da correção; evidência da reexecução; responsável e status. Preservar tanto o log de falha quanto o log do reteste.

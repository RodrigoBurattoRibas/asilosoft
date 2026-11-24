# Execução dos testes no servidor configurado do Railway

Em 24/11/2026, os testes foram executados usando o acesso ao MySQL configurado no `.env`, conforme autorização do responsável pelo projeto. A conexão foi verificada na base `railway`, com MySQL 9.7.4 e 19 tabelas.

O usuário do banco permitiu criar bases temporárias no mesmo servidor. Assim, as seis suítes utilizaram o mesmo servidor e as mesmas credenciais da aplicação, cada uma em um schema `asilosoft_test_*` próprio. Os testes não fizeram gravações na base `railway`. Isso permitiu executar os cenários que recriam ou removem bases sem apagar os dados existentes.

## Resultados

| Verificação | Resultado | Evidência |
| --- | --- | --- |
| Unitários | 71 casos aprovados, nenhuma falha | [unitarios.json](evidencias/20261124T181500_12219/unitarios.json) |
| Regras legadas de estoque e compras | 40 verificações aprovadas | [execucao.log](evidencias/20261124T181500_12219/execucao.log) |
| CT-I01 Banco, migrations e contratos de rotas | Passou | [CT-I01.log](evidencias/20261124T181500_12219/CT-I01.log) |
| CT-I02 Familiares, vínculos e concorrência | Passou | [CT-I02.log](evidencias/20261124T181500_12219/CT-I02.log) |
| CT-I03 Clínica, prontuário, autoria e auditoria | Passou | [CT-I03.log](evidencias/20261124T181500_12219/CT-I03.log) |
| CT-I04 Estoque, compras, idempotência e concorrência | Passou | [CT-I04.log](evidencias/20261124T181500_12219/CT-I04.log) |
| CT-I05 Instalação do primeiro administrador | Passou | [CT-I05.log](evidencias/20261124T181500_12219/CT-I05.log) |
| CT-I06 Fluxos HTTP, sessão, CSRF e permissões | Passou | [CT-I06.log](evidencias/20261124T181500_12219/CT-I06.log) |

A suíte começou às 18:15:00 e terminou às 18:22:32, horário de São Paulo. O código de saída foi zero. A limpeza das bases retidas terminou às 18:22:37. Esse tempo corresponde à execução funcional completa pela conexão remota; não é um benchmark de desempenho ou carga.

Os 71 unitários usam dublês e não acessam o banco. As seis integrações acessaram efetivamente o MySQL do Railway. O servidor HTTP da suíte foi iniciado localmente, conectado às bases temporárias no Railway; não foi realizado deploy nem teste do endereço público da aplicação.

## Limpeza e verificação

Familiares, instalação e HTTP removeram suas bases nos próprios runners. Após a execução, também foram removidas as bases de banco/migrations, clínica e estoque/compras, normalmente retidas para inspeção. Uma nova conexão verificou que nenhuma das seis bases temporárias permanecia e que a base original continuava com suas 19 tabelas. O inventário de tabelas é uma verificação de schema; não constitui uma comparação de cada registro.

- [Resumo do ambiente e da execução](evidencias/20261124T181500_12219/ambiente.json).
- [Confirmação da limpeza](evidencias/20261124T181500_12219/limpeza.json).
- [Resumo das seis integrações](evidencias/20261124T181500_12219/resumo.txt).

As credenciais foram lidas pela configuração existente e repassadas aos processos de teste por variáveis de ambiente. Senha e endereço de conexão não foram incluídos na documentação ou nos comandos versionados. O carregamento do `.env` ocorreu em um lançador temporário somente nesta execução explicitamente autorizada; os runners versionados continuam exigindo variáveis de teste explícitas.

A cobertura de linhas não foi coletada novamente nesta execução. O resultado anterior de 68,09% por phpdbg permanece documentado separadamente em [PHPDBG.md](PHPDBG.md). Os testes aqui confirmam as integrações no servidor configurado, com MySQL 9.7.4; as categorias restantes do M3 seguem descritas em [DEFEITOS_E_PENDENCIAS.md](DEFEITOS_E_PENDENCIAS.md).

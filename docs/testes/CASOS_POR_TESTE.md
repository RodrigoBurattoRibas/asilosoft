# Casos de teste do AsiloSoft

Casos executados em 23/11/2026 no AsiloSoft: 50 unitários e seis cenários compostos de integração, todos aprovados. Cada tabela apresenta os dez campos solicitados pela professora. Os cenários compostos de integração reúnem as verificações descritas nos respectivos logs. Os dados são fictícios e os unitários utilizam dublês de repositório. As 40 verificações legadas complementares não são contadas como 40 novos casos.

A aprovação dos casos não comprova 60% de cobertura de linhas. Essa medição permanece pendente; o PHP CLI desta execução não dispõe de Xdebug, PCOV ou phpdbg. A notação str_repeat representa exatamente o texto extenso usado no teste.

# Como reproduzir os testes

Unitários: `php tests/unit/run.php`. Integrações com logs: configurar as quatro variáveis ASILOSOFT_TEST_DB_HOST, PORTA, USUARIO e SENHA para MySQL exclusivo de testes e executar `bash tests/m3.sh`. Todos os horários citados nos logs gerais são de São Paulo; o JSON também registra o instante equivalente em UTC.

# CT-U01 Quartos limites da capacidade

| Campo | Descrição |
| --- | --- |
| ID | CT-U01 |
| Requisito/Funcionalidade | Quartos: limites da capacidade |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloQuartoService com dublê sem quarto duplicado. 2. Chamar criar com os dados indicados. 3. Conferir retorno e payload de criação, ou DomainException sem chamadas ao repositório. |
| Dados de entrada | {"codigo": " A ", "capacidade": 1} |
| Resultado esperado | Retornar id 1 e chamar criar uma vez com código A, capacidade informada e ativo=true. |
| Resultado obtido | As verificações automatizadas passaram. Retornar id 1 e chamar criar uma vez com código A, capacidade informada e ativo=true. |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U01; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U02 Quartos limites da capacidade

| Campo | Descrição |
| --- | --- |
| ID | CT-U02 |
| Requisito/Funcionalidade | Quartos: limites da capacidade |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloQuartoService com dublê sem quarto duplicado. 2. Chamar criar com os dados indicados. 3. Conferir retorno e payload de criação, ou DomainException sem chamadas ao repositório. |
| Dados de entrada | {"codigo": " A ", "capacidade": 99} |
| Resultado esperado | Retornar id 1 e chamar criar uma vez com código A, capacidade informada e ativo=true. |
| Resultado obtido | As verificações automatizadas passaram. Retornar id 1 e chamar criar uma vez com código A, capacidade informada e ativo=true. |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U02; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U03 Quartos limites da capacidade

| Campo | Descrição |
| --- | --- |
| ID | CT-U03 |
| Requisito/Funcionalidade | Quartos: limites da capacidade |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloQuartoService com dublê sem quarto duplicado. 2. Chamar criar com os dados indicados. 3. Conferir retorno e payload de criação, ou DomainException sem chamadas ao repositório. |
| Dados de entrada | {"codigo": " A ", "capacidade": 0} |
| Resultado esperado | Lançar DomainException e não chamar o repositório. |
| Resultado obtido | As verificações automatizadas passaram. Lançar DomainException e não chamar o repositório. |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U03; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U04 Quartos limites da capacidade

| Campo | Descrição |
| --- | --- |
| ID | CT-U04 |
| Requisito/Funcionalidade | Quartos: limites da capacidade |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloQuartoService com dublê sem quarto duplicado. 2. Chamar criar com os dados indicados. 3. Conferir retorno e payload de criação, ou DomainException sem chamadas ao repositório. |
| Dados de entrada | {"codigo": " A ", "capacidade": 100} |
| Resultado esperado | Lançar DomainException e não chamar o repositório. |
| Resultado obtido | As verificações automatizadas passaram. Lançar DomainException e não chamar o repositório. |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U04; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U05 Quartos limites da capacidade

| Campo | Descrição |
| --- | --- |
| ID | CT-U05 |
| Requisito/Funcionalidade | Quartos: limites da capacidade |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloQuartoService com dublê sem quarto duplicado. 2. Chamar criar com os dados indicados. 3. Conferir retorno e payload de criação, ou DomainException sem chamadas ao repositório. |
| Dados de entrada | {"codigo": " A ", "capacidade": "1.5"} |
| Resultado esperado | Lançar DomainException e não chamar o repositório. |
| Resultado obtido | As verificações automatizadas passaram. Lançar DomainException e não chamar o repositório. |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U05; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U06 Quarto ocupado não pode ser desativado

| Campo | Descrição |
| --- | --- |
| ID | CT-U06 |
| Requisito/Funcionalidade | Quarto ocupado não pode ser desativado |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O dublê encontra o quarto 1 e informa uma pessoa ocupante. |
| Passos | 1. Chamar alterarStatus(1, false). 2. Verificar DomainException. 3. Conferir que houve somente consulta do quarto e da ocupação. |
| Dados de entrada | {"id": 1, "ativo": false, "ocupacao": 1} |
| Resultado esperado | Rejeitar sem atualizar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem atualizar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U06; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U07 Capacidade não pode ficar abaixo da ocupação

| Campo | Descrição |
| --- | --- |
| ID | CT-U07 |
| Requisito/Funcionalidade | Capacidade não pode ficar abaixo da ocupação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O quarto 1 existe, o código não está duplicado e a ocupação é 2. |
| Passos | 1. Chamar atualizar(1, dados). 2. Verificar que reduzir a capacidade para 1 lança DomainException. |
| Dados de entrada | {"id": 1, "codigo": "A", "capacidade": 1, "ocupacao": 2} |
| Resultado esperado | Rejeitar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U07; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U08 Usuários limite da senha

| Campo | Descrição |
| --- | --- |
| ID | CT-U08 |
| Requisito/Funcionalidade | Usuários: limite da senha |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloUsuarioService sem e-mail duplicado. 2. Chamar criar. 3. Para senha de 7 caracteres, exigir DomainException e nenhuma chamada. 4. Para senha de 8, conferir e-mail ana@example.com e validar o hash com password_verify. |
| Dados de entrada | {"nome": " Ana ", "email": " ANA@EXAMPLE.COM ", "senha": "1234567", "perfil": "Administrador"} |
| Resultado esperado | Rejeitar sem gravar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem gravar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U08; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U09 Usuários limite da senha

| Campo | Descrição |
| --- | --- |
| ID | CT-U09 |
| Requisito/Funcionalidade | Usuários: limite da senha |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloUsuarioService sem e-mail duplicado. 2. Chamar criar. 3. Para senha de 7 caracteres, exigir DomainException e nenhuma chamada. 4. Para senha de 8, conferir e-mail ana@example.com e validar o hash com password_verify. |
| Dados de entrada | {"nome": " Ana ", "email": " ANA@EXAMPLE.COM ", "senha": "12345678", "perfil": "Administrador"} |
| Resultado esperado | Hash válido e e-mail normalizado |
| Resultado obtido | As verificações automatizadas passaram. Hash válido e e-mail normalizado |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U09; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U10 Último administrador ativo

| Campo | Descrição |
| --- | --- |
| ID | CT-U10 |
| Requisito/Funcionalidade | Último administrador ativo |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O usuário 1 é o único administrador ativo. |
| Passos | 1. Chamar alterarStatus(1, false). 2. Exigir DomainException. 3. Conferir apenas duas consultas, sem atualização. |
| Dados de entrada | {"id": 1, "perfil": "Administrador", "ativo_atual": true, "novo_ativo": false, "administradores_ativos": 1} |
| Resultado esperado | Rejeitar desativação |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar desativação |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U10; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U11 Estoque quantidade decimal

| Campo | Descrição |
| --- | --- |
| ID | CT-U11 |
| Requisito/Funcionalidade | Estoque: quantidade decimal |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Chamar AsiloAlmoxarifadoService::validarQuantidade com a entrada indicada. 2. Comparar a string decimal retornada nos casos válidos ou exigir DomainException nos inválidos. |
| Dados de entrada | {"quantidade": "0"} |
| Resultado esperado | Rejeitar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U11; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U12 Estoque quantidade decimal

| Campo | Descrição |
| --- | --- |
| ID | CT-U12 |
| Requisito/Funcionalidade | Estoque: quantidade decimal |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Chamar AsiloAlmoxarifadoService::validarQuantidade com a entrada indicada. 2. Comparar a string decimal retornada nos casos válidos ou exigir DomainException nos inválidos. |
| Dados de entrada | {"quantidade": "0.001"} |
| Resultado esperado | Aceitar decimal exato |
| Resultado obtido | As verificações automatizadas passaram. Aceitar decimal exato |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U12; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U13 Estoque quantidade decimal

| Campo | Descrição |
| --- | --- |
| ID | CT-U13 |
| Requisito/Funcionalidade | Estoque: quantidade decimal |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Chamar AsiloAlmoxarifadoService::validarQuantidade com a entrada indicada. 2. Comparar a string decimal retornada nos casos válidos ou exigir DomainException nos inválidos. |
| Dados de entrada | {"quantidade": "999999999.999"} |
| Resultado esperado | Aceitar decimal exato |
| Resultado obtido | As verificações automatizadas passaram. Aceitar decimal exato |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U13; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U14 Estoque quantidade decimal

| Campo | Descrição |
| --- | --- |
| ID | CT-U14 |
| Requisito/Funcionalidade | Estoque: quantidade decimal |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Chamar AsiloAlmoxarifadoService::validarQuantidade com a entrada indicada. 2. Comparar a string decimal retornada nos casos válidos ou exigir DomainException nos inválidos. |
| Dados de entrada | {"quantidade": "1000000000"} |
| Resultado esperado | Rejeitar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U14; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U15 Estoque quantidade decimal

| Campo | Descrição |
| --- | --- |
| ID | CT-U15 |
| Requisito/Funcionalidade | Estoque: quantidade decimal |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Chamar AsiloAlmoxarifadoService::validarQuantidade com a entrada indicada. 2. Comparar a string decimal retornada nos casos válidos ou exigir DomainException nos inválidos. |
| Dados de entrada | {"quantidade": "1e3"} |
| Resultado esperado | Rejeitar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U15; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U16 Estoque quantidade decimal

| Campo | Descrição |
| --- | --- |
| ID | CT-U16 |
| Requisito/Funcionalidade | Estoque: quantidade decimal |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Chamar AsiloAlmoxarifadoService::validarQuantidade com a entrada indicada. 2. Comparar a string decimal retornada nos casos válidos ou exigir DomainException nos inválidos. |
| Dados de entrada | {"quantidade": "1.0001"} |
| Resultado esperado | Rejeitar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U16; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U17 Estoque quantidade decimal

| Campo | Descrição |
| --- | --- |
| ID | CT-U17 |
| Requisito/Funcionalidade | Estoque: quantidade decimal |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Chamar AsiloAlmoxarifadoService::validarQuantidade com a entrada indicada. 2. Comparar a string decimal retornada nos casos válidos ou exigir DomainException nos inválidos. |
| Dados de entrada | {"quantidade": []} |
| Resultado esperado | Rejeitar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U17; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U18 Estoque quantidade decimal

| Campo | Descrição |
| --- | --- |
| ID | CT-U18 |
| Requisito/Funcionalidade | Estoque: quantidade decimal |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Chamar AsiloAlmoxarifadoService::validarQuantidade com a entrada indicada. 2. Comparar a string decimal retornada nos casos válidos ou exigir DomainException nos inválidos. |
| Dados de entrada | {"quantidade": null} |
| Resultado esperado | Rejeitar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U18; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U19 Retirada sem finalidade

| Campo | Descrição |
| --- | --- |
| ID | CT-U19 |
| Requisito/Funcionalidade | Retirada sem finalidade |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O material 1 existe e possui saldo disponível 10. |
| Passos | 1. Chamar registrarRetirada com material 1, dados e usuário 1. 2. Exigir DomainException. 3. Conferir uma única consulta de material e nenhuma chamada de retirada. |
| Dados de entrada | {"material_id": 1, "usuario_id": 1, "quantidade": "1", "quantidade_disponivel": 10} |
| Resultado esperado | Rejeitar sem retirar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem retirar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U19; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U20 Retirada superior ao saldo

| Campo | Descrição |
| --- | --- |
| ID | CT-U20 |
| Requisito/Funcionalidade | Retirada superior ao saldo |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O material 1 existe e possui saldo disponível 10. |
| Passos | 1. Chamar registrarRetirada com material 1, dados e usuário 1. 2. Exigir DomainException. 3. Conferir uma única consulta de material e nenhuma chamada de retirada. |
| Dados de entrada | {"material_id": 1, "usuario_id": 1, "quantidade": "11", "quantidade_disponivel": 10, "observacao": "Uso"} |
| Resultado esperado | Rejeitar sem retirar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem retirar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U20; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U21 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U21 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Pendente", "destino": "Pendente"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U21; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U22 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U22 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Pendente", "destino": "Aprovada"} |
| Resultado esperado | Delegar uma alteração |
| Resultado obtido | As verificações automatizadas passaram. Delegar uma alteração |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U22; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U23 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U23 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Pendente", "destino": "Recusada"} |
| Resultado esperado | Delegar uma alteração |
| Resultado obtido | As verificações automatizadas passaram. Delegar uma alteração |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U23; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U24 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U24 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Pendente", "destino": "Atendida"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U24; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U25 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U25 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Aprovada", "destino": "Pendente"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U25; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U26 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U26 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Aprovada", "destino": "Aprovada"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U26; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U27 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U27 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Aprovada", "destino": "Recusada"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U27; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U28 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U28 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Aprovada", "destino": "Atendida"} |
| Resultado esperado | Delegar uma alteração |
| Resultado obtido | As verificações automatizadas passaram. Delegar uma alteração |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U28; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U29 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U29 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Recusada", "destino": "Pendente"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U29; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U30 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U30 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Recusada", "destino": "Aprovada"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U30; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U31 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U31 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Recusada", "destino": "Recusada"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U31; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U32 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U32 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Recusada", "destino": "Atendida"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U32; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U33 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U33 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Atendida", "destino": "Pendente"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U33; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U34 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U34 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Atendida", "destino": "Aprovada"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U34; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U35 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U35 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Atendida", "destino": "Recusada"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U35; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U36 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U36 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Atendida", "destino": "Atendida"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U36; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U37 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U37 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Inválida", "destino": "Pendente"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U37; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U38 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U38 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Inválida", "destino": "Aprovada"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U38; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U39 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U39 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Inválida", "destino": "Recusada"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U39; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U40 Compras transição de situação

| Campo | Descrição |
| --- | --- |
| ID | CT-U40 |
| Requisito/Funcionalidade | Compras: transição de situação |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloCompraService com dublê novo. 2. Chamar alterarSituacao(1, origem, destino). 3. Para Pendente→Aprovada, Pendente→Recusada ou Aprovada→Atendida, conferir uma chamada com os três argumentos. 4. Para as demais combinações, exigir DomainException e nenhuma chamada. |
| Dados de entrada | {"id": 1, "origem": "Inválida", "destino": "Atendida"} |
| Resultado esperado | Rejeitar sem alterar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem alterar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U40; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U41 Saúde autorização e autoria

| Campo | Descrição |
| --- | --- |
| ID | CT-U41 |
| Requisito/Funcionalidade | Saúde: autorização e autoria |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O idoso 1 existe. |
| Passos | 1. Chamar AsiloSaudeService::registrar. 2. Para Administrador, exigir DomainException e nenhuma gravação. 3. Para Profissional de Saúde, conferir registrar(1, 2, "Nota", false), usando o autor real e ignorando usuario_id forjado. |
| Dados de entrada | {"idoso_id": 1, "autor": {"id": 2, "perfil": "Administrador", "ativo": true}, "dados": {"descricao": " Nota ", "usuario_id": 999}} |
| Resultado esperado | Somente profissional ativo grava com autor real |
| Resultado obtido | As verificações automatizadas passaram. Somente profissional ativo grava com autor real |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U41; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U42 Saúde autorização e autoria

| Campo | Descrição |
| --- | --- |
| ID | CT-U42 |
| Requisito/Funcionalidade | Saúde: autorização e autoria |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O idoso 1 existe. |
| Passos | 1. Chamar AsiloSaudeService::registrar. 2. Para Administrador, exigir DomainException e nenhuma gravação. 3. Para Profissional de Saúde, conferir registrar(1, 2, "Nota", false), usando o autor real e ignorando usuario_id forjado. |
| Dados de entrada | {"idoso_id": 1, "autor": {"id": 2, "perfil": "Profissional de Saúde", "ativo": true}, "dados": {"descricao": " Nota ", "usuario_id": 999}} |
| Resultado esperado | Somente profissional ativo grava com autor real |
| Resultado obtido | As verificações automatizadas passaram. Somente profissional ativo grava com autor real |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U42; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U43 Saúde descrição inválida

| Campo | Descrição |
| --- | --- |
| ID | CT-U43 |
| Requisito/Funcionalidade | Saúde: descrição inválida |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O idoso 1 existe e o autor é profissional ativo. |
| Passos | 1. Chamar registrar com a descrição indicada. 2. Exigir DomainException. 3. Conferir que não houve gravação no repositório de saúde. |
| Dados de entrada | {"idoso_id": 1, "autor": {"id": 2, "perfil": "Profissional de Saúde", "ativo": true}, "descricao": ""} |
| Resultado esperado | Rejeitar sem gravar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem gravar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U43; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U44 Saúde descrição inválida

| Campo | Descrição |
| --- | --- |
| ID | CT-U44 |
| Requisito/Funcionalidade | Saúde: descrição inválida |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O idoso 1 existe e o autor é profissional ativo. |
| Passos | 1. Chamar registrar com a descrição indicada. 2. Exigir DomainException. 3. Conferir que não houve gravação no repositório de saúde. |
| Dados de entrada | {"idoso_id": 1, "autor": {"id": 2, "perfil": "Profissional de Saúde", "ativo": true}, "descricao": "str_repeat(\"a\", 65001)"} |
| Resultado esperado | Rejeitar sem gravar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem gravar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U44; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U45 Saúde descrição inválida

| Campo | Descrição |
| --- | --- |
| ID | CT-U45 |
| Requisito/Funcionalidade | Saúde: descrição inválida |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O idoso 1 existe e o autor é profissional ativo. |
| Passos | 1. Chamar registrar com a descrição indicada. 2. Exigir DomainException. 3. Conferir que não houve gravação no repositório de saúde. |
| Dados de entrada | {"idoso_id": 1, "autor": {"id": 2, "perfil": "Profissional de Saúde", "ativo": true}, "descricao": ["x"]} |
| Resultado esperado | Rejeitar sem gravar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem gravar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U45; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U46 Familiares e-mail opcional

| Campo | Descrição |
| --- | --- |
| ID | CT-U46 |
| Requisito/Funcionalidade | Familiares: e-mail opcional |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloFamiliarService. 2. Chamar salvar com os dados indicados. 3. E-mail inválido deve lançar DomainException sem chamadas. 4. E-mail vazio deve ser salvo como null; e-mail válido deve ser preservado; retorno id 1. |
| Dados de entrada | {"nome": "Ana", "telefone": "123", "email": ""} |
| Resultado esperado | Salvar com campos opcionais normalizados |
| Resultado obtido | As verificações automatizadas passaram. Salvar com campos opcionais normalizados |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U46; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U47 Familiares e-mail opcional

| Campo | Descrição |
| --- | --- |
| ID | CT-U47 |
| Requisito/Funcionalidade | Familiares: e-mail opcional |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloFamiliarService. 2. Chamar salvar com os dados indicados. 3. E-mail inválido deve lançar DomainException sem chamadas. 4. E-mail vazio deve ser salvo como null; e-mail válido deve ser preservado; retorno id 1. |
| Dados de entrada | {"nome": "Ana", "telefone": "123", "email": "inválido"} |
| Resultado esperado | Rejeitar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U47; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U48 Familiares e-mail opcional

| Campo | Descrição |
| --- | --- |
| ID | CT-U48 |
| Requisito/Funcionalidade | Familiares: e-mail opcional |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. |
| Passos | 1. Instanciar AsiloFamiliarService. 2. Chamar salvar com os dados indicados. 3. E-mail inválido deve lançar DomainException sem chamadas. 4. E-mail vazio deve ser salvo como null; e-mail válido deve ser preservado; retorno id 1. |
| Dados de entrada | {"nome": "Ana", "telefone": "123", "email": "ana@example.com"} |
| Resultado esperado | Salvar com campos opcionais normalizados |
| Resultado obtido | As verificações automatizadas passaram. Salvar com campos opcionais normalizados |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U48; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U49 Idoso sem vaga

| Campo | Descrição |
| --- | --- |
| ID | CT-U49 |
| Requisito/Funcionalidade | Idoso sem vaga |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. CPF e identificador livres; quarto 1 ativo com todas as vagas ocupadas. |
| Passos | 1. Instanciar AsiloIdosoService com dublês de idosos e quartos. 2. Chamar criar. 3. Exigir DomainException. 4. Conferir que o repositório de idosos recebeu somente consultas de CPF e identificador. |
| Dados de entrada | {"nome": "Ana", "cpf": "12345678901", "identificador": "A", "quarto_id": 1, "quarto_ativo": true, "capacidade": 1, "ocupacao": 1} |
| Resultado esperado | Rejeitar sem criar |
| Resultado obtido | As verificações automatizadas passaram. Rejeitar sem criar |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U49; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-U50 Desativar idoso libera quarto

| Campo | Descrição |
| --- | --- |
| ID | CT-U50 |
| Requisito/Funcionalidade | Desativar idoso libera quarto |
| Tipo de teste | Unitário de regra de negócio |
| Pré-condições | PHP CLI com mbstring; repositório substituído por dublê novo, sem banco, rede ou sessão real. O idoso 1 existe e a atualização retorna id 1. |
| Passos | 1. Chamar alterarStatus(1, false). 2. Conferir chamada atualizar(1, {ativo:false, quarto_id:null}), liberando o quarto na mesma atualização. |
| Dados de entrada | {"idoso_id": 1, "ativo": false} |
| Resultado esperado | Atualizar ativo e quarto juntos |
| Resultado obtido | As verificações automatizadas passaram. Atualizar ativo e quarto juntos |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/unitarios.json — registro CT-U50; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-I01 Instalação, migrations e capacidade de quarto

| Campo | Descrição |
| --- | --- |
| ID | CT-I01 |
| Requisito/Funcionalidade | Instalação, migrations e capacidade de quarto |
| Tipo de teste | Integração PHP ↔ MySQL |
| Pré-condições | MySQL 8.0.16+, credenciais explícitas, base exclusiva asilosoft_test_* |
| Passos | 1. Executar DatabaseIntegrationTest.php; 2. Migrar base nova; 3. Repetir; 4. Simular schema histórico; 5. Tentar exceder ocupação |
| Dados de entrada | Material Medicamento; entrada 10; quarto capacidade 1; dois residentes fictícios |
| Resultado esperado | Migrations idempotentes, dados preservados, capacidade imposta no banco e rotas protegidas |
| Resultado obtido | Todas as verificações aprovadas |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/CT-I01.log; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-I02 Cadastro e vínculos familiares

| Campo | Descrição |
| --- | --- |
| ID | CT-I02 |
| Requisito/Funcionalidade | Cadastro e vínculos familiares |
| Tipo de teste | Integração serviço ↔ repositório ↔ MySQL |
| Pré-condições | mbstring, pcntl e base nova; privilégios CREATE/DROP |
| Passos | 1. Executar FamiliaresIntegrationTest.php; 2. Salvar e vincular familiares; 3. Validar contas e responsável; 4. Executar concorrência |
| Dados de entrada | Fixtures fictícias declaradas no script, incluindo vínculos e 24 operações concorrentes |
| Resultado esperado | Associação e responsável consistentes; duplicidade e conflitos rejeitados; rollback íntegro |
| Resultado obtido | Cadastro, rollback e concorrência aprovados |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/CT-I02.log; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-I03 Prontuário, busca e ficha consolidada

| Campo | Descrição |
| --- | --- |
| ID | CT-I03 |
| Requisito/Funcionalidade | Prontuário, busca e ficha consolidada |
| Tipo de teste | Integração serviço ↔ repositório ↔ MySQL |
| Pré-condições | Schema migrado; transação de fixtures |
| Passos | 1. Executar ClinicaIntegrationTest.php; 2. Buscar residente; 3. Registrar nota privada, publicada e retificação; 4. Conferir autoria, limites e auditoria |
| Dados de entrada | CPF 12345678901; identificador CLINICA-001; descrições Privado/Publicado; arrays e textos acima de 65000 bytes |
| Resultado esperado | Autoria real; retificação aditiva; histórico e auditoria preservados; entradas inválidas rejeitadas |
| Resultado obtido | Todas as verificações aprovadas; fixtures revertidas |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/CT-I03.log; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-I04 Estoque e ciclo de compras

| Campo | Descrição |
| --- | --- |
| ID | CT-I04 |
| Requisito/Funcionalidade | Estoque e ciclo de compras |
| Tipo de teste | Integração serviço ↔ repositório ↔ MySQL, concorrência |
| Pré-condições | pcntl; base exclusiva; fixtures fictícias |
| Passos | 1. Executar EstoqueComprasIntegrationTest.php; 2. Entrar 10; 3. Consumir 2.125 e repetir chave; 4. Concorrer duas retiradas de 5; 5. Transicionar compras |
| Dados de entrada | Entrada 10 SUS; consumo 2.125; duas retiradas 5; quantidades 0, 1.0001, 1000000000 e 1e3 |
| Resultado esperado | Uma baixa por chave; apenas uma retirada concorrente; saldo final 2.875; nenhuma auditoria parcial; transições ilegais rejeitadas |
| Resultado obtido | Todas as verificações aprovadas |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/CT-I04.log; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-I05 Primeiro administrador

| Campo | Descrição |
| --- | --- |
| ID | CT-I05 |
| Requisito/Funcionalidade | Primeiro administrador |
| Tipo de teste | Integração CLI ↔ serviço/script ↔ MySQL |
| Pré-condições | Base nova isolada; proc_open |
| Passos | 1. Executar InstalacaoIntegrationTest.php; 2. Tentar senha inválida; 3. Inicializar administrador; 4. Repetir instalação |
| Dados de entrada | Conta e senhas fictícias do script |
| Resultado esperado | Senha inválida não grava; primeiro administrador usa hash; segunda inicialização recusada |
| Resultado obtido | Todas as verificações aprovadas |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/CT-I05.log; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

# CT-I06 Fluxos HTTP, sessão e permissões

| Campo | Descrição |
| --- | --- |
| ID | CT-I06 |
| Requisito/Funcionalidade | Fluxos HTTP, sessão e permissões |
| Tipo de teste | Integração HTTP ↔ aplicação ↔ MySQL; regressão |
| Pré-condições | curl, proc_open; porta local 18087 livre; base nova exclusiva |
| Passos | 1. Executar HttpIntegrationTest.php; 2. Login por perfil; 3. Cadastrar/vincular familiar e publicar nota; 4. Consumir e reenvio; 5. Ciclo de compras; 6. Tentar ações sem CSRF e sem permissão; 7. Logout |
| Dados de entrada | Usuários admin/saúde/estoque/familiares; Ana; RES-001; CSRF ausente/inválido; IDs trocados |
| Resultado esperado | Dados persistem; sessão revogada; permissões e CSRF aplicados; consumo idempotente; arquivos internos inacessíveis |
| Resultado obtido | Todas as verificações HTTP e regressões aprovadas |
| Status | Passou |
| Evidência | docs/testes/evidencias/20261123T201841_16280/CT-I06.log; execução geral em docs/testes/evidencias/20261123T201841_16280/execucao.log |

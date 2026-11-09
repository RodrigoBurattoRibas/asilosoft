# Roteiro de homologação do AsiloSoft

Versão preparada em 09/11/2026. RF01–RF12 implementados; o aceite abaixo deve ser registrado pela equipe de homologação. Usar dados fictícios em banco exclusivo. A aprovação humana, a compatibilidade nos navegadores da equipe e o comportamento no ambiente de destino serão verificados durante a homologação.

## Preparar o ambiente

1. Disponibilizar PHP 8 com `pdo_mysql` e `mbstring`, MySQL 8.0.16+ e banco exclusivo. Para executar testes, também habilitar `curl`, `dom`, `pcntl` e `proc_open` no PHP CLI.
2. Criar `.env` a partir de `.env.example` com os dados desse banco. Em atualização de instalação existente, fazer backup e testar sua restauração antes de aplicar migrations.
3. Executar `php src/infra/Database/bootstrap.php`. Repetir e conferir “Banco de dados já está atualizado”. Não executar `schema.sql` manualmente por cima das migrations.
4. Em instalação nova, definir `ASILOSOFT_ADMIN_NOME`, `ASILOSOFT_ADMIN_EMAIL` e `ASILOSOFT_ADMIN_SENHA` no ambiente e executar `php scripts/criar-administrador.php`. A senha deve ter de 12 a 72 bytes. Para informar a senha sem gravá-la no histórico do Bash:

   ```bash
   export ASILOSOFT_ADMIN_NOME='Administrador de Homologação'
   export ASILOSOFT_ADMIN_EMAIL='admin@example.test'
   read -r -s -p 'Senha inicial: ' ASILOSOFT_ADMIN_SENHA
   export ASILOSOFT_ADMIN_SENHA
   php scripts/criar-administrador.php
   unset ASILOSOFT_ADMIN_SENHA
   ```

5. Iniciar `php -S localhost:8000 router.php` na raiz e abrir `http://localhost:8000`. Para encerrar, usar Ctrl+C. Manter debug desligado. Em servidor Apache/Nginx, permitir somente o front controller e os assets; bloquear arquivos internos. HTTPS deve ser fornecido no ambiente remoto.
6. Entrar com o administrador e criar contas para Profissional de Saúde, Almoxarife e dois Familiares. Usar senhas próprias, sem credenciais padrão. Em **Quartos → Novo quarto**, cadastrar um quarto com capacidade 2, dois idosos, dois familiares e um material com estoque mínimo 5. Associar cada conta Familiar ao respectivo contato e vincular somente um deles ao primeiro idoso.
7. Para regressão automatizada, apontar as variáveis `ASILOSOFT_TEST_DB_*` a outro MySQL exclusivo e executar `bash tests/run.sh` conforme `tests/README.md`. Não apontar os testes para o banco usado pela equipe.

## Matriz de acesso esperada

| Área/ação | Administrador | Saúde | Almoxarife | Familiar |
| --- | --- | --- | --- | --- |
| Usuários, quartos e cadastro de idosos | Gerenciar | Sem gestão | Sem acesso | Sem acesso |
| Busca e ficha clínica | Consultar | Consultar | Sem acesso | Sem acesso |
| Anotação e retificação de saúde | Consultar | Registrar | Sem acesso | Sem acesso |
| Familiares e vínculos | Gerenciar | Sem gestão | Sem acesso | Sem gestão |
| Estoque, entrada e retirada | Gerenciar | Sem gestão | Gerenciar | Sem acesso |
| Consumo individual | Registrar | Registrar | Registrar | Sem acesso |
| Compras | Solicitar e decidir | Sem acesso | Solicitar | Sem acesso |
| Portal publicado | Sem portal familiar | Sem portal familiar | Sem portal familiar | Somente idosos ativos vinculados |

A interface deve apresentar apenas as áreas autorizadas. Conferir também URLs diretas: ocultar um atalho não substitui a autorização do servidor.

## Casos de aceite

Registrar cada caso como aprovado ou reprovado, com navegador, perfil, evidência e descrição de qualquer divergência.

| Caso | Ação | Resultado esperado |
| --- | --- | --- |
| H01 — RF10/11 | Entrar com cada perfil, errar senha, desativar conta e tentar acessar novamente | Credenciais inválidas rejeitadas; conta desativada perde acesso; logout invalida acesso privado |
| H02 — RF01 | Confirmar que Idosos não tem gestão de quartos; em Quartos criar/editar com capacidade 1 e 99 e rejeitar 0 e 100; ativar/desativar quarto vazio. Criar/editar idoso, repetir CPF/identificador, lotar quarto e tentar uma terceira alocação | Dados válidos persistem; duplicidade e excesso de capacidade rejeitados; quarto inativo não recebe alocação |
| H03 — RF02 | Vincular dois contatos, escolher principal, associar um contato a dois idosos e remover vínculo | Vínculos corretos; no máximo um principal; remoção refletida na ficha e portal |
| H04 — RF03/05 | Buscar por nome, CPF pontuado e identificador; registrar anotação privada e publicada; retificar | Busca encontra idoso; ficha consolidada; só Saúde escreve; original preservado após retificação |
| H05 — RF04/06/07 | Criar material mínimo 5; entrar 10 com origem; retirar 2 com finalidade; tentar retirar 99 ou sem finalidade | Saldo 8; origem/autor/finalidade no histórico; tentativas inválidas não alteram saldo/histórico |
| H06 — RF12 | Consumir 2 para o idoso; reenviar o mesmo formulário; tentar quantidade acima do saldo | Saldo 6; um uso e uma baixa; falha mantém os campos para correção; uso aparece na ficha |
| H07 — estoque mínimo | Retirar 2 com finalidade após H06 | Saldo 4 e indicação “Abaixo do mínimo”; editar mínimo reflete situação |
| H08 — RF08 | Solicitar compra; tentar aprovar como Almoxarife; aprovar e atender como Admin; tentar decisão obsoleta; recusar outra compra | Só Admin decide; transições válidas; estados terminais respeitados; estoque não muda |
| H09 — RF09 | Consultar como familiar vinculado e como outro familiar; testar URL de outro idoso | Somente anotações publicadas do idoso ativo vinculado; sem dados privados ou de outro idoso |
| H10 — revogação | Remover vínculo, desativar idoso ou conta e repetir consulta | Acesso revogado na requisição seguinte |
| H11 — proteção | Acessar `/.env`, `/.git/config`, `/src/infra/Database/schema.sql` e `/scripts/criar-administrador.php` | HTTP 404 sem conteúdo interno; CSS e JS continuam carregando |
| H12 — RNF01/07/08 | Repetir login, ficha, consumo e compras em Chrome, Edge e Firefox; larguras 390, 768 e 1366 px | Campos, foco, mensagens e ações acessíveis; sem corte global; tabelas podem rolar internamente |
| H13 — RNF05 | Medir consultas principais no destino, com massa e usuários simultâneos representativos | Até 2 segundos em uso normal; registrar massa, concorrência, rede, amostras e p95 |
| H14 — RNF06 | Configurar monitoramento externo e janela de medição com horário de funcionamento e manutenções registradas | Disponibilidade ≥99% na janela acordada; responsável e evidência identificados |

## Registro do aceite e operação

| Campo | Preencher pela homologação |
| --- | --- |
| Commit e URL do ambiente | |
| Responsável e data | |
| PHP/MySQL e navegadores/versões | |
| Casos aprovados e evidências | |
| Defeitos, gravidade e reprodução | |
| Desempenho e período de disponibilidade | |
| Decisão: aprovado / aprovado com ressalvas / reprovado | |

Bloquear publicação se houver acesso indevido, perda de dados, saldo incorreto ou falha em fluxo principal. Após correções, repetir os casos afetados e a regressão automatizada. Para retorno de versão, preservar logs/evidências, interromper escritas, restaurar código e backup compatíveis conforme o ensaio de restauração; não apagar tabelas ou tentar desfazer migrations por tentativa.

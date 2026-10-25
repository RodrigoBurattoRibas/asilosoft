# Validação visual — 24/10/2026

Ambiente: servidor PHP local em 127.0.0.1:18088, MySQL descartável em
127.0.0.1:33317, base asilosoft_test_visual e dados fictícios. A configuração
.env não foi alterada. Os envios de formulários foram feitos com dados fictícios.
Após o relato de falha no servidor do usuário, foi aplicada somente a migration
008_idempotencia_uso no banco configurado, preservando os registros existentes.

## Correções

- Navegação com fundo escuro, risco amarelo inferior e aria-current para a seção atual.
  Compras, Familiares e Consumo agora indicam suas próprias áreas; o acesso
  do profissional às fichas e o portal familiar também recebem o marcador.
- Compras: situação atual em coluna independente, coluna própria de ações,
  seletor identificado e opção inicial neutra. A seleção exige uma escolha
  antes de enviar, sem aparentar que uma compra pendente já foi aprovada.
- Consumo: formulário com largura limitada e campos adaptáveis.
- Avisos de sucesso/erro no fluxo da página, sem cobrir o cabeçalho.
- Espaçamento entre cartões da ficha, títulos das tabelas, foco de teclado,
  quebra do cabeçalho e alinhamento das células.

## Conferência no navegador

Navegador integrado Chromium, viewport normal e largura móvel de 390 px.

| Fluxo | Conferência |
| --- | --- |
| Login e início | Login com administrador fictício e navegação |
| Consumo | Seleção de idoso/material, envio, sucesso, baixa refletida na ficha e estoque; erro por saldo insuficiente |
| Compras | Lista vazia, nova solicitação, pendente → aprovada → atendida; outra solicitação pendente → recusada |
| Compras móvel | Situação separada do seletor, ações acessíveis por rolagem da tabela; largura do documento igual ao viewport (390 px) |
| Consumo móvel | Campos e título contidos na tela; navegação destacada |
| Ficha do idoso | Cartões separados e consumo listado; conferência móvel |
| Almoxarifado | Lista, histórico, formulário de entrada e registro de entrada |
| Familiares | Formulário, cadastro e retorno à lista com sucesso |
| Quartos | Lista, cadastro e retorno à lista com ocupação |
| Usuários | Inspeção visual da lista e ações |

## Verificações automatizadas

- tests/integration/HttpIntegrationTest.php: aprovado em base isolada,
  incluindo autenticação dos cinco perfis de teste, vínculos, prontuário,
  consumo idempotente, compras, permissões, CSRF, quartos e persistência.
- tests/estoque_compras_rules.php: 40 verificações aprovadas.
- Sintaxe de todos os arquivos PHP em src: aprovada.
- git diff --check: aprovado.

A inspeção visual não cobre todas as combinações de dados, todos os perfis
em cada resolução, nem navegadores externos como Safari e Firefox. Os testes
HTTP complementam a inspeção, mas não substituem a renderização visual.

## Correções complementares

- Removido o atalho interno de consumo no Almoxarifado; acesso pelo menu e início.
- Cabeçalhos do estoque e histórico padronizados para “Nome do item”.
- Cards do início com dimensões iguais; espaçamento de fichas restrito aos cartões
  diretos do conteúdo, sem afetar os atalhos.
- Consumo exibe saldo/unidade e mantém campos e chave de reenvio após falha.
  Mensagem de erro permanece visível para permitir a correção.
- Teste HTTP adicional confirma que falha não grava consumo nem baixa estoque,
  preserva campos e permite corrigir a quantidade e reenviar sem duplicação.
- Causa da falha no banco configurado: coluna chave_requisicao ausente na tabela
  usos_materiais. Aplicada a migration 008 existente e verificada a coluna e a
  preservação da contagem de registros. Nenhum consumo fictício foi gravado
  nesse banco; os testes de envio permaneceram na base descartável.
- No navegador de teste, consumo de 2 unidades para Bruno confirmado no histórico
  e saldo atualizado para 9 unidades. Destaque discreto e rótulos conferidos.

# Correções e dependências - AsiloSoft

Atualizado em 28/11/2026. O código completo das correções está em [correcoes.patch](correcoes.patch) (diferença entre o commit `0608bc1`, anterior às correções, e a versão atual de `index.php`, `router.php` e `src/`).

## 1. Correções aplicadas

| ID | Origem | Severidade | Problema | Correção | Arquivo | Comprovação |
| --- | --- | --- | --- | --- | --- | --- |
| DEF-S01 | Suíte automatizada (CT-S13) | Alta | Login sem limite de tentativas | Máximo de 5 falhas por e-mail em 15 minutos, guardadas como hash na tabela `tentativas_login` | `AsiloAutenticacaoService.php`, `AsiloTentativaLoginRepository.php`, migration `009_tentativas_login.sql` | CT-S13 e MT-08 |
| DEF-S02 | Suíte automatizada (CT-S17) | Média | Cookie de sessão sem HttpOnly e SameSite | `session_set_cookie_params` com HttpOnly, SameSite=Lax e Secure em HTTPS | `index.php` | CT-S17 e MT-01 |
| DEF-S03 | Suíte automatizada (CT-S28) | Média | Sistema podia ser aberto em iframe (clickjacking) | `X-Frame-Options: DENY` e CSP `frame-ancestors 'none'` | `index.php` | CT-S28 e MT-01 |
| DEF-S04 | Suíte automatizada (CT-S29) | Baixa | Faltavam nosniff, CSP e Referrer-Policy | Cabeçalhos enviados em todas as páginas | `index.php` | CT-S29 e MT-01 |
| DEF-S05 | Suíte automatizada (CT-S12) | Baixa | Tempo do login revelava se o e-mail existe (2 ms x 195 ms) | `password_verify` sempre executado, com hash fictício quando a conta não existe | `AsiloAutenticacaoService.php` | CT-S12 (diferença caiu para 10 ms) |
| DEF-S06 | Suíte automatizada (CT-S20) | Baixa | Servidor aceitava ID de sessão inventado | `session.use_strict_mode=1` | `index.php` | CT-S20 |
| DEF-S07 | Suíte automatizada (CT-S30) | Baixa | Versão do PHP exposta em `X-Powered-By` | `header_remove('X-Powered-By')` | `index.php` | CT-S30 e MT-01 |
| DEF-S08 | OWASP ZAP (alertas 10021 e 10037) | Baixa | CSS, JS e página 404 saíam sem `nosniff` e com `X-Powered-By`, porque o `router.php` não passa pelo `index.php` | O `router.php` envia os cabeçalhos e serve os arquivos estáticos pelo PHP | `router.php` | ZAP depois (0 alertas baixos) e MT-10 |

### Resultado antes e depois

| Verificação | Antes | Depois |
| --- | --- | --- |
| Suíte automatizada (36 casos OWASP Top 10) | 29 passaram, 7 vulnerabilidades | 36 de 36 |
| OWASP ZAP 2.17.0 (spider + scan ativo autenticado) | 0 alto, 0 médio, **2 baixos**, 3 informativos ([relatório antes](relatorio-zap-antes.html)) | 0 alto, 0 médio, **0 baixo**, 3 informativos ([relatório depois](relatorio-zap.html)) |
| Testes manuais com curl | - | 10 de 10 ([testes-manuais.md](testes-manuais.md)) |
| Testes unitários | 71 de 71 | 74 de 74 (3 novos para o bloqueio de login) |

Os 3 alertas informativos do ZAP que restaram não são vulnerabilidades:

- **Session Management Response Identified**: o ZAP só registra que identificou o cookie de sessão.
- **User Agent Fuzzer**: o ZAP testou outros navegadores e a resposta não mudou.
- **User Controllable HTML Element Attribute (Potential XSS)**: o valor do filtro (`situacao`) aparece num atributo da página, mas sai escapado. A suíte automatizada (CT-S09) e o MT-04 confirmam que um script enviado nesse lugar não executa.

## 2. Dependências

O AsiloSoft não usa gerenciador de pacotes: não existe `composer.json` nem `package.json`, e nenhuma biblioteca de terceiros é carregada (nem por CDN). Por isso não há dependência externa com vulnerabilidade conhecida a corrigir. O que precisa ficar atualizado é o próprio ambiente:

| Dependência | Uso no AsiloSoft | Versão usada nos testes | Cuidado |
| --- | --- | --- | --- |
| PHP 8 | Linguagem da aplicação | 8.5.4 | Manter na última versão de correção do ramo em uso; versões antigas sem suporte deixam de receber correções de segurança |
| Extensão `pdo_mysql` | Acesso ao banco com consultas preparadas | Do PHP 8.5.4 | Necessária; vem com o pacote do PHP |
| Extensão `mbstring` | Texto com acentos (`mb_strtolower`, `mb_strlen`, `mb_substr`) | Do PHP 8.5.4 | Necessária; sem ela o login quebra |
| MySQL | Banco de dados (Railway) | MySQL 8.0.16+ exigido; testes locais em MariaDB 10.4.32 | Manter o serviço do Railway atualizado |
| JavaScript próprio | `src/assets/js/asilosoft.js` (18 linhas, sem bibliotecas) | - | Sem dependências |
| Ferramentas só de teste | OWASP ZAP 2.17.0 (Java 25); extensões `pcntl` e `posix` para a carga | - | Não fazem parte da aplicação publicada |

## 3. Como o ZAP foi executado

- Versão: OWASP ZAP 2.17.0, pacote oficial para Linux, em modo linha de comando (`zap.sh -cmd -autorun plano-zap.yaml`).
- Alvo: cópia local do AsiloSoft em `127.0.0.1:18092`, com base de teste e dados fictícios. O sistema publicado não foi escaneado.
- Autenticação: cookie de sessão de um administrador fictício injetado em todas as requisições. O logout (`sair`) e a desativação de usuários (`usuarios-status`) ficaram de fora, para o scan não derrubar a própria sessão.
- Etapas: spider (120 URLs), scan passivo e scan ativo (injeção de SQL, XSS, travessia de diretório, inclusão de arquivo, entre outros).
- Plano usado: [plano-zap.yaml](plano-zap.yaml), com o ID de sessão substituído por um marcador.

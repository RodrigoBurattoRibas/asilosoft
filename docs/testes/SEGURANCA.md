# Testes de segurança - AsiloSoft

Atualizado em 27/11/2026. Script: [tests/seguranca/SegurancaTest.php](../../tests/seguranca/SegurancaTest.php). PHP puro, sem dependências novas.

## 1. Objetivo e abordagem

Procurar vulnerabilidades reais pelo HTTP, como um atacante faria, cobrindo as categorias do OWASP Top 10 (2021) que se aplicam ao AsiloSoft: injeção de SQL e XSS (A03), controle de acesso e CSRF (A01), autenticação e sessão (A07), armazenamento de senhas (A02), configuração e cabeçalhos (A05) e tratamento de entradas inesperadas (A04/A05).

O teste é **caixa-cinza**: conhece as rotas e os perfis (lidos do próprio Router), mas ataca somente pela interface HTTP, com um usuário logado de cada perfil e também sem login. Os resultados no banco são conferidos direto nas tabelas, para provar que um ataque não gravou nada.

Segurança do próprio teste: sobe o servidor em 127.0.0.1 com a mesma configuração do Railway (`php -S ... router.php`), cria uma base própria `asilosoft_test_seguranca_<data>_<pid>`, usa somente dados fictícios (`@example.test`) e remove a base ao terminar. Nenhum ataque é enviado ao sistema publicado.

## 2. Como executar

```bash
export ASILOSOFT_TEST_DB_HOST=127.0.0.1
export ASILOSOFT_TEST_DB_PORTA=3306
export ASILOSOFT_TEST_DB_USUARIO=usuario_de_testes
export ASILOSOFT_TEST_DB_SENHA='senha_de_testes'
php tests/seguranca/SegurancaTest.php
```

Leva cerca de 30 segundos. Código de saída: 0 sem vulnerabilidades, 1 com vulnerabilidades, 2 em impedimento de ambiente. A porta padrão é 18091 (`ASILOSOFT_TEST_HTTP_PORT` troca). Evidências em `docs/testes/evidencias/seguranca_<data>/` (`relatorio.md`, `resultado.json` e `servidor.log`).

## 3. Resultados de 27/11/2026: antes e depois das correções

| Execução | Evidência | Resultado |
| --- | --- | --- |
| Antes (código original) | [seguranca_20261127T203744](evidencias/seguranca_20261127T203744/relatorio.md) | **29 de 36 passaram; 7 vulnerabilidades** (1 alta, 2 médias, 4 baixas, nenhuma crítica) |
| Depois (correções aplicadas) | [seguranca_20261127T204403](evidencias/seguranca_20261127T204403/relatorio.md) | **36 de 36 passaram; 0 vulnerabilidades** |

Ambiente das duas execuções: PHP 8.5.4, MariaDB 10.4.32 descartável local, servidor `php -S` com `router.php`.

### O que está protegido

- **Injeção de SQL**: nenhuma das 29 variações de ataque funcionou (login, busca, ids, cadastros, injeção cega por tempo com `SLEEP`). Todas as consultas usam parâmetros preparados com `ATTR_EMULATE_PREPARES=false`, e a única interpolação (`buscarUm`) usa lista fechada de colunas.
- **XSS**: textos com `<script>` e `onerror` gravados em familiares, materiais e prontuário aparecem escapados em todas as telas, inclusive no portal da família. Parâmetros refletidos (busca, filtro, página, rota) também saem escapados.
- **Controle de acesso**: as 48 rotas internas exigem login; a matriz de 25 telas x 4 perfis (100 combinações) bate com as regras; familiar não abre idoso de outra família trocando o id; requisições forjadas não criam usuário, não aprovam compra nem gravam prontuário; o autor do registro de saúde vem da sessão.
- **CSRF**: as 23 rotas de gravação exigem token válido e método POST; token de uma sessão não vale em outra.
- **Sessão**: o ID é trocado no login (sem fixação de sessão) e o logout invalida a sessão no servidor.
- **Senhas (RNF02)**: bcrypt em todas as contas; senha com menos de 8 caracteres recusada; mensagem de erro igual para e-mail inexistente e senha errada.
- **Arquivos internos**: `.env`, `.git`, migrations, testes, documentação e 6 variações de travessia de diretório (`../`, `%2e%2e`, `%00`) devolvem 404; só os assets públicos são servidos.
- **Erros**: campos enviados como lista, entradas de 100 mil caracteres e métodos HTTP estranhos não geram erro 500, stack trace nem caminho de arquivo.

### Vulnerabilidades encontradas e corrigidas

| ID | Caso | Severidade | Antes | Risco | Correção aplicada | Depois |
| --- | --- | --- | --- | --- | --- | --- |
| DEF-S01 | CT-S13 | Alta | Login sem limite de tentativas: 30 senhas erradas em 5,9 s e a senha certa entrou logo em seguida | Descobrir senhas por tentativa e erro, principalmente de administradores | No máximo 5 falhas por e-mail em 15 minutos, guardadas na tabela `tentativas_login` (migration 009) | Bloqueio a partir da 6ª tentativa; a senha certa também é recusada durante o bloqueio |
| DEF-S02 | CT-S17 | Média | Cookie de sessão só com `path=/` | Script injetado leria o cookie e roubaria a sessão; cookie enviado em requisições de outros sites | `session_set_cookie_params` com `HttpOnly`, `SameSite=Lax` e `Secure` em HTTPS | `path=/; HttpOnly; SameSite=Lax` |
| DEF-S03 | CT-S28 | Média | Nenhum cabeçalho impedia abrir o sistema em iframe | Clickjacking | `X-Frame-Options: DENY` e CSP `frame-ancestors 'none'` | Cabeçalhos presentes |
| DEF-S04 | CT-S29 | Baixa | Sem `X-Content-Type-Options`, `Content-Security-Policy` e `Referrer-Policy` | Menos defesa caso surja um XSS; endereço com id do idoso vazaria para sites externos | Os três cabeçalhos em todas as respostas; CSP só permite recursos do próprio site | Todos presentes |
| DEF-S05 | CT-S12 | Baixa | Login em 2 ms para e-mail inexistente e 195 ms para existente | Descobrir quais e-mails têm conta pelo tempo | `password_verify` sempre executado, contra hash fictício quando a conta não existe | 201 ms e 211 ms (diferença de 10 ms) |
| DEF-S06 | CT-S20 | Baixa | Servidor adotava ID de sessão inventado (`session.use_strict_mode=0`) | Facilita fixação de sessão | `session.use_strict_mode=1` antes do `session_start()` | ID inventado substituído |
| DEF-S07 | CT-S30 | Baixa | `X-Powered-By: PHP/8.5.4` | Versão exata do PHP para quem procura falhas conhecidas | `header_remove('X-Powered-By')` | Cabeçalho ausente |
| DEF-S08 | OWASP ZAP (10021, 10037) | Baixa | CSS, JS e página 404 sem `nosniff` e com `X-Powered-By` (o `router.php` não passa pelo `index.php`) | Mesmo risco de DEF-S04 e DEF-S07 nesses arquivos | `router.php` envia os cabeçalhos e serve os arquivos estáticos pelo PHP | ZAP: 2 alertas baixos antes, 0 depois |

Arquivos alterados: [index.php](../../index.php) (sessão e cabeçalhos), [AsiloAutenticacaoService.php](../../src/modules/autenticacao/services/AsiloAutenticacaoService.php) (limite e tempo constante), [AsiloTentativaLoginRepository.php](../../src/modules/autenticacao/repository/AsiloTentativaLoginRepository.php) (novo), [AsiloAutenticacaoController.php](../../src/modules/autenticacao/controller/AsiloAutenticacaoController.php) e a migration [009_tentativas_login.sql](../../src/infra/Database/migrations/009_tentativas_login.sql). O Railway cria a tabela sozinho no próximo deploy, pelo `bootstrap.php`.

Decisões:

- **Limite por e-mail, não por IP.** No Railway todos os acessos chegam pelo IP do proxy; limitar por IP bloquearia todo mundo junto. O e-mail é guardado como hash SHA-256, não em texto.
- **E-mails inexistentes também são bloqueados**, para que a mensagem "Muitas tentativas" não revele quais contas existem.
- **Efeito colateral conhecido:** quem souber o e-mail de um usuário pode bloquear a conta dele por 15 minutos errando a senha de propósito. É o custo usual desse tipo de proteção; o bloqueio expira sozinho e um login certo depois disso zera as falhas.
- DEF-S02, DEF-S06 e DEF-S07 também dependiam do `php.ini`. Agora o código define esses valores, então o resultado é o mesmo em qualquer servidor.

Reteste e regressão após as correções:

- Suíte de segurança: 36 de 36.
- Unitários: 74 de 74 (os 71 anteriores mais CT-U72 a CT-U74: bloqueio após 5 falhas sem consultar o usuário, registro de falha inclusive para e-mail inexistente e limpeza das falhas após login certo).
- Integrações database, familiares, clínica, estoque/compras e instalação: todas aprovadas, inclusive a aplicação da migration 009. A integração HTTP não foi executada nesta máquina porque ela usa a extensão `curl`, que não está instalada.
- Carga rápida (5, 20 e 60 usuários): mesmos tempos de antes das correções e integridade aprovada.
- O caso CT-S20 foi ajustado para usar um ID de sessão aleatório a cada execução: com um ID fixo, a sessão criada pela execução anterior continuava existindo no servidor e o modo estrito a aceitava corretamente.

### Todos os casos

| Caso | Categoria | OWASP | O que foi testado | Resultado (antes / depois das correções) |
| --- | --- | --- | --- | --- |
| CT-S01 | Injeção de SQL | A03 | Login com `' OR '1'='1`, comentário `--` e `#` no e-mail e na senha | Passou |
| CT-S02 | Injeção de SQL | A03 | Busca de idosos com aspas, `OR 1=1` e `UNION SELECT` na tabela de usuários | Passou |
| CT-S03 | Injeção de SQL | A03 | Injeção cega por tempo com `SLEEP(3)` na busca, id e página | Passou |
| CT-S04 | Injeção de SQL | A03 | Texto e SQL em ids de ficha, prontuário e edição | Passou |
| CT-S05 | Injeção de SQL | A03 | `DROP TABLE` gravado em cadastro e lido de novo (segunda ordem) | Passou |
| CT-S06 | XSS | A03 | XSS armazenado no cadastro de familiar | Passou |
| CT-S07 | XSS | A03 | XSS armazenado no cadastro de material (3 telas) | Passou |
| CT-S08 | XSS | A03 | XSS armazenado no prontuário até o portal da família | Passou |
| CT-S09 | XSS | A03 | XSS refletido na busca, filtros, paginação e rota | Passou |
| CT-S10 | XSS | A03 | Mensagem de erro do login não reflete a entrada | Passou |
| CT-S11 | Autenticação | A07 | Mensagem igual para e-mail inexistente e senha errada | Passou |
| CT-S12 | Autenticação | A07 | Tempo de resposta revela se o e-mail existe | Falhou (Baixa) antes; **Passou** depois |
| CT-S13 | Autenticação | A07 | Proteção contra força bruta | Falhou (Alta) antes; **Passou** depois |
| CT-S14 | Autenticação | A07 | Conta desativada não entra | Passou |
| CT-S15 | Senhas | A02 | Senhas com hash forte, sem texto puro (RNF02) | Passou |
| CT-S16 | Senhas | A07 | Senha com menos de 8 caracteres recusada | Passou |
| CT-S17 | Sessão | A07 | Cookie com HttpOnly e SameSite | Falhou (Média) antes; **Passou** depois |
| CT-S18 | Sessão | A07 | ID de sessão trocado no login | Passou |
| CT-S19 | Sessão | A07 | Logout invalida a sessão no servidor | Passou |
| CT-S20 | Sessão | A07 | Modo estrito de sessão | Falhou (Baixa) antes; **Passou** depois |
| CT-S21 | Controle de acesso | A01 | Todas as rotas internas exigem login | Passou |
| CT-S22 | Controle de acesso | A01 | Matriz de 25 telas x 4 perfis (RNF03) | Passou |
| CT-S23 | Controle de acesso | A01 | Familiar trocando o id do idoso (IDOR) | Passou |
| CT-S24 | Controle de acesso | A01 | Escalada de privilégio por requisição forjada | Passou |
| CT-S25 | Controle de acesso | A01 | Autor do prontuário não pode ser forjado | Passou |
| CT-S26 | CSRF | A01 | 23 rotas de gravação sem token, com token inválido e por GET | Passou |
| CT-S27 | CSRF | A01 | Token de outra sessão | Passou |
| CT-S28 | Configuração | A05 | Proteção contra clickjacking | Falhou (Média) antes; **Passou** depois |
| CT-S29 | Configuração | A05 | Cabeçalhos nosniff, CSP e Referrer-Policy | Falhou (Baixa) antes; **Passou** depois |
| CT-S30 | Configuração | A05 | Versão do PHP exposta | Falhou (Baixa) antes; **Passou** depois |
| CT-S31 | Configuração | A05 | Prontuário e ficha sem cache no navegador | Passou |
| CT-S32 | Arquivos | A05 | 20 caminhos internos e de travessia de diretório | Passou |
| CT-S33 | Arquivos | A01 | Parâmetro route com `../`, `php://` e nomes de arquivo | Passou |
| CT-S34 | Erros | A05 | Campos enviados como lista (array) | Passou |
| CT-S35 | Erros | A04 | Entradas de 10 mil e 100 mil caracteres | Passou |
| CT-S36 | Erros | A05 | Métodos TRACE, PUT, DELETE, OPTIONS e PATCH | Passou |

Os textos completos de esperado e obtido de cada caso estão no `relatorio.md` da evidência.

## 4. OWASP ZAP e testes manuais (28/11/2026)

- OWASP ZAP 2.17.0, spider e scan ativo autenticado como administrador fictício, contra a cópia local: antes da correção do `router.php`, 0 alto, 0 médio, 2 baixos (DEF-S08); depois, 0 alto, 0 médio, 0 baixo e 3 informativos. Relatórios em [entrega/Seguranca](entrega/Seguranca/).
- 10 testes manuais com curl, com passos e respostas reais: 10 de 10 ([entrega/Seguranca/testes-manuais.md](entrega/Seguranca/testes-manuais.md)).
- Correções e dependências: [entrega/Seguranca/correcoes-e-dependencias.md](entrega/Seguranca/correcoes-e-dependencias.md). Suíte automatizada depois da correção do router: [seguranca_20261128T082049](evidencias/seguranca_20261128T082049/relatorio.md), 36 de 36.

## 5. Limitações

- O ZAP rodou com as regras padrão por poucos minutos; um scan mais longo ou manual com Burp pode encontrar outros vetores.
- HTTPS, certificado e configuração do Railway não foram avaliados, porque o teste não ataca o sistema publicado.
- O projeto não usa Composer nem bibliotecas de terceiros, então não há análise de dependências vulneráveis a fazer além da versão do PHP.
- Banco de teste: MariaDB 10.4 local em vez de MySQL 8 (os mesmos motivos de [CARGA_ESTRESSE.md](CARGA_ESTRESSE.md)). Não afeta os achados, que são da camada HTTP e PHP.
- Negação de serviço não faz parte desta suíte; a capacidade sob muitos acessos está no teste de carga e estresse.

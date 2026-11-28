# Testes manuais de segurança - AsiloSoft

Executados em 28/11/2026, às 08:18, com `curl`, contra uma cópia local do AsiloSoft (servidor `php -S` com `router.php`, base de teste `asilosoft_test_*` e dados fictícios). Nenhum teste foi feito no sistema publicado. As respostas abaixo são as saídas reais; o log completo está em [testes-manuais.log](testes-manuais.log) e os comandos em [testes-manuais.sh](testes-manuais.sh).

Contas fictícias usadas: `admin02@example.test` (Administrador), `familia01@example.test` (Familiar ligado ao idoso 1), `saude01@example.test` (Profissional de Saúde) e `estoque02@example.test` (Almoxarife). Senha de teste: `SenhaTeste123`.

## Resumo

| ID | Teste | Esperado | Obtido | Resultado |
| --- | --- | --- | --- | --- |
| MT-01 | Cabeçalhos de segurança e cookie de sessão | X-Frame-Options, nosniff, Referrer-Policy, CSP; cookie HttpOnly e SameSite; sem X-Powered-By | Todos presentes; `HttpOnly; SameSite=Lax`; X-Powered-By ausente | Passou |
| MT-02 | Arquivos internos pelo navegador | 404 para `.env`, `.git`, migrations, testes e travessia `%2e%2e`; CSS público 200 | 404 em todos; CSS 200 | Passou |
| MT-03 | Injeção de SQL no login | Ataque não autentica | Volta para o login; home continua bloqueada | Passou |
| MT-04 | Injeção de SQL e XSS na busca de idosos | Nenhum idoso extra, sem erro SQL; script exibido como texto | 0 resultados, 0 erros SQL; `&lt;script&gt;` escapado | Passou |
| MT-05 | Tela interna sem login | Redireciona para o login | 303 para `?route=login` | Passou |
| MT-06 | Familiar acessando telas de outros perfis e idoso de outra família | Só o próprio idoso (id 1) liberado | 200 só em `acompanhamento-familiar&id=1`; 403/303 no resto | Passou |
| MT-07 | CSRF: sem token, token de outra sessão e GET | 419, 419 e 405 | 419, 419 e 405 | Passou |
| MT-08 | Força bruta: 6 senhas erradas seguidas | Bloqueio na 6ª tentativa, inclusive com a senha certa | Tentativas 1 a 5: senha inválida; 6ª: "Muitas tentativas"; senha certa recusada | Passou |
| MT-09 | Logout invalida cookie copiado | Cookie antigo perde o acesso | 303 para o login | Passou |
| MT-10 | Cabeçalhos em CSS e página 404 (achado do ZAP) | `X-Content-Type-Options: nosniff` e sem X-Powered-By | nosniff presente nos dois; X-Powered-By ausente | Passou |

**10 de 10 testes passaram.**

## Passo a passo e respostas

### MT-01 Cabeçalhos de segurança e cookie de sessão

```
$ curl -s -D - -o /dev/null 'http://127.0.0.1:18092/?route=login'
HTTP/1.1 200 OK
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
Referrer-Policy: same-origin
Content-Security-Policy: default-src 'self'; frame-ancestors 'none'; form-action 'self'; base-uri 'self'; object-src 'none'
Set-Cookie: PHPSESSID=6d72cf24c580cf820c6cbb4881ab7856; path=/; HttpOnly; SameSite=Lax
Cache-Control: no-store, no-cache, must-revalidate
```

A primeira tentativa usou `curl -I` (método HEAD) e recebeu 405, porque a tela de login aceita só GET. Os cabeçalhos já vinham iguais; o teste foi refeito com GET.

### MT-02 Arquivos internos

```
$ curl -s -o /dev/null -w '%{http_code}' <caminho>
/.env                                                   HTTP 404
/.git/config                                            HTTP 404
/src/infra/Database/migrations/009_tentativas_login.sql HTTP 404
/tests/README.md                                        HTTP 404
/src/assets/%2e%2e/%2e%2e/.env                          HTTP 404
/src/assets/css/asilosoft.css                           HTTP 200
```

### MT-03 Injeção de SQL no login

```
$ curl ... --data-urlencode "email=' OR '1'='1' -- " --data-urlencode "senha=x' OR '1'='1" '.../?route=entrar'
303 http://127.0.0.1:18092/?route=login
Acesso à home depois do ataque: HTTP 303 http://127.0.0.1:18092/?route=login
```

### MT-04 Injeção de SQL e XSS na busca (logado como administrador)

```
Login admin02@example.test: 303 http://127.0.0.1:18092/?route=home
busca=' OR '1'='1 -> residentes listados: 0; erro SQL na página: 0
busca=<script>alert(1)</script> -> tag crua na página: 0; versão escapada: &lt;script&gt;alert(1)&lt;/script&gt;
```

### MT-05 Tela interna sem login

```
$ curl -s -o /dev/null -w '%{http_code} %{redirect_url}' 'http://127.0.0.1:18092/?route=usuarios'
303 http://127.0.0.1:18092/?route=login
```

### MT-06 Familiar acessando outras telas

```
Login familia01@example.test: 303 http://127.0.0.1:18092/?route=home
usuarios                         HTTP 303
idosos                           HTTP 403
idosos-ficha&id=1                HTTP 403
almoxarifado                     HTTP 403
compras                          HTTP 403
acompanhamento-familiar&id=1     HTTP 200
acompanhamento-familiar&id=2     HTTP 403
```

### MT-07 CSRF

```
Sem token: {"success":false,"mensagem":"A sessão do formulário expirou. Atualize a página e tente novamente."} HTTP 419
Token da sessão A usado na sessão B: {"success":false,"mensagem":"A sessão do formulário expirou. Atualize a página e tente novamente."} HTTP 419
GET em rota de gravação: HTTP 405
```

### MT-08 Força bruta

```
Tentativa 1: E-mail ou senha inválidos.
Tentativa 2: E-mail ou senha inválidos.
Tentativa 3: E-mail ou senha inválidos.
Tentativa 4: E-mail ou senha inválidos.
Tentativa 5: E-mail ou senha inválidos.
Tentativa 6: Muitas tentativas sem sucesso. Aguarde 15 minutos e tente novamente.
Senha certa durante o bloqueio: 303 http://127.0.0.1:18092/?route=login
```

### MT-09 Logout

```
Cookie copiado depois do logout: 303 http://127.0.0.1:18092/?route=login
```

### MT-10 Cabeçalhos em arquivos estáticos e 404

```
$ curl -s -D - -o /dev/null 'http://127.0.0.1:18092/src/assets/css/asilosoft.css'
HTTP/1.1 200 OK
X-Content-Type-Options: nosniff
Content-Type: text/css; charset=utf-8
$ curl -s -D - -o /dev/null 'http://127.0.0.1:18092/robots.txt'
HTTP/1.1 404 Not Found
X-Content-Type-Options: nosniff
```

## Como repetir

Com o AsiloSoft de teste rodando em `127.0.0.1:18092` e as contas fictícias acima criadas (o ambiente da suíte `tests/seguranca/SegurancaTest.php` cria essas contas), execute `bash testes-manuais.sh`. O teste MT-08 bloqueia a conta `estoque02` por 15 minutos.

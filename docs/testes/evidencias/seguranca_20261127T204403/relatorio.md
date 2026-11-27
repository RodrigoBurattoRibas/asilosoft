# Relatório de testes de segurança - AsiloSoft

Gerado em 27/11/2026 20:44:10 por `php tests/seguranca/SegurancaTest.php`.

PHP 8.5.4, MySQL 10.4.32-MariaDB, PHP embutido (php -S) com router.php em 127.0.0.1:18091; asilosoft_test_* criada e removida pela suíte. Dados fictícios.

**36 de 36 casos passaram. 0 vulnerabilidades encontradas.**

## Todos os casos

| Caso | Categoria | OWASP | Título | Esperado | Obtido | Resultado |
| --- | --- | --- | --- | --- | --- | --- |
| CT-S01 | Injeção de SQL | A03 | Login não aceita injeção no e-mail ou na senha | Nenhum payload autentica; resposta volta ao login sem erro interno | 5 payloads recusados; todos redirecionaram para o login | Passou |
| CT-S02 | Injeção de SQL | A03 | Busca de idosos trata aspas e UNION como texto | Nenhum residente extra listado, sem hash de senha e sem mensagem de erro SQL | 4 payloads tratados como texto: 0 resultados, sem erro | Passou |
| CT-S03 | Injeção de SQL | A03 | Injeção cega por tempo (SLEEP) não executa | Respostas em menos de 2,5 s com payload SLEEP(3) | Maior tempo: 3 ms | Passou |
| CT-S04 | Injeção de SQL | A03 | Parâmetros numéricos (id) rejeitam texto e SQL | Sem erro 500 nem mensagem SQL; nenhum dado de outro registro | 15 combinações sem erro interno; ids inválidos tratados | Passou |
| CT-S05 | Injeção de SQL | A03 | Campos de cadastro gravam payload SQL como texto (inclusive segunda ordem) | Familiar salvo com o nome literal; tabela idosos intacta; busca posterior sem erro | Nome literal gravado; idosos com 5 linhas; listagem normal | Passou |
| CT-S06 | XSS | A03 | XSS armazenado no cadastro de familiar | Lista e edição exibem o texto escapado; tag não é interpretada | Payload salvo e exibido como &lt;script&gt; nas 2 telas | Passou |
| CT-S07 | XSS | A03 | XSS armazenado no cadastro de material | Lista do almoxarifado, compras e consumo exibem o nome escapado | Escapado em almoxarifado, compras-nova e almoxarifado-uso | Passou |
| CT-S08 | XSS | A03 | XSS armazenado no prontuário chega escapado ao portal da família | Prontuário e acompanhamento familiar não interpretam a tag | Escapado no prontuário e no portal da família | Passou |
| CT-S09 | XSS | A03 | XSS refletido na busca, na paginação e na rota | Parâmetros devolvidos na página sempre escapados | 6 pontos de reflexão testados, todos escapados | Passou |
| CT-S10 | XSS | A03 | Mensagens de erro do login não refletem a entrada | E-mail com script gera mensagem genérica, sem a tag | Mensagem genérica "E-mail ou senha inválidos." | Passou |
| CT-S11 | Autenticação | A07 | Mensagem de erro não revela se o e-mail existe | Mesma mensagem para e-mail inexistente e senha errada | Existente: [E-mail ou senha inválidos.] / inexistente: [E-mail ou senha inválidos.] | Passou |
| CT-S12 | Autenticação | A07 | Tempo de resposta não revela se o e-mail existe | Diferença das medianas abaixo de 40 ms | Mediana com e-mail existente 196 ms; inexistente 198 ms; diferença -2 ms | Passou |
| CT-S13 | Autenticação | A07 | Proteção contra força bruta no login | Após no máximo 10 senhas erradas seguidas, novas tentativas são bloqueadas, inclusive com a senha certa | Bloqueio a partir da tentativa 6 (5 falhas aceitas); a senha certa também foi recusada durante o bloqueio | Passou |
| CT-S14 | Autenticação | A07 | Conta desativada não entra | Login recusado para usuário inativo | Login recusado | Passou |
| CT-S15 | Senhas | A02 | Senhas guardadas com hash forte (RNF02) | Todas as senhas com bcrypt/argon2; nenhuma em texto puro | 8 contas com bcrypt | Passou |
| CT-S16 | Senhas | A07 | Política mínima de senha no cadastro de usuário | Senha com menos de 8 caracteres recusada | Cadastro recusado | Passou |
| CT-S17 | Sessão | A07 | Cookie de sessão com HttpOnly e SameSite | Set-Cookie do PHPSESSID com HttpOnly e SameSite=Lax/Strict | Atributos: path=/; HttpOnly; SameSite=Lax | Passou |
| CT-S18 | Sessão | A07 | ID de sessão trocado no login (fixação de sessão) | ID após o login diferente do anterior; ID antigo sem acesso | ID renovado; ID antigo recebeu HTTP 303 em home | Passou |
| CT-S19 | Sessão | A07 | Logout invalida a sessão no servidor | Cookie copiado antes do logout perde o acesso | Cookie copiado recebeu HTTP 303 em home após o logout | Passou |
| CT-S20 | Sessão | A07 | Servidor recusa ID de sessão escolhido pelo cliente (modo estrito) | ID inventado é descartado e o servidor emite outro | ID inventado substituído | Passou |
| CT-S21 | Controle de acesso | A01 | Visitante sem login não acessa nenhuma rota interna | Todas as rotas não públicas redirecionam para o login | 48 rotas protegidas redirecionaram ao login | Passou |
| CT-S22 | Controle de acesso | A01 | Matriz de permissões por perfil em todas as telas (RNF03) | Perfil autorizado recebe 200; os demais recebem 403 ou redirecionamento | 100 combinações perfil x tela conforme a matriz | Passou |
| CT-S23 | Controle de acesso | A01 | Familiar não acessa idoso de outra família (IDOR) | Ids de outros idosos, inexistentes ou inválidos não exibem dados | 7 ids testados; nenhum dado de outra família | Passou |
| CT-S24 | Controle de acesso | A01 | Escalada de privilégio por requisição forjada | Almoxarife, profissional e familiar não criam usuário, não aprovam compra nem registram prontuário | Usuários novos: 0; compra: Pendente; prontuário forjado: 0; administrador ativo: 1 | Passou |
| CT-S25 | Controle de acesso | A01 | Autor do registro de saúde vem da sessão, não do formulário | Campo usuario_id enviado é ignorado | Autor gravado: usuário 3 (esperado 3) | Passou |
| CT-S26 | CSRF | A01 | Todas as ações de gravação exigem token CSRF válido e método POST | Sem token ou com token inválido: 419; GET: 405 | 23 rotas de gravação protegidas | Passou |
| CT-S27 | CSRF | A01 | Token de uma sessão não vale em outra | Token roubado do usuário A é recusado na sessão do usuário B | HTTP 419; quartos criados: 0 | Passou |
| CT-S28 | Configuração | A05 | Proteção contra clickjacking | X-Frame-Options DENY/SAMEORIGIN ou CSP frame-ancestors | X-Frame-Options: DENY; CSP: default-src 'self'; frame-ancestors 'none'; form-action 'self'; base-uri 'self'; object-src 'none' | Passou |
| CT-S29 | Configuração | A05 | Cabeçalhos de proteção do navegador | X-Content-Type-Options: nosniff, Content-Security-Policy e Referrer-Policy presentes | Todos presentes | Passou |
| CT-S30 | Configuração | A05 | Servidor não divulga versão do PHP | Sem cabeçalho X-Powered-By com versão | Cabeçalho ausente | Passou |
| CT-S31 | Configuração | A05 | Páginas com dados clínicos não ficam em cache | Cache-Control com no-store no prontuário e na ficha | Cache-Control: no-store, no-cache, must-revalidate | Passou |
| CT-S32 | Arquivos | A05 | Arquivos internos e travessia de diretório bloqueados | Somente CSS/JS/imagens de src/assets são servidos; o resto devolve 404 | 20 caminhos bloqueados; CSS público servido | Passou |
| CT-S33 | Arquivos | A01 | Parâmetro route não permite incluir arquivos | Valores com ../ ou nomes de arquivo levam ao login/404, sem conteúdo de arquivo | 7 valores maliciosos tratados como rota inexistente | Passou |
| CT-S34 | Erros | A05 | Campos enviados como lista (array) não quebram a aplicação | Sem HTTP 500, stack trace ou caminho de arquivo na resposta | 7 requisições com arrays tratadas sem erro interno | Passou |
| CT-S35 | Erros | A04 | Entradas muito grandes são recusadas sem erro interno | Busca de 10 mil caracteres e nome de 100 mil caracteres sem HTTP 500 | Busca: HTTP 200; cadastro: HTTP 303; nomes acima de 120 caracteres gravados: 0 | Passou |
| CT-S36 | Erros | A05 | Métodos HTTP inesperados não causam erro nem refletem cabeçalhos | TRACE, PUT, DELETE e OPTIONS sem HTTP 500 e sem eco de cookies | 5 métodos tratados sem erro e sem eco | Passou |

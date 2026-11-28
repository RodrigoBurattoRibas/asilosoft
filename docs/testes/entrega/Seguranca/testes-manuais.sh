#!/usr/bin/env bash
# Testes manuais de segurança com curl contra o AsiloSoft de teste em 127.0.0.1:18092.
set -u
B=http://127.0.0.1:18092
T=$(mktemp -d)
SENHA=SenhaTeste123
passo() { echo; echo "### $1"; echo "\$ $2"; }
login() { # $1=email $2=jar
  local tok
  tok=$(curl -s -c "$2" -b "$2" "$B/?route=login" | grep -o 'name="csrf_token" value="[^"]*"' | head -1 | sed 's/.*value="//;s/"$//')
  curl -s -o /dev/null -c "$2" -b "$2" -w '%{http_code} %{redirect_url}' --data-urlencode "csrf_token=$tok" --data-urlencode "email=$1" --data-urlencode "senha=$SENHA" "$B/?route=entrar"
}
token_de() { curl -s -c "$2" -b "$2" "$B/?route=$1" | grep -o 'name="csrf_token" value="[^"]*"' | tail -1 | sed 's/.*value="//;s/"$//'; }

echo "Execução: $(date '+%d/%m/%Y %H:%M:%S') - alvo $B (base de teste, dados fictícios)"

passo "MT-01 Cabeçalhos de segurança na tela de login" "curl -sI '$B/?route=login'"
curl -sI "$B/?route=login" | grep -iE '^(HTTP|x-frame|x-content|referrer|content-security|x-powered|set-cookie|cache-control)'

passo "MT-02 Arquivos internos bloqueados" "curl -s -o /dev/null -w '%{http_code}' <caminho>"
for p in /.env /.git/config /src/infra/Database/migrations/009_tentativas_login.sql /tests/README.md '/src/assets/%2e%2e/%2e%2e/.env' /src/assets/css/asilosoft.css; do
  printf '%-55s HTTP %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' "$B$p")"
done

passo "MT-03 Injeção de SQL no login" "curl -s -o /dev/null -w '%{http_code} %{redirect_url}' --data-urlencode \"email=' OR '1'='1' -- \" --data-urlencode 'senha=x' ... ?route=entrar"
J=$T/sqli; tok=$(curl -s -c $J -b $J "$B/?route=login" | grep -o 'name="csrf_token" value="[^"]*"' | head -1 | sed 's/.*value="//;s/"$//')
curl -s -o /dev/null -c $J -b $J -w '%{http_code} %{redirect_url}\n' --data-urlencode "csrf_token=$tok" --data-urlencode "email=' OR '1'='1' -- " --data-urlencode "senha=x' OR '1'='1" "$B/?route=entrar"
echo "Acesso à home depois do ataque: HTTP $(curl -s -o /dev/null -b $J -w '%{http_code} %{redirect_url}' "$B/?route=home")"

passo "MT-04 Injeção de SQL e XSS na busca de idosos (logado como administrador)" "curl -s -b <sessao> '$B/?route=idosos&busca=<payload>'"
A=$T/admin; echo "Login admin02@example.test: $(login admin02@example.test $A)"
r=$(curl -s -b $A -G "$B/" --data-urlencode "route=idosos" --data-urlencode "busca=' OR '1'='1")
echo "busca=' OR '1'='1 -> residentes listados: $(grep -c 'Residente Teste' <<<"$r"); erro SQL na página: $(grep -ciE 'sqlstate|syntax' <<<"$r")"
r=$(curl -s -b $A -G "$B/" --data-urlencode "route=idosos" --data-urlencode 'busca=<script>alert(1)</script>')
echo "busca=<script>alert(1)</script> -> tag crua na página: $(grep -c '<script>alert(1)</script>' <<<"$r"); versão escapada: $(grep -o '&lt;script&gt;alert(1)&lt;/script&gt;' <<<"$r" | head -1)"

passo "MT-05 Tela interna sem login" "curl -s -o /dev/null -w '%{http_code} %{redirect_url}' '$B/?route=usuarios'"
curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' "$B/?route=usuarios"

passo "MT-06 Familiar tentando telas de outros perfis" "curl -s -o /dev/null -b <sessao familiar> -w '%{http_code}' '$B/?route=<tela>'"
F=$T/familia; echo "Login familia01@example.test: $(login familia01@example.test $F)"
for r in usuarios idosos 'idosos-ficha&id=1' almoxarifado compras 'acompanhamento-familiar&id=1' 'acompanhamento-familiar&id=2'; do
  printf '%-32s HTTP %s\n' "$r" "$(curl -s -o /dev/null -b $F -w '%{http_code}' "$B/?route=$r")"
done

passo "MT-07 CSRF: gravar sem token e com token de outra sessão" "curl -s -b <sessao> -d 'codigo=X&capacidade=2' '$B/?route=quartos-criar'"
echo "Sem token: $(curl -s -b $A -w ' HTTP %{http_code}' -d 'codigo=CSRF1&capacidade=2' "$B/?route=quartos-criar")"
A2=$T/admin_b; login admin02@example.test $A2 >/dev/null; tokA=$(token_de quartos-novo $A)
echo "Token da sessão A usado na sessão B: $(curl -s -b $A2 -w ' HTTP %{http_code}' --data-urlencode "csrf_token=$tokA" -d 'codigo=CSRF2&capacidade=2' "$B/?route=quartos-criar")"
echo "GET em rota de gravação: HTTP $(curl -s -o /dev/null -b $A -w '%{http_code}' "$B/?route=quartos-criar")"

passo "MT-08 Força bruta: 6 senhas erradas seguidas na mesma conta" "curl ... --data-urlencode 'email=estoque02@example.test' --data-urlencode 'senha=errada<N>' ?route=entrar (6 vezes)"
X=$T/bruta
for i in 1 2 3 4 5 6; do
  tok=$(curl -s -c $X -b $X "$B/?route=login" | grep -o 'name="csrf_token" value="[^"]*"' | head -1 | sed 's/.*value="//;s/"$//')
  curl -s -o /dev/null -c $X -b $X --data-urlencode "csrf_token=$tok" --data-urlencode "email=estoque02@example.test" --data-urlencode "senha=errada$i" "$B/?route=entrar"
  msg=$(curl -s -c $X -b $X "$B/?route=login" | grep -oE 'E-mail ou senha inválidos\.|Muitas tentativas[^<]*' | head -1)
  echo "Tentativa $i: $msg"
done
echo "Senha certa durante o bloqueio: $(login estoque02@example.test $X)"

passo "MT-09 Logout invalida a sessão copiada" "copiar cookie, POST ?route=sair, reutilizar cookie copiado"
L=$T/logout; login saude01@example.test $L >/dev/null; cp $L $T/copia
tok=$(curl -s -b $L "$B/?route=home" | grep -o 'name="csrf_token" value="[^"]*"' | head -1 | sed 's/.*value="//;s/"$//')
curl -s -o /dev/null -b $L -c $L --data-urlencode "csrf_token=$tok" "$B/?route=sair"
echo "Cookie copiado depois do logout: $(curl -s -o /dev/null -b $T/copia -w '%{http_code} %{redirect_url}' "$B/?route=home")"

rm -rf "$T"

#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
for key in HOST PORTA USUARIO SENHA; do
    variable="ASILOSOFT_TEST_DB_$key"
    if [[ ! -v "$variable" ]]; then
        echo "Defina explicitamente $variable para o MySQL exclusivo de testes." >&2
        exit 1
    fi
done
php tests/estoque_compras_rules.php
find src tests scripts -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
php -l router.php
run_id="$(date +%Y%m%d%H%M%S)_$$"
for suite in database/Database Familiares Clinica EstoqueCompras Instalacao Http; do
    name="${suite##*/}"
    export ASILOSOFT_TEST_DB_NOME="asilosoft_test_${run_id}_${name,,}"
    echo "Suíte: $suite — base: $ASILOSOFT_TEST_DB_NOME"
    php "tests/integration/${suite}IntegrationTest.php"
    if [[ "$suite" == database/Database ]]; then
        php tests/integration/BuscaBenchmark.php
    fi
done
echo 'Todas as suítes passaram. Bases retidas seguem a política de tests/README.md.'

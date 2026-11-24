#!/usr/bin/env bash
# Evidências com horário, código de saída e identificação de cada suíte.
# Guia: docs/testes/README.md; opção --coverage: docs/testes/PHPDBG.md
set -euo pipefail
cd "$(dirname "$0")/.."
out="docs/testes/evidencias/$(date +%Y%m%dT%H%M%S)_$$"
mkdir -p "$out"
exec > >(tee "$out/execucao.log") 2>&1
trap 'code=$?; echo "Fim: $(date -Iseconds); código: $code"; exit "$code"' EXIT
echo "Evidências: $out"
echo "Início: $(date -Iseconds)"
php -r 'echo "Ambiente: PHP ".PHP_VERSION." / ".PHP_OS_FAMILY."\n";'
if [[ "${1:-}" != --integration-only ]]; then
    if [[ "${1:-}" == --coverage ]]; then
        ASILOSOFT_TEST_REPORT_DIR="$out/cobertura" bash tests/coverage.sh
        cp "$out/cobertura/cobertura.json" "$out/cobertura/cobertura.html" "$out/cobertura/unitarios.json" "$out/"
    else
        ASILOSOFT_TEST_REPORT_DIR="$out" php tests/unit/run.php
    fi
    ASILOSOFT_TEST_REPORT_DIR="$out" php tests/catalogo.php
    find src tests scripts -name '*.php' -print0 | xargs -0 -n1 php -l > "$out/sintaxe.log"
    php tests/estoque_compras_rules.php
fi
for key in HOST PORTA USUARIO SENHA; do
    variable="ASILOSOFT_TEST_DB_$key"
    if [[ ! -v "$variable" ]]; then
        echo "INTEGRAÇÃO BLOQUEADA: defina $variable para MySQL exclusivo de testes."
        exit 2
    fi
done
run_id="$(date +%Y%m%d%H%M%S)_$$"
number=0
for suite in database/Database Familiares Clinica EstoqueCompras Instalacao Http; do
    number=$((number+1))
    name="${suite##*/}"
    export ASILOSOFT_TEST_DB_NOME="asilosoft_test_${run_id}_${name,,}"
    id="CT-I0$number"
    echo "$id: $suite; início $(date -Iseconds); banco $ASILOSOFT_TEST_DB_NOME"
    echo "Executando: tests/integration/${suite}IntegrationTest.php"
    echo "Esperado: todas as verificações da suíte concluídas sem erro."
    if timeout --signal=TERM --kill-after=10s 600s php "tests/integration/${suite}IntegrationTest.php" 2>&1 | tee "$out/$id.log"; then
        echo "Obtido: suíte concluída com código 0; verificações detalhadas acima."
        echo "$id Passou" | tee -a "$out/resumo.txt"
    else
        codes=("${PIPESTATUS[@]}")
        if [[ "${codes[0]}" == 124 || "${codes[0]}" == 137 ]]; then
            echo "$id: limite de 10 minutos excedido; suíte interrompida." | tee -a "$out/$id.log"
        fi
        echo "$id Falhou" | tee -a "$out/resumo.txt"
        exit 1
    fi
done

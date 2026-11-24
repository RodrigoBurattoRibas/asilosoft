#!/usr/bin/env bash
# O phpdbg é ferramenta de desenvolvimento; nenhuma dependência de produção.
# Instalação, uso, relatórios e limitações: docs/testes/PHPDBG.md
set -euo pipefail
cd "$(dirname "$0")/.."
phpdbg_bin="${PHPDBG_BIN:-phpdbg}"
if ! command -v "$phpdbg_bin" >/dev/null 2>&1; then
    echo 'phpdbg não encontrado. Instale a versão correspondente ao seu PHP ou defina PHPDBG_BIN com o caminho do executável.' >&2
    exit 2
fi
out="${ASILOSOFT_TEST_REPORT_DIR:-docs/testes/evidencias/cobertura_$(date +%Y%m%dT%H%M%S)_$$}"
mkdir -p "$out"
# Sem opcache/JIT para manter a análise dos opcodes sem otimizações.
# -q: sem banner; -r: executar; -r: encerrar após executar.
set +e
ASILOSOFT_TEST_REPORT_DIR="$out" "$phpdbg_bin" -qrr -d opcache.enable_cli=0 -d opcache.jit=0 tests/unit/run.php --coverage > "$out/execucao.log" 2>&1
status=$?
set -e
cat "$out/execucao.log"
echo "Código de saída: $status; evidências: $out" | tee -a "$out/execucao.log"
exit "$status"

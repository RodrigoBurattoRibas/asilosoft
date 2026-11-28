# Script de carga e estresse

Cópia dos arquivos usados no teste. No repositório eles ficam em:

- `tests/carga/CargaEstresse.php`: script principal (usuários virtuais, etapas, métricas e integridade).
- `tests/support/HttpCliente.php`: cliente HTTP sem a extensão curl.
- `tests/support/AmbienteHttp.php`: cria a base de teste com dados fictícios e sobe o servidor.
- `tests/support/RelatorioCarga.php`: gera o relatório Markdown e o HTML com gráficos.

Para executar, use os caminhos do repositório (o script carrega os outros arquivos a partir de `tests/`):

```bash
export ASILOSOFT_TEST_DB_HOST=127.0.0.1
export ASILOSOFT_TEST_DB_PORTA=3306
export ASILOSOFT_TEST_DB_USUARIO=usuario_de_testes
export ASILOSOFT_TEST_DB_SENHA='senha_de_testes'
php tests/carga/CargaEstresse.php
```

Metodologia, critérios e análise completa: `docs/testes/CARGA_ESTRESSE.md`.

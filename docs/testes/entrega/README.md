# Entrega: carga/estresse e segurança - AsiloSoft

## Carga

| Arquivo | Conteúdo |
| --- | --- |
| [relatorio-carga.html](Carga/relatorio-carga.html) | Relatório gráfico: p95, vazão e taxa de erro de 10 a 300 usuários simultâneos, com 1 processo (Railway) e com 4 workers |
| [graficos-carga.png](Carga/graficos-carga.png) | Imagem do relatório gráfico |
| [relatorio-carga.md](Carga/relatorio-carga.md) | Tabelas por etapa e tempo por ação |
| [script/](Carga/script/) | Script do teste e arquivos de apoio |

Resultado: carga normal (até 50 usuários) aprovada, com p95 de no máximo 154 ms e 0% de erros. Com 1 processo o desempenho degrada a partir de 100 usuários; com 4 workers fica dentro dos critérios até 300.

## Segurança

| Arquivo | Conteúdo |
| --- | --- |
| [correcoes-e-dependencias.md](Seguranca/correcoes-e-dependencias.md) | 8 vulnerabilidades corrigidas, antes/depois e dependências |
| [correcoes.patch](Seguranca/correcoes.patch) | Código das correções |
| [relatorio-zap.html](Seguranca/relatorio-zap.html) | OWASP ZAP depois das correções: 0 alto, 0 médio, 0 baixo |
| [relatorio-zap-antes.html](Seguranca/relatorio-zap-antes.html) | OWASP ZAP antes da correção do `router.php`: 2 alertas baixos |
| [testes-manuais.md](Seguranca/testes-manuais.md) | 10 testes manuais com curl, passos e respostas reais (10 de 10) |
| [testes-manuais.log](Seguranca/testes-manuais.log) e [testes-manuais.sh](Seguranca/testes-manuais.sh) | Saída bruta e comandos dos testes manuais |
| [plano-zap.yaml](Seguranca/plano-zap.yaml) | Configuração usada no ZAP |

Documentação completa: `docs/testes/CARGA_ESTRESSE.md` e `docs/testes/SEGURANCA.md`.

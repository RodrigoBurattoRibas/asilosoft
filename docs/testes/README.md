# Entrega M3 - testes do AsiloSoft

Atualizado em 23/11/2026. Esta entrega implementa 71 casos unitários em PHP puro, reaproveita e valida seis suítes reais de integração e organiza evidências por ID. Não foram adicionados frameworks, pacotes Composer, containers ou dependências de execução ao projeto. A aplicação não foi alterada.

## Documentação em Markdown no repositório

A documentação técnica desta etapa fica nos arquivos `.md` abaixo e acompanha o código. Documentos externos e formatos de entrega acadêmica ficam para uma etapa posterior; os arquivos Markdown são a referência para manutenção e execução.

- [phpdbg: instalação, uso, implementação, resultados e limitações](PHPDBG.md).
- [Validação da versão preparada para commit](VALIDACAO_ANTES_COMMIT.md).
- [Plano de testes e escopo](PLANO_DE_TESTES.md).
- [71 casos unitários com os campos solicitados](CASOS_UNITARIOS.md).
- [Seis cenários compostos de integração](CASOS_INTEGRACAO.md).
- [Tabelas individuais da execução inicial](CASOS_POR_TESTE.md), com os 50 unitários originais e seis integrações; os 21 casos posteriores constam no catálogo atualizado.
- [Relatório final desta etapa](RELATORIO_FINAL.md).
- [Defeitos, limitações e pendências](DEFEITOS_E_PENDENCIAS.md).
- [Log da revalidação antes dos commits](evidencias/20261123T213334_34296/execucao.log), [resumo das integrações](evidencias/20261123T213334_34296/resumo.txt) e [relatório HTML de cobertura](evidencias/20261123T213334_34296/cobertura.html).

**A entrega acadêmica completa ainda não está concluída:** E2E no navegador, performance/carga com gráficos, segurança completa, avaliação com três usuários e vídeo permanecem pendentes. A cobertura de 68,09% refere-se à camada de serviços, não à aplicação inteira.

## Executar unitários (sem banco)

Na raiz do projeto:

```bash
php tests/unit/run.php
php tests/estoque_compras_rules.php
php tests/catalogo.php
```

O primeiro comando executa CT-U01 a CT-U71 e salva `evidencias/unitarios.json`. O segundo preserva as 40 verificações anteriores de estoque/compras; elas são complementares, não são contadas como 40 novos casos na tabela. O terceiro atualiza o catálogo dos 71 casos a partir dos resultados reais. Código de saída zero significa aprovação; um significa falha. PHP CLI com mbstring é necessário.

## Executar unitários e integrações com evidências

Use **um servidor MySQL exclusivo de testes**. Os scripts criam e alguns recriam/removem bases. Não utilize credenciais ou servidor de produção. O prefixo `asilosoft_test_` protege o nome, mas não substitui separar o servidor. A conta de teste precisa de permissão para criar/remover bases, tabelas, triggers e executar migrations.

```bash
export ASILOSOFT_TEST_DB_HOST=127.0.0.1
export ASILOSOFT_TEST_DB_PORTA=3306
export ASILOSOFT_TEST_DB_USUARIO=usuario_de_testes
export ASILOSOFT_TEST_DB_SENHA='senha_do_servidor_de_testes'
bash tests/m3.sh
```

Substitua as quatro variáveis pelos dados do seu MySQL de testes. A senha precisa ser explicitamente definida, mesmo quando vazia. Nenhum runner usa o `.env` para escolher o banco de teste. O comando gera nomes de base únicos, executa as seis suítes e salva logs datados em `docs/testes/evidencias/<data>_<pid>/`. Interrompe na primeira falha; suítes posteriores não devem ser consideradas executadas. `resumo.txt` lista somente as tentativas realizadas. A porta HTTP padrão é 18087; se ocupada, defina `export ASILOSOFT_TEST_HTTP_PORT=18088`.

Requisitos já utilizados pelos testes do projeto: PHP CLI, pdo_mysql, mbstring, curl, pcntl, proc_open habilitado, Bash e MySQL 8.0.16+. Se houver erro de conexão, confira servidor/porta/credenciais; se pcntl estiver ausente, os cenários concorrentes ficarão bloqueados. Não substitua MySQL por SQLite: triggers, migrations e travas precisam do banco real.

O runner original `bash tests/run.sh` continua disponível, agora também incluindo os novos unitários; ele executa ainda o benchmark preexistente. Para executar uma integração isoladamente:

```bash
export ASILOSOFT_TEST_DB_NOME=asilosoft_test_demo_clinica
php tests/integration/ClinicaIntegrationTest.php
```

Nomes dos demais scripts e suas políticas estão em [tests/README.md](../../tests/README.md). Database recria a base; familiares, instalação e HTTP removem as próprias bases; clínica reverte fixtures, mantendo schema; estoque/compras mantém fixtures fictícias para inspeção. Remova apenas as bases de teste retidas quando não precisar delas.

## Cobertura via phpdbg

A suíte atual possui **71 casos unitários aprovados** e atingiu **68,09% de cobertura de linhas nos dez serviços** (224/329). As seis integrações também passaram. Consulte [a documentação completa do phpdbg](PHPDBG.md) para instalação, escopo, resultados por serviço, limitações e diagnóstico.

```bash
bash tests/coverage.sh
# Para incluir cobertura antes das integrações, com as variáveis de banco definidas:
bash tests/m3.sh --coverage
```

Se o executável for phpdbg8.4, defina `export PHPDBG_BIN=phpdbg8.4`. A execução final está em [evidencias/20261123T205106_29124](evidencias/20261123T205106_29124/execucao.log). Abra [o relatório HTML](evidencias/20261123T205106_29124/cobertura.html) para consultar as linhas. Código zero exige testes aprovados e meta global de 60% atingida.

## Apresentação ao vivo (15 a 20 minutos)

1. Contexto e escopo (2 min): explicar gestão de idosos, familiares, prontuário, quartos e estoque.
2. Fluxos na aplicação com dados fictícios (5 min): familiar e vínculo; anotação de saúde e consulta autorizada; entrada, consumo e compra.
3. Testes ao vivo (3 min): `php tests/unit/run.php` e uma integração clínica ou `bash tests/m3.sh`, com MySQL de testes já disponível.
4. Evidências e limites (3 min): abrir tabela, JSON e logs; explicar dublê unitário versus repositório/banco reais e a pendência da cobertura.
5. Defeitos, melhorias e perguntas (2 a 5 min): mostrar um defeito real se encontrado e corrigido posteriormente; não inventar um bug para cumprir a apresentação.

Gravar vídeo de contingência e obter prints com data/hora visíveis antes da entrega. Cada integrante deve saber explicar fixtures, exceções, idempotência, rollback e permissões.

## Organização para envio

No Drive indicado pela professora, criar `AsiloSoft/Unitarios`, `Integracao`, `E2E`, `Performance`, `Carga`, `Seguranca`, `Usabilidade`, `Regressao` e `Defeitos`. Copiar os logs de CT-U e CT-I para a categoria correspondente e preservar os IDs nos nomes. Os logs HTTP também servem como evidência parcial de segurança e regressão, sem equivaler às categorias completas. Enviar plano PDF, tabelas de casos e relatório final pelo AVA, após revisar as pendências. Nenhum envio ao AVA/Drive foi realizado por esta tarefa.

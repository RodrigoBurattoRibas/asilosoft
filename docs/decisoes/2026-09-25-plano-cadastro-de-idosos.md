# Plano de implementação - Cadastro de idosos

> Para execução: implementar em tarefas pequenas, com testes antes do código e commits em português.

**Objetivo:** disponibilizar cadastro e listagem de idosos associados a quartos com vagas na Sprint 2.

**Arquitetura:** o módulo seguirá as camadas atuais: Controller recebe as rotas, Service aplica validação e ocupação, Repository persiste dados em MySQL ou sessão de demonstração e Views exibem listagem e formulário. Quartos não terão interface própria nesta entrega; serão criados como dados iniciais para permitir seleção.

**Tecnologias:** PHP 8, MySQL/MariaDB, PDO, JavaScript puro e testes PHP CLI.

**Especificação:** `docs/decisoes/2026-09-25-cadastro-de-idosos.md`

## Restrições globais

- Usar somente PHP, JavaScript puro e MySQL, sem frameworks.
- Manter mensagens, rotas e commits em português do Brasil.
- Preservar o modo demonstração quando o MySQL estiver indisponível.
- Não incluir familiares, prontuário, edição de idosos ou interface de quartos.
- Aplicar as regras RNO1 e RNO2: um quarto ativo por idoso e nenhuma alocação acima da capacidade.

## Pontos de revisão

- CPF com pontuação deve ser normalizado e armazenado somente com dígitos.
- CPF ou identificador já utilizado deve impedir o cadastro.
- Quarto inativo não pode ser selecionado.
- Quarto sem vaga não pode receber novo idoso.
- A listagem deve exibir o código do quarto associado.

### Tarefa 1: Modelo, repositórios e validação do cadastro

**Arquivos:**

- Criar: `app/Repositories/QuartoRepository.php`
- Criar: `app/Repositories/IdosoRepository.php`
- Criar: `app/Repositories/RepositorioIdososDeSessao.php`
- Criar: `app/Services/IdosoService.php`
- Criar: `tests/IdosoServiceTest.php`
- Modificar: `database/schema.sql`
- Modificar: `app/Repositories/RepositorioDeSessao.php`

**Interfaces:**

- Produz `IdosoService::criar(array $dados): array`.
- Repositórios expõem busca por CPF, identificador, listagem de quartos disponíveis e criação de idoso.

- [ ] Escrever testes de cadastro válido, CPF duplicado, identificador duplicado e quarto lotado.
- [ ] Executar `php tests/run.php` e confirmar falha pela ausência do serviço.
- [ ] Criar as tabelas `quartos` e `idosos`, com chaves únicas para CPF, identificador e código do quarto.
- [ ] Implementar repositórios PDO e de sessão, com quartos iniciais no modo demonstração.
- [ ] Implementar `IdosoService::criar(array $dados): array`, normalizando CPF, validando campos e conferindo vaga antes de persistir.
- [ ] Executar `php tests/run.php` e confirmar todos os testes verdes.
- [ ] Registrar commit: `feat: estruturar cadastro de idosos`.

### Tarefa 2: Rotas, telas e fluxo administrativo

**Arquivos:**

- Criar: `app/Controllers/ControladorIdosos.php`
- Criar: `app/Views/idosos/lista.php`
- Criar: `app/Views/idosos/formulario.php`
- Modificar: `public/index.php`
- Modificar: `app/Views/layout.php`
- Modificar: `public/css/estilo.css`
- Criar: `tests/ControladorIdososTest.php`

**Interfaces:**

- Consome `IdosoService::criar(array $dados): array` e os métodos de listagem dos repositórios.
- Produz as rotas `GET /idosos`, `GET /idosos/novo` e `POST /idosos`.

- [ ] Escrever teste que cobre acesso administrativo e a mensagem de retorno após cadastro.
- [ ] Executar `php tests/run.php` e confirmar falha pela ausência do controlador.
- [ ] Implementar controlador com autenticação administrativa e proteção CSRF já existente.
- [ ] Adicionar rotas e link de navegação para a área de idosos.
- [ ] Criar listagem com nome, CPF mascarado, identificador e quarto; criar formulário com quartos disponíveis.
- [ ] Executar `php tests/run.php`, `find app public tests -type f -name '*.php' -print0 | xargs -0 -n1 php -l` e `git diff --check`.
- [ ] Registrar commit: `feat: cadastrar e listar idosos`.

### Tarefa 3: Verificação do modo demonstração e documentação

**Arquivos:**

- Modificar: `README.md`
- Modificar: `tests/RepositorioDeSessaoTest.php`

**Interfaces:**

- Consome o repositório de sessão criado na Tarefa 1.
- Produz instruções de demonstração atualizadas para a área de idosos.

- [ ] Escrever teste que cria e lista idoso usando apenas armazenamento de sessão.
- [ ] Executar `php tests/run.php` e confirmar a falha esperada antes da integração.
- [ ] Documentar que o cadastro de idosos está disponível no modo demonstração.
- [ ] Executar a suíte completa e revisar o fluxo manual de login, listagem e cadastro.
- [ ] Registrar commit: `docs: orientar demonstração do cadastro de idosos`.

## Cobertura da especificação

As tabelas, campos e quarto associado são atendidos pela Tarefa 1. A listagem e o formulário são atendidos pela Tarefa 2. A compatibilidade com o modo demonstração e o critério de pronto são atendidos pela Tarefa 3. Familiares, edição, prontuário e gestão visual de quartos permanecem explicitamente fora deste plano.

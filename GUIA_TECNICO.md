# Guia técnico do AsiloSoft

> Referência do estado atual do repositório. Atualizado em 01/10/2026.

## 1. Visão geral

O **AsiloSoft** é um sistema web para apoio à gestão de instituições de longa permanência para idosos. Trata-se de um monólito PHP sem framework, com HTML renderizado no servidor, JavaScript puro e MySQL via PDO. O banco de dados é obrigatório para leitura e gravação das informações do sistema.

### Funcionalidades implementadas

- Autenticação por e-mail e senha, encerramento de sessão e proteção CSRF.
- Administração de usuários: listagem por situação, criação, edição, ativação e desativação.
- Administração de idosos: listagem por situação, criação, edição, alocação e retirada de quarto, ativação e desativação.
- Validação de capacidade dos quartos e unicidade de CPF/identificador.
- Persistência de usuários, quartos, idosos e estoque em MySQL.

### Funcionalidades previstas, mas sem módulo web implementado

O schema ainda prevê familiares, registros de saúde, uso de materiais, solicitações de compra e auditoria. Esses recursos ainda não têm rotas, controllers, services, repositories e views no diretório `src/modules`.

## 2. Arquitetura

O projeto usa um MVC customizado com camadas adicionais de serviço e repositório:

```text
Navegador
  -> index.php (front controller)
  -> RouteSanitizer + Router
  -> middlewares de autenticação, método HTTP e CSRF
  -> controller do módulo
  -> service (regras de negócio e validação)
  -> repository (PDO/MySQL)
  -> view + layout PHP
  -> HTML, CSS e JavaScript ao navegador
```

### Inicialização e roteamento

1. `index.php` inicia a sessão, configura erros, carrega infraestrutura e módulos.
2. `App::init()` define o fuso `America/Sao_Paulo` e a localidade `pt_BR.UTF-8`.
3. `RouteLoader` encontra cada `src/modules/*/routes/web.php` e os registra.
4. O parâmetro `?route=` é sanitizado por `RouteSanitizer`.
5. Rotas privadas passam por `AsiloAuthMiddleware` antes do despacho.
6. `Router` executa os middlewares específicos e instancia o controller.

Rotas desconhecidas são privadas por padrão: sem sessão, redirecionam ao login; com sessão, retornam a página 404.

### Composição em tempo de execução

Cada controller cria seus services com repositories PDO, usando a conexão centralizada em `PDOMysqli::connection()`. Falhas de conexão são propagadas como erro de infraestrutura; não há repositório alternativo em sessão.

## 3. Estrutura de diretórios e arquivos

```text
.
├── index.php                         # Entrada única da aplicação
├── README.md                         # Instruções resumidas de uso
├── .env.example                      # Variáveis de conexão MySQL
├── src/
│   ├── assets/
│   │   ├── css/asilosoft.css          # Estilos globais e responsividade
│   │   └── js/asilosoft.js            # Confirmação e fechamento de avisos
│   ├── core/
│   │   ├── Config/                    # Configuração da aplicação
│   │   ├── Helpers/RouteSanitizer.php # Sanitização de rotas
│   │   ├── Security/CsrfToken.php     # Tokens CSRF por escopo
│   │   └── Session/AsiloSession.php   # Sessão e mensagens flash
│   ├── infra/
│   │   ├── Database/                  # PDO, schema e migrations
│   │   ├── Middleware/                # Autenticação, método e CSRF
│   │   └── Routers/                   # Registro e despacho de rotas
│   ├── modules/
│   │   ├── almoxarifado/              # Materiais, estoque e movimentações
│   │   ├── autenticacao/              # Login e logout
│   │   ├── usuarios/                  # Administração de usuários
│   │   └── idosos/                    # Administração de residentes/quartos
│   └── presentation/
│       ├── components/                # Páginas 403 e 404
│       └── layout/                    # Layout base e renderizador
└── GUIA_TECNICO.md                    # Este documento
```

Cada módulo segue a convenção `controller/`, `services/`, `repository/`, `routes/` e `view/`. O módulo de autenticação utiliza o `AsiloUsuarioRepository` PDO para consultar as contas no banco.

## 4. Endpoints web

As rotas são endpoints de páginas/formulários, não uma API JSON pública. A URL base local é `http://localhost:8000/?route=<rota>`.

| Rota | Método | Acesso | CSRF | Ação | Parâmetros |
| --- | --- | --- | --- | --- | --- |
| `login` | GET | Público | Não | Exibe o formulário de login | — |
| `entrar` | POST | Público | `asilosoft_login` | Autentica e redireciona para usuários | `email`, `senha`, `csrf_token` |
| `sair` | POST | Autenticado | `asilosoft_sair` | Encerra a sessão | `csrf_token` |
| `usuarios` | GET | Administrador | Não | Lista usuários ativos ou desativados | `situacao=ativos|desativados` |
| `usuarios-novo` | GET | Administrador | Não | Exibe cadastro de usuário | — |
| `usuarios-criar` | POST | Administrador | `asilosoft_usuarios` | Cria usuário | `nome`, `email`, `perfil`, `senha`, `csrf_token` |
| `usuarios-editar` | GET | Administrador | Não | Exibe edição de usuário | `id` inteiro positivo |
| `usuarios-atualizar` | POST | Administrador | `asilosoft_usuarios` | Atualiza usuário | `id`, campos de usuário, `csrf_token` |
| `usuarios-status` | POST | Administrador | `asilosoft_usuarios` | Ativa ou desativa usuário | `id`, `ativo=0|1`, `csrf_token` |
| `idosos` | GET | Administrador | Não | Lista idosos ativos ou desativados | `situacao=ativos|desativados` |
| `idosos-novo` | GET | Administrador | Não | Exibe cadastro de idoso | — |
| `idosos-criar` | POST | Administrador | `asilosoft_idosos` | Cria idoso e o aloca em quarto | `nome`, `cpf`, `identificador`, `quarto_id`, `csrf_token` |
| `idosos-editar` | GET | Administrador | Não | Exibe edição de idoso | `id` inteiro positivo |
| `idosos-atualizar` | POST | Administrador | `asilosoft_idosos` | Atualiza idoso e quarto opcional | `id`, campos de idoso, `csrf_token` |
| `idosos-retirar-quarto` | POST | Administrador | `asilosoft_idosos` | Remove a alocação em quarto | `id`, `csrf_token` |
| `idosos-status` | POST | Administrador | `asilosoft_idosos` | Ativa/desativa; ao desativar remove quarto | `id`, `ativo=0|1`, `csrf_token` |

**Nota de autorização:** as rotas de usuários e idosos são privadas no roteador, mas os respectivos controllers chamam `exigirAdministrador()`. Assim, um usuário autenticado sem perfil `Administrador` recebe HTTP 403.

## 5. Módulos e regras atuais

### Autenticação

- A sessão guarda o identificador em `asilosoft_usuario_id`.
- O e-mail é normalizado com `trim` e minúsculas antes da busca.
- A senha é verificada por `password_verify`; o ID da sessão é regenerado após login e logout.
- Usuários inexistentes ou inativos não conseguem entrar; uma sessão que passe a apontar para usuário inativo é encerrada.

### Usuários

- Perfis aceitos: `Administrador`, `Profissional de Saúde`, `Almoxarife` e `Familiar`.
- Nome e e-mail válidos são obrigatórios; senha possui mínimo de oito caracteres na criação e é opcional na edição.
- O e-mail é único e senhas são armazenadas com `password_hash`.
- Não é permitido desativar o último administrador ativo.
- O filtro de listagem aceita somente `ativos` e `desativados`; qualquer outro valor resulta em `ativos`.

### Idosos e quartos

- Nome, CPF de 11 dígitos, identificador e quarto são obrigatórios na criação.
- CPF e identificador precisam ser únicos.
- Somente quartos ativos, existentes e com ocupação abaixo da capacidade podem receber nova alocação.
- Na edição, é permitido deixar `quarto_id` vazio, removendo a alocação. O quarto atual inativo continua selecionável apenas para preservar a edição do registro.
- Desativar o idoso remove automaticamente o quarto. A capacidade considera apenas idosos ativos.

## 6. Persistência

### Configuração MySQL

Crie `.env` a partir de `.env.example` e habilite a extensão PHP `pdo_mysql`:

```dotenv
ASILOSOFT_DB_HOST=127.0.0.1
ASILOSOFT_DB_PORTA=3306
ASILOSOFT_DB_NOME=asilosoft
ASILOSOFT_DB_USUARIO=asilosoft_app
ASILOSOFT_DB_SENHA=SUA_SENHA_LOCAL_DO_BANCO
```

`PDOMysqli` prioriza variáveis do processo e depois lê `.env` na raiz do projeto. Ela configura UTF-8 (`utf8mb4`), exceções PDO, resultados associativos e prepared statements nativos.

### Schema

As migrações versionadas em `src/infra/Database/migrations/` criam estas entidades; `schema.sql` é somente um adaptador de compatibilidade que aponta para `bootstrap.php`.

| Área | Tabelas | Situação na aplicação |
| --- | --- | --- |
| Pessoas e ocupação | `usuarios`, `idosos`, `quartos` | Implementada no módulo web |
| Familiares | `familiares`, `idosos_familiares` | Disponível no banco, sem módulo web |
| Saúde | `registros_saude` | Disponível no banco, sem módulo web |
| Materiais e estoque | `materiais`, `estoques`, `movimentacoes_estoque` | Implementada no módulo web |
| Uso e compras | `usos_materiais`, `solicitacoes_compra` | Disponível no banco, sem módulo web |
| Auditoria | `logs_auditoria` | Disponível no banco, sem módulo web |

O schema também impõe capacidade de quarto, cria o saldo de estoque ao cadastrar material, impede saída acima do saldo, projeta movimentações no saldo e registra auditoria para materiais e registros de saúde. As regras de quarto são repetidas na camada PHP e em triggers SQL, o que protege tanto o fluxo web quanto alterações diretas no banco.

### Migrações

Depois de configurar o `.env`, aplique as migrations versionadas com `php src/infra/Database/bootstrap.php`. O comando é idempotente e registra as versões aplicadas em `schema_migrations`.

## 7. Segurança e tratamento de erros

- **CSRF:** token aleatório de 32 bytes em hexadecimal, isolado por escopo e validado com `hash_equals` quando disponível.
- **Método HTTP:** rotas de leitura usam `RequestMethodMiddleware('GET')`; o middleware CSRF aceita somente POST e responde HTTP 419 em token inválido.
- **Autorização:** middleware de sessão para rotas privadas e verificação de administrador no controller.
- **Saída HTML:** views usam `AsiloView::escape()` com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- **Erros de domínio:** services lançam `DomainException`; controllers exibem a mensagem via flash e fazem redirect HTTP 303.
- **Erros de rota/registro:** ID inválido ou registro ausente devolve 404; perfil sem permissão devolve 403.

## 8. Convenções de escrita observadas

### PHP

- Classes usam `PascalCase` e prefixo `Asilo` quando pertencem ao domínio (`AsiloIdosoService`, `AsiloView`); exceções de infraestrutura mantêm nomes próprios, como `Router` e `CsrfToken`.
- Arquivos de classe têm o mesmo nome da classe. Não há autoload Composer: dependências são carregadas manualmente por `require_once` com `dirname(__DIR__)`.
- Métodos e variáveis usam `camelCase`; nomes de dados de domínio são em português.
- Classes concretas normalmente são `final`; tipos escalares e retornos são declarados nas partes mais recentes do código.
- Services recebem o repositório PDO do módulo. Regras de negócio ficam no service; consultas e persistência ficam no repository; controllers orquestram HTTP, redirect, view e flash.

### Views, CSS e JavaScript

- Views são PHP/HTML sem template engine e recebem dados por `extract(..., EXTR_SKIP)` no renderizador.
- Todo dado dinâmico exibido deve passar por `AsiloView::escape()`.
- O layout único `presentation/layout/asilo.php` inclui CSS e JavaScript globais e exibe alertas flash.
- Classes CSS e atributos de dados usam nomes em português/kebab-case, como `cartao-login`, `texto-suave`, `data-confirmacao` e `data-aviso`.
- JavaScript é pequeno, sem biblioteca: confirma formulários de ações sensíveis e fecha avisos após cinco segundos.

## 9. Como executar

### Com banco MySQL

1. Configure o arquivo `.env` com as credenciais.
2. Execute `php src/infra/Database/bootstrap.php`.
3. Na raiz do projeto, execute:

```bash
php -S localhost:8000
```

4. Abra `http://localhost:8000`.

## 10. Pontos de atenção para evolução

- Não há suíte de testes automatizados ou gerenciador de dependências configurado.
- `index.php` liga `display_errors`; desative-o em produção e direcione erros para logs.
- A autenticação por perfil está implementada para administradores e almoxarifes. Antes de expor novos módulos, defina uma matriz de permissões por perfil.
- Falhas de conexão devem ser monitoradas e corrigidas na configuração do ambiente; a aplicação não usa dados de sessão como fallback.
- Novos módulos devem seguir a mesma estrutura de controller, service, repository PDO, rotas e views.

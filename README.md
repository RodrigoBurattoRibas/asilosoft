# AsiloSoft

Sistema web para apoio à gestão de instituições de longa permanência para idosos.

## Tecnologias

- PHP 8 sem framework
- JavaScript puro
- MySQL

## Preparação local

1. Copie `.env.example` para `.env` e preencha as variáveis do banco.
2. Execute o conteúdo de `database/schema.sql` no MySQL.
3. Crie o administrador inicial sem inserir senha em arquivo:

```bash
ASILOSOFT_ADMIN_EMAIL="seu-email" ASILOSOFT_ADMIN_NOME="Administrador" ASILOSOFT_ADMIN_SENHA="uma-senha-segura" php database/seed_admin.php
```

4. Inicie o servidor local:

```bash
php -S localhost:8000 -t public
```

5. Acesse `http://localhost:8000`.

## Modo demonstração

Se o MySQL não estiver acessível, a aplicação abre em modo demonstração para facilitar a apresentação da Sprint 1. Use `admin@asilosoft.local` e a senha `asilosoft123`. Os usuários criados nesse modo ficam apenas na sessão do navegador e não são permanentes.

## Iniciar para apresentação

Para demonstrar a Sprint 1 sem configurar MySQL, use o servidor local:

```bash
cd "/home/rodrigo/Documentos/ChatGPT/Engenharia de Dados"
php -S localhost:8000 -t public
```

No navegador, acesse `http://localhost:8000` e entre com `admin@asilosoft.local` e `asilosoft123`. O aviso "Modo demonstração" confirma que não há banco de dados configurado; os cadastros criados nessa apresentação somem quando a sessão do navegador é encerrada.

## Testes

```bash
php tests/run.php
```

## Configuração detalhada do banco de dados

### Criar o arquivo de ambiente

Na raiz do projeto, crie a configuração local:

```bash
cp .env.example .env
nano .env
```

Preencha o `.env` com dados locais. Nunca envie esse arquivo pelo grupo nem faça commit dele.

```dotenv
ASILOSOFT_DB_HOST=127.0.0.1
ASILOSOFT_DB_PORTA=3306
ASILOSOFT_DB_NOME=asilosoft
ASILOSOFT_DB_USUARIO=root
ASILOSOFT_DB_SENHA=sua_senha_do_mysql

ASILOSOFT_ADMIN_EMAIL=seu-email@exemplo.com
ASILOSOFT_ADMIN_NOME=Administrador
ASILOSOFT_ADMIN_SENHA=crie_uma_senha_segura
```

### Iniciar e preparar o MySQL

Em instalações Linux que usam MySQL:

```bash
sudo systemctl start mysql
mysql -u root -p < database/schema.sql
```

Se o serviço da sua máquina se chamar `mariadb`, substitua `mysql` por `mariadb` no primeiro comando. O schema cria o banco `asilosoft` e a tabela `usuarios`.

### Criar o administrador inicial

Depois de preencher o `.env` e criar a estrutura do banco:

```bash
php database/seed_admin.php
```

Em seguida, inicie o servidor normalmente:

```bash
php -S localhost:8000 -t public
```

## Roadmap

| Sprint | Objetivo e entregas |
| --- | --- |
| S1 | Login, gerenciamento de usuários e senha com hash seguro. |
| S2 | Cadastro e edição de idosos, vínculo de familiares e permissões por perfil. |
| S3 | Registro de saúde e consulta da ficha do idoso. |
| S4 | Materiais, medicamentos e controle de estoque. |
| S5 | Origem/cobertura de itens e registro de retirada ou uso. |
| S6 | Solicitações de compra institucional. |
| S7 | Consulta de acompanhamento pelo familiar vinculado. |
| S8 | Testes, usabilidade e otimização de consultas. |
| S9 | Responsividade e compatibilidade com Chrome, Edge e Firefox. |
| S10 | Integração dos módulos, testes integrados e correções. |
| S11 | Homologação, documentação e apresentação final. |

## Scrumban e divisão de tarefas

O quadro usa quatro estados:

- **A fazer:** tarefa ainda não assumida.
- **Fazendo:** alguém está implementando.
- **Em teste:** implementação concluída, aguardando validação.
- **Pronto:** código implementado, testado e funcionando conforme requisitos e regras de negócio.

Cada integrante deve puxar uma tarefa pequena, atribuir-se a ela e atualizar o cartão conforme o andamento. Antes de mover uma tarefa para **Pronto**, execute os testes e valide o fluxo correspondente.

## Fluxo de trabalho com Git

Antes de começar uma tarefa:

```bash
git pull origin main
```

Depois de concluir uma alteração:

```bash
git status
git add .
git commit -m "feat: descrever a alteração em português"
git push origin main
```

Para tarefas maiores, use uma branch por funcionalidade e integre após revisão. Nunca faça commit de `.env`, senhas, tokens ou dados reais de idosos, familiares e prontuários.

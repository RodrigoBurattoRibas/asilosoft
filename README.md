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

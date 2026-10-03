# AsiloSoft

Sistema web para apoio à gestão de instituições de longa permanência para idosos.

## Tecnologias

- PHP 8
- JavaScript puro
- MySQL, com modo demonstração em sessão

## Executar

Inicie o servidor na raiz do projeto:

```bash
php -S localhost:8000
```

Depois acesse `http://localhost:8000`. As rotas são carregadas no formato `?route=nome-da-rota`.

## Banco de dados

Copie `.env.example` para `.env`, configure `ASILOSOFT_DB_*` e execute:

```bash
php src/infra/Database/bootstrap.php
php src/infra/Database/seed_admin.php # opcional
php src/infra/Database/seed_dados_ficticios.php # opcional
```

As migrações versionadas são a fonte de verdade. `schema.sql` é somente um adaptador de compatibilidade e não deve ser executado diretamente. A operação, os contratos e o teste de integração estão em `docs/GUIA_BANCO.md`.

Se a conexão não estiver disponível, a aplicação inicia automaticamente no modo demonstração:

- E-mail: `admin@asilosoft.local`
- Senha: `asilosoft123`

Os dados do modo demonstração permanecem somente na sessão do navegador.

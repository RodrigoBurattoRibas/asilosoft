# AsiloSoft

Sistema web para apoio à gestão de instituições de longa permanência para idosos.

## Tecnologias

- PHP 8
- JavaScript puro
- MySQL, com modo demonstração em sessão

## Estrutura

```text
index.php
src/
  assets/
  core/
  infra/
  modules/
    autenticacao/
    usuarios/
    idosos/
  presentation/
```

Cada módulo possui suas próprias rotas, controllers, serviços, repositórios e views.

## Executar

Inicie o servidor na raiz do projeto:

```bash
php -S localhost:8000
```

Depois acesse `http://localhost:8000`.

As rotas são carregadas no formato `?route=nome-da-rota`.

## Banco de dados

Crie um arquivo `.env` a partir de `.env.example` quando houver um banco MySQL disponível:

```dotenv
ASILOSOFT_DB_HOST=127.0.0.1
ASILOSOFT_DB_PORTA=3306
ASILOSOFT_DB_NOME=asilosoft
ASILOSOFT_DB_USUARIO=root
ASILOSOFT_DB_SENHA=sua_senha
```

Se a conexão não estiver disponível, a aplicação inicia automaticamente no modo demonstração:

- E-mail: `admin@asilosoft.local`
- Senha: `asilosoft123`

Os dados do modo demonstração permanecem somente na sessão do navegador.

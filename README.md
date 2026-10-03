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
    almoxarifado/
    autenticacao/
    idosos/
    usuarios/
  presentation/
```

Cada módulo possui suas próprias rotas, controllers, serviços, repositórios e views.

O módulo de almoxarifado permite cadastrar materiais, insumos e medicamentos, registrar entradas com origem, registrar retiradas e consultar o histórico de movimentações. O acesso é permitido aos perfis `Administrador` e `Almoxarife`.

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
MYSQLHOST=127.0.0.1
MYSQLPORT=3306
MYSQLDATABASE=asilosoft
MYSQLUSER=root
MYSQLPASSWORD=sua_senha
```

No Railway, essas mesmas variáveis podem ser preenchidas pelas referências fornecidas pelo serviço. Para executar localmente, use valores concretos no `.env`.

Se a conexão não estiver disponível, a aplicação inicia automaticamente no modo demonstração:

- E-mail: `admin@asilosoft.local`
- Senha: `asilosoft123`

Os dados do modo demonstração permanecem somente na sessão do navegador.

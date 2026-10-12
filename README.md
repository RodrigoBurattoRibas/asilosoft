# AsiloSoft

Sistema web para apoio à gestão de instituições de longa permanência para idosos.

## Tecnologias

- PHP 8
- Extensão PHP `pdo_mysql`
- JavaScript puro
- MySQL via PDO

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

As tabelas e regras do banco são criadas pelas migrations versionadas. Execute uma vez após configurar o `.env`:

```bash
php src/infra/Database/bootstrap.php
```

A aplicação exige uma conexão PDO válida; sem banco configurado, ela interrompe a requisição com erro de configuração. Não existe fallback para dados em sessão. `PDOMysqli` configura UTF-8 (`utf8mb4`), exceções PDO, resultados associativos e prepared statements nativos.

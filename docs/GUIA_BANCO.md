# Guia de banco de dados do AsiloSoft

Este guia serve para Windows, macOS e Linux. O colega não precisa usar terminal Linux; pode usar uma ferramenta gráfica como **DBeaver Community** ou **MySQL Workbench**.

> Ter somente o arquivo `database/schema.sql` não basta: é necessário instalar um servidor MySQL ou MariaDB no computador. Para executar a aplicação, também é necessário ter o projeto e o PHP instalados.

## O que cada colega precisa ter

- Uma cópia do projeto AsiloSoft ou, no mínimo, o arquivo `database/schema.sql`.
- MySQL 8 ou MariaDB instalado e em execução.
- DBeaver Community ou MySQL Workbench instalado.

## 1. Criar uma conexão com o MySQL

Abra o DBeaver ou MySQL Workbench e crie uma nova conexão MySQL usando os dados da instalação local:

| Campo | Valor comum |
| --- | --- |
| Host | `localhost` |
| Porta | `3306` |
| Usuário | `root` ou outro usuário administrador local |
| Senha | A senha criada durante a instalação do MySQL |

Use o botão de teste da ferramenta. Se a conexão falhar, confirme se o serviço MySQL/MariaDB está iniciado.

## 2. Criar o banco e as tabelas

1. Na ferramenta, abra um editor SQL novo.
2. Abra o arquivo `database/schema.sql` do projeto.
3. Execute todo o conteúdo do arquivo.
4. Atualize a lista de bancos e confirme que existe o banco `asilosoft`.
5. Dentro dele, confirme as tabelas `usuarios`, `quartos` e `idosos`.

O arquivo já contém os comandos `CREATE DATABASE` e `CREATE TABLE`, portanto não é necessário criar as tabelas manualmente.

## 3. Criar o usuário da aplicação

No editor SQL, conectado como administrador, execute o comando abaixo. Troque o texto entre aspas simples por uma senha local nova e forte.

```sql
CREATE USER 'asilosoft_app'@'localhost' IDENTIFIED BY 'TROQUE_POR_UMA_SENHA_LOCAL';
GRANT ALL PRIVILEGES ON asilosoft.* TO 'asilosoft_app'@'localhost';
FLUSH PRIVILEGES;
```

Se a ferramenta informar que o usuário já existe, use este bloco para recriá-lo:

```sql
DROP USER IF EXISTS 'asilosoft_app'@'localhost';
CREATE USER 'asilosoft_app'@'localhost' IDENTIFIED BY 'TROQUE_POR_UMA_SENHA_LOCAL';
GRANT ALL PRIVILEGES ON asilosoft.* TO 'asilosoft_app'@'localhost';
FLUSH PRIVILEGES;
```

## 4. Inserir quartos para teste

O cadastro de idosos precisa de quartos cadastrados. Para testes locais, execute:

```sql
USE asilosoft;

INSERT INTO quartos (codigo, capacidade, ativo) VALUES
('A-101', 2, 1),
('A-102', 1, 1);
```

## 5. Configurar o arquivo `.env`

Na pasta principal do projeto, copie `.env.example` e renomeie a cópia para `.env`. No Windows, ative a exibição de extensões de arquivo para não criar acidentalmente `.env.txt`.

Preencha o `.env` com a mesma senha escolhida no passo 3:

```dotenv
ASILOSOFT_DB_HOST=localhost
ASILOSOFT_DB_PORTA=3306
ASILOSOFT_DB_NOME=asilosoft
ASILOSOFT_DB_USUARIO=asilosoft_app
ASILOSOFT_DB_SENHA=TROQUE_POR_A_SENHA_LOCAL

ASILOSOFT_ADMIN_EMAIL=admin@asilosoft.local
ASILOSOFT_ADMIN_NOME=Administrador
ASILOSOFT_ADMIN_SENHA=CRIE_OUTRA_SENHA_SEGURA
```

O e-mail deve ser texto simples, por exemplo `admin@asilosoft.local`; não use link, colchetes ou `mailto:`.

## 6. Validar a instalação

No DBeaver ou MySQL Workbench, execute:

```sql
USE asilosoft;
SHOW TABLES;
SELECT * FROM quartos;
```

Se os comandos exibirem as tabelas e os quartos inseridos, o banco está pronto.

## Segurança

- Nunca envie o arquivo `.env` por WhatsApp, e-mail ou GitHub.
- Nunca faça commit de `.env`, senhas ou tokens.
- Cada colega deve criar a própria senha local e o próprio `.env`.

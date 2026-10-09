# Guia rápido: colocar o AsiloSoft no Railway

Este passo a passo cria o site e o banco sem executar cada tabela, trigger ou view manualmente.

## 1. Enviar o projeto

1. Crie um repositório no GitHub e envie os arquivos do projeto.
2. No Railway, crie um projeto e escolha **Deploy from GitHub Repo**.
3. Selecione esse repositório e crie o serviço web.
4. No mesmo projeto, clique em **New** e adicione **MySQL**.

## 2. Ligar o site ao MySQL

Abra o serviço do site, entre em **Variables** e clique em **Add a Variable Reference**.
Selecione o serviço MySQL e adicione estas referências:

| Variável no site | Variável referenciada do MySQL |
| --- | --- |
| `MYSQLHOST` | `MYSQLHOST` |
| `MYSQLPORT` | `MYSQLPORT` |
| `MYSQLDATABASE` | `MYSQLDATABASE` |
| `MYSQLUSER` | `MYSQLUSER` |
| `MYSQLPASSWORD` | `MYSQLPASSWORD` |

Não é preciso preencher `MYSQL_URL`. Não cole senha no GitHub, no guia ou no código: a referência compartilha a variável com segurança dentro do Railway.

## 3. Configurar o início do site

No serviço do site, abra **Settings > Deploy**. Em **Custom Start Command**, informe:

```bash
php -S 0.0.0.0:$PORT -t .
```

O arquivo `railway.toml` do projeto manda o Railway executar, antes de cada deploy:

```bash
php src/infra/Database/bootstrap.php
```

Esse comando cria `schema_migrations`, todas as tabelas, triggers e views. Depois disso, ele só executa migrations novas.

## 4. Publicar e conferir

1. Clique em **Deploy** ou faça um `git push` para a branch conectada.
2. Abra os logs do deploy. Procure por `Migrations aplicadas:` na primeira publicação ou `Banco de dados já está atualizado.` nas próximas.
3. Abra **Settings > Networking > Generate Domain** e acesse o endereço criado.

Se o deploy parar no passo de migration, confira se as cinco referências de variável foram criadas no **serviço web** (não apenas no MySQL).

## Arquivo .env

O `.env` serve apenas para desenvolvimento local e é ignorado pelo Git. O host `mysql.railway.internal` funciona somente entre serviços dentro do Railway; para conectar pelo computador, use a conexão TCP pública fornecida pelo painel do MySQL.

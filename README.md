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
    quartos/
    usuarios/
    familiares/
    zacompanhar/
    zcompras/
  presentation/
```

Cada módulo possui suas próprias rotas, controllers, serviços, repositórios e views.

Após o login, todos os perfis são enviados para a home (`?route=home`). A home mostra somente as áreas permitidas para o perfil atual. Tentativas de acesso a áreas sem permissão retornam para a home com uma mensagem informativa.

O módulo de quartos permite cadastrar a capacidade dos quartos, acompanhar ocupação e ativar/desativar espaços. Ao cadastrar um idoso sem vaga disponível, o sistema sugere criar um quarto. O acesso é permitido ao perfil `Administrador`.

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

## Fluxos integrados

- Administrador gerencia familiares e associa contas ativas de perfil Familiar. Na ficha do idoso, vincula contatos, parentesco e responsável principal.
- Administrador e Profissional de Saúde buscam idosos por nome, CPF ou identificador. Somente o profissional registra saúde; correções geram nova anotação, preservando o histórico.
- Administrador, Almoxarife e Profissional de Saúde registram consumo individual. A trigger realiza uma única baixa; reenvios usam chave persistente única.
- Administrador/Almoxarife solicitam compras. Administrador aprova ou recusa pendentes e marca aprovadas como atendidas. Compras não movimentam estoque.
- Familiar consulta apenas acompanhamento publicado de idosos ativos vinculados à sua conta. Remoção do vínculo e desativação revogam acesso na próxima requisição.

Aplique também as migrations `007_convergir_tipo_material` e `008_idempotencia_uso` pelo bootstrap antes de usar o consumo. As migrations anteriores foram preservadas. Erros PHP ficam ocultos por padrão; `ASILOSOFT_DEBUG=1` habilita exibição somente para desenvolvimento.

## Validação

Os testes usam PHP puro e MySQL isolado, sem novas dependências. Veja [instruções dos testes](tests/README.md) e [status das sprints](STATUS_CONCLUSAO_SPRINTS.md), incluindo limites da validação e ajustes recomendados para M2.

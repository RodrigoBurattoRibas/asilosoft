# Cadastro e proteção do administrador

A migration estrutural `009_preservar_administrador.sql` impede excluir, desativar ou mudar o perfil do último administrador ativo, inclusive por SQL direto e em transações concorrentes. A alteração de nome, e-mail e senha continua permitida. Havendo outro administrador ativo, é possível remover ou rebaixar uma conta administrativa.

Aplique as migrations com a aplicação em manutenção, sem gravações concorrentes, usando o bootstrap habitual ou o comando de cadastro abaixo. A instalação de triggers é DDL e não é transacional no MySQL. O contador em `controle_administradores` usa uma única linha InnoDB para serializar as alterações; seu conteúdo é mantido pelas triggers e não deve ser editado manualmente. `TRUNCATE`, remoção de triggers ou alterações estruturais feitas por uma conta com privilégios administrativos ficam fora desta proteção.

## Cadastrar novamente o administrador excluído

A conta padrão é `admin@asilosoft.local`. O cadastro gera um novo ID e recebe uma senha de 12 a 72 bytes. No Bash, na raiz do projeto:

```bash
read -r -s -p 'Nova senha do administrador: ' ASILOSOFT_ADMIN_SENHA
printf '\n'
export ASILOSOFT_ADMIN_SENHA
php scripts/registrar-administrador.php
unset ASILOSOFT_ADMIN_SENHA
```

O comando utiliza a conexão da aplicação, aplica as migrations estruturais e executa a migration opcional `src/infra/Database/seeds/001_registrar_administrador.sql` em uma transação. Cria uma nova conta com novo ID. Se o e-mail já existir, o cadastro é recusado sem alterar nome, senha, perfil ou status da conta existente. Não restaura vínculos históricos perdidos pela exclusão. Outros usuários são preservados.

Para outra conta, defina `ASILOSOFT_ADMIN_EMAIL` e, opcionalmente, `ASILOSOFT_ADMIN_NOME`. O SQL opcional depende das variáveis de sessão com nome, e-mail e hash preenchidas pelo script; execute-o por esse comando. Ele não faz parte do bootstrap, não é registrado em `schema_migrations` e só insere a conta quando o operador executa explicitamente o cadastro.

Uma instalação nova pode começar sem usuários para permitir o bootstrap inicial. Depois de criado o primeiro administrador ativo, as triggers impedem que a aplicação volte a ficar sem administrador ativo. Em uma base já sem administrador, a migration estrutural instala a proteção e o comando opcional cadastra novamente o administrador.

## Testes locais

Sem acessar o banco real:

```bash
ASILOSOFT_ADMIN_TEST_SOCKET=/caminho/mysql.sock php tests/integration/database/AdministradorIntegrationTest.php
ASILOSOFT_TEST_REPORT_DIR=/tmp/asilosoft-admin-unit php tests/unit/run.php
```

O teste de integração exige MySQL local descartável com acesso root sem senha via socket. Cria e remove apenas `asilosoft_test_admin_protecao`; se ela já existir, recusa iniciar. Verifica cadastro, senha, recusa de e-mail duplicado e preservação de contas existentes, exclusão, alteração de perfil, desativação, rollback de operações em lote, duas conexões concorrentes e tradução do erro na aplicação.

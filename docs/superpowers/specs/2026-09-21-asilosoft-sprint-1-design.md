# AsiloSoft - Design da Sprint 1

## Objetivo

Entregar a base do AsiloSoft com autenticacao por e-mail e senha, gestao centralizada de usuarios e armazenamento seguro de senhas. Esta sprint cobre somente RF10, RF11 e RNF02. Nenhum recurso de idosos, familiares, prontuario, quartos, estoque ou compras sera antecipado.

## Restrições confirmadas

- Tecnologias: PHP, JavaScript, MySQL e PDO, sem frameworks.
- Arquitetura: monolito modular em camadas com MVC customizado.
- Entrada HTTP: `index.php`, seguido de roteador, controlador, servico, repositorio, PDO/MySQL e view.
- Interface: responsiva, com identidade vintage administrativa, sem imagens, bibliotecas, fontes ou outros recursos externos.
- A conta inicial sera `rodyburatto@gmail.com`; a senha recebida sera usada apenas para gerar um hash na carga inicial, nunca armazenada em texto puro ou registrada nesta especificacao.
- Quatro perfis serao cadastraveis: Administrador/Gestor, Profissional de Saude, Almoxarife e Familiar. Nesta sprint, o Administrador/Gestor centraliza a administracao de usuarios; o refinamento do controle de acesso por perfil pertence a Sprint 2.

## Escopo funcional

### Autenticacao

- Exibir formulario de acesso por e-mail e senha.
- Validar campos obrigatorios e credenciais invalidas sem revelar se o e-mail existe.
- Bloquear o acesso de usuarios inativos.
- Criar uma sessao apos autenticacao valida e permitir encerramento da sessao.
- Proteger as telas administrativas contra visitantes sem sessao.

### Gestao de usuarios

- Listar usuarios com nome, e-mail, perfil e situacao.
- Cadastrar usuario com nome, e-mail unico, senha e perfil.
- Editar nome, e-mail e perfil de um usuario.
- Ativar ou desativar uma conta, preservando seu historico.
- Impedir que o administrador inicial se desative acidentalmente.

## Modelo de dados inicial

Tabela `usuarios`:

| Campo | Regra |
| --- | --- |
| id | identificador numerico primario |
| nome | obrigatorio |
| email | obrigatorio e unico |
| senha_hash | resultado de `password_hash`, nunca senha em texto puro |
| perfil | Administrador/Gestor, Profissional de Saude, Almoxarife ou Familiar |
| ativo | booleano; usuarios desativados nao autenticam |
| criado_em | data e hora da criacao |
| atualizado_em | data e hora da ultima alteracao |

## Organizacao do codigo

```text
index.php
app/
  Controllers/
  Services/
  Repositories/
  Views/
  Core/
config/
database/
public/
  css/
  js/
tests/
KANBAN.md
```

O `AuthService` concentra autenticacao, sessao e validacoes de acesso. O `UsuarioService` concentra regras de cadastro, edicao e ativacao. Os repositorios usam consultas preparadas via PDO. Controllers nao acessam o banco diretamente e views nao contem regras de negocio.

## Identidade visual

A interface sera funcional e propositalmente simples, com acabamento de projeto academico. A paleta deve remeter a madeira e a acolhimento, mas beleza e refinamento visual nao sao objetivo desta sprint.

| Elemento | Direcao |
| --- | --- |
| Fundo | pergaminho claro `#F4E6CE` |
| Superficies | linho quente `#E6D2AF` |
| Estruturas | nogueira `#4B2E20` |
| Destaques | cobre envelhecido `#A85F32` |
| Acao positiva | verde-musgo `#53664A` |
| Texto | castanho profundo `#2C1B14` |

Cartoes terao bordas simples, sombras leves e espacamento suficiente para leitura. Os elementos de formulario serao diretos. A textura, se usada, sera sugerida por gradientes CSS suaves, sem imagens. A tipografia usara familias serifadas e system sans-serif disponiveis localmente.

## Kanban da Sprint 1

O arquivo `KANBAN.md` sera a fonte versionada do quadro e cada item abaixo sera um cartao individual. Quando o repositorio for publicado e houver acesso ao GitHub, estes mesmos cartoes serao reproduzidos em GitHub Projects.

| Coluna inicial | Cartao |
| --- | --- |
| A fazer | Criar estrutura MVC e configuracao PDO |
| A fazer | Modelar e criar tabela de usuarios |
| A fazer | Criar carga inicial do administrador |
| A fazer | Implementar hash e verificacao segura de senha |
| A fazer | Implementar login, sessao e logout |
| A fazer | Proteger rotas administrativas |
| A fazer | Implementar listagem de usuarios |
| A fazer | Implementar cadastro e edicao de usuarios |
| A fazer | Implementar ativacao e desativacao de usuarios |
| A fazer | Aplicar identidade visual amadeirada |
| A fazer | Testar os fluxos da Sprint 1 |

## Testes e criterios de aceite

- Senhas persistidas no banco sao hashes e `password_verify` autentica a conta inicial.
- E-mail ou senha invalidos nao autenticam.
- Conta desativada nao autentica.
- Visitantes sao redirecionados para o login ao tentar abrir usuarios.
- O Administrador/Gestor consegue cadastrar, editar, ativar e desativar contas.
- E-mail duplicado e campos invalidos recebem mensagens compreensiveis.
- Nenhum recurso de sprints posteriores aparece nas telas ou no banco.

## Commits planejados

1. `chore: estruturar projeto e kanban da sprint 1`
2. `feat: entregar autenticacao e gestao de usuarios da sprint 1`

Cada commit sera feito somente apos a respectiva verificacao. A criacao e a publicacao do repositorio remoto dependem da autenticacao do GitHub disponivel neste ambiente.

# Objetivos e requisitos do AsiloSoft

> Síntese dos documentos `Apresentação_Asilosoft.pdf` e `AsiloSoft_Engenharia_de_Software_II.pdf`, analisados em 01/10/2026. O conteúdo abaixo organiza os pontos apresentados; não altera o escopo do projeto nem substitui a validação da equipe.

## 1. Contexto e problema

Instituições de longa permanência para idosos ainda podem depender de papel, planilhas e registros descentralizados. Segundo os documentos, isso dificulta a consulta do histórico de saúde e contatos de familiares, o acompanhamento de ocupação de quartos e a rastreabilidade de insumos e medicamentos. As consequências esperadas são lentidão operacional, informação inconsistente, risco de falta de itens essenciais e custos desnecessários.

## 2. Objetivo do sistema

Centralizar a gestão operacional, o acompanhamento clínico e o controle de insumos das instituições de longa permanência, reduzindo a desorganização de dados e o descontrole logístico para tornar o atendimento mais seguro, ágil e transparente.

## 3. Público-alvo

| Público | Necessidade principal |
| --- | --- |
| Administração e gestores | Gerir leitos, quartos, cadastros, prontuários e estoque. |
| Enfermagem e cuidadores | Registrar condições de saúde, observações e uso/requisição de insumos. |
| Almoxarifado/responsável pelo estoque | Controlar materiais, entradas, retiradas, origem e compras. |
| Familiares/responsáveis | Acompanhar, quando autorizado, informações do idoso vinculado. |

## 4. Escopo funcional proposto

1. Autenticação de usuários por credenciais.
2. Cadastro e edição de idosos, com nome, CPF, identificador e quarto.
3. Associação de um ou mais familiares/responsáveis a idosos.
4. Registro e atualização de prontuário e acompanhamento de saúde.
5. Gestão de materiais, insumos e medicamentos.
6. Registro do uso/retirada de item destinado a um idoso.
7. Controle de estoque, com entradas, retiradas e saldo atualizado.
8. Registro da origem das entradas: SUS, plano de saúde ou instituição.
9. Solicitações de compra de materiais e medicamentos.
10. Busca consolidada da ficha do idoso por nome, CPF ou identificador.
11. Consulta, por familiar vinculado, de informações liberadas pela instituição.
12. Gestão de contas e acessos pelo administrador/gestor.

## 5. Fora do escopo

- Faturamento, boletos e cobranças de mensalidades.
- Folha de pagamento, ponto e recursos humanos.
- Videochamadas ou chat em tempo real entre familiares e equipe.
- Integrações diretas com sistemas governamentais, secretarias ou hospitais externos.
- Aplicativo móvel nativo; o produto previsto é um sistema web responsivo.

## 6. Requisitos funcionais

| ID | Requisito | Caso de uso associado |
| --- | --- | --- |
| RF01 | Permitir ao administrador/gestor cadastrar e editar idosos, inclusive quarto. | UC01 - Cadastrar/Editar Idoso |
| RF02 | Vincular um ou mais familiares/responsáveis e seus contatos a cada idoso. | UC02 - Vincular Familiar |
| RF03 | Permitir a profissionais de saúde registrar e atualizar o acompanhamento do idoso. | UC03 - Registrar Condição de Saúde |
| RF04 | Cadastrar e gerenciar materiais, insumos e medicamentos. | UC04 - Gerenciar Materiais |
| RF05 | Buscar e consultar ficha completa do idoso por nome, CPF ou identificador. | UC06 - Consultar Ficha |
| RF06 | Registrar entradas e retiradas, mantendo o estoque atualizado. | UC07 - Gerenciar Estoque |
| RF07 | Informar origem da entrada: SUS, plano de saúde ou instituição. | UC09 - Definir Origem |
| RF08 | Registrar solicitações institucionais de compra. | UC10 - Solicitar Compra |
| RF09 | Permitir ao familiar vinculado consultar o acompanhamento disponibilizado. | UC11 - Consultar Acompanhamento |
| RF10 | Permitir ao administrador/gestor cadastrar e gerenciar usuários. | UC12 - Gerenciar Usuários |
| RF11 | Permitir login por credenciais. | UC13 - Realizar Login |
| RF12 | Registrar retirada/uso de materiais e medicamentos para um idoso. | UC05 - Registrar Uso |

## 7. Requisitos não funcionais

| ID | Requisito |
| --- | --- |
| RNF01 | Usabilidade: funções principais acessíveis de forma simples, sem navegação excessiva. |
| RNF02 | Segurança: senhas não podem ser armazenadas em texto puro; usar hash seguro. |
| RNF03 | Controle de acesso: recursos e informações restritos conforme perfil autenticado. |
| RNF04 | Privacidade: dados pessoais e clínicos acessíveis apenas a autorizados. |
| RNF05 | Desempenho: consultas principais devem responder em até dois segundos em uso normal. |
| RNF06 | Disponibilidade: mínimo de 99% durante o funcionamento, exceto manutenção programada. |
| RNF07 | Compatibilidade: versões atuais de Chrome, Edge e Firefox. |
| RNF08 | Responsividade: uso viável em computador, tablet e celular. |

## 8. Regras de negócio

| ID | Regra |
| --- | --- |
| RN01 | Um idoso pode possuir somente um quarto ativo por vez. |
| RN02 | Um quarto recebe novos idosos apenas enquanto houver vagas. |
| RN03 | Um familiar pode vincular-se a mais de um idoso. |
| RN04 | Somente administradores gerenciam usuários. |
| RN05 | Somente profissionais autorizados registram ou alteram saúde. |
| RN06 | Uma retirada não pode deixar o estoque negativo. |
| RN07 | Toda entrada de material/medicamento deve informar a origem. |
| RN08 | Familiar consulta somente idosos aos quais está vinculado. |
| RN09 | Usuários desativados não acessam o sistema. |

## 9. Backlog e planejamento das sprints

O planejamento apresentado usa sprints de até uma semana e Scrumban: tarefas ficam em quadro Kanban e podem ser puxadas por integrantes disponíveis.

| Sprint | Objetivo | Entregas planejadas |
| --- | --- | --- |
| S1 | Estrutura e acesso inicial | Login, usuários e hash de senhas |
| S2 | Cadastros principais | Idosos, familiares e permissões |
| S3 | Acompanhamento | Prontuário e ficha do idoso |
| S4 | Materiais | Materiais, medicamentos e estoque |
| S5 | Rastreabilidade | Origem e registro de uso |
| S6 | Aquisição | Solicitações de compra |
| S7 | Acesso de familiares | Consulta do acompanhamento |
| S8 | Qualidade | Usabilidade, desempenho e otimização |
| S9 | Interface | Responsividade e compatibilidade |
| S10 | Integração | Testes integrados e correções |
| S11 | Entrega | Homologação, documentação e revisão |

Os documentos estabelecem que uma funcionalidade só é concluída quando estiver implementada, testada e em conformidade com requisitos e regras de negócio.

## 10. Direção arquitetural declarada

A proposta define um monólito modular em camadas, baseado em MVC customizado, com Service e Repository, implementado em PHP, JavaScript e MySQL, sem framework. O fluxo esperado é:

```text
Requisição HTTP -> Front Controller -> Router/Middlewares -> Controller
-> Service/Validação -> Repository -> PDO/MySQL -> View -> Usuário
```

Os atores previstos são Administrador/Gestor, Profissional de Saúde, Almoxarife/Responsável pelo Estoque e Familiar/Responsável. O material também prevê diagramas de caso de uso, sequência e classes para os fluxos principais.

## 11. Aderência ao código atual

| Escopo | Estado observado |
| --- | --- |
| Arquitetura em camadas, PHP, JavaScript, MySQL e sem framework | Implementado |
| RF11, RF10 e RNF02 | Implementados: login, gestão de usuários e hash de senha |
| Parte de RF01, RN01, RN02 e RN09 | Implementados: idosos, quartos, capacidade, ativação/desativação |
| Perfis previstos e restrição administrativa | Parcial: perfis existem; as telas atuais exigem administrador |
| RF02-RF09 e RF12 | Ainda sem módulos web; parte do modelo de dados já existe no schema |
| RNF01, RNF05-RNF08 | Requisitos de qualidade pendentes de validação/documentação de evidências |

Essa diferença é esperada pelo planejamento por sprints: o schema antecipa parte do domínio, enquanto a camada web entregue até agora cobre os primeiros blocos de acesso, usuários e idosos.

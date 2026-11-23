# Evolução reconstruída do Kanban AsiloSoft

Capturas reais do GitHub Projects, realizadas em 23/10/2026 após reposicionar os cartões. São reconstruções interpretativas com base nos commits e na orientação do usuário; não são capturas históricas originais, nem comprovam o status do quadro nas datas representadas. Datas de implementação e de aceite não são necessariamente iguais. As colunas possuem rolagem: cada print mostra a porção visível do quadro.

O histórico disponível começa em 21/09. Para obter seis capturas sem inventar uma semana anterior, foram usados dois marcos na primeira semana e um nas quatro semanas seguintes. Datas no fuso America/Sao_Paulo.

| Print | Semana | Marco | Commits de referência |
|---|---|---|---|
| 01 | 21–27/09 | 22/09: estrutura, login e usuários | b2f5d4e, 1d0a58f, 49cc760 |
| 02 | 21–27/09 | 27/09: idosos, schema e estrutura modular | 18eb7a1, a07a185, faea555, 8f33d35, 0a00d0c, eb62737 |
| 03 | 28/09–04/10 | 03/10: banco versionado e almoxarifado; origem/uso ainda em desenvolvimento | 1928d7f, a921fc9 |
| 04 | 05–11/10 | 09/10: organização e automação das migrations | d046369, 9935a0b |
| 05 | 12–18/10 | 12/10: quartos, saúde, ficha, familiares, compras e portal | a5dcdcf, 682c4b1, a2dd7b3, 21a5e0d, af6801e, be422a3, 7226ee4 |
| 06 | 19–25/10 | 20/10: integração, correções, testes e conclusão funcional local S1–S7 | b9e33b8, fb24a80, 12f4b5c, 831e737, 99cda5c, 805d0e7, 3fadacb, 96132e1 |

Fonte: https://github.com/RodrigoBurattoRibas/asilosoft/commits/main/ e branch feat/add-nova-estrutura. A busca remota identificou main e feat/add-nova-estrutura. O histórico local também contém codex/sprint-1. As referências locais consultadas apontavam main/origin/main para 96132e1 e origin/feat/add-nova-estrutura para b9e33b8; não foi inferida uma data de merge na main apenas a partir da data dos commits.

Status intermediários são inferências para visualização. Os cartões de identidade visual, listagem e ativação/desativação foram preservados como Pronto nas reconstruções, usando sua conclusão atual e a presença da implementação inicial; não há comprovação de aceite naquelas datas. Banco e Railway voltam a Pronto no marco 04 por associação à entrega de infraestrutura, também sem comprovação de aceite histórico.

Estado final confirmado no GitHub: 9 A fazer (sprints futuras/refinamentos), 0 Fazendo, 16 Em teste (S1–S7) e 5 Pronto. A ordem final é A fazer, Fazendo, Em teste, Pronto. Os dois cartões homônimos da Sprint 7 foram preservados. Nenhum cartão foi excluído. As issues existentes não foram fechadas ou reabertas por esta operação.

Testes em 20/10 constam de STATUS_CONCLUSAO_SPRINTS.md no commit 96132e1 e do commit 3fadacb. Validação local não equivale a homologação em produção.

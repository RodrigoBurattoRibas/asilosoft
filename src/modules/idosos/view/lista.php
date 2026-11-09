<?php $administrador = $usuarioAtual['perfil'] === 'Administrador'; ?>
<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Cadastro</p>
        <h1>Idosos</h1>
    </div>
    <?php if ($administrador): ?><a class="botao" href="?route=idosos-novo">Cadastrar idoso</a><?php endif; ?>
</div>
<nav class="abas" aria-label="Situação dos idosos">
    <a class="<?= $situacao === 'ativos' ? 'atual' : '' ?>" href="?route=idosos&amp;situacao=ativos">Ativos</a>
    <a class="<?= $situacao === 'desativados' ? 'atual' : '' ?>" href="?route=idosos&amp;situacao=desativados">Desativados</a>
</nav>
<form method="get" class="formulario cartao">
    <input type="hidden" name="route" value="idosos"><input type="hidden" name="situacao" value="<?= AsiloView::escape($situacao) ?>">
    <label>Buscar por nome, CPF ou identificador<input name="busca" maxlength="150" value="<?= AsiloView::escape($busca) ?>"></label><button>Buscar</button>
</form>
<section class="cartao tabela-responsiva">
    <table>
        <thead>
            <tr><th>Nome</th><th>CPF</th><th>Identificador</th><th>Quarto</th><th>Situação</th><th>Ações</th></tr>
        </thead>
        <tbody>
        <?php if ($idosos === []): ?>
            <tr><td colspan="6" class="texto-suave">Nenhum idoso cadastrado até o momento.</td></tr>
        <?php else: ?>
            <?php foreach ($idosos as $idoso): ?>
                <tr>
                    <td><?= AsiloView::escape($idoso['nome']) ?></td>
                    <td><?= AsiloView::escape($idoso['cpf']) ?></td>
                    <td><?= AsiloView::escape($idoso['identificador']) ?></td>
                    <td><?= AsiloView::escape($idoso['quarto_codigo'] ?? 'Sem quarto') ?></td>
                    <td>
                        <span class="etiqueta <?= $idoso['ativo'] ? 'ativo' : 'inativo' ?>">
                            <?= $idoso['ativo'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td class="acoes">
                        <?php if ($administrador): ?><a href="?route=idosos-editar&amp;id=<?= AsiloView::escape($idoso['id']) ?>">Editar</a><?php endif; ?>
                        <a href="?route=idosos-saude&amp;id=<?= AsiloView::escape($idoso['id']) ?>">Prontuário</a>
                        <a href="?route=idosos-ficha&amp;id=<?= (int)$idoso['id'] ?>">Ficha completa</a>
                        <?php if ($administrador): ?>
                        <?php if ($idoso['quarto_id'] !== null): ?>
                            <form method="post" action="?route=idosos-retirar-quarto&amp;id=<?= AsiloView::escape($idoso['id']) ?>" data-confirmacao="Confirma a retirada deste idoso do quarto?">
                                <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
                                <button class="botao-link" type="submit">Retirar do quarto</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="?route=idosos-status&amp;id=<?= AsiloView::escape($idoso['id']) ?>" data-confirmacao="Confirma a alteração da situação deste idoso?">
                            <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
                            <input type="hidden" name="ativo" value="<?= $idoso['ativo'] ? '0' : '1' ?>">
                            <button class="botao-link" type="submit"><?= $idoso['ativo'] ? 'Desativar' : 'Ativar' ?></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>

<nav class="abas" aria-label="Paginação">
<?php foreach ([$pagina - 1 => 'Anterior', $pagina + 1 => 'Próxima'] as $numero => $rotulo): if ($numero < 1 || ($numero > $pagina && !$temProxima)) continue; ?>
<a href="?<?= AsiloView::escape(http_build_query(['route'=>'idosos','situacao'=>$situacao,'busca'=>$busca,'pagina'=>$numero])) ?>"><?= $rotulo ?></a>
<?php endforeach; ?><span>Página <?= $pagina ?></span></nav>

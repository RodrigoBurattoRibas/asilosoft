<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Estrutura</p>
        <h1>Quartos</h1>
    </div>
    <a class="botao" href="?route=quartos-novo">Cadastrar quarto</a>
</div>

<nav class="abas" aria-label="Situação dos quartos">
    <a class="<?= $situacao === 'ativos' ? 'atual' : '' ?>" href="?route=quartos&amp;situacao=ativos">Ativos</a>
    <a class="<?= $situacao === 'desativados' ? 'atual' : '' ?>" href="?route=quartos&amp;situacao=desativados">Desativados</a>
</nav>

<section class="cartao tabela-responsiva">
    <table>
        <thead>
            <tr><th>Código</th><th>Capacidade</th><th>Ocupação</th><th>Situação</th><th>Ações</th></tr>
        </thead>
        <tbody>
        <?php if ($quartos === []): ?>
            <tr><td colspan="5" class="texto-suave">Nenhum quarto encontrado nesta situação.</td></tr>
        <?php else: ?>
            <?php foreach ($quartos as $quarto): ?>
                <tr>
                    <td><?= AsiloView::escape($quarto['codigo']) ?></td>
                    <td><?= AsiloView::escape($quarto['capacidade']) ?> residente(s)</td>
                    <td><?= AsiloView::escape($quarto['ocupacao']) ?>/<?= AsiloView::escape($quarto['capacidade']) ?></td>
                    <td>
                        <span class="etiqueta <?= $quarto['ativo'] ? 'ativo' : 'inativo' ?>">
                            <?= $quarto['ativo'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td class="acoes">
                        <a href="?route=quartos-editar&amp;id=<?= AsiloView::escape($quarto['id']) ?>">Editar</a>
                        <form method="post" action="?route=quartos-status&amp;id=<?= AsiloView::escape($quarto['id']) ?>" data-confirmacao="Confirma a alteração da situação deste quarto?">
                            <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
                            <input type="hidden" name="ativo" value="<?= $quarto['ativo'] ? '0' : '1' ?>">
                            <button class="botao-link" type="submit"><?= $quarto['ativo'] ? 'Desativar' : 'Ativar' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>

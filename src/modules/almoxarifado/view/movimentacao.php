<?php $entrada = $tipoMovimentacao === 'Entrada'; ?>
<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Almoxarifado</p>
        <h1><?= AsiloView::escape($tipoMovimentacao) ?> de estoque</h1>
    </div>
    <a href="?route=almoxarifado">Voltar</a>
</div>

<section class="cartao">
    <div class="resumo-estoque">
        <strong><?= AsiloView::escape($item['nome']) ?></strong>
        <span class="texto-suave">
            Estoque atual: <?= AsiloView::escape($item['quantidade_disponivel']) ?> <?= AsiloView::escape($item['unidade_medida']) ?>
        </span>
    </div>

    <form
        method="post"
        action="?route=<?= $entrada ? 'almoxarifado-registrar-entrada' : 'almoxarifado-registrar-retirada' ?>&amp;id=<?= AsiloView::escape($item['id']) ?>"
        class="formulario formulario-largo"
    >
        <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">

        <label>Quantidade
            <input
                type="number"
                name="quantidade"
                min="1"
                <?= $entrada ? '' : 'max="' . AsiloView::escape($item['quantidade_disponivel']) . '"' ?>
                required
            >
        </label>

        <?php if ($entrada): ?>
            <label>Origem
                <select name="origem" required>
                    <option value="">Selecione a origem</option>
                    <?php foreach ($origens as $origem): ?>
                        <option value="<?= AsiloView::escape($origem) ?>"><?= AsiloView::escape($origem) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php endif; ?>

        <button type="submit">Registrar <?= mb_strtolower($tipoMovimentacao) ?></button>
    </form>
</section>

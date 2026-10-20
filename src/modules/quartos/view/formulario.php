<?php $edicao = $quarto !== null; ?>
<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Cadastro</p>
        <h1><?= $edicao ? 'Editar quarto' : 'Novo quarto' ?></h1>
    </div>
    <a href="?route=quartos">Voltar</a>
</div>

<section class="cartao">
    <form
        method="post"
        action="?route=<?= $edicao ? 'quartos-atualizar&amp;id=' . AsiloView::escape($quarto['id']) : 'quartos-criar' ?>"
        class="formulario formulario-largo"
    >
        <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">

        <label>Código do quarto
            <input
                type="text"
                name="codigo"
                maxlength="30"
                required
                value="<?= AsiloView::escape($quarto['codigo'] ?? '') ?>"
                placeholder="Ex.: A-101"
            >
        </label>

        <label>Capacidade
            <input
                type="number"
                name="capacidade"
                min="1"
                max="255"
                required
                value="<?= AsiloView::escape($quarto['capacidade'] ?? '1') ?>"
            >
        </label>

        <?php if ($edicao && isset($quarto['ocupacao'])): ?>
            <p class="texto-suave">
                Ocupação atual: <strong><?= AsiloView::escape($quarto['ocupacao']) ?></strong> residente(s).
            </p>
        <?php endif; ?>

        <button type="submit"><?= $edicao ? 'Salvar alterações' : 'Cadastrar quarto' ?></button>
    </form>
</section>

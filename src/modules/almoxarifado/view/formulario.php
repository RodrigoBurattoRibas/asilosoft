<?php $edicao = $item !== null; ?>
<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Almoxarifado</p>
        <h1><?= $edicao ? 'Editar item' : 'Novo item' ?></h1>
    </div>
    <a href="?route=almoxarifado">Voltar</a>
</div>

<section class="cartao">
    <form
        method="post"
        action="?route=<?= $edicao ? 'almoxarifado-atualizar&amp;id=' . AsiloView::escape($item['id']) : 'almoxarifado-criar' ?>"
        class="formulario formulario-largo"
    >
        <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">

        <label>Nome do item
            <input
                type="text"
                name="nome"
                maxlength="120"
                required
                value="<?= AsiloView::escape($item['nome'] ?? '') ?>"
                placeholder="Ex.: Luvas descartáveis"
            >
        </label>

        <label>Tipo
            <select name="tipo" required>
                <option value="">Selecione o tipo</option>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?= AsiloView::escape($tipo) ?>" <?= ($item['tipo'] ?? '') === $tipo ? 'selected' : '' ?>>
                        <?= AsiloView::escape($tipo) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Unidade de medida
            <input
                type="text"
                name="unidade_medida"
                maxlength="30"
                required
                list="unidades-medida"
                value="<?= AsiloView::escape($item['unidade_medida'] ?? '') ?>"
                placeholder="Ex.: unidade, caixa, frasco"
            >
            <datalist id="unidades-medida">
                <option value="unidade">
                <option value="caixa">
                <option value="frasco">
                <option value="pacote">
                <option value="comprimido">
            </datalist>
        </label>

        <?php if ($edicao): ?>
            <p class="texto-suave">
                Estoque atual: <strong><?= AsiloView::escape($item['quantidade_disponivel']) ?> <?= AsiloView::escape($item['unidade_medida']) ?></strong>.
                A quantidade é alterada somente por entradas e retiradas.
            </p>
        <?php endif; ?>

        <button type="submit"><?= $edicao ? 'Salvar alterações' : 'Cadastrar item' ?></button>
    </form>
</section>

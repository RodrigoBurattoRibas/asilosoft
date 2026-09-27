<?php $edicao = $idoso !== null; ?>
<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Cadastro</p>
        <h1><?= $edicao ? 'Editar idoso' : 'Novo idoso' ?></h1>
    </div>
    <a href="?route=idosos">Voltar</a>
</div>
<section class="cartao">
    <?php if ($quartos === [] && !$edicao): ?>
        <p class="texto-suave">Não há quartos com vagas disponíveis no momento.</p>
    <?php else: ?>
        <form
            method="post"
            action="?route=<?= $edicao ? 'idosos-atualizar&amp;id=' . AsiloView::escape($idoso['id']) : 'idosos-criar' ?>"
            class="formulario formulario-largo"
        >
            <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
            <label>Nome completo
                <input type="text" name="nome" required autocomplete="name" value="<?= AsiloView::escape($idoso['nome'] ?? '') ?>">
            </label>
            <label>CPF
                <input type="text" name="cpf" required inputmode="numeric" maxlength="14" placeholder="000.000.000-00" value="<?= AsiloView::escape($idoso['cpf'] ?? '') ?>">
            </label>
            <label>Identificador
                <input type="text" name="identificador" required maxlength="60" placeholder="Ex.: IDOSO-001" value="<?= AsiloView::escape($idoso['identificador'] ?? '') ?>">
            </label>
            <label>Quarto
                <select name="quarto_id" <?= $edicao ? '' : 'required' ?>>
                    <option value="">Selecione um quarto</option>
                    <?php foreach ($quartos as $quarto): ?>
                        <option value="<?= AsiloView::escape($quarto['id']) ?>" <?= (int) ($idoso['quarto_id'] ?? 0) === $quarto['id'] ? 'selected' : '' ?>>
                            <?= AsiloView::escape($quarto['codigo']) ?> —
                            <?= AsiloView::escape($quarto['ocupacao']) ?>/<?= AsiloView::escape($quarto['capacidade']) ?> ocupação
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit"><?= $edicao ? 'Salvar alterações' : 'Cadastrar idoso' ?></button>
        </form>
    <?php endif; ?>
</section>

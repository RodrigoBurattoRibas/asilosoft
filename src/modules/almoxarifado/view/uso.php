<?php
$valores = [];
foreach (['idoso_id', 'material_id', 'quantidade', 'observacao'] as $campo) {
    $valores[$campo] = is_scalar($dadosConsumo[$campo] ?? null) ? (string) $dadosConsumo[$campo] : '';
}
?>
<div class="titulo-pagina">
    <div><p class="sobretitulo">Uso por idoso</p><h1>Consumo de material por idoso</h1></div>
    <a href="?route=home">Voltar</a>
</div>
<section class="cartao">
    <?php if ($erroConsumo !== null): ?>
        <div class="aviso erro" role="alert"><?= AsiloView::escape($erroConsumo) ?></div>
    <?php endif; ?>
    <?php if ($idosos === [] || $materiais === []): ?>
        <p class="aviso erro">É necessário ter um idoso ativo e um item ativo cadastrado para registrar consumo.</p>
    <?php endif; ?>
    <form class="formulario formulario-largo" method="post" action="?route=almoxarifado-registrar-uso">
        <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
        <input type="hidden" name="chave_requisicao" value="<?= AsiloView::escape($chaveRequisicao) ?>">
        <label for="consumo-idoso">Idoso</label>
        <select id="consumo-idoso" name="idoso_id" required>
            <option value="">Selecione o idoso</option>
            <?php foreach ($idosos as $idoso): ?>
                <option value="<?= (int) $idoso['id'] ?>" <?= $valores['idoso_id'] === (string) $idoso['id'] ? 'selected' : '' ?>><?= AsiloView::escape($idoso['nome'].' (#'.$idoso['id'].')') ?></option>
            <?php endforeach; ?>
        </select>
        <label for="consumo-item">Nome do item</label>
        <select id="consumo-item" name="material_id" required aria-describedby="consumo-saldo">
            <option value="">Selecione o item</option>
            <?php foreach ($materiais as $material): ?>
                <option value="<?= (int) $material['id'] ?>" <?= $valores['material_id'] === (string) $material['id'] ? 'selected' : '' ?>><?= AsiloView::escape($material['nome'].' — disponível: '.number_format($material['quantidade_disponivel'], 3, ',', '.').' '.$material['unidade_medida']) ?></option>
            <?php endforeach; ?>
        </select>
        <p id="consumo-saldo" class="texto-suave">A quantidade consumida deve respeitar o saldo e a unidade de medida do item. Itens sem saldo precisam de uma entrada de estoque.</p>
        <label for="consumo-quantidade">Quantidade</label>
        <input id="consumo-quantidade" name="quantidade" type="number" min="0.001" max="999999999.999" step="0.001" required value="<?= AsiloView::escape($valores['quantidade']) ?>">
        <label for="consumo-observacao">Finalidade / observação</label>
        <textarea id="consumo-observacao" name="observacao" maxlength="500"><?= AsiloView::escape($valores['observacao']) ?></textarea>
        <button type="submit" <?= $idosos === [] || $materiais === [] ? 'disabled' : '' ?>>Registrar consumo</button>
    </form>
</section>

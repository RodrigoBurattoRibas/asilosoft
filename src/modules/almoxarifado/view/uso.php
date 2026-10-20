<div class="titulo-pagina"><h1>Consumo de material por idoso</h1><a href="?route=home">Voltar</a></div>
<section class="cartao"><form class="formulario" method="post" action="?route=almoxarifado-registrar-uso">
<input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
<input type="hidden" name="chave_requisicao" value="<?= AsiloView::escape($chaveRequisicao) ?>">
<label>Idoso<select name="idoso_id" required><option value="">Selecione</option><?php foreach ($idosos as $idoso): ?><option value="<?= AsiloView::escape($idoso['id']) ?>"><?= AsiloView::escape($idoso['nome'].' (#'.$idoso['id'].')') ?></option><?php endforeach; ?></select></label>
<label>Material<select name="material_id" required><option value="">Selecione</option><?php foreach ($materiais as $material): ?><option value="<?= AsiloView::escape($material['id']) ?>"><?= AsiloView::escape($material['nome'].' ('.$material['unidade_medida'].')') ?></option><?php endforeach; ?></select></label>
<label>Quantidade<input name="quantidade" type="number" min="0.001" max="999999999.999" step="0.001" required></label>
<label>Finalidade / observação<textarea name="observacao" maxlength="500"></textarea></label>
<button type="submit">Registrar consumo</button>
</form></section>

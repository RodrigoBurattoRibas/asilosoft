<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Acompanhamento clínico</p>
        <h1>Prontuário de <?= AsiloView::escape($idoso['nome']) ?></h1>
    </div>
    <a href="?route=idosos">Voltar</a>
</div>
<section class="cartao">
    <form method="post" action="?route=idosos-saude-registrar&amp;id=<?= AsiloView::escape($idoso['id']) ?>" class="formulario formulario-largo">
        <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
        <label>Registro de saúde
            <textarea name="descricao" required maxlength="65535" rows="5" placeholder="Condição, acompanhamento ou observação clínica"></textarea>
        </label>
        <label><input type="checkbox" name="visivel_para_familiar" value="1"> Disponibilizar ao familiar responsável</label>
        <button type="submit">Registrar acompanhamento</button>
    </form>
</section>
<section class="cartao tabela-responsiva">
    <table>
        <thead><tr><th>Data</th><th>Profissional</th><th>Registro</th><th>Visibilidade</th></tr></thead>
        <tbody>
        <?php if ($registros === []): ?>
            <tr><td colspan="4" class="texto-suave">Nenhum registro de saúde cadastrado.</td></tr>
        <?php else: foreach ($registros as $registro): ?>
            <tr>
                <td><?= AsiloView::escape(date('d/m/Y H:i', strtotime($registro['registrado_em']))) ?></td>
                <td><?= AsiloView::escape($registro['profissional']) ?></td>
                <td><?= nl2br(AsiloView::escape($registro['descricao'])) ?></td>
                <td><?= $registro['visivel_para_familiar'] ? 'Familiar responsável' : 'Equipe autorizada' ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</section>

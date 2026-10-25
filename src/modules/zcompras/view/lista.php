<div class="titulo-pagina">
    <div><p class="sobretitulo">Aquisições</p><h1>Solicitações de compra</h1></div>
    <a class="botao" href="?route=compras-nova">Nova solicitação</a>
</div>
<section class="cartao tabela-responsiva">
    <table>
        <thead><tr><th>Data</th><th>Material</th><th>Quantidade</th><th>Solicitante</th><th>Situação</th><th>Ações</th></tr></thead>
        <tbody>
        <?php if ($solicitacoes === []): ?>
            <tr><td colspan="6" class="texto-suave">Nenhuma solicitação cadastrada.</td></tr>
        <?php else: foreach ($solicitacoes as $x): ?>
            <tr>
                <td><?= AsiloView::escape(date('d/m/Y H:i', strtotime($x['solicitada_em']))) ?></td>
                <td><?= AsiloView::escape($x['material_nome']) ?></td>
                <td><?= AsiloView::escape($x['quantidade']) ?></td>
                <td><?= AsiloView::escape($x['solicitante']) ?></td>
                <td><span><?= AsiloView::escape($x['situacao']) ?></span></td>
                <td>
                    <?php if ($usuarioAtual['perfil'] === 'Administrador' && AsiloCompraService::transicoes($x['situacao']) !== []): ?>
                    <form class="formulario-situacao" method="post" action="?route=compras-situacao&amp;id=<?= AsiloView::escape($x['id']) ?>">
                        <input type="hidden" name="csrf_token" value="<?= AsiloView::escape(CsrfToken::obter('asilosoft_compras')) ?>">
                        <input type="hidden" name="situacao_atual" value="<?= AsiloView::escape($x['situacao']) ?>">
                        <label>Alterar situação
                            <select name="situacao" required>
                                <option value="">Selecione</option>
                                <?php foreach (AsiloCompraService::transicoes($x['situacao']) as $opcao): ?>
                                <option><?= AsiloView::escape($opcao) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="submit">Atualizar</button>
                    </form>
                    <?php else: ?>
                        <span class="texto-suave">Sem ações disponíveis</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</section>

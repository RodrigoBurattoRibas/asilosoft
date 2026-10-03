<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Estoque</p>
        <h1>Almoxarifado</h1>
    </div>
    <a class="botao" href="?route=almoxarifado-novo">Cadastrar item</a>
</div>

<section class="cartao tabela-responsiva">
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Tipo</th>
                <th>Quantidade disponível</th>
                <th>Situação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($itens === []): ?>
            <tr><td colspan="5" class="texto-suave">Nenhum item cadastrado no almoxarifado.</td></tr>
        <?php else: ?>
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td><?= AsiloView::escape($item['nome']) ?></td>
                    <td><?= AsiloView::escape($item['tipo']) ?></td>
                    <td>
                        <?= AsiloView::escape($item['quantidade_disponivel']) ?>
                        <?= AsiloView::escape($item['unidade_medida']) ?>
                    </td>
                    <td>
                        <span class="etiqueta <?= $item['quantidade_disponivel'] > 0 ? 'ativo' : 'inativo' ?>">
                            <?= $item['quantidade_disponivel'] > 0 ? 'Disponível' : 'Sem estoque' ?>
                        </span>
                    </td>
                    <td class="acoes">
                        <a href="?route=almoxarifado-editar&amp;id=<?= AsiloView::escape($item['id']) ?>">Editar</a>
                        <a href="?route=almoxarifado-entrada&amp;id=<?= AsiloView::escape($item['id']) ?>">Entrada</a>
                        <?php if ($item['quantidade_disponivel'] > 0): ?>
                            <a href="?route=almoxarifado-retirada&amp;id=<?= AsiloView::escape($item['id']) ?>">Retirada</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>

<div class="titulo-pagina titulo-secao">
    <div>
        <p class="sobretitulo">Rastreabilidade</p>
        <h2>Histórico de movimentações</h2>
    </div>
</div>

<section class="cartao tabela-responsiva">
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Item</th>
                <th>Movimentação</th>
                <th>Quantidade</th>
                <th>Origem</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($movimentacoes === []): ?>
            <tr><td colspan="5" class="texto-suave">Nenhuma movimentação registrada.</td></tr>
        <?php else: ?>
            <?php foreach ($movimentacoes as $movimentacao): ?>
                <?php $horario = strtotime($movimentacao['data_hora']); ?>
                <tr>
                    <td><?= AsiloView::escape($horario === false ? $movimentacao['data_hora'] : date('d/m/Y H:i', $horario)) ?></td>
                    <td><?= AsiloView::escape($movimentacao['item_nome']) ?></td>
                    <td>
                        <span class="etiqueta <?= $movimentacao['tipo'] === 'Entrada' ? 'ativo' : 'inativo' ?>">
                            <?= AsiloView::escape($movimentacao['tipo']) ?>
                        </span>
                    </td>
                    <td>
                        <?= AsiloView::escape($movimentacao['quantidade']) ?>
                        <?= AsiloView::escape($movimentacao['unidade_medida']) ?>
                    </td>
                    <td><?= AsiloView::escape($movimentacao['origem'] ?? '—') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>

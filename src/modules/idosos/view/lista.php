<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Cadastro</p>
        <h1>Idosos</h1>
    </div>
    <a class="botao" href="?route=idosos-novo">Cadastrar idoso</a>
</div>
<nav class="abas" aria-label="Situação dos idosos">
    <a class="<?= $situacao === 'ativos' ? 'atual' : '' ?>" href="?route=idosos&amp;situacao=ativos">Ativos</a>
    <a class="<?= $situacao === 'desativados' ? 'atual' : '' ?>" href="?route=idosos&amp;situacao=desativados">Desativados</a>
</nav>
<section class="cartao tabela-responsiva">
    <table>
        <thead>
            <tr><th>Nome</th><th>CPF</th><th>Identificador</th><th>Quarto</th><th>Situação</th><th>Ações</th></tr>
        </thead>
        <tbody>
        <?php if ($idosos === []): ?>
            <tr><td colspan="6" class="texto-suave">Nenhum idoso cadastrado até o momento.</td></tr>
        <?php else: ?>
            <?php foreach ($idosos as $idoso): ?>
                <tr>
                    <td><?= AsiloView::escape($idoso['nome']) ?></td>
                    <td><?= AsiloView::escape($idoso['cpf']) ?></td>
                    <td><?= AsiloView::escape($idoso['identificador']) ?></td>
                    <td><?= AsiloView::escape($idoso['quarto_codigo'] ?? 'Sem quarto') ?></td>
                    <td>
                        <span class="etiqueta <?= $idoso['ativo'] ? 'ativo' : 'inativo' ?>">
                            <?= $idoso['ativo'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td class="acoes">
                        <a href="?route=idosos-editar&amp;id=<?= AsiloView::escape($idoso['id']) ?>">Editar</a>
                        <?php if ($idoso['quarto_id'] !== null): ?>
                            <form method="post" action="?route=idosos-retirar-quarto&amp;id=<?= AsiloView::escape($idoso['id']) ?>" data-confirmacao="Confirma a retirada deste idoso do quarto?">
                                <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
                                <button class="botao-link" type="submit">Retirar do quarto</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="?route=idosos-status&amp;id=<?= AsiloView::escape($idoso['id']) ?>" data-confirmacao="Confirma a alteração da situação deste idoso?">
                            <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
                            <input type="hidden" name="ativo" value="<?= $idoso['ativo'] ? '0' : '1' ?>">
                            <button class="botao-link" type="submit"><?= $idoso['ativo'] ? 'Desativar' : 'Ativar' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>

<div class="titulo-pagina titulo-secao">
    <div>
        <p class="sobretitulo">Acomodação</p>
        <h2>Quartos e vagas</h2>
    </div>
</div>
<section class="cartao">
    <form method="post" action="?route=quartos-criar" class="formulario formulario-largo">
        <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
        <label>Código do quarto
            <input type="text" name="codigo" maxlength="30" required placeholder="Ex.: B-201">
        </label>
        <label>Capacidade
            <input type="number" name="capacidade" min="1" max="99" required>
        </label>
        <button type="submit">Cadastrar quarto</button>
    </form>
</section>
<section class="cartao tabela-responsiva">
    <table>
        <thead><tr><th>Quarto</th><th>Ocupação</th><th>Capacidade</th><th>Situação</th><th>Ações</th></tr></thead>
        <tbody>
        <?php if ($quartos === []): ?>
            <tr><td colspan="5" class="texto-suave">Nenhum quarto cadastrado.</td></tr>
        <?php else: ?>
            <?php foreach ($quartos as $quarto): ?>
                <tr>
                    <td><?= AsiloView::escape($quarto['codigo']) ?></td>
                    <td><?= AsiloView::escape($quarto['ocupacao']) ?></td>
                    <td><?= AsiloView::escape($quarto['capacidade']) ?></td>
                    <td><span class="etiqueta <?= $quarto['ativo'] ? 'ativo' : 'inativo' ?>"><?= $quarto['ativo'] ? 'Ativo' : 'Inativo' ?></span></td>
                    <td>
                        <form method="post" action="?route=quartos-atualizar&amp;id=<?= AsiloView::escape($quarto['id']) ?>" class="acoes">
                            <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
                            <input type="text" name="codigo" maxlength="30" required value="<?= AsiloView::escape($quarto['codigo']) ?>" aria-label="Código do quarto">
                            <input type="number" name="capacidade" min="1" max="99" required value="<?= AsiloView::escape($quarto['capacidade']) ?>" aria-label="Capacidade">
                            <input type="hidden" name="ativo" value="<?= $quarto['ativo'] ? '0' : '1' ?>">
                            <button class="botao-link" type="submit">Salvar e <?= $quarto['ativo'] ? 'desativar' : 'ativar' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>

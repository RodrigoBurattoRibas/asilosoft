<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Administração</p>
        <h1>Usuários</h1>
    </div>
    <a class="botao" href="?route=usuarios-novo">Cadastrar usuário</a>
</div>
<nav class="abas" aria-label="Situação dos usuários">
    <a class="<?= $situacao === 'ativos' ? 'atual' : '' ?>" href="?route=usuarios&amp;situacao=ativos">Ativos</a>
    <a class="<?= $situacao === 'desativados' ? 'atual' : '' ?>" href="?route=usuarios&amp;situacao=desativados">Desativados</a>
</nav>
<section class="cartao tabela-responsiva">
    <table>
        <thead>
            <tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Situação</th><th>Ações</th></tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= AsiloView::escape($usuario['nome']) ?></td>
                <td><?= AsiloView::escape($usuario['email']) ?></td>
                <td><?= AsiloView::escape($usuario['perfil']) ?></td>
                <td>
                    <span class="etiqueta <?= $usuario['ativo'] ? 'ativo' : 'inativo' ?>">
                        <?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </span>
                </td>
                <td class="acoes">
                    <a href="?route=usuarios-editar&amp;id=<?= AsiloView::escape($usuario['id']) ?>">Editar</a>
                    <form method="post" action="?route=usuarios-status&amp;id=<?= AsiloView::escape($usuario['id']) ?>" data-confirmacao="Confirma a alteração da situação deste usuário?">
                        <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
                        <input type="hidden" name="ativo" value="<?= $usuario['ativo'] ? '0' : '1' ?>">
                        <button class="botao-link" type="submit"><?= $usuario['ativo'] ? 'Desativar' : 'Ativar' ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

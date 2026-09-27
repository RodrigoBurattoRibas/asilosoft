<?php $edicao = $usuario !== null; ?>
<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Administração</p>
        <h1><?= $edicao ? 'Editar usuário' : 'Novo usuário' ?></h1>
    </div>
    <a href="?route=usuarios">Voltar</a>
</div>
<section class="cartao">
    <form
        method="post"
        action="?route=<?= $edicao ? 'usuarios-atualizar&amp;id=' . AsiloView::escape($usuario['id']) : 'usuarios-criar' ?>"
        class="formulario formulario-largo"
    >
        <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfToken) ?>">
        <label>Nome
            <input type="text" name="nome" value="<?= AsiloView::escape($usuario['nome'] ?? '') ?>" required>
        </label>
        <label>E-mail
            <input type="email" name="email" value="<?= AsiloView::escape($usuario['email'] ?? '') ?>" required>
        </label>
        <label>Perfil
            <select name="perfil" required>
                <?php foreach (['Administrador', 'Profissional de Saúde', 'Almoxarife', 'Familiar'] as $perfil): ?>
                    <option value="<?= AsiloView::escape($perfil) ?>" <?= ($usuario['perfil'] ?? '') === $perfil ? 'selected' : '' ?>>
                        <?= AsiloView::escape($perfil) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Senha
            <?php if ($edicao): ?><span class="texto-suave">(deixe em branco para manter)</span><?php endif; ?>
            <input type="password" name="senha" <?= $edicao ? '' : 'required' ?> minlength="8">
        </label>
        <button type="submit"><?= $edicao ? 'Salvar alterações' : 'Cadastrar usuário' ?></button>
    </form>
</section>

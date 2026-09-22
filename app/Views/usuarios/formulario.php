<?php $edicao = $usuario !== null; ?>
<div class="titulo-pagina">
    <div><p class="sobretitulo">Administração</p><h1><?= $edicao ? 'Editar usuário' : 'Novo usuário' ?></h1></div>
    <a href="/usuarios">Voltar</a>
</div>
<section class="cartao">
    <form method="post" action="<?= $edicao ? '/usuarios/' . e($usuario['id']) : '/usuarios' ?>" class="formulario formulario-largo">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <label>Nome
            <input type="text" name="nome" value="<?= e($usuario['nome'] ?? '') ?>" required>
        </label>
        <label>E-mail
            <input type="email" name="email" value="<?= e($usuario['email'] ?? '') ?>" required>
        </label>
        <label>Perfil
            <select name="perfil" required>
                <?php foreach (['Administrador', 'Profissional de Saúde', 'Almoxarife', 'Familiar'] as $perfil): ?>
                    <option value="<?= e($perfil) ?>" <?= ($usuario['perfil'] ?? '') === $perfil ? 'selected' : '' ?>><?= e($perfil) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Senha <?= $edicao ? '<span class="texto-suave">(deixe em branco para manter)</span>' : '' ?>
            <input type="password" name="senha" <?= $edicao ? '' : 'required' ?> minlength="8">
        </label>
        <button type="submit"><?= $edicao ? 'Salvar alterações' : 'Cadastrar usuário' ?></button>
    </form>
</section>

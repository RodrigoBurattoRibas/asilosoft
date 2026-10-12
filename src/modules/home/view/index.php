<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Área autenticada</p>
        <h1>Início</h1>
    </div>
</div>

<section class="cartao cartao-boas-vindas">
    <h2>Olá, <?= AsiloView::escape($usuarioAtual['nome']) ?>.</h2>
    <p class="texto-suave">
        Você está conectado como <strong><?= AsiloView::escape($usuarioAtual['perfil']) ?></strong>.
        Selecione uma área para continuar.
    </p>
</section>

<?php if ($atalhos !== []): ?>
    <section class="atalhos-home" aria-label="Áreas disponíveis">
        <?php foreach ($atalhos as $atalho): ?>
            <a class="cartao atalho-home" href="?route=<?= AsiloView::escape($atalho['rota']) ?>">
                <h2><?= AsiloView::escape($atalho['titulo']) ?></h2>
                <p class="texto-suave"><?= AsiloView::escape($atalho['descricao']) ?></p>
                <span> Acessar área &rarr;</span>
            </a>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <section class="cartao">
        <h2>Áreas em preparação</h2>
        <p class="texto-suave">Seu perfil está autenticado, mas ainda não possui módulos disponíveis para acesso nesta versão.</p>
    </section>
<?php endif; ?>

<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= AsiloView::escape($titulo ?? 'AsiloSoft') ?> | AsiloSoft</title>
    <link rel="stylesheet" href="src/assets/css/asilosoft.css">
</head>
<body>
    <?php $rotaInicial = ($usuarioAtual['perfil'] ?? '') === 'Almoxarife' ? 'almoxarifado' : 'usuarios'; ?>
    <header class="cabecalho">
        <a class="marca" href="?route=<?= AsiloView::escape($rotaInicial) ?>">AsiloSoft</a>
        <?php if (isset($usuarioAtual)): ?>
            <nav class="navegacao" aria-label="Navegação principal">
                <?php if (($usuarioAtual['perfil'] ?? '') === 'Administrador'): ?>
                    <a class="<?= ($secaoAtual ?? '') === 'usuarios' ? 'atual' : '' ?>" href="?route=usuarios">Usuários</a>
                    <a class="<?= ($secaoAtual ?? '') === 'idosos' ? 'atual' : '' ?>" href="?route=idosos">Idosos</a>
                    <a class="<?= ($secaoAtual ?? '') === 'quartos' ? 'atual' : '' ?>" href="?route=quartos">Quartos</a>
                <?php endif; ?>
                <?php if (in_array(($usuarioAtual['perfil'] ?? ''), ['Administrador', 'Almoxarife'], true)): ?>
                    <a class="<?= ($secaoAtual ?? '') === 'almoxarifado' ? 'atual' : '' ?>" href="?route=almoxarifado">Almoxarifado</a>
                <?php endif; ?>
            </nav>
            <div class="usuario-logado">
                <span><?= AsiloView::escape($usuarioAtual['nome']) ?></span>
                <form method="post" action="?route=sair">
                    <input type="hidden" name="csrf_token" value="<?= AsiloView::escape($csrfSairToken) ?>">
                    <button class="botao-link" type="submit">Sair</button>
                </form>
            </div>
        <?php endif; ?>
    </header>
    <div class="avisos-flutuantes" aria-live="polite">
        <?php if (!empty($mensagemErro)): ?>
            <div class="aviso erro aviso-flutuante" data-aviso>
                <span><?= AsiloView::escape($mensagemErro) ?></span><button type="button" aria-label="Fechar aviso" data-fechar-aviso>&times;</button>
            </div>
        <?php endif; ?>
        <?php if (!empty($mensagemSucesso)): ?>
            <div class="aviso sucesso aviso-flutuante" data-aviso>
                <span><?= AsiloView::escape($mensagemSucesso) ?></span><button type="button" aria-label="Fechar aviso" data-fechar-aviso>&times;</button>
            </div>
        <?php endif; ?>
    </div>
    <main class="conteudo">
        <?php require $arquivoDaView; ?>
    </main>
    <script src="src/assets/js/asilosoft.js"></script>
</body>
</html>

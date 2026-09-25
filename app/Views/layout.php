<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'AsiloSoft') ?> | AsiloSoft</title>
    <link rel="stylesheet" href="/css/estilo.css">
</head>
<body>
    <header class="cabecalho">
        <a class="marca" href="/usuarios">AsiloSoft</a>
        <?php if (isset($usuarioAtual)): ?>
            <nav class="navegacao" aria-label="Navegação principal">
                <a class="<?= ($secaoAtual ?? '') === 'usuarios' ? 'atual' : '' ?>" href="/usuarios">Usuários</a>
                <a class="<?= ($secaoAtual ?? '') === 'idosos' ? 'atual' : '' ?>" href="/idosos">Idosos</a>
            </nav>
            <div class="usuario-logado">
                <span><?= e($usuarioAtual['nome']) ?></span>
                <form method="post" action="/sair">
                    <input type="hidden" name="csrf" value="<?= e($csrf ?? '') ?>">
                    <button class="botao-link" type="submit">Sair</button>
                </form>
            </div>
        <?php endif; ?>
    </header>
    <div class="avisos-flutuantes" aria-live="polite">
        <?php if (!empty($mensagemErro)): ?>
            <div class="aviso erro aviso-flutuante" data-aviso>
                <span><?= e($mensagemErro) ?></span><button type="button" aria-label="Fechar aviso" data-fechar-aviso>&times;</button>
            </div>
        <?php endif; ?>
        <?php if (!empty($mensagemSucesso)): ?>
            <div class="aviso sucesso aviso-flutuante" data-aviso>
                <span><?= e($mensagemSucesso) ?></span><button type="button" aria-label="Fechar aviso" data-fechar-aviso>&times;</button>
            </div>
        <?php endif; ?>
    </div>
    <main class="conteudo">
        <?php if ($modoDemonstracao): ?><p class="aviso demonstracao">Modo demonstração: os dados deste acesso não são permanentes.</p><?php endif; ?>
        <?php require $arquivoDaView; ?>
    </main>
    <script src="/js/aplicacao.js"></script>
</body>
</html>

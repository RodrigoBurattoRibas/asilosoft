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
            <div class="usuario-logado">
                <span><?= e($usuarioAtual['nome']) ?></span>
                <form method="post" action="/sair">
                    <input type="hidden" name="csrf" value="<?= e($csrf ?? '') ?>">
                    <button class="botao-link" type="submit">Sair</button>
                </form>
            </div>
        <?php endif; ?>
    </header>
    <main class="conteudo">
        <?php if ($modoDemonstracao): ?><p class="aviso demonstracao">Modo demonstração: os dados deste acesso não são permanentes.</p><?php endif; ?>
        <?php if (!empty($mensagemErro)): ?><p class="aviso erro"><?= e($mensagemErro) ?></p><?php endif; ?>
        <?php if (!empty($mensagemSucesso)): ?><p class="aviso sucesso"><?= e($mensagemSucesso) ?></p><?php endif; ?>
        <?php require $arquivoDaView; ?>
    </main>
    <script src="/js/aplicacao.js"></script>
</body>
</html>

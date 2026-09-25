<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Cadastro</p>
        <h1>Idosos</h1>
    </div>
    <a class="botao" href="/idosos/novo">Cadastrar idoso</a>
</div>
<section class="cartao tabela-responsiva">
    <table>
        <thead><tr><th>Nome</th><th>CPF</th><th>Identificador</th><th>Quarto</th></tr></thead>
        <tbody>
        <?php if ($idosos === []): ?>
            <tr><td colspan="4" class="texto-suave">Nenhum idoso cadastrado até o momento.</td></tr>
        <?php else: ?>
            <?php foreach ($idosos as $idoso): ?>
                <tr>
                    <td><?= e($idoso['nome']) ?></td>
                    <td><?= e($idoso['cpf']) ?></td>
                    <td><?= e($idoso['identificador']) ?></td>
                    <td><?= e($idoso['quarto_codigo']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>

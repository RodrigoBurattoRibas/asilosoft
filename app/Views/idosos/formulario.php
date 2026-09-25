<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Cadastro</p>
        <h1>Novo idoso</h1>
    </div>
    <a href="/idosos">Voltar</a>
</div>
<section class="cartao">
    <?php if ($quartos === []): ?>
        <p class="texto-suave">Não há quartos com vagas disponíveis no momento.</p>
    <?php else: ?>
        <form method="post" action="/idosos" class="formulario formulario-largo">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <label>Nome completo
                <input type="text" name="nome" required autocomplete="name">
            </label>
            <label>CPF
                <input type="text" name="cpf" required inputmode="numeric" maxlength="14" placeholder="000.000.000-00">
            </label>
            <label>Identificador
                <input type="text" name="identificador" required maxlength="60" placeholder="Ex.: IDOSO-001">
            </label>
            <label>Quarto
                <select name="quarto_id" required>
                    <option value="">Selecione um quarto</option>
                    <?php foreach ($quartos as $quarto): ?>
                        <option value="<?= e($quarto['id']) ?>">
                            <?= e($quarto['codigo']) ?> — <?= e($quarto['ocupacao']) ?>/<?= e($quarto['capacidade']) ?> ocupação
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit">Cadastrar idoso</button>
        </form>
    <?php endif; ?>
</section>

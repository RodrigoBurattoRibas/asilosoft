<section class="cartao cartao-login">
    <p class="sobretitulo">Acesso ao sistema</p>
    <h1>Bem-vindo ao AsiloSoft</h1>
    <p class="texto-suave">Entre com seu e-mail e sua senha.</p>
    <form method="post" action="/login" class="formulario">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <label>E-mail
            <input type="email" name="email" autocomplete="email" required>
        </label>
        <label>Senha
            <input type="password" name="senha" autocomplete="current-password" required>
        </label>
        <button type="submit">Entrar</button>
    </form>
</section>

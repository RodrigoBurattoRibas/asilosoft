<div class="titulo-pagina">
    <div>
        <p class="sobretitulo">Administração</p>
        <h1>Usuários</h1>
    </div>
    <a class="botao" href="/usuarios/novo">Cadastrar usuário</a>
</div>
<section class="cartao tabela-responsiva">
    <table>
        <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Situação</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= e($usuario['nome']) ?></td>
                <td><?= e($usuario['email']) ?></td>
                <td><?= e($usuario['perfil']) ?></td>
                <td><span class="etiqueta <?= $usuario['ativo'] ? 'ativo' : 'inativo' ?>"><?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?></span></td>
                <td class="acoes">
                    <a href="/usuarios/<?= e($usuario['id']) ?>/editar">Editar</a>
                    <form method="post" action="/usuarios/<?= e($usuario['id']) ?>/status" data-confirmacao="Confirma a alteração da situação deste usuário?">
                        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                        <input type="hidden" name="ativo" value="<?= $usuario['ativo'] ? '0' : '1' ?>">
                        <button class="botao-link" type="submit"><?= $usuario['ativo'] ? 'Desativar' : 'Ativar' ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

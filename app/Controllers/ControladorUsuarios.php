<?php

declare(strict_types=1);

final class ControladorUsuarios
{
    public function __construct(
        private object $repositorio,
        private UsuarioService $usuarios,
        private AutenticacaoService $autenticacao,
        private Sessao $sessao,
        private FiltroDeSituacao $filtro,
        private Csrf $csrf
    ) {
    }

    public function listar(): void
    {
        $usuarioAtual = $this->exigirAdministrador();
        $situacao = $this->filtro->normalizar((string) ($_GET['situacao'] ?? 'ativos'));
        renderizar('usuarios/lista', [
            'titulo' => 'Usuários',
            'usuarioAtual' => $usuarioAtual,
            'usuarios' => $this->filtro->filtrar($this->repositorio->listar(), $situacao),
            'situacao' => $situacao,
            'secaoAtual' => 'usuarios',
            'csrf' => $this->csrf->token(),
        ]);
    }

    public function novo(): void
    {
        $usuarioAtual = $this->exigirAdministrador();
        renderizar('usuarios/formulario', [
            'titulo' => 'Novo usuário',
            'usuarioAtual' => $usuarioAtual,
            'usuario' => null,
            'secaoAtual' => 'usuarios',
            'csrf' => $this->csrf->token(),
        ]);
    }

    public function criar(): void
    {
        $this->exigirAdministrador();
        validarCsrf($this->csrf);
        try {
            $this->usuarios->criar($_POST);
            $this->sessao->mensagem('sucesso', 'Usuário cadastrado com sucesso.');
            redirecionar('/usuarios');
        } catch (DomainException $erro) {
            $this->sessao->mensagem('erro', $erro->getMessage());
            redirecionar('/usuarios/novo');
        }
    }

    public function editar(int $id): void
    {
        $usuarioAtual = $this->exigirAdministrador();
        $usuario = $this->repositorio->buscarPorId($id);
        if ($usuario === null) {
            respostaNaoEncontrada();
        }
        renderizar('usuarios/formulario', [
            'titulo' => 'Editar usuário',
            'usuarioAtual' => $usuarioAtual,
            'usuario' => $usuario,
            'secaoAtual' => 'usuarios',
            'csrf' => $this->csrf->token(),
        ]);
    }

    public function atualizar(int $id): void
    {
        $this->exigirAdministrador();
        validarCsrf($this->csrf);
        try {
            $this->usuarios->atualizar($id, $_POST);
            $this->sessao->mensagem('sucesso', 'Dados do usuário atualizados.');
            redirecionar('/usuarios');
        } catch (DomainException $erro) {
            $this->sessao->mensagem('erro', $erro->getMessage());
            redirecionar('/usuarios/' . $id . '/editar');
        }
    }

    public function alterarStatus(int $id): void
    {
        $this->exigirAdministrador();
        validarCsrf($this->csrf);
        try {
            $this->usuarios->alterarStatus($id, (string) ($_POST['ativo'] ?? '') === '1');
            $this->sessao->mensagem('sucesso', 'Situação do usuário atualizada.');
        } catch (DomainException $erro) {
            $this->sessao->mensagem('erro', $erro->getMessage());
        }
        redirecionar('/usuarios');
    }

    private function exigirAdministrador(): array
    {
        $usuario = $this->autenticacao->usuarioAtual();
        if ($usuario === null) {
            redirecionar('/login');
        }
        if ($usuario['perfil'] !== 'Administrador') {
            http_response_code(403);
            renderizar('acesso_negado', ['titulo' => 'Acesso negado', 'usuarioAtual' => $usuario]);
            exit;
        }

        return $usuario;
    }
}

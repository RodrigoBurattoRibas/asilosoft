<?php

declare(strict_types=1);

final class ControladorIdosos
{
    public function __construct(
        private object $repositorio,
        private object $repositorioQuartos,
        private IdosoService $idosos,
        private AutenticacaoService $autenticacao,
        private Sessao $sessao,
        private Csrf $csrf
    ) {
    }

    public function listar(): void
    {
        $usuarioAtual = $this->exigirAdministrador();
        renderizar('idosos/lista', [
            'titulo' => 'Idosos',
            'usuarioAtual' => $usuarioAtual,
            'idosos' => $this->repositorio->listar(),
            'csrf' => $this->csrf->token(),
        ]);
    }

    public function novo(): void
    {
        $usuarioAtual = $this->exigirAdministrador();
        renderizar('idosos/formulario', [
            'titulo' => 'Cadastrar idoso',
            'usuarioAtual' => $usuarioAtual,
            'quartos' => $this->repositorioQuartos->listarDisponiveis(),
            'csrf' => $this->csrf->token(),
        ]);
    }

    public function criar(): void
    {
        $this->exigirAdministrador();
        validarCsrf($this->csrf);

        try {
            $this->idosos->criar($_POST);
            $this->sessao->mensagem('sucesso', 'Idoso cadastrado com sucesso.');
            redirecionar('/idosos');
        } catch (DomainException $erro) {
            $this->sessao->mensagem('erro', $erro->getMessage());
            redirecionar('/idosos/novo');
        }
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

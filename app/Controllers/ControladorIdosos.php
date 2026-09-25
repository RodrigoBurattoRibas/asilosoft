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
            'idoso' => null,
            'csrf' => $this->csrf->token(),
        ]);
    }

    public function editar(int $id): void
    {
        $usuarioAtual = $this->exigirAdministrador();
        $idoso = $this->repositorio->buscarPorId($id);
        if ($idoso === null) {
            respostaNaoEncontrada();
        }

        $quartos = $this->repositorioQuartos->listarDisponiveis();
        $quartoAtualEstaNaLista = in_array($idoso['quarto_id'], array_column($quartos, 'id'), true);
        if (!$quartoAtualEstaNaLista) {
            $quartoAtual = $this->repositorio->buscarQuartoPorId($idoso['quarto_id']);
            if ($quartoAtual !== null) {
                $quartoAtual['ocupacao'] = $this->repositorio->contarOcupacaoDoQuarto($idoso['quarto_id']);
                $quartos[] = $quartoAtual;
            }
        }

        renderizar('idosos/formulario', [
            'titulo' => 'Editar idoso',
            'usuarioAtual' => $usuarioAtual,
            'idoso' => $idoso,
            'quartos' => $quartos,
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

    public function atualizar(int $id): void
    {
        $this->exigirAdministrador();
        validarCsrf($this->csrf);

        try {
            $this->idosos->atualizar($id, $_POST);
            $this->sessao->mensagem('sucesso', 'Dados do idoso atualizados.');
            redirecionar('/idosos');
        } catch (DomainException $erro) {
            $this->sessao->mensagem('erro', $erro->getMessage());
            redirecionar('/idosos/' . $id . '/editar');
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

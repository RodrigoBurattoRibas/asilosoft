<?php

final class AsiloQuartoService
{
    public function __construct(private object $repository)
    {
    }

    public function listar(): array
    {
        return $this->repository->listar();
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->repository->buscarPorId($id);
    }

    public function criar(array $dados): array
    {
        $quarto = $this->validarDados($dados);
        if ($this->repository->buscarPorCodigo($quarto['codigo']) !== null) {
            throw new DomainException('Já existe um quarto com este código.');
        }

        return $this->repository->criar($quarto + ['ativo' => true]);
    }

    public function atualizar(int $id, array $dados): array
    {
        if ($this->repository->buscarPorId($id) === null) {
            throw new DomainException('Quarto não encontrado.');
        }

        $quarto = $this->validarDados($dados);
        $existing = $this->repository->buscarPorCodigo($quarto['codigo']);
        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new DomainException('Já existe um quarto com este código.');
        }

        $ocupacao = $this->repository->contarOcupacao($id);
        if ($quarto['capacidade'] < $ocupacao) {
            throw new DomainException(
                "A capacidade não pode ser menor que a ocupação atual ({$ocupacao})."
            );
        }

        return $this->repository->atualizar($id, $quarto);
    }

    public function alterarStatus(int $id, bool $ativo): array
    {
        if ($this->repository->buscarPorId($id) === null) {
            throw new DomainException('Quarto não encontrado.');
        }

        if (!$ativo && $this->repository->contarOcupacao($id) > 0) {
            throw new DomainException('Retire os idosos antes de desativar este quarto.');
        }

        return $this->repository->atualizar($id, ['ativo' => $ativo]);
    }

    private function validarDados(array $dados): array
    {
        $codigo = trim((string) ($dados['codigo'] ?? ''));
        $capacidade = filter_var($dados['capacidade'] ?? null, FILTER_VALIDATE_INT);

        if ($codigo === '' || mb_strlen($codigo) > 30) {
            throw new DomainException('Informe um código de quarto com até 30 caracteres.');
        }

        if ($capacidade === false || $capacidade < 1 || $capacidade > 255) {
            throw new DomainException('A capacidade deve ser um número inteiro entre 1 e 255.');
        }

        return [
            'codigo' => $codigo,
            'capacidade' => $capacidade,
        ];
    }
}

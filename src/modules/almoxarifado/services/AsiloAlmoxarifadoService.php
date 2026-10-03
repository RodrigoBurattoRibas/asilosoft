<?php

final class AsiloAlmoxarifadoService
{
    private const TIPOS = ['Material', 'Insumo', 'Medicamento'];
    private const ORIGENS = ['SUS', 'Plano de Saúde', 'Instituição'];

    public function __construct(private object $repository)
    {
    }

    public function listarItens(): array
    {
        return $this->repository->listarItens();
    }

    public function buscarItemPorId(int $id): ?array
    {
        return $this->repository->buscarItemPorId($id);
    }

    public function listarMovimentacoes(): array
    {
        return $this->repository->listarMovimentacoes();
    }

    public function tiposPermitidos(): array
    {
        return self::TIPOS;
    }

    public function origensPermitidas(): array
    {
        return self::ORIGENS;
    }

    public function criarItem(array $dados): array
    {
        $item = $this->validarItem($dados);
        if ($this->repository->buscarItemPorNome($item['nome']) !== null) {
            throw new DomainException('Já existe um item com este nome.');
        }

        return $this->repository->criarItem($item);
    }

    public function atualizarItem(int $id, array $dados): array
    {
        if ($this->repository->buscarItemPorId($id) === null) {
            throw new DomainException('Item não encontrado.');
        }

        $item = $this->validarItem($dados);
        $existing = $this->repository->buscarItemPorNome($item['nome']);
        if ($existing !== null && $existing['id'] !== $id) {
            throw new DomainException('Já existe um item com este nome.');
        }

        return $this->repository->atualizarItem($id, $item);
    }

    public function registrarEntrada(int $itemId, array $dados): array
    {
        $item = $this->repository->buscarItemPorId($itemId);
        if ($item === null) {
            throw new DomainException('Item não encontrado.');
        }

        $quantidade = $this->validarQuantidade($dados['quantidade'] ?? null);
        $origem = trim((string) ($dados['origem'] ?? ''));
        if (!in_array($origem, self::ORIGENS, true)) {
            throw new DomainException('Informe uma origem válida para a entrada.');
        }

        return $this->repository->registrarEntrada($itemId, $quantidade, $origem);
    }

    public function registrarRetirada(int $itemId, array $dados): array
    {
        $item = $this->repository->buscarItemPorId($itemId);
        if ($item === null) {
            throw new DomainException('Item não encontrado.');
        }

        $quantidade = $this->validarQuantidade($dados['quantidade'] ?? null);
        if ($quantidade > $item['quantidade_disponivel']) {
            throw new DomainException('A retirada não pode deixar o estoque negativo.');
        }

        return $this->repository->registrarRetirada($itemId, $quantidade);
    }

    private function validarItem(array $dados): array
    {
        $nome = trim((string) ($dados['nome'] ?? ''));
        $tipo = trim((string) ($dados['tipo'] ?? ''));
        $unidade = trim((string) ($dados['unidade_medida'] ?? ''));

        if ($nome === '' || mb_strlen($nome) > 120) {
            throw new DomainException('Informe um nome de item com até 120 caracteres.');
        }

        if (!in_array($tipo, self::TIPOS, true)) {
            throw new DomainException('Selecione um tipo de item válido.');
        }

        if ($unidade === '' || mb_strlen($unidade) > 30) {
            throw new DomainException('Informe uma unidade de medida com até 30 caracteres.');
        }

        return ['nome' => $nome, 'tipo' => $tipo, 'unidade_medida' => $unidade];
    }

    private function validarQuantidade(mixed $quantidade): int
    {
        $quantidade = filter_var($quantidade, FILTER_VALIDATE_INT);
        if (!is_int($quantidade) || $quantidade <= 0) {
            throw new DomainException('A quantidade deve ser um número inteiro maior que zero.');
        }

        return $quantidade;
    }
}

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
            throw new DomainException('Já existe um material com este nome.');
        }

        return $this->repository->criarItem($item);
    }

    public function atualizarItem(int $id, array $dados): array
    {
        if ($this->repository->buscarItemPorId($id) === null) {
            throw new DomainException('Material não encontrado.');
        }

        $item = $this->validarItem($dados);
        $existing = $this->repository->buscarItemPorNome($item['nome']);
        if ($existing !== null && $existing['id'] !== $id) {
            throw new DomainException('Já existe um material com este nome.');
        }

        return $this->repository->atualizarItem($id, $item);
    }

    public function registrarEntrada(int $itemId, array $dados, int $usuarioId): array
    {
        $this->garantirItem($itemId);
        $quantidade = $this->validarQuantidade($dados['quantidade'] ?? null);
        $origem = trim((string) ($dados['origem'] ?? ''));
        if (!in_array($origem, self::ORIGENS, true)) {
            throw new DomainException('Informe uma origem válida para a entrada.');
        }

        return $this->repository->registrarEntrada(
            $itemId,
            $quantidade,
            $origem,
            $usuarioId,
            $this->validarObservacao($dados['observacao'] ?? null)
        );
    }

    public function registrarRetirada(int $itemId, array $dados, int $usuarioId): array
    {
        $item = $this->garantirItem($itemId);
        $quantidade = $this->validarQuantidade($dados['quantidade'] ?? null);
        if ($quantidade > $item['quantidade_disponivel']) {
            throw new DomainException('A retirada não pode deixar o estoque negativo.');
        }

        return $this->repository->registrarRetirada(
            $itemId,
            $quantidade,
            $usuarioId,
            $this->validarObservacao($dados['observacao'] ?? null)
        );
    }

    public function listarIdososAtivos(): array
    {
        return $this->repository->listarIdososAtivos();
    }

    public function registrarUso(array $dados, int $usuarioId): array
    {
        $idosoId = filter_var($dados['idoso_id'] ?? null, FILTER_VALIDATE_INT);
        $materialId = filter_var($dados['material_id'] ?? null, FILTER_VALIDATE_INT);
        $chave = $dados['chave_requisicao'] ?? '';
        if (!$idosoId || $idosoId < 1 || !$materialId || $materialId < 1 || $usuarioId < 1) {
            throw new DomainException('Selecione idoso e material válidos.');
        }
        if (!is_string($chave) || !preg_match('/^[a-f0-9]{64}$/D', $chave)) {
            throw new DomainException('Formulário inválido. Abra um novo formulário de consumo.');
        }
        return $this->repository->registrarUso($idosoId, $materialId,
            self::validarQuantidade($dados['quantidade'] ?? null), $usuarioId,
            $this->validarObservacao($dados['observacao'] ?? null), $chave);
    }

    private function garantirItem(int $id): array
    {
        return $this->repository->buscarItemPorId($id)
            ?? throw new DomainException('Material não encontrado.');
    }

    private function validarItem(array $dados): array
    {
        $nome = trim((string) ($dados['nome'] ?? ''));
        $tipo = trim((string) ($dados['tipo'] ?? ''));
        $descricao = trim((string) ($dados['descricao'] ?? ''));
        $unidade = trim((string) ($dados['unidade_medida'] ?? ''));
        $estoqueMinimo = $this->validarDecimal(
            $dados['estoque_minimo'] ?? '0',
            'O estoque mínimo deve ser um número maior ou igual a zero.',
            true
        );

        if ($nome === '' || mb_strlen($nome) > 160) {
            throw new DomainException('Informe um nome de material com até 160 caracteres.');
        }

        if (!in_array($tipo, self::TIPOS, true)) {
            throw new DomainException('Selecione um tipo de material válido.');
        }

        if (mb_strlen($descricao) > 500) {
            throw new DomainException('A descrição deve ter até 500 caracteres.');
        }

        if ($unidade === '' || mb_strlen($unidade) > 30) {
            throw new DomainException('Informe uma unidade de medida com até 30 caracteres.');
        }

        return [
            'nome' => $nome,
            'tipo' => $tipo,
            'descricao' => $descricao === '' ? null : $descricao,
            'unidade_medida' => $unidade,
            'estoque_minimo' => $estoqueMinimo,
        ];
    }

    private function validarObservacao(mixed $observacao): ?string
    {
        if ($observacao !== null && !is_string($observacao)) {
            throw new DomainException('Observação inválida.');
        }
        $observacao = trim((string) ($observacao ?? ''));
        if (mb_strlen($observacao) > 500) {
            throw new DomainException('A observação deve ter até 500 caracteres.');
        }

        return $observacao === '' ? null : $observacao;
    }

    public static function validarQuantidade(mixed $quantidade): string
    {
        return self::validarDecimal(
            $quantidade,
            'A quantidade deve ser um número maior que zero.',
            false
        );
    }

    private static function validarDecimal(mixed $value, string $message, bool $zeroAllowed): string
    {
        if (!is_scalar($value)) {
            throw new DomainException($message);
        }

        $value = trim(str_replace(',', '.', (string) $value));
        if (!preg_match('/^\d+(?:\.\d{1,3})?$/', $value)) {
            throw new DomainException($message);
        }

        $number = round((float) $value, 3);
        if ($number > 999999999.999 || $number < 0 || (!$zeroAllowed && $number <= 0)) {
            throw new DomainException($message);
        }

        return number_format($number, 3, '.', '');
    }
}

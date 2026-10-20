<?php
require_once dirname(__DIR__, 2) . '/almoxarifado/services/AsiloAlmoxarifadoService.php';
final class AsiloCompraService
{
    public function __construct(private object $repository) {}

    public static function transicoes(string $situacao): array
    {
        return match ($situacao) {
            'Pendente' => ['Aprovada', 'Recusada'],
            'Aprovada' => ['Atendida'],
            default => [],
        };
    }

    public function listar(): array { return $this->repository->listar(); }
    public function materiais(): array { return $this->repository->materiais(); }

    public function criar(array $dados, int $usuarioId): int
    {
        $materialId = filter_var($dados['material_id'] ?? null, FILTER_VALIDATE_INT);
        $observacao = $dados['observacao'] ?? '';
        if (!is_string($observacao)) throw new DomainException('Justificativa inválida.');
        $observacao = trim($observacao);
        if (!$materialId || $materialId < 1 || $usuarioId < 1 || $observacao === '' || mb_strlen($observacao) > 500) {
            throw new DomainException('Informe material válido e justificativa com até 500 caracteres.');
        }
        return $this->repository->criar($materialId, $usuarioId,
            AsiloAlmoxarifadoService::validarQuantidade($dados['quantidade'] ?? null), $observacao);
    }

    public function alterarSituacao(int $id, string $origem, string $destino): void
    {
        if ($id < 1 || !in_array($destino, self::transicoes($origem), true)) {
            throw new DomainException('Transição de situação inválida.');
        }
        $this->repository->alterarSituacao($id, $origem, $destino);
    }
}

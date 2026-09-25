<?php

declare(strict_types=1);

final class RepositorioIdososDeSessao
{
    private const CHAVE_IDOSOS = 'idosos_demonstracao';
    private const CHAVE_QUARTOS = 'quartos_demonstracao';

    public function __construct(private object $sessao)
    {
        if (!is_array($this->sessao->obter(self::CHAVE_QUARTOS))) {
            $this->sessao->definir(self::CHAVE_QUARTOS, [
                ['id' => 1, 'codigo' => 'A-101', 'capacidade' => 2, 'ativo' => true],
                ['id' => 2, 'codigo' => 'A-102', 'capacidade' => 1, 'ativo' => true],
            ]);
        }

        if (!is_array($this->sessao->obter(self::CHAVE_IDOSOS))) {
            $this->sessao->definir(self::CHAVE_IDOSOS, []);
        }
    }

    public function buscarPorCpf(string $cpf): ?array
    {
        foreach ($this->idosos() as $idoso) {
            if ($idoso['cpf'] === $cpf) {
                return $idoso;
            }
        }

        return null;
    }

    public function buscarPorIdentificador(string $identificador): ?array
    {
        foreach ($this->idosos() as $idoso) {
            if ($idoso['identificador'] === $identificador) {
                return $idoso;
            }
        }

        return null;
    }

    public function buscarQuartoPorId(int $id): ?array
    {
        foreach ($this->quartos() as $quarto) {
            if ($quarto['id'] === $id) {
                return $quarto;
            }
        }

        return null;
    }

    public function contarOcupacaoDoQuarto(int $quartoId): int
    {
        return count(array_filter(
            $this->idosos(),
            static fn (array $idoso): bool => $idoso['quarto_id'] === $quartoId
        ));
    }

    public function criar(array $dados): array
    {
        $idosos = $this->idosos();
        $ids = array_column($idosos, 'id');
        $idoso = $dados + ['id' => $ids === [] ? 1 : max($ids) + 1];
        $idosos[] = $idoso;
        $this->salvarIdosos($idosos);

        return $idoso;
    }

    public function listar(): array
    {
        $idosos = array_map(function (array $idoso): array {
            $quarto = $this->buscarQuartoPorId($idoso['quarto_id']);
            $idoso['quarto_codigo'] = $quarto['codigo'] ?? 'Não informado';

            return $idoso;
        }, $this->idosos());
        usort($idosos, static fn (array $primeiro, array $segundo): int => $primeiro['nome'] <=> $segundo['nome']);

        return $idosos;
    }

    public function listarDisponiveis(): array
    {
        $quartos = [];
        foreach ($this->quartos() as $quarto) {
            $ocupacao = $this->contarOcupacaoDoQuarto($quarto['id']);
            if ($quarto['ativo'] && $ocupacao < $quarto['capacidade']) {
                $quarto['ocupacao'] = $ocupacao;
                $quartos[] = $quarto;
            }
        }
        usort($quartos, static fn (array $primeiro, array $segundo): int => $primeiro['codigo'] <=> $segundo['codigo']);

        return $quartos;
    }

    private function idosos(): array
    {
        return $this->sessao->obter(self::CHAVE_IDOSOS);
    }

    private function quartos(): array
    {
        return $this->sessao->obter(self::CHAVE_QUARTOS);
    }

    private function salvarIdosos(array $idosos): void
    {
        $this->sessao->definir(self::CHAVE_IDOSOS, $idosos);
    }
}

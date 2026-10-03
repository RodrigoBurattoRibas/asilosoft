<?php

final class AsiloRepositorioAlmoxarifadoSessao
{
    private const ITEMS_KEY = 'asilosoft_almoxarifado_itens_demonstracao';
    private const MOVEMENTS_KEY = 'asilosoft_almoxarifado_movimentacoes_demonstracao';

    public function __construct(private object $session)
    {
        if (!is_array($this->session->get(self::ITEMS_KEY))) {
            $this->session->set(self::ITEMS_KEY, []);
        }

        if (!is_array($this->session->get(self::MOVEMENTS_KEY))) {
            $this->session->set(self::MOVEMENTS_KEY, []);
        }
    }

    public function listarItens(): array
    {
        $items = $this->itens();
        usort($items, static fn (array $first, array $second): int => $first['nome'] <=> $second['nome']);

        return $items;
    }

    public function buscarItemPorId(int $id): ?array
    {
        foreach ($this->itens() as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }

    public function buscarItemPorNome(string $nome): ?array
    {
        foreach ($this->itens() as $item) {
            if (mb_strtolower($item['nome']) === mb_strtolower($nome)) {
                return $item;
            }
        }

        return null;
    }

    public function criarItem(array $dados): array
    {
        $items = $this->itens();
        $ids = array_column($items, 'id');
        $item = $dados + [
            'id' => $ids === [] ? 1 : max($ids) + 1,
            'quantidade_disponivel' => 0,
        ];
        $items[] = $item;
        $this->salvarItens($items);

        return $item;
    }

    public function atualizarItem(int $id, array $dados): array
    {
        $items = $this->itens();
        foreach ($items as $index => $item) {
            if ($item['id'] === $id) {
                $items[$index] = array_merge($item, $dados);
                $this->salvarItens($items);

                return $items[$index];
            }
        }

        throw new DomainException('Item não encontrado.');
    }

    public function registrarEntrada(int $itemId, int $quantidade, string $origem): array
    {
        $item = $this->buscarItemPorId($itemId);
        if ($item === null) {
            throw new DomainException('Item não encontrado.');
        }

        $item = $this->atualizarItem($itemId, [
            'quantidade_disponivel' => $item['quantidade_disponivel'] + $quantidade,
        ]);
        $this->adicionarMovimentacao($item, 'Entrada', $quantidade, $origem);

        return $item;
    }

    public function registrarRetirada(int $itemId, int $quantidade): array
    {
        $item = $this->buscarItemPorId($itemId);
        if ($item === null) {
            throw new DomainException('Item não encontrado.');
        }

        if ($item['quantidade_disponivel'] < $quantidade) {
            throw new DomainException('A retirada não pode deixar o estoque negativo.');
        }

        $item = $this->atualizarItem($itemId, [
            'quantidade_disponivel' => $item['quantidade_disponivel'] - $quantidade,
        ]);
        $this->adicionarMovimentacao($item, 'Retirada', $quantidade, null);

        return $item;
    }

    public function listarMovimentacoes(): array
    {
        $movements = $this->session->get(self::MOVEMENTS_KEY);
        usort($movements, static fn (array $first, array $second): int => $second['id'] <=> $first['id']);

        return $movements;
    }

    private function adicionarMovimentacao(
        array $item,
        string $tipo,
        int $quantidade,
        ?string $origem
    ): void {
        $movements = $this->session->get(self::MOVEMENTS_KEY);
        $ids = array_column($movements, 'id');
        $movements[] = [
            'id' => $ids === [] ? 1 : max($ids) + 1,
            'item_id' => $item['id'],
            'item_nome' => $item['nome'],
            'unidade_medida' => $item['unidade_medida'],
            'tipo' => $tipo,
            'quantidade' => $quantidade,
            'origem' => $origem,
            'data_hora' => date('Y-m-d H:i:s'),
        ];
        $this->session->set(self::MOVEMENTS_KEY, $movements);
    }

    private function itens(): array
    {
        return $this->session->get(self::ITEMS_KEY);
    }

    private function salvarItens(array $items): void
    {
        $this->session->set(self::ITEMS_KEY, $items);
    }
}

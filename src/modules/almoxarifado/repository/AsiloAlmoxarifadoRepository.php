<?php

final class AsiloAlmoxarifadoRepository
{
    public function __construct(private PDO $pdo)
    {
        $this->garantirEstrutura();
    }

    public function listarItens(): array
    {
        $query = $this->pdo->query(
            'SELECT id, nome, tipo, quantidade_disponivel, unidade_medida
             FROM itens_estoque
             ORDER BY nome ASC'
        );

        return array_map(fn (array $item): array => $this->mapearItem($item), $query->fetchAll());
    }

    public function buscarItemPorId(int $id): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT id, nome, tipo, quantidade_disponivel, unidade_medida
             FROM itens_estoque
             WHERE id = :id
             LIMIT 1'
        );
        $query->execute(['id' => $id]);
        $item = $query->fetch();

        return $item === false ? null : $this->mapearItem($item);
    }

    public function buscarItemPorNome(string $nome): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT id, nome, tipo, quantidade_disponivel, unidade_medida
             FROM itens_estoque
             WHERE nome = :nome
             LIMIT 1'
        );
        $query->execute(['nome' => $nome]);
        $item = $query->fetch();

        return $item === false ? null : $this->mapearItem($item);
    }

    public function criarItem(array $dados): array
    {
        $query = $this->pdo->prepare(
            'INSERT INTO itens_estoque (nome, tipo, unidade_medida)
             VALUES (:nome, :tipo, :unidade_medida)'
        );
        $query->execute($dados);

        return $this->buscarItemPorId((int) $this->pdo->lastInsertId())
            ?? throw new RuntimeException('Não foi possível carregar o item cadastrado.');
    }

    public function atualizarItem(int $id, array $dados): array
    {
        $query = $this->pdo->prepare(
            'UPDATE itens_estoque
             SET nome = :nome, tipo = :tipo, unidade_medida = :unidade_medida
             WHERE id = :id'
        );
        $query->execute($dados + ['id' => $id]);

        return $this->buscarItemPorId($id)
            ?? throw new DomainException('Item não encontrado.');
    }

    public function registrarEntrada(int $itemId, int $quantidade, string $origem): array
    {
        $this->pdo->beginTransaction();

        try {
            $item = $this->buscarItemParaAtualizacao($itemId);
            if ($item === null) {
                throw new DomainException('Item não encontrado.');
            }

            $this->atualizarQuantidade($itemId, $item['quantidade_disponivel'] + $quantidade);
            $this->registrarMovimentacao($itemId, 'Entrada', $quantidade, $origem);
            $this->pdo->commit();
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $error;
        }

        return $this->buscarItemPorId($itemId)
            ?? throw new DomainException('Item não encontrado.');
    }

    public function registrarRetirada(int $itemId, int $quantidade): array
    {
        $this->pdo->beginTransaction();

        try {
            $item = $this->buscarItemParaAtualizacao($itemId);
            if ($item === null) {
                throw new DomainException('Item não encontrado.');
            }

            if ($item['quantidade_disponivel'] < $quantidade) {
                throw new DomainException('A retirada não pode deixar o estoque negativo.');
            }

            $this->atualizarQuantidade($itemId, $item['quantidade_disponivel'] - $quantidade);
            $this->registrarMovimentacao($itemId, 'Retirada', $quantidade, null);
            $this->pdo->commit();
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $error;
        }

        return $this->buscarItemPorId($itemId)
            ?? throw new DomainException('Item não encontrado.');
    }

    public function listarMovimentacoes(): array
    {
        $query = $this->pdo->query(
            'SELECT m.id, m.item_id, i.nome AS item_nome, i.unidade_medida,
                    m.tipo, m.quantidade, m.origem, m.data_hora
             FROM movimentacoes_estoque m
             INNER JOIN itens_estoque i ON i.id = m.item_id
             ORDER BY m.data_hora DESC, m.id DESC'
        );

        return array_map(static function (array $movimentacao): array {
            $movimentacao['id'] = (int) $movimentacao['id'];
            $movimentacao['item_id'] = (int) $movimentacao['item_id'];
            $movimentacao['quantidade'] = (int) $movimentacao['quantidade'];

            return $movimentacao;
        }, $query->fetchAll());
    }

    private function buscarItemParaAtualizacao(int $id): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT id, nome, tipo, quantidade_disponivel, unidade_medida
             FROM itens_estoque
             WHERE id = :id
             FOR UPDATE'
        );
        $query->execute(['id' => $id]);
        $item = $query->fetch();

        return $item === false ? null : $this->mapearItem($item);
    }

    private function atualizarQuantidade(int $itemId, int $quantidade): void
    {
        $query = $this->pdo->prepare(
            'UPDATE itens_estoque
             SET quantidade_disponivel = :quantidade
             WHERE id = :id'
        );
        $query->execute(['quantidade' => $quantidade, 'id' => $itemId]);
    }

    private function registrarMovimentacao(
        int $itemId,
        string $tipo,
        int $quantidade,
        ?string $origem
    ): void {
        $query = $this->pdo->prepare(
            'INSERT INTO movimentacoes_estoque (item_id, tipo, quantidade, origem)
             VALUES (:item_id, :tipo, :quantidade, :origem)'
        );
        $query->execute([
            'item_id' => $itemId,
            'tipo' => $tipo,
            'quantidade' => $quantidade,
            'origem' => $origem,
        ]);
    }

    private function mapearItem(array $item): array
    {
        $item['id'] = (int) $item['id'];
        $item['quantidade_disponivel'] = (int) $item['quantidade_disponivel'];

        return $item;
    }

    private function garantirEstrutura(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS itens_estoque (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(120) NOT NULL,
                tipo VARCHAR(30) NOT NULL,
                quantidade_disponivel INT UNSIGNED NOT NULL DEFAULT 0,
                unidade_medida VARCHAR(30) NOT NULL,
                criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY itens_estoque_nome_unico (nome)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS movimentacoes_estoque (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                item_id BIGINT UNSIGNED NOT NULL,
                tipo VARCHAR(20) NOT NULL,
                quantidade INT UNSIGNED NOT NULL,
                origem VARCHAR(40) NULL,
                data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX movimentacoes_item_indice (item_id),
                INDEX movimentacoes_data_indice (data_hora),
                CONSTRAINT movimentacoes_item_fk
                    FOREIGN KEY (item_id) REFERENCES itens_estoque (id)
                    ON UPDATE CASCADE ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
}

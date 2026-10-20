<?php

final class AsiloAlmoxarifadoRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listarItens(): array
    {
        $query = $this->pdo->query(
            'SELECT m.id, m.nome, m.tipo, m.descricao, m.unidade_medida,
                    m.estoque_minimo, m.ativo,
                    e.quantidade_atual AS quantidade_disponivel
             FROM materiais m
             INNER JOIN estoques e ON e.material_id = m.id
             ORDER BY m.nome ASC'
        );

        return array_map(fn (array $item): array => $this->mapearItem($item), $query->fetchAll());
    }

    public function buscarItemPorId(int $id): ?array
    {
        $query = $this->pdo->prepare($this->consultaItem() . ' WHERE m.id = :id LIMIT 1');
        $query->execute(['id' => $id]);
        $item = $query->fetch();

        return $item === false ? null : $this->mapearItem($item);
    }

    public function buscarItemPorNome(string $nome): ?array
    {
        $query = $this->pdo->prepare($this->consultaItem() . ' WHERE m.nome = :nome LIMIT 1');
        $query->execute(['nome' => $nome]);
        $item = $query->fetch();

        return $item === false ? null : $this->mapearItem($item);
    }

    public function criarItem(array $dados): array
    {
        $query = $this->pdo->prepare(
            'INSERT INTO materiais (nome, tipo, descricao, unidade_medida, estoque_minimo)
             VALUES (:nome, :tipo, :descricao, :unidade_medida, :estoque_minimo)'
        );
        $query->execute($dados);

        return $this->buscarItemPorId((int) $this->pdo->lastInsertId())
            ?? throw new RuntimeException('Não foi possível carregar o material cadastrado.');
    }

    public function atualizarItem(int $id, array $dados): array
    {
        $query = $this->pdo->prepare(
            'UPDATE materiais
             SET nome = :nome,
                 tipo = :tipo,
                 descricao = :descricao,
                 unidade_medida = :unidade_medida,
                 estoque_minimo = :estoque_minimo
             WHERE id = :id'
        );
        $query->execute($dados + ['id' => $id]);

        return $this->buscarItemPorId($id)
            ?? throw new DomainException('Material não encontrado.');
    }

    public function registrarEntrada(
        int $itemId,
        string $quantidade,
        string $origem,
        int $usuarioId,
        ?string $observacao
    ): array {
        return $this->registrarMovimentacao(
            $itemId,
            'Entrada',
            $quantidade,
            $origem,
            $usuarioId,
            $observacao
        );
    }

    public function registrarRetirada(
        int $itemId,
        string $quantidade,
        int $usuarioId,
        ?string $observacao
    ): array {
        return $this->registrarMovimentacao(
            $itemId,
            'Saída',
            $quantidade,
            null,
            $usuarioId,
            $observacao
        );
    }

    public function listarMovimentacoes(): array
    {
        $query = $this->pdo->query(
            'SELECT me.id,
                    me.material_id AS item_id,
                    m.nome AS item_nome,
                    m.unidade_medida,
                    me.tipo,
                    me.quantidade,
                    me.origem,
                    me.movimentado_em AS data_hora,
                    me.usuario_id,
                    u.nome AS usuario_nome,
                    um.idoso_id, i.nome AS idoso_nome,
                    me.observacao
             FROM movimentacoes_estoque me
             INNER JOIN materiais m ON m.id = me.material_id
             LEFT JOIN usuarios u ON u.id = me.usuario_id
             LEFT JOIN usos_materiais um ON um.id = me.uso_material_id
             LEFT JOIN idosos i ON i.id = um.idoso_id
             ORDER BY me.movimentado_em DESC, me.id DESC'
        );

        return array_map(static function (array $movimentacao): array {
            $movimentacao['id'] = (int) $movimentacao['id'];
            $movimentacao['item_id'] = (int) $movimentacao['item_id'];
            $movimentacao['quantidade'] = (float) $movimentacao['quantidade'];

            return $movimentacao;
        }, $query->fetchAll());
    }

    private function registrarMovimentacao(
        int $itemId,
        string $tipo,
        string $quantidade,
        ?string $origem,
        int $usuarioId,
        ?string $observacao
    ): array {
        $this->pdo->beginTransaction();

        try {
            $item = $this->buscarItemParaAtualizacao($itemId);
            if ($item === null || !$item['ativo']) {
                throw new DomainException('Material não encontrado.');
            }

            if ($tipo === 'Saída' && $quantidade > $item['quantidade_disponivel']) {
                throw new DomainException('Saldo insuficiente para a retirada.');
            }
            if ($tipo === 'Entrada' && (float) $quantidade + $item['quantidade_disponivel'] > 999999999.999) {
                throw new DomainException('Entrada excede o limite do estoque.');
            }
            $query = $this->pdo->prepare(
                'INSERT INTO movimentacoes_estoque
                    (material_id, tipo, quantidade, origem, usuario_id, observacao)
                 VALUES (:material_id, :tipo, :quantidade, :origem, :usuario_id, :observacao)'
            );
            $query->execute([
                'material_id' => $itemId,
                'tipo' => $tipo,
                'quantidade' => $quantidade,
                'origem' => $origem,
                'usuario_id' => $usuarioId,
                'observacao' => $observacao,
            ]);
            $this->pdo->commit();
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $error;
        }

        return $this->buscarItemPorId($itemId)
            ?? throw new DomainException('Material não encontrado.');
    }

    public function listarIdososAtivos(): array
    {
        return $this->pdo->query('SELECT id, nome FROM idosos WHERE ativo=1 ORDER BY nome, id')->fetchAll();
    }

    public function registrarUso(int $idosoId, int $materialId, string $quantidade,
        int $usuarioId, ?string $observacao, string $chave): array
    {
        $this->pdo->beginTransaction();
        try {
            // All stock writers lock the same material/stock rows before inserting.
            $item = $this->buscarItemParaAtualizacao($materialId);
            $existing = $this->pdo->prepare('SELECT * FROM usos_materiais WHERE chave_requisicao=:chave FOR UPDATE');
            $existing->execute(['chave' => $chave]);
            $uso = $existing->fetch();
            if ($uso) {
                if ((int) $uso['usuario_id'] !== $usuarioId || (int) $uso['idoso_id'] !== $idosoId
                    || (int) $uso['material_id'] !== $materialId || $uso['quantidade'] !== $quantidade
                    || $uso['observacao'] !== $observacao) {
                    throw new DomainException('Este formulário já foi utilizado com outros dados.');
                }
                $this->pdo->commit();
                return $uso;
            }
            if (!$item || !$item['ativo']) {
                throw new DomainException('Material ativo não encontrado.');
            }
            $idoso = $this->pdo->prepare('SELECT id FROM idosos WHERE id=:id AND ativo=1 FOR UPDATE');
            $idoso->execute(['id' => $idosoId]);
            if (!$idoso->fetch()) {
                throw new DomainException('Idoso ativo não encontrado.');
            }
            if ($quantidade > $item['quantidade_disponivel']) {
                throw new DomainException('Saldo insuficiente para o consumo.');
            }
            // ai_um_registrar_saida creates exactly one movement and updates stock.
            $query = $this->pdo->prepare('INSERT INTO usos_materiais
                (idoso_id,material_id,usuario_id,quantidade,observacao,chave_requisicao)
                VALUES (:idoso,:material,:usuario,:quantidade,:observacao,:chave)');
            $query->execute(['idoso'=>$idosoId,'material'=>$materialId,'usuario'=>$usuarioId,
                'quantidade'=>$quantidade,'observacao'=>$observacao,'chave'=>$chave]);
            $id = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();
            return ['id' => $id];
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $error;
        }
    }

    private function buscarItemParaAtualizacao(int $id): ?array
    {
        $query = $this->pdo->prepare($this->consultaItem() . ' WHERE m.id = :id FOR UPDATE');
        $query->execute(['id' => $id]);
        $item = $query->fetch();

        return $item === false ? null : $this->mapearItem($item);
    }

    private function consultaItem(): string
    {
        return 'SELECT m.id, m.nome, m.tipo, m.descricao, m.unidade_medida,
                       m.estoque_minimo, m.ativo,
                       e.quantidade_atual AS quantidade_disponivel
                FROM materiais m
                INNER JOIN estoques e ON e.material_id = m.id';
    }

    private function mapearItem(array $item): array
    {
        $item['id'] = (int) $item['id'];
        $item['quantidade_disponivel'] = (float) $item['quantidade_disponivel'];
        $item['estoque_minimo'] = (float) $item['estoque_minimo'];
        $item['ativo'] = (bool) $item['ativo'];

        return $item;
    }
}

<?php
final class AsiloCompraRepository
{
    public function __construct(private PDO $pdo) {}

    public function listar(): array
    {
        return $this->pdo->query('SELECT sc.*, m.nome material_nome, u.nome solicitante
            FROM solicitacoes_compra sc JOIN materiais m ON m.id=sc.material_id
            JOIN usuarios u ON u.id=sc.usuario_solicitante_id
            ORDER BY sc.solicitada_em DESC, sc.id DESC')->fetchAll();
    }

    public function materiais(): array
    {
        return $this->pdo->query('SELECT id,nome,unidade_medida FROM materiais WHERE ativo=1 ORDER BY nome,id')->fetchAll();
    }

    public function criar(int $materialId, int $usuarioId, string $quantidade, string $observacao): int
    {
        $this->pdo->beginTransaction();
        try {
            $material = $this->pdo->prepare('SELECT id FROM materiais WHERE id=:id AND ativo=1 FOR UPDATE');
            $material->execute(['id'=>$materialId]);
            if (!$material->fetch()) throw new DomainException('Material ativo não encontrado.');
            $query = $this->pdo->prepare("INSERT INTO solicitacoes_compra
                (material_id,usuario_solicitante_id,quantidade,observacao,situacao)
                VALUES (:material,:usuario,:quantidade,:observacao,'Pendente')");
            $query->execute(['material'=>$materialId,'usuario'=>$usuarioId,'quantidade'=>$quantidade,'observacao'=>$observacao]);
            $id = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();
            return $id;
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $error;
        }
    }

    public function alterarSituacao(int $id, string $origem, string $destino): void
    {
        // Compare-and-set preserves the state displayed when the form was opened.
        $query = $this->pdo->prepare('UPDATE solicitacoes_compra SET situacao=:destino WHERE id=:id AND situacao=:origem');
        $query->execute(['destino'=>$destino,'id'=>$id,'origem'=>$origem]);
        if ($query->rowCount() !== 1) {
            throw new DomainException('Solicitação inexistente ou situação alterada por outro usuário. Atualize a lista.');
        }
    }
}

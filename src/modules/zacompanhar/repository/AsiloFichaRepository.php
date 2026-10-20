<?php

final class AsiloFichaRepository
{
    public function __construct(private PDO $pdo) {}

    public function usos(int $idosoId): array
    {
        $q = $this->pdo->prepare('SELECT um.usado_em,m.nome material_nome,um.quantidade,m.unidade_medida,um.observacao,u.nome usuario_nome FROM usos_materiais um JOIN materiais m ON m.id=um.material_id JOIN usuarios u ON u.id=um.usuario_id WHERE um.idoso_id=:id ORDER BY um.usado_em DESC,um.id DESC');
        $q->execute(['id'=>$idosoId]);
        return $q->fetchAll();
    }
}

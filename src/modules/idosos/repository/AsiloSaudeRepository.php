<?php

final class AsiloSaudeRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listarPorIdoso(int $idosoId): array
    {
        $query = $this->pdo->prepare(
            'SELECT r.id, r.descricao, r.visivel_para_familiar, r.registrado_em, u.nome AS profissional
             FROM registros_saude r
             INNER JOIN usuarios u ON u.id = r.usuario_id
             WHERE r.idoso_id = :idoso_id
             ORDER BY r.registrado_em DESC, r.id DESC'
        );
        $query->execute(['idoso_id' => $idosoId]);

        return array_map(static function (array $registro): array {
            $registro['id'] = (int) $registro['id'];
            $registro['visivel_para_familiar'] = (bool) $registro['visivel_para_familiar'];
            return $registro;
        }, $query->fetchAll());
    }

    public function pertenceAoIdoso(int $registroId, int $idosoId): bool
    {
        $q = $this->pdo->prepare('SELECT id FROM registros_saude WHERE id=:id AND idoso_id=:idoso');
        $q->execute(['id'=>$registroId, 'idoso'=>$idosoId]);
        return $q->fetchColumn() !== false;
    }

    public function registrar(int $idosoId, int $usuarioId, string $descricao, bool $visivelParaFamiliar): void
    {
        $query = $this->pdo->prepare(
            'INSERT INTO registros_saude (idoso_id, usuario_id, descricao, visivel_para_familiar)
             VALUES (:idoso_id, :usuario_id, :descricao, :visivel_para_familiar)'
        );
        $query->execute([
            'idoso_id' => $idosoId,
            'usuario_id' => $usuarioId,
            'descricao' => $descricao,
            'visivel_para_familiar' => $visivelParaFamiliar ? 1 : 0,
        ]);
    }
}

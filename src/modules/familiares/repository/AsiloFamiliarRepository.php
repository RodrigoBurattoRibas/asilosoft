<?php

final class AsiloFamiliarRepository
{
    public function __construct(private PDO $pdo) {}

    public function listar(): array
    {
        return $this->pdo->query('SELECT id, usuario_id, nome, cpf, telefone, email, parentesco_principal FROM familiares ORDER BY nome')->fetchAll();
    }
    public function buscar(int $id): ?array
    {
        $q = $this->pdo->prepare('SELECT id, usuario_id, nome, cpf, telefone, email, parentesco_principal FROM familiares WHERE id=:id');
        $q->execute(['id'=>$id]); return $q->fetch() ?: null;
    }
    public function salvar(array $d, ?int $id = null): array
    {
        if ($id === null) {
            $q=$this->pdo->prepare('INSERT INTO familiares (usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (:usuario_id,:nome,:cpf,:telefone,:email,:parentesco_principal)');
            $q->execute($d); return $this->buscar((int)$this->pdo->lastInsertId());
        }
        $q=$this->pdo->prepare('UPDATE familiares SET usuario_id=:usuario_id,nome=:nome,cpf=:cpf,telefone=:telefone,email=:email,parentesco_principal=:parentesco_principal WHERE id=:id');
        $q->execute($d+['id'=>$id]); return $this->buscar($id);
    }
    public function vincular(int $idoso, int $familiar, string $parentesco, bool $responsavel): void
    {
        if ($responsavel) { $q=$this->pdo->prepare('UPDATE idosos_familiares SET responsavel_principal=0 WHERE idoso_id=:id'); $q->execute(['id'=>$idoso]); }
        $q=$this->pdo->prepare('INSERT INTO idosos_familiares (idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (:i,:f,:p,:r) ON DUPLICATE KEY UPDATE parentesco=VALUES(parentesco), responsavel_principal=VALUES(responsavel_principal)');
        $q->execute(['i'=>$idoso,'f'=>$familiar,'p'=>$parentesco,'r'=>$responsavel ? 1 : 0]);
    }
    public function desvincular(int $idoso, int $familiar): void { $q=$this->pdo->prepare('DELETE FROM idosos_familiares WHERE idoso_id=:i AND familiar_id=:f'); $q->execute(['i'=>$idoso,'f'=>$familiar]); }
    public function porIdoso(int $id): array { $q=$this->pdo->prepare('SELECT f.id,f.nome,f.telefone,f.email,ifm.parentesco,ifm.responsavel_principal FROM idosos_familiares ifm JOIN familiares f ON f.id=ifm.familiar_id WHERE ifm.idoso_id=:id ORDER BY ifm.responsavel_principal DESC,f.nome');$q->execute(['id'=>$id]);return $q->fetchAll(); }
    public function ficha(int $id): ?array { $q=$this->pdo->prepare('SELECT * FROM vw_ficha_idoso WHERE idoso_id=:id');$q->execute(['id'=>$id]);$r=$q->fetchAll();return $r[0]??null; }
    public function acompanhamentosPublicos(int $usuarioId, int $idosoId): ?array { $q=$this->pdo->prepare('SELECT f.id FROM familiares f JOIN idosos_familiares x ON x.familiar_id=f.id WHERE f.usuario_id=:u AND x.idoso_id=:i');$q->execute(['u'=>$usuarioId,'i'=>$idosoId]);if(!$q->fetch()) return null;$q=$this->pdo->prepare('SELECT v.id,v.descricao,v.registrado_em,u.nome profissional FROM vw_acompanhamento_familiar v JOIN usuarios u ON u.id=v.usuario_id WHERE v.familiar_id=(SELECT id FROM familiares WHERE usuario_id=:u) AND v.idoso_id=:i ORDER BY v.registrado_em DESC,v.id DESC');$q->execute(['u'=>$usuarioId,'i'=>$idosoId]);return $q->fetchAll(); }
    public function idososDoUsuario(int $usuarioId): array { $q=$this->pdo->prepare('SELECT i.id,i.nome,i.identificador FROM familiares f JOIN idosos_familiares x ON x.familiar_id=f.id JOIN idosos i ON i.id=x.idoso_id WHERE f.usuario_id=:u AND i.ativo=1 ORDER BY i.nome');$q->execute(['u'=>$usuarioId]);return $q->fetchAll(); }
}

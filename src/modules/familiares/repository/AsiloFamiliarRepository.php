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
    public function contasDisponiveis(?int $familiarId = null): array
    {
        $q = $this->pdo->prepare("SELECT u.id,u.nome,u.email FROM usuarios u LEFT JOIN familiares f ON f.usuario_id=u.id WHERE u.ativo=1 AND u.perfil='Familiar' AND (f.id IS NULL OR f.id=:id) ORDER BY u.nome,u.id");
        $q->execute(['id'=>$familiarId ?? 0]);
        return $q->fetchAll();
    }
    public function salvar(array $d, ?int $id = null, bool $preservarConta = false): array
    {
        $this->pdo->beginTransaction();
        try {
            $anterior = null;
            if ($id !== null) {
                $q=$this->pdo->prepare('SELECT id,usuario_id FROM familiares WHERE id=:id FOR UPDATE');
                $q->execute(['id'=>$id]);
                if (!($anterior=$q->fetch())) throw new DomainException('Familiar não encontrado.');
            }
            if ($id !== null && $preservarConta) $d['usuario_id'] = $anterior['usuario_id'];
            if ($d['usuario_id'] !== null && (string)$d['usuario_id'] !== (string)($anterior['usuario_id'] ?? '')) {
                $q=$this->pdo->prepare("SELECT id FROM usuarios WHERE id=:id AND ativo=1 AND perfil='Familiar' FOR UPDATE");
                $q->execute(['id'=>$d['usuario_id']]);
                if (!$q->fetch()) throw new DomainException('Selecione uma conta ativa de perfil Familiar ou remova a associação.');
                $q=$this->pdo->prepare('SELECT id FROM familiares WHERE usuario_id=:usuario AND id<>:id');
                $q->execute(['usuario'=>$d['usuario_id'],'id'=>$id ?? 0]);
                if ($q->fetch()) throw new DomainException('Esta conta já está associada a outro familiar.');
            }
            if ($id === null) {
                $q=$this->pdo->prepare('INSERT INTO familiares (usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (:usuario_id,:nome,:cpf,:telefone,:email,:parentesco_principal)');
                $q->execute($d);
                $id=(int)$this->pdo->lastInsertId();
            } else {
                $q=$this->pdo->prepare('UPDATE familiares SET usuario_id=:usuario_id,nome=:nome,cpf=:cpf,telefone=:telefone,email=:email,parentesco_principal=:parentesco_principal WHERE id=:id');
                $q->execute($d+['id'=>$id]);
            }
            $resultado=$this->buscar($id);
            $this->pdo->commit();
            return $resultado;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }
    public function vincular(int $idoso, int $familiar, string $parentesco, bool $responsavel): void
    {
        $this->alterarVinculo($idoso, $familiar, $parentesco, $responsavel);
    }
    public function desvincular(int $idoso, int $familiar): void
    {
        $this->alterarVinculo($idoso, $familiar, null, false);
    }
    private function alterarVinculo(int $idoso, int $familiar, ?string $parentesco, bool $responsavel): void
    {
        $this->pdo->beginTransaction();
        try {
            // A mesma linha-pai serializa inclusões e remoções, inclusive sem vínculos prévios.
            $q=$this->pdo->prepare('SELECT id FROM idosos WHERE id=:id FOR UPDATE');
            $q->execute(['id'=>$idoso]);
            if (!$q->fetch()) throw new DomainException('Idoso não encontrado.');
            $q=$this->pdo->prepare('SELECT id FROM familiares WHERE id=:id FOR UPDATE');
            $q->execute(['id'=>$familiar]);
            if (!$q->fetch()) throw new DomainException('Familiar não encontrado.');
            if ($parentesco === null) {
                $q=$this->pdo->prepare('DELETE FROM idosos_familiares WHERE idoso_id=:i AND familiar_id=:f');
                $q->execute(['i'=>$idoso,'f'=>$familiar]);
                if (!$q->rowCount()) throw new DomainException('Vínculo não encontrado.');
            } else {
                if ($responsavel) {
                    $q=$this->pdo->prepare('UPDATE idosos_familiares SET responsavel_principal=0 WHERE idoso_id=:id');
                    $q->execute(['id'=>$idoso]);
                }
                $q=$this->pdo->prepare('INSERT INTO idosos_familiares (idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (:i,:f,:p,:r) ON DUPLICATE KEY UPDATE parentesco=VALUES(parentesco), responsavel_principal=VALUES(responsavel_principal)');
                $q->execute(['i'=>$idoso,'f'=>$familiar,'p'=>$parentesco,'r'=>$responsavel ? 1 : 0]);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }
    public function porIdoso(int $id): array { $q=$this->pdo->prepare('SELECT f.id,f.nome,f.telefone,f.email,ifm.parentesco,ifm.responsavel_principal FROM idosos_familiares ifm JOIN familiares f ON f.id=ifm.familiar_id WHERE ifm.idoso_id=:id ORDER BY ifm.responsavel_principal DESC,f.nome');$q->execute(['id'=>$id]);return $q->fetchAll(); }
    public function ficha(int $id): ?array { $q=$this->pdo->prepare('SELECT * FROM vw_ficha_idoso WHERE idoso_id=:id');$q->execute(['id'=>$id]);$r=$q->fetchAll();return $r[0]??null; }
    public function acompanhamentosPublicos(int $usuarioId, int $idosoId): ?array { $q=$this->pdo->prepare('SELECT f.id FROM familiares f JOIN usuarios conta ON conta.id=f.usuario_id AND conta.ativo=1 AND conta.perfil=\'Familiar\' JOIN idosos_familiares x ON x.familiar_id=f.id JOIN idosos i ON i.id=x.idoso_id AND i.ativo=1 WHERE f.usuario_id=:u AND x.idoso_id=:i');$q->execute(['u'=>$usuarioId,'i'=>$idosoId]);if(!$q->fetch()) return null;$q=$this->pdo->prepare('SELECT v.id,v.descricao,v.registrado_em,u.nome profissional FROM vw_acompanhamento_familiar v JOIN usuarios u ON u.id=v.usuario_id JOIN familiares f ON f.id=v.familiar_id JOIN usuarios conta ON conta.id=f.usuario_id AND conta.ativo=1 AND conta.perfil=\'Familiar\' JOIN idosos i ON i.id=v.idoso_id AND i.ativo=1 WHERE f.usuario_id=:u AND v.idoso_id=:i AND v.visivel_para_familiar=1 ORDER BY v.registrado_em DESC,v.id DESC');$q->execute(['u'=>$usuarioId,'i'=>$idosoId]);return $q->fetchAll(); }
    public function idososDoUsuario(int $usuarioId): array { $q=$this->pdo->prepare('SELECT i.id,i.nome,i.identificador FROM familiares f JOIN usuarios conta ON conta.id=f.usuario_id AND conta.ativo=1 AND conta.perfil=\'Familiar\' JOIN idosos_familiares x ON x.familiar_id=f.id JOIN idosos i ON i.id=x.idoso_id WHERE f.usuario_id=:u AND i.ativo=1 ORDER BY i.nome,i.id');$q->execute(['u'=>$usuarioId]);return $q->fetchAll(); }
}

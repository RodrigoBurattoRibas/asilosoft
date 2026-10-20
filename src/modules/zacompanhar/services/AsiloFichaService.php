<?php

final class AsiloFichaService
{
    public function __construct(private object $idosos, private object $familiares, private object $saude, private object $ficha) {}

    public function consultar(int $id): ?array
    {
        $idoso = $id > 0 ? $this->idosos->buscarPorId($id) : null;
        if ($idoso === null) return null;
        return ['idoso'=>$idoso, 'familiares'=>$this->familiares->porIdoso($id),
            'registros'=>$this->saude->listarPorIdoso($id), 'usos'=>$this->ficha->usos($id)];
    }
}

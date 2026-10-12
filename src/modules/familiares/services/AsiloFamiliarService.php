<?php
final class AsiloFamiliarService
{
    public function __construct(private object $repository) {}
    public function listar(): array { return $this->repository->listar(); }
    public function buscar(int $id): ?array { return $this->repository->buscar($id); }
    public function salvar(array $d, ?int $id=null): array { $nome=trim((string)($d['nome']??''));$telefone=trim((string)($d['telefone']??''));$cpf=preg_replace('/\D/','',(string)($d['cpf']??''));if($nome===''||$telefone===''||($cpf!==''&&strlen($cpf)!==11))throw new DomainException('Informe nome, telefone e CPF válido quando preenchido.');return $this->repository->salvar(['usuario_id'=>($d['usuario_id']??'')!==''?(int)$d['usuario_id']:null,'nome'=>$nome,'cpf'=>$cpf?:null,'telefone'=>$telefone,'email'=>trim((string)($d['email']??''))?:null,'parentesco_principal'=>trim((string)($d['parentesco_principal']??''))?:null],$id); }
    public function vincular(int $idoso,int $familiar,array $d):void{$p=trim((string)($d['parentesco']??''));if($p==='')throw new DomainException('Informe o parentesco.');$this->repository->vincular($idoso,$familiar,$p,isset($d['responsavel_principal']));}
}

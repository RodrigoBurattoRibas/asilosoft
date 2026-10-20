<?php
final class AsiloFamiliarService
{
    public function __construct(private object $repository) {}
    public function listar(): array { return $this->repository->listar(); }
    public function buscar(int $id): ?array { return $id > 0 ? $this->repository->buscar($id) : null; }
    public function salvar(array $d, ?int $id = null): array
    {
        $atual = $id === null ? null : $this->buscar($id);
        if ($id !== null && !$atual) throw new DomainException('Familiar não encontrado.');
        $dados = [];
        foreach (['nome'=>120, 'telefone'=>30, 'email'=>160, 'parentesco_principal'=>60] as $campo=>$limite) {
            if (isset($d[$campo]) && !is_scalar($d[$campo])) throw new DomainException('Campo inválido.');
            $valor = trim((string)($d[$campo] ?? ''));
            if (mb_strlen($valor) > $limite) throw new DomainException('O campo '.$campo.' excede o limite permitido.');
            $dados[$campo] = $valor === '' && !in_array($campo, ['nome','telefone'], true) ? null : $valor;
        }
        if ($dados['nome'] === '' || $dados['telefone'] === '') throw new DomainException('Informe nome e telefone.');
        if ($dados['email'] !== null && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) throw new DomainException('Informe um e-mail válido.');
        if (isset($d['cpf']) && !is_scalar($d['cpf'])) throw new DomainException('CPF inválido.');
        $cpf = preg_replace('/\D/', '', (string)($d['cpf'] ?? ''));
        if ($cpf !== '' && strlen($cpf) !== 11) throw new DomainException('Informe um CPF com 11 dígitos.');
        $dados['cpf'] = $cpf ?: null;
        // Ausência preserva a associação; opção vazia no formulário remove explicitamente.
        $dados['usuario_id'] = $atual['usuario_id'] ?? null;
        if (array_key_exists('usuario_id', $d)) {
            $dados['usuario_id'] = $d['usuario_id'] === '' || $d['usuario_id'] === null ? null : filter_var($d['usuario_id'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            if ($dados['usuario_id'] === false) throw new DomainException('Conta inválida.');
        }
        try { return $this->repository->salvar($dados, $id, !array_key_exists('usuario_id', $d)); }
        catch (PDOException $e) { throw new DomainException('Não foi possível salvar: confira se CPF ou conta já pertencem a outro familiar.', 0, $e); }
    }
    public function vincular(int $idoso, int $familiar, array $d): void
    {
        if ($idoso <= 0 || $familiar <= 0) throw new DomainException('Selecione idoso e familiar válidos.');
        if (isset($d['parentesco']) && !is_string($d['parentesco'])) throw new DomainException('Parentesco inválido.');
        $p = trim((string)($d['parentesco'] ?? ''));
        if ($p === '' || mb_strlen($p) > 60) throw new DomainException('Informe parentesco com até 60 caracteres.');
        try { $this->repository->vincular($idoso, $familiar, $p, !empty($d['responsavel_principal'])); }
        catch (PDOException $e) { throw new DomainException('Não foi possível atualizar o vínculo. Tente novamente.', 0, $e); }
    }
    public function desvincular(int $idoso, int $familiar): void
    {
        if ($idoso <= 0 || $familiar <= 0) throw new DomainException('Vínculo inválido.');
        try { $this->repository->desvincular($idoso, $familiar); }
        catch (PDOException $e) { throw new DomainException('Não foi possível remover o vínculo. Tente novamente.', 0, $e); }
    }
}

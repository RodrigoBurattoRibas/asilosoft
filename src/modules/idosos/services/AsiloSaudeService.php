<?php

final class AsiloSaudeService
{
    public function __construct(private object $repository, private object $idosos) {}

    public function registrar(int $idosoId, array $autor, array $dados): void
    {
        if (($autor['perfil'] ?? '') !== 'Profissional de Saúde' || empty($autor['ativo']) || (int)($autor['id'] ?? 0) <= 0) {
            throw new DomainException('Somente profissionais de saúde podem registrar acompanhamento.');
        }
        if ($idosoId <= 0 || $this->idosos->buscarPorId($idosoId) === null) {
            throw new DomainException('Idoso não encontrado.');
        }
        foreach (['descricao', 'visivel_para_familiar', 'corrige_id'] as $campo) {
            if (array_key_exists($campo, $dados) && !is_scalar($dados[$campo])) {
                throw new DomainException('Dados de acompanhamento inválidos.');
            }
        }
        $descricao = trim((string)($dados['descricao'] ?? ''));
        $visibilidade = (string)($dados['visivel_para_familiar'] ?? '0');
        if ($descricao === '' || strlen($descricao) > 65000) {
            throw new DomainException('Informe uma descrição com até 65000 bytes.');
        }
        if (!in_array($visibilidade, ['0','1'], true)) {
            throw new DomainException('Visibilidade inválida.');
        }
        if (!empty($dados['corrige_id'])) {
            $original = filter_var($dados['corrige_id'], FILTER_VALIDATE_INT);
            if (!$original || $original <= 0 || !$this->repository->pertenceAoIdoso($original, $idosoId)) {
                throw new DomainException('Registro original não encontrado neste prontuário.');
            }
            $descricao = 'Retificação do registro #'.$original.': '.$descricao;
        }
        if (strlen($descricao) > 65000) {
            throw new DomainException('A descrição completa, incluindo a referência da retificação, deve ter até 65000 bytes.');
        }
        $this->repository->registrar($idosoId, (int)$autor['id'], $descricao, $visibilidade === '1');
    }
}

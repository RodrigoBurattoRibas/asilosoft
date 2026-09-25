<?php

declare(strict_types=1);

final class IdosoService
{
    public function __construct(private object $repositorio)
    {
    }

    public function criar(array $dados): array
    {
        $nome = trim((string) ($dados['nome'] ?? ''));
        $cpf = preg_replace('/\D/', '', (string) ($dados['cpf'] ?? '')) ?? '';
        $identificador = trim((string) ($dados['identificador'] ?? ''));
        $quartoId = (int) ($dados['quarto_id'] ?? 0);

        if ($nome === '' || strlen($cpf) !== 11 || $identificador === '' || $quartoId <= 0) {
            throw new DomainException('Informe nome, CPF, identificador e quarto válidos.');
        }

        if ($this->repositorio->buscarPorCpf($cpf) !== null) {
            throw new DomainException('Este CPF já está em uso.');
        }

        if ($this->repositorio->buscarPorIdentificador($identificador) !== null) {
            throw new DomainException('Este identificador já está em uso.');
        }

        $quarto = $this->repositorio->buscarQuartoPorId($quartoId);
        if ($quarto === null || !$quarto['ativo']) {
            throw new DomainException('Selecione um quarto disponível.');
        }

        if ($this->repositorio->contarOcupacaoDoQuarto($quartoId) >= $quarto['capacidade']) {
            throw new DomainException('Este quarto não possui vaga disponível.');
        }

        return $this->repositorio->criar([
            'nome' => $nome,
            'cpf' => $cpf,
            'identificador' => $identificador,
            'quarto_id' => $quartoId,
        ]);
    }

    public function atualizar(int $id, array $dados): array
    {
        $idosoAtual = $this->repositorio->buscarPorId($id);
        if ($idosoAtual === null) {
            throw new DomainException('Idoso não encontrado.');
        }

        $nome = trim((string) ($dados['nome'] ?? ''));
        $cpf = preg_replace('/\D/', '', (string) ($dados['cpf'] ?? '')) ?? '';
        $identificador = trim((string) ($dados['identificador'] ?? ''));
        $quartoId = (int) ($dados['quarto_id'] ?? 0);

        if ($nome === '' || strlen($cpf) !== 11 || $identificador === '' || $quartoId <= 0) {
            throw new DomainException('Informe nome, CPF, identificador e quarto válidos.');
        }

        $mesmoCpf = $this->repositorio->buscarPorCpf($cpf);
        if ($mesmoCpf !== null && $mesmoCpf['id'] !== $id) {
            throw new DomainException('Este CPF já está em uso.');
        }

        $mesmoIdentificador = $this->repositorio->buscarPorIdentificador($identificador);
        if ($mesmoIdentificador !== null && $mesmoIdentificador['id'] !== $id) {
            throw new DomainException('Este identificador já está em uso.');
        }

        $quarto = $this->repositorio->buscarQuartoPorId($quartoId);
        if ($quarto === null || (!$quarto['ativo'] && $quartoId !== $idosoAtual['quarto_id'])) {
            throw new DomainException('Selecione um quarto disponível.');
        }

        if ($quartoId !== $idosoAtual['quarto_id'] && $this->repositorio->contarOcupacaoDoQuarto($quartoId) >= $quarto['capacidade']) {
            throw new DomainException('Este quarto não possui vaga disponível.');
        }

        return $this->repositorio->atualizar($id, [
            'nome' => $nome,
            'cpf' => $cpf,
            'identificador' => $identificador,
            'quarto_id' => $quartoId,
        ]);
    }
}

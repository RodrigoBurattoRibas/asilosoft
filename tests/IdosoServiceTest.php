<?php

declare(strict_types=1);

require_once __DIR__ . '/TestCase.php';

final class RepositorioIdososEmMemoria
{
    /** @var array<int, array<string, mixed>> */
    private array $idosos = [];

    /** @var array<int, array<string, mixed>> */
    private array $quartos;

    private int $proximoId = 1;

    public function __construct(array $quartos)
    {
        $this->quartos = $quartos;
    }

    public function buscarPorCpf(string $cpf): ?array
    {
        foreach ($this->idosos as $idoso) {
            if ($idoso['cpf'] === $cpf) {
                return $idoso;
            }
        }

        return null;
    }

    public function buscarPorIdentificador(string $identificador): ?array
    {
        foreach ($this->idosos as $idoso) {
            if ($idoso['identificador'] === $identificador) {
                return $idoso;
            }
        }

        return null;
    }

    public function buscarQuartoPorId(int $id): ?array
    {
        return $this->quartos[$id] ?? null;
    }

    public function contarOcupacaoDoQuarto(int $quartoId): int
    {
        return count(array_filter($this->idosos, static fn (array $idoso): bool => $idoso['quarto_id'] === $quartoId));
    }

    public function criar(array $dados): array
    {
        $idoso = $dados + ['id' => $this->proximoId++];
        $this->idosos[$idoso['id']] = $idoso;

        return $idoso;
    }
}

function criarIdosoServiceDeTeste(array $quartos = []): object
{
    if (!class_exists('IdosoService')) {
        throw new FalhaDeTeste('O serviço de cadastro de idosos ainda não foi implementado.');
    }

    return new IdosoService(new RepositorioIdososEmMemoria($quartos));
}

function testarCadastroDeIdosoNormalizaCpfEAssociaQuarto(): void
{
    $servico = criarIdosoServiceDeTeste([
        1 => ['id' => 1, 'codigo' => 'A-101', 'capacidade' => 2, 'ativo' => true],
    ]);
    $idoso = $servico->criar([
        'nome' => 'Maria de Souza',
        'cpf' => '123.456.789-00',
        'identificador' => 'IDOSO-001',
        'quarto_id' => 1,
    ]);

    afirmarIgual('12345678900', $idoso['cpf'], 'O CPF deve ser salvo apenas com dígitos.');
    afirmarIgual(1, $idoso['quarto_id'], 'O idoso deve ficar associado ao quarto escolhido.');
}

function testarCadastroDeIdosoRejeitaCpfDuplicado(): void
{
    $servico = criarIdosoServiceDeTeste([
        1 => ['id' => 1, 'codigo' => 'A-101', 'capacidade' => 2, 'ativo' => true],
    ]);
    $dados = ['nome' => 'Maria', 'cpf' => '12345678900', 'identificador' => 'IDOSO-001', 'quarto_id' => 1];
    $servico->criar($dados);

    try {
        $servico->criar($dados + ['identificador' => 'IDOSO-002']);
        throw new FalhaDeTeste('O CPF duplicado deveria ser rejeitado.');
    } catch (DomainException $erro) {
        afirmarIgual('Este CPF já está em uso.', $erro->getMessage(), 'A mensagem deve indicar CPF já utilizado.');
    }
}

function testarCadastroDeIdosoRejeitaIdentificadorDuplicado(): void
{
    $servico = criarIdosoServiceDeTeste([
        1 => ['id' => 1, 'codigo' => 'A-101', 'capacidade' => 2, 'ativo' => true],
    ]);
    $servico->criar(['nome' => 'Maria', 'cpf' => '12345678900', 'identificador' => 'IDOSO-001', 'quarto_id' => 1]);

    try {
        $servico->criar(['nome' => 'João', 'cpf' => '98765432100', 'identificador' => 'IDOSO-001', 'quarto_id' => 1]);
        throw new FalhaDeTeste('O identificador duplicado deveria ser rejeitado.');
    } catch (DomainException $erro) {
        afirmarIgual('Este identificador já está em uso.', $erro->getMessage(), 'A mensagem deve indicar identificador já utilizado.');
    }
}

function testarCadastroDeIdosoRejeitaQuartoLotado(): void
{
    $servico = criarIdosoServiceDeTeste([
        1 => ['id' => 1, 'codigo' => 'A-101', 'capacidade' => 1, 'ativo' => true],
    ]);
    $servico->criar(['nome' => 'Maria', 'cpf' => '12345678900', 'identificador' => 'IDOSO-001', 'quarto_id' => 1]);

    try {
        $servico->criar(['nome' => 'João', 'cpf' => '98765432100', 'identificador' => 'IDOSO-002', 'quarto_id' => 1]);
        throw new FalhaDeTeste('Um quarto lotado não pode receber outro idoso.');
    } catch (DomainException $erro) {
        afirmarIgual('Este quarto não possui vaga disponível.', $erro->getMessage(), 'A capacidade do quarto deve ser respeitada.');
    }
}

if (is_file(__DIR__ . '/../app/Services/IdosoService.php')) {
    require_once __DIR__ . '/../app/Services/IdosoService.php';
}


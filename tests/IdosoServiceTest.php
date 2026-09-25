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

    public function buscarPorId(int $id): ?array
    {
        return $this->idosos[$id] ?? null;
    }

    public function atualizar(int $id, array $dados): array
    {
        if (!isset($this->idosos[$id])) {
            throw new DomainException('Idoso não encontrado.');
        }

        $this->idosos[$id] = array_merge($this->idosos[$id], $dados);

        return $this->idosos[$id];
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

function testarEdicaoDeIdosoMantemQuartoAtualMesmoQuandoLotado(): void
{
    $servico = criarIdosoServiceDeTeste([
        1 => ['id' => 1, 'codigo' => 'A-101', 'capacidade' => 1, 'ativo' => true],
    ]);
    $idoso = $servico->criar([
        'nome' => 'Maria de Souza',
        'cpf' => '12345678900',
        'identificador' => 'IDOSO-001',
        'quarto_id' => 1,
    ]);

    $atualizado = $servico->atualizar($idoso['id'], [
        'nome' => 'Maria da Silva',
        'cpf' => '123.456.789-00',
        'identificador' => 'IDOSO-001',
        'quarto_id' => 1,
    ]);

    afirmarIgual('Maria da Silva', $atualizado['nome'], 'A edição deve permitir manter o quarto atual, mesmo sem vaga adicional.');
}

function testarEdicaoDeIdosoRejeitaCpfDeOutroCadastro(): void
{
    $servico = criarIdosoServiceDeTeste([
        1 => ['id' => 1, 'codigo' => 'A-101', 'capacidade' => 2, 'ativo' => true],
    ]);
    $servico->criar(['nome' => 'Maria', 'cpf' => '12345678900', 'identificador' => 'IDOSO-001', 'quarto_id' => 1]);
    $joao = $servico->criar(['nome' => 'João', 'cpf' => '98765432100', 'identificador' => 'IDOSO-002', 'quarto_id' => 1]);

    try {
        $servico->atualizar($joao['id'], [
            'nome' => 'João',
            'cpf' => '12345678900',
            'identificador' => 'IDOSO-002',
            'quarto_id' => 1,
        ]);
        throw new FalhaDeTeste('O CPF de outro idoso deveria ser rejeitado durante a edição.');
    } catch (DomainException $erro) {
        afirmarIgual('Este CPF já está em uso.', $erro->getMessage(), 'A edição deve preservar a unicidade do CPF.');
    }
}

if (is_file(__DIR__ . '/../app/Services/IdosoService.php')) {
    require_once __DIR__ . '/../app/Services/IdosoService.php';
}


if (is_file(__DIR__ . '/../app/Repositories/RepositorioIdososDeSessao.php')) {
    require_once __DIR__ . '/../app/Repositories/RepositorioIdososDeSessao.php';
}


function testarRepositorioTemporarioListaQuartosECadastraIdoso(): void
{
    if (!class_exists('RepositorioIdososDeSessao')) {
        throw new FalhaDeTeste('O repositório temporário de idosos ainda não foi implementado.');
    }

    $sessao = new ArmazenamentoDeSessaoEmMemoria();
    $repositorio = new RepositorioIdososDeSessao($sessao);
    $quartos = $repositorio->listarDisponiveis();

    afirmar(count($quartos) > 0, 'O modo demonstração deve disponibilizar quartos para cadastro.');
    $criado = $repositorio->criar([
        'nome' => 'Ana Maria',
        'cpf' => '11122233344',
        'identificador' => 'IDOSO-DEMO-001',
        'quarto_id' => $quartos[0]['id'],
    ]);

    afirmarIgual($criado['id'], $repositorio->listar()[0]['id'], 'O idoso deve ficar disponível na listagem durante a sessão.');
}

function testarRepositorioTemporarioNaoListaQuartoLotado(): void
{
    $sessao = new ArmazenamentoDeSessaoEmMemoria();
    $repositorio = new RepositorioIdososDeSessao($sessao);
    $quarto = $repositorio->listarDisponiveis()[1];
    $repositorio->criar([
        'nome' => 'Joana',
        'cpf' => '99988877766',
        'identificador' => 'IDOSO-DEMO-002',
        'quarto_id' => $quarto['id'],
    ]);

    afirmarIgual(1, count($repositorio->listarDisponiveis()), 'O quarto sem vaga não deve aparecer no formulário de cadastro.');
}

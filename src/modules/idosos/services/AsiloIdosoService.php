<?php


final class AsiloIdosoService
{
    public function __construct(private object $repository, private object $roomRepository)
    {
    }

    public function listar(): array
    {
        return $this->repository->listar();
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->repository->buscarPorId($id);
    }

    public function listarQuartosDisponiveis(): array
    {
        return $this->roomRepository->listarDisponiveis();
    }

    public function listarQuartos(): array
    {
        return method_exists($this->roomRepository, 'listarQuartos')
            ? $this->roomRepository->listarQuartos()
            : $this->roomRepository->listar();
    }

    public function criarQuarto(array $data): array
    {
        $room = $this->validarQuarto($data);
        foreach ($this->listarQuartos() as $existing) {
            if (mb_strtolower($existing['codigo']) === mb_strtolower($room['codigo'])) {
                throw new DomainException('Já existe um quarto com este código.');
            }
        }
        return method_exists($this->roomRepository, 'criarQuarto')
            ? $this->roomRepository->criarQuarto($room)
            : $this->roomRepository->criar($room);
    }

    public function atualizarQuarto(int $id, array $data): array
    {
        $current = $this->buscarQuartoPorId($id);
        if ($current === null) {
            throw new DomainException('Quarto não encontrado.');
        }
        $room = $this->validarQuarto($data);
        $room['ativo'] = (string) ($data['ativo'] ?? '1') === '1';
        if ($room['capacidade'] < $this->contarOcupacaoDoQuarto($id)) {
            throw new DomainException('A capacidade não pode ser menor que a ocupação atual.');
        }
        foreach ($this->listarQuartos() as $existing) {
            if ($existing['id'] !== $id && mb_strtolower($existing['codigo']) === mb_strtolower($room['codigo'])) {
                throw new DomainException('Já existe um quarto com este código.');
            }
        }
        return method_exists($this->roomRepository, 'atualizarQuarto')
            ? $this->roomRepository->atualizarQuarto($id, $room)
            : $this->roomRepository->atualizar($id, $room);
    }

    public function buscarQuartoPorId(int $id): ?array
    {
        return $this->repository->buscarQuartoPorId($id);
    }

    public function contarOcupacaoDoQuarto(int $id): int
    {
        return $this->repository->contarOcupacaoDoQuarto($id);
    }

    public function criar(array $data): array
    {
        $name = trim((string) ($data['nome'] ?? ''));
        $cpf = preg_replace('/\D/', '', (string) ($data['cpf'] ?? '')) ?? '';
        $identifier = trim((string) ($data['identificador'] ?? ''));
        $roomId = (int) ($data['quarto_id'] ?? 0);

        if ($name === '' || strlen($cpf) !== 11 || $identifier === '' || $roomId <= 0) {
            throw new DomainException('Informe nome, CPF, identificador e quarto válidos.');
        }

        if ($this->repository->buscarPorCpf($cpf) !== null) {
            throw new DomainException('Este CPF já está em uso.');
        }

        if ($this->repository->buscarPorIdentificador($identifier) !== null) {
            throw new DomainException('Este identificador já está em uso.');
        }

        $room = $this->repository->buscarQuartoPorId($roomId);
        if ($room === null || !$room['ativo']) {
            throw new DomainException('Selecione um quarto disponível.');
        }

        if ($this->repository->contarOcupacaoDoQuarto($roomId) >= $room['capacidade']) {
            throw new DomainException('Este quarto não possui vaga disponível.');
        }

        return $this->repository->criar([
            'nome' => $name,
            'cpf' => $cpf,
            'identificador' => $identifier,
            'quarto_id' => $roomId,
        ]);
    }

    public function atualizar(int $id, array $data): array
    {
        $current = $this->repository->buscarPorId($id);
        if ($current === null) {
            throw new DomainException('Idoso não encontrado.');
        }

        $name = trim((string) ($data['nome'] ?? ''));
        $cpf = preg_replace('/\D/', '', (string) ($data['cpf'] ?? '')) ?? '';
        $identifier = trim((string) ($data['identificador'] ?? ''));
        $roomId = (int) ($data['quarto_id'] ?? 0);

        if ($name === '' || strlen($cpf) !== 11 || $identifier === '') {
            throw new DomainException('Informe nome, CPF, identificador e quarto válidos.');
        }

        $sameCpf = $this->repository->buscarPorCpf($cpf);
        if ($sameCpf !== null && (int) $sameCpf['id'] !== $id) {
            throw new DomainException('Este CPF já está em uso.');
        }

        $sameIdentifier = $this->repository->buscarPorIdentificador($identifier);
        if ($sameIdentifier !== null && (int) $sameIdentifier['id'] !== $id) {
            throw new DomainException('Este identificador já está em uso.');
        }

        if ($roomId > 0) {
            $room = $this->repository->buscarQuartoPorId($roomId);
            $currentRoomId = $current['quarto_id'] === null ? null : (int) $current['quarto_id'];

            if ($room === null || (!$room['ativo'] && $roomId !== $currentRoomId)) {
                throw new DomainException('Selecione um quarto disponível.');
            }

            if (
                $roomId !== $currentRoomId
                && $this->repository->contarOcupacaoDoQuarto($roomId) >= $room['capacidade']
            ) {
                throw new DomainException('Este quarto não possui vaga disponível.');
            }
        }

        return $this->repository->atualizar($id, [
            'nome' => $name,
            'cpf' => $cpf,
            'identificador' => $identifier,
            'quarto_id' => $roomId > 0 ? $roomId : null,
        ]);
    }

    public function retirarDoQuarto(int $id): array
    {
        if ($this->repository->buscarPorId($id) === null) {
            throw new DomainException('Idoso não encontrado.');
        }

        return $this->repository->atualizar($id, ['quarto_id' => null]);
    }

    private function validarQuarto(array $data): array
    {
        $codigo = trim((string) ($data['codigo'] ?? ''));
        $capacidade = filter_var($data['capacidade'] ?? null, FILTER_VALIDATE_INT);
        if ($codigo === '' || mb_strlen($codigo) > 30 || !is_int($capacidade) || $capacidade < 1 || $capacidade > 99) {
            throw new DomainException('Informe código e capacidade válida para o quarto.');
        }
        return ['codigo' => $codigo, 'capacidade' => $capacidade];
    }

    public function alterarStatus(int $id, bool $active): array
    {
        if ($this->repository->buscarPorId($id) === null) {
            throw new DomainException('Idoso não encontrado.');
        }

        $data = ['ativo' => $active];
        if (!$active) {
            $data['quarto_id'] = null;
        }

        return $this->repository->atualizar($id, $data);
    }
}

<?php


final class AsiloRepositorioIdososSessao
{
    private const RESIDENTS_KEY = 'asilosoft_idosos_demonstracao';
    private const ROOMS_KEY = 'asilosoft_quartos_demonstracao';

    public function __construct(private object $session)
    {
        if (!is_array($this->session->get(self::ROOMS_KEY))) {
            $this->session->set(self::ROOMS_KEY, [
                ['id' => 1, 'codigo' => 'A-101', 'capacidade' => 2, 'ativo' => true],
                ['id' => 2, 'codigo' => 'A-102', 'capacidade' => 1, 'ativo' => true],
            ]);
        }

        if (!is_array($this->session->get(self::RESIDENTS_KEY))) {
            $this->session->set(self::RESIDENTS_KEY, []);
        }
    }

    public function buscarPorCpf(string $cpf): ?array
    {
        foreach ($this->residentes() as $resident) {
            if ($resident['cpf'] === $cpf) {
                return $resident;
            }
        }

        return null;
    }

    public function buscarPorIdentificador(string $identifier): ?array
    {
        foreach ($this->residentes() as $resident) {
            if ($resident['identificador'] === $identifier) {
                return $resident;
            }
        }

        return null;
    }

    public function buscarQuartoPorId(int $id): ?array
    {
        foreach ($this->quartos() as $room) {
            if ($room['id'] === $id) {
                return $room;
            }
        }

        return null;
    }

    public function contarOcupacaoDoQuarto(int $roomId): int
    {
        return count(array_filter(
            $this->residentes(),
            static fn (array $resident): bool =>
                ($resident['ativo'] ?? true) && $resident['quarto_id'] === $roomId
        ));
    }

    public function criar(array $data): array
    {
        $residents = $this->residentes();
        $ids = array_column($residents, 'id');
        $resident = $data + ['id' => $ids === [] ? 1 : max($ids) + 1, 'ativo' => true];
        $residents[] = $resident;
        $this->save($residents);

        return $resident;
    }

    public function listar(): array
    {
        $residents = array_map(function (array $resident): array {
            $room = $resident['quarto_id'] === null ? null : $this->buscarQuartoPorId($resident['quarto_id']);
            $resident['quarto_codigo'] = $room['codigo'] ?? 'Sem quarto';
            $resident['ativo'] = $resident['ativo'] ?? true;

            return $resident;
        }, $this->residentes());

        usort($residents, static fn (array $first, array $second): int => $first['nome'] <=> $second['nome']);

        return $residents;
    }

    public function buscarPorId(int $id): ?array
    {
        foreach ($this->residentes() as $resident) {
            if ($resident['id'] === $id) {
                return $resident;
            }
        }

        return null;
    }

    public function atualizar(int $id, array $data): array
    {
        $residents = $this->residentes();
        foreach ($residents as $index => $resident) {
            if ($resident['id'] === $id) {
                $residents[$index] = array_merge($resident, $data);
                $this->save($residents);

                return $residents[$index];
            }
        }

        throw new DomainException('Idoso não encontrado.');
    }

    public function listarDisponiveis(): array
    {
        $rooms = [];
        foreach ($this->quartos() as $room) {
            $occupancy = $this->contarOcupacaoDoQuarto($room['id']);
            if ($room['ativo'] && $occupancy < $room['capacidade']) {
                $room['ocupacao'] = $occupancy;
                $rooms[] = $room;
            }
        }

        usort($rooms, static fn (array $first, array $second): int => $first['codigo'] <=> $second['codigo']);

        return $rooms;
    }

    private function residentes(): array
    {
        return $this->session->get(self::RESIDENTS_KEY);
    }

    private function quartos(): array
    {
        return $this->session->get(self::ROOMS_KEY);
    }

    private function save(array $residents): void
    {
        $this->session->set(self::RESIDENTS_KEY, $residents);
    }
}

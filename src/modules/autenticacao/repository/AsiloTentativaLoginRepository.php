<?php

/**
 * Falhas de login por chave (hash SHA-256 do e-mail normalizado).
 * Não guarda o e-mail em texto nem o IP: atrás do proxy do Railway todos chegam pelo mesmo IP.
 */
final class AsiloTentativaLoginRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function falhasRecentes(string $chave, int $minutos): int
    {
        $query = $this->pdo->prepare(
            'SELECT COUNT(*) FROM tentativas_login
             WHERE chave = :chave AND criado_em > NOW() - INTERVAL :minutos MINUTE'
        );
        $query->bindValue('chave', $chave);
        $query->bindValue('minutos', $minutos, PDO::PARAM_INT);
        $query->execute();

        return (int) $query->fetchColumn();
    }

    public function registrarFalha(string $chave): void
    {
        $this->pdo->prepare('INSERT INTO tentativas_login (chave) VALUES (:chave)')->execute(['chave' => $chave]);
        // Mantém a tabela pequena: falhas com mais de um dia não influenciam o bloqueio.
        $this->pdo->prepare('DELETE FROM tentativas_login WHERE chave = :chave AND criado_em < NOW() - INTERVAL 1 DAY')
            ->execute(['chave' => $chave]);
    }

    public function limpar(string $chave): void
    {
        $this->pdo->prepare('DELETE FROM tentativas_login WHERE chave = :chave')->execute(['chave' => $chave]);
    }
}

<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../config/ambiente.php';

/** @return int */
function idPor(PDO $pdo, string $sql, array $valores): int
{
    $consulta = $pdo->prepare($sql);
    $consulta->execute($valores);
    $id = $consulta->fetchColumn();
    if ($id === false) {
        throw new RuntimeException('Não foi possível localizar o registro de demonstração.');
    }

    return (int) $id;
}

function inserirSeAusente(PDO $pdo, string $sql, array $valores): void
{
    $pdo->prepare($sql)->execute($valores);
}

$pdo = Database::conectar(require __DIR__ . '/../config/database.php');
$pdo->beginTransaction();

try {
    $senha = password_hash('dados-ficticios-apenas', PASSWORD_DEFAULT);
    foreach ([
        ['Admin Demonstração', 'admin.demo@asilosoft.local', 'Administrador'],
        ['Enf. Ana Lima', 'ana.lima@asilosoft.local', 'Profissional de Saúde'],
        ['Almox. Bruno Reis', 'bruno.reis@asilosoft.local', 'Almoxarife'],
        ['Carla Familiar', 'carla.familiar@asilosoft.local', 'Familiar'],
    ] as [$nome, $email, $perfil]) {
        inserirSeAusente($pdo, 'INSERT IGNORE INTO usuarios (nome,email,senha_hash,perfil) VALUES (?,?,?,?)', [$nome, $email, $senha, $perfil]);
    }

    inserirSeAusente($pdo, 'INSERT IGNORE INTO quartos (codigo,capacidade) VALUES (?,?)', ['A-101', 2]);
    $quarto = idPor($pdo, 'SELECT id FROM quartos WHERE codigo = ?', ['A-101']);
    inserirSeAusente($pdo, 'INSERT IGNORE INTO idosos (nome,cpf,identificador,quarto_id) VALUES (?,?,?,?)', ['José da Silva', '12345678901', 'IDOSO-DEMO-001', $quarto]);
    inserirSeAusente($pdo, 'INSERT IGNORE INTO idosos (nome,cpf,identificador,quarto_id) VALUES (?,?,?,?)', ['Maria Oliveira', '98765432100', 'IDOSO-DEMO-002', $quarto]);
    $idoso = idPor($pdo, 'SELECT id FROM idosos WHERE identificador = ?', ['IDOSO-DEMO-001']);
    $familiarUsuario = idPor($pdo, 'SELECT id FROM usuarios WHERE email = ?', ['carla.familiar@asilosoft.local']);
    $profissional = idPor($pdo, 'SELECT id FROM usuarios WHERE email = ?', ['ana.lima@asilosoft.local']);
    $almoxarife = idPor($pdo, 'SELECT id FROM usuarios WHERE email = ?', ['bruno.reis@asilosoft.local']);
    inserirSeAusente($pdo, 'INSERT IGNORE INTO familiares (usuario_id,nome,cpf,telefone,email,parentesco_principal) VALUES (?,?,?,?,?,?)', [$familiarUsuario, 'Carla Familiar', '11122233344', '(11) 99999-0000', 'carla.familiar@asilosoft.local', 'Filha']);
    $familiar = idPor($pdo, 'SELECT id FROM familiares WHERE cpf = ?', ['11122233344']);
    inserirSeAusente($pdo, 'INSERT IGNORE INTO idosos_familiares (idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (?,?,?,1)', [$idoso, $familiar, 'Filha']);
    inserirSeAusente($pdo, 'INSERT IGNORE INTO registros_saude (idoso_id,usuario_id,descricao,visivel_para_familiar) VALUES (?,?,?,1)', [$idoso, $profissional, 'Registro fictício para desenvolvimento.']);

    inserirSeAusente($pdo, 'INSERT IGNORE INTO materiais (nome,descricao,unidade_medida,estoque_minimo) VALUES (?,?,?,?)', ['Luvas descartáveis', 'Material fictício de demonstração', 'caixa', 2]);
    $material = idPor($pdo, 'SELECT id FROM materiais WHERE nome = ?', ['Luvas descartáveis']);
    foreach (['SUS', 'Plano de Saúde', 'Instituição'] as $origem) {
        $existe = idPor($pdo, 'SELECT COUNT(*) FROM movimentacoes_estoque WHERE material_id = ? AND tipo = "Entrada" AND origem = ?', [$material, $origem]);
        if ($existe === 0) {
            inserirSeAusente($pdo, 'INSERT INTO movimentacoes_estoque (material_id,tipo,quantidade,origem,usuario_id,observacao) VALUES (?,"Entrada",10,?,?,?)', [$material, $origem, $almoxarife, 'Entrada fictícia']);
        }
    }
    $uso = idPor($pdo, 'SELECT COUNT(*) FROM usos_materiais WHERE idoso_id = ? AND material_id = ?', [$idoso, $material]);
    if ($uso === 0) {
        inserirSeAusente($pdo, 'INSERT INTO usos_materiais (idoso_id,material_id,usuario_id,quantidade,observacao) VALUES (?,?,?,?,?)', [$idoso, $material, $profissional, 1, 'Uso fictício']);
    }
    $solicitacao = idPor($pdo, 'SELECT COUNT(*) FROM solicitacoes_compra WHERE material_id = ? AND observacao = ?', [$material, 'Solicitação fictícia']);
    if ($solicitacao === 0) {
        inserirSeAusente($pdo, 'INSERT INTO solicitacoes_compra (material_id,usuario_solicitante_id,quantidade,observacao) VALUES (?,?,?,?)', [$material, $almoxarife, 20, 'Solicitação fictícia']);
    }

    $pdo->commit();
    echo "Dados fictícios inseridos ou reutilizados com sucesso.\n";
} catch (Throwable $erro) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, $erro->getMessage() . "\n");
    exit(1);
}

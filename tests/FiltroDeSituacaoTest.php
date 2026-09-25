<?php

declare(strict_types=1);

require_once __DIR__ . '/TestCase.php';

if (is_file(__DIR__ . '/../app/Services/FiltroDeSituacao.php')) {
    require_once __DIR__ . '/../app/Services/FiltroDeSituacao.php';
}

function testarFiltroDeSituacaoSeparaAtivosEDesativados(): void
{
    if (!class_exists('FiltroDeSituacao')) {
        throw new FalhaDeTeste('O filtro de situação ainda não foi implementado.');
    }

    $filtro = new FiltroDeSituacao();
    $cadastros = [
        ['nome' => 'Ana', 'ativo' => true],
        ['nome' => 'Bruno', 'ativo' => false],
        ['nome' => 'Clara', 'ativo' => true],
    ];

    $ativos = $filtro->filtrar($cadastros, 'ativos');
    $desativados = $filtro->filtrar($cadastros, 'desativados');

    afirmarIgual(['Ana', 'Clara'], array_column($ativos, 'nome'), 'A aba de ativos deve ocultar os desativados.');
    afirmarIgual(['Bruno'], array_column($desativados, 'nome'), 'A aba de desativados deve ocultar os ativos.');
}

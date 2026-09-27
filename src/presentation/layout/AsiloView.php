<?php


require_once dirname(dirname(__DIR__)) . '/core/Session/AsiloSession.php';
require_once dirname(dirname(__DIR__)) . '/core/Security/CsrfToken.php';

final class AsiloView
{
    public static function render(string $viewFile, array $data = []): void
    {
        $session = new AsiloSession();
        $data['mensagemErro'] = $session->flash('erro');
        $data['mensagemSucesso'] = $session->flash('sucesso');
        $data['modoDemonstracao'] = $data['modoDemonstracao'] ?? false;
        $data['csrfSairToken'] = CsrfToken::obter('asilosoft_sair');
        $data['arquivoDaView'] = $viewFile;
        extract($data, EXTR_SKIP);

        require __DIR__ . '/asilo.php';
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

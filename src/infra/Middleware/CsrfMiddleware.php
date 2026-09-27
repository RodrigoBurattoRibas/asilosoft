<?php

require_once dirname(dirname(__DIR__)) . '/core/Security/CsrfToken.php';

class CsrfMiddleware
{
    private $escopo;
    private $campo;

    public function __construct($escopo, $campo = 'csrf_token')
    {
        $this->escopo = (string) $escopo;
        $this->campo = (string) $campo;
    }

    public function handle()
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            $this->responder(
                405,
                'Método de requisição inválido.'
            );
            return false;
        }

        $token = isset($_POST[$this->campo]) && is_string($_POST[$this->campo])
            ? $_POST[$this->campo]
            : '';

        // usado em algumas requisições expecificas
        if ($token === '' && isset($_SERVER['HTTP_X_CSRF_TOKEN']) && is_string($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }

        if (!CsrfToken::validar($token, $this->escopo)) {
            $this->responder(
                419,
                'A sessão do formulário expirou. Atualize a página e tente novamente.'
            );
            return false;
        }

        return true;
    }

    private function responder($status, $mensagem)
    {
        http_response_code((int) $status);
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
        }

        echo json_encode([
            'success' => false,
            'mensagem' => $mensagem,
        ], JSON_UNESCAPED_UNICODE);
    }
}

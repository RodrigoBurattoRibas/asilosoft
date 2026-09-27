<?php


final class RequestMethodMiddleware
{
    public function __construct(private string $expectedMethod)
    {
    }

    public function handle(): bool
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        if ($method === strtoupper($this->expectedMethod)) {
            return true;
        }

        http_response_code(405);
        header('Allow: ' . strtoupper($this->expectedMethod));
        echo 'Método de requisição inválido.';

        return false;
    }
}

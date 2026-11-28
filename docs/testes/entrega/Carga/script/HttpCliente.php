<?php
/**
 * Cliente HTTP mínimo para os testes de carga e segurança.
 * Usa somente sockets do PHP (sem a extensão curl), guarda cookies por instância
 * e mede o tempo de cada requisição em milissegundos.
 */
final class HttpCliente
{
    /** @var array<string,string> */
    public array $cookies = [];
    /** @var list<string> Cabeçalhos Set-Cookie brutos recebidos, para inspecionar atributos. */
    public array $setCookiesBrutos = [];

    public function __construct(
        private string $host,
        private int $porta,
        private float $timeout = 15.0
    ) {
    }

    /**
     * @param array<string,mixed>|string|null $post Campos do formulário ou corpo bruto.
     * @param array<string,string> $cabecalhos
     * @return array{status:int,headers:array<string,list<string>>,body:string,ms:float,erro:?string}
     */
    public function request(string $caminho, array|string|null $post = null, string $metodo = '', array $cabecalhos = []): array
    {
        $caminho = str_starts_with($caminho, '/') ? $caminho : '/?route='.$caminho;
        $metodo = $metodo !== '' ? strtoupper($metodo) : ($post === null ? 'GET' : 'POST');
        $corpo = is_array($post) ? http_build_query($post) : (string) ($post ?? '');

        $linhas = [
            $metodo.' '.$caminho.' HTTP/1.1',
            'Host: '.$this->host.':'.$this->porta,
            'User-Agent: AsiloSoft-Testes/1.0',
            'Accept: text/html,application/json',
            'Connection: close',
        ];
        if ($this->cookies !== []) {
            $pares = [];
            foreach ($this->cookies as $nome => $valor) $pares[] = $nome.'='.$valor;
            $linhas[] = 'Cookie: '.implode('; ', $pares);
        }
        foreach ($cabecalhos as $nome => $valor) $linhas[] = $nome.': '.$valor;
        if ($post !== null || in_array($metodo, ['POST', 'PUT', 'PATCH'], true)) {
            if (!isset($cabecalhos['Content-Type'])) $linhas[] = 'Content-Type: application/x-www-form-urlencoded';
            $linhas[] = 'Content-Length: '.strlen($corpo);
        }
        $requisicao = implode("\r\n", $linhas)."\r\n\r\n".$corpo;

        $inicio = hrtime(true);
        $socket = @stream_socket_client('tcp://'.$this->host.':'.$this->porta, $codigo, $mensagem, $this->timeout);
        if ($socket === false) {
            return $this->falha('conexao', $inicio);
        }
        stream_set_timeout($socket, (int) $this->timeout, (int) (fmod($this->timeout, 1) * 1e6));
        if (@fwrite($socket, $requisicao) === false) {
            fclose($socket);
            return $this->falha('escrita', $inicio);
        }

        $resposta = '';
        while (!feof($socket)) {
            $parte = fread($socket, 65536);
            if ($parte === false) break;
            $resposta .= $parte;
            if (stream_get_meta_data($socket)['timed_out']) {
                fclose($socket);
                return $this->falha('timeout', $inicio);
            }
        }
        fclose($socket);
        $ms = (hrtime(true) - $inicio) / 1e6;

        $separador = strpos($resposta, "\r\n\r\n");
        if ($separador === false || !preg_match('#^HTTP/\d\.\d (\d{3})#', $resposta, $m)) {
            return ['status' => 0, 'headers' => [], 'body' => '', 'ms' => $ms, 'erro' => 'resposta_invalida'];
        }
        $status = (int) $m[1];
        $headers = [];
        foreach (array_slice(explode("\r\n", substr($resposta, 0, $separador)), 1) as $linha) {
            if (!str_contains($linha, ':')) continue;
            [$nome, $valor] = explode(':', $linha, 2);
            $headers[strtolower(trim($nome))][] = trim($valor);
        }
        $body = substr($resposta, $separador + 4);
        if (in_array('chunked', array_map('strtolower', $headers['transfer-encoding'] ?? []), true)) {
            $body = self::decodificarChunked($body);
        }
        foreach ($headers['set-cookie'] ?? [] as $cookie) {
            $this->setCookiesBrutos[] = $cookie;
            [$par] = explode(';', $cookie, 2);
            if (!str_contains($par, '=')) continue;
            [$nome, $valor] = explode('=', $par, 2);
            if ($valor === '' || $valor === 'deleted') unset($this->cookies[trim($nome)]);
            else $this->cookies[trim($nome)] = trim($valor);
        }

        return ['status' => $status, 'headers' => $headers, 'body' => $body, 'ms' => $ms, 'erro' => null];
    }

    /** Token CSRF do último formulário da página (o primeiro costuma ser o de "Sair"). */
    public static function tokenFormulario(string $html): ?string
    {
        preg_match_all('/name="csrf_token"\s+value="([^"]+)"/', $html, $m);
        return $m[1] === [] ? null : html_entity_decode(end($m[1]), ENT_QUOTES);
    }

    public static function campo(string $html, string $nome): ?string
    {
        if (!preg_match('/name="'.preg_quote($nome, '/').'"\s+value="([^"]*)"/', $html, $m)) return null;
        return html_entity_decode($m[1], ENT_QUOTES);
    }

    public function entrar(string $email, string $senha): bool
    {
        $pagina = $this->request('login');
        $token = self::campo($pagina['body'], 'csrf_token');
        if ($token === null) return false;
        $resposta = $this->request('entrar', ['csrf_token' => $token, 'email' => $email, 'senha' => $senha]);
        return $resposta['status'] === 303
            && str_contains(implode('', $resposta['headers']['location'] ?? []), 'route=home');
    }

    /** @return array{status:int,headers:array,body:string,ms:float,erro:string} */
    private function falha(string $tipo, int $inicio): array
    {
        return ['status' => 0, 'headers' => [], 'body' => '', 'ms' => (hrtime(true) - $inicio) / 1e6, 'erro' => $tipo];
    }

    private static function decodificarChunked(string $corpo): string
    {
        $saida = '';
        while ($corpo !== '') {
            $fim = strpos($corpo, "\r\n");
            if ($fim === false) break;
            $tamanho = hexdec(trim(explode(';', substr($corpo, 0, $fim))[0]));
            if ($tamanho === 0) break;
            $saida .= substr($corpo, $fim + 2, $tamanho);
            $corpo = substr($corpo, $fim + 2 + $tamanho + 2);
        }
        return $saida;
    }
}

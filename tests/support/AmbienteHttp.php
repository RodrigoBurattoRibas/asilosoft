<?php
/**
 * Ambiente isolado para testes HTTP de carga e segurança.
 * Cria uma base própria (prefixo asilosoft_test_), aplica as migrations oficiais,
 * insere massa fictícia e sobe um servidor PHP embutido com router.php.
 * Nunca usa a base MYSQLDATABASE da aplicação e remove a base criada ao encerrar.
 */
require_once __DIR__.'/../../src/infra/Database/DatabaseMigrator.php';

final class AmbienteHttp
{
    public const SENHA = 'SenhaTeste123';

    public PDO $pdo;
    public string $base;
    public int $porta;
    /** @var array<string,list<array{id:int,email:string}>> */
    public array $usuarios = [];
    public int $totalIdosos = 0;
    public int $totalMateriais = 0;

    private array $cfg;
    private $servidor = null;
    private ?string $prepend = null;
    private bool $baseCriada = false;

    /**
     * Conexão: variáveis ASILOSOFT_TEST_DB_HOST, PORTA, USUARIO e SENHA (padrão das suítes).
     * Com $conexaoDaAplicacao, reutiliza o servidor do .env/ambiente, como tests/integration/run.php,
     * mas continua criando uma base auxiliar separada.
     */
    public function __construct(string $suite, bool $conexaoDaAplicacao = false)
    {
        $this->cfg = $conexaoDaAplicacao ? self::conexaoDaAplicacao() : self::conexaoExplicita();
        $this->base = 'asilosoft_test_'.$suite.'_'.date('YmdHis').'_'.getmypid();
        if (!preg_match('/^asilosoft_test_[a-z0-9_]+$/D', $this->base)) {
            throw new RuntimeException('Nome de base de teste inseguro: '.$this->base);
        }
        $this->pdo = new PDO(
            'mysql:host='.$this->cfg['HOST'].';port='.$this->cfg['PORTA'].';charset=utf8mb4',
            $this->cfg['USUARIO'],
            $this->cfg['SENHA'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_TIMEOUT => 5]
        );
        $existe = $this->pdo->prepare('SELECT COUNT(*) FROM information_schema.schemata WHERE schema_name=?');
        $existe->execute([$this->base]);
        if ((int) $existe->fetchColumn() !== 0) throw new RuntimeException('A base '.$this->base.' já existe; nada foi alterado.');
    }

    public function host(): string
    {
        return $this->cfg['HOST'];
    }

    /** Cria a base, aplica migrations e insere a massa fictícia. */
    public function preparar(int $usuariosPorPerfil, int $idosos, int $materiais, int $registrosPorIdoso = 2): void
    {
        $this->pdo->exec('CREATE DATABASE `'.$this->base.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->baseCriada = true;
        $this->pdo->exec('USE `'.$this->base.'`');
        (new DatabaseMigrator())->migrate($this->pdo, dirname(__DIR__, 2).'/src/infra/Database/migrations');

        $hash = password_hash(self::SENHA, PASSWORD_DEFAULT);
        $usuario = $this->pdo->prepare('INSERT INTO usuarios(nome,email,senha_hash,perfil) VALUES (?,?,?,?)');
        $perfis = ['admin' => 'Administrador', 'saude' => 'Profissional de Saúde', 'estoque' => 'Almoxarife', 'familia' => 'Familiar'];
        foreach ($perfis as $chave => $perfil) {
            for ($i = 1; $i <= $usuariosPorPerfil; $i++) {
                $email = sprintf('%s%02d@example.test', $chave, $i);
                $usuario->execute([sprintf('Usuário Teste %s %02d', ucfirst($chave), $i), $email, $hash, $perfil]);
                $this->usuarios[$chave][] = ['id' => (int) $this->pdo->lastInsertId(), 'email' => $email];
            }
        }

        $this->pdo->beginTransaction();
        $quarto = $this->pdo->prepare('INSERT INTO quartos(codigo,capacidade) VALUES (?,99)');
        $quartos = [];
        for ($q = 1; $q <= max(1, (int) ceil($idosos / 90)); $q++) {
            $quarto->execute([sprintf('T%02d', $q)]);
            $quartos[] = (int) $this->pdo->lastInsertId();
        }
        $idoso = $this->pdo->prepare('INSERT INTO idosos(nome,cpf,identificador,quarto_id) VALUES (?,?,?,?)');
        for ($i = 1; $i <= $idosos; $i++) {
            $idoso->execute([sprintf('Residente Teste %04d', $i), sprintf('9%010d', $i), sprintf('RES-%04d', $i), $quartos[intdiv($i - 1, 90)]]);
        }
        $this->totalIdosos = $idosos;

        $saude = $this->usuarios['saude'][0]['id'];
        $registro = $this->pdo->prepare('INSERT INTO registros_saude(idoso_id,usuario_id,descricao,visivel_para_familiar) VALUES (?,?,?,?)');
        for ($i = 1; $i <= $idosos; $i++) {
            for ($r = 1; $r <= $registrosPorIdoso; $r++) {
                $registro->execute([$i, $saude, 'Acompanhamento fictício '.$r.' do residente '.$i, $r % 2]);
            }
        }

        $admin = $this->usuarios['admin'][0]['id'];
        $material = $this->pdo->prepare('INSERT INTO materiais(nome,unidade_medida) VALUES (?,?)');
        $entrada = $this->pdo->prepare("INSERT INTO movimentacoes_estoque(material_id,tipo,quantidade,origem,usuario_id) VALUES (?,'Entrada',100000,'SUS',?)");
        for ($m = 1; $m <= $materiais; $m++) {
            $material->execute([sprintf('Material Teste %02d', $m), 'un']);
            $entrada->execute([(int) $this->pdo->lastInsertId(), $admin]);
        }
        $this->totalMateriais = $materiais;

        $familiar = $this->pdo->prepare('INSERT INTO familiares(usuario_id,nome,telefone) VALUES (?,?,?)');
        $vinculo = $this->pdo->prepare('INSERT INTO idosos_familiares(idoso_id,familiar_id,parentesco,responsavel_principal) VALUES (?,?,?,1)');
        foreach ($this->usuarios['familia'] as $indice => $conta) {
            $familiar->execute([$conta['id'], 'Familiar Teste '.($indice + 1), '(47) 90000-0000']);
            $vinculo->execute([min($indice + 1, $idosos), (int) $this->pdo->lastInsertId(), 'Filho(a)']);
        }
        $this->pdo->commit();
    }

    /** Sobe php -S com router.php apontando para a base de teste. $workers > 1 ativa PHP_CLI_SERVER_WORKERS. */
    public function iniciarServidor(int $porta, int $workers, string $log): void
    {
        $this->porta = $porta;
        $socket = @fsockopen('127.0.0.1', $porta, $e, $m, 0.2);
        if ($socket) {
            fclose($socket);
            throw new RuntimeException("Porta $porta já está em uso. Defina outra com ASILOSOFT_TEST_HTTP_PORT.");
        }
        $variaveis = [
            'MYSQLHOST' => $this->cfg['HOST'],
            'MYSQLPORT' => (string) $this->cfg['PORTA'],
            'MYSQLDATABASE' => $this->base,
            'MYSQLUSER' => $this->cfg['USUARIO'],
            'MYSQLPASSWORD' => $this->cfg['SENHA'],
        ];
        $env = getenv();
        foreach ($variaveis as $chave => $valor) $env[$chave] = $valor;
        $env['ASILOSOFT_DEBUG'] = '0';
        unset($env['PHP_CLI_SERVER_WORKERS']);
        if ($workers > 1) $env['PHP_CLI_SERVER_WORKERS'] = (string) $workers;

        // Valores vazios podem sumir no proc_open; cada requisição redefine as variáveis
        // de teste para nunca cair no .env da aplicação.
        $this->prepend = tempnam(sys_get_temp_dir(), 'asilo-teste-env-');
        $codigo = '<?php ';
        foreach ($variaveis as $chave => $valor) $codigo .= 'putenv('.var_export($chave.'='.$valor, true).');';
        file_put_contents($this->prepend, $codigo);

        $raiz = dirname(__DIR__, 2);
        $this->servidor = proc_open(
            [PHP_BINARY, '-d', 'auto_prepend_file='.$this->prepend, '-d', 'display_errors=0', '-S', '127.0.0.1:'.$porta, '-t', $raiz, $raiz.'/router.php'],
            [0 => ['file', '/dev/null', 'r'], 1 => ['file', $log, 'a'], 2 => ['file', $log, 'a']],
            $pipes,
            $raiz,
            $env
        );
        if (!is_resource($this->servidor)) throw new RuntimeException('Não foi possível iniciar o servidor PHP.');
        for ($i = 0; $i < 100; $i++) {
            $socket = @fsockopen('127.0.0.1', $porta, $e, $m, 0.2);
            if ($socket) {
                fclose($socket);
                return;
            }
            usleep(50000);
        }
        throw new RuntimeException('O servidor PHP não respondeu na porta '.$porta.'. Veja '.$log);
    }

    /** PIDs do servidor e dos workers, para medir CPU/memória. @return list<int> */
    public function pidsServidor(): array
    {
        if ($this->prepend === null) return [];
        $pids = [];
        foreach (glob('/proc/[0-9]*/cmdline') ?: [] as $arquivo) {
            $cmd = @file_get_contents($arquivo);
            if ($cmd !== false && str_contains($cmd, $this->prepend)) $pids[] = (int) basename(dirname($arquivo));
        }
        return $pids;
    }

    public function pararServidor(): void
    {
        $pids = $this->pidsServidor();
        if (is_resource($this->servidor)) proc_terminate($this->servidor);
        foreach ($pids as $pid) @posix_kill($pid, SIGTERM);
        usleep(300000);
        foreach ($this->pidsServidor() as $pid) @posix_kill($pid, SIGKILL);
        if (is_resource($this->servidor)) proc_close($this->servidor);
        $this->servidor = null;
        if ($this->prepend) @unlink($this->prepend);
        $this->prepend = null;
    }

    /** Encerra o servidor e remove somente a base criada por esta instância. */
    public function encerrar(): void
    {
        $this->pararServidor();
        if ($this->baseCriada) {
            $this->pdo->exec('DROP DATABASE IF EXISTS `'.$this->base.'`');
            $this->baseCriada = false;
        }
    }

    public function reconectar(): void
    {
        $this->pdo = new PDO(
            'mysql:host='.$this->cfg['HOST'].';port='.$this->cfg['PORTA'].';dbname='.$this->base.';charset=utf8mb4',
            $this->cfg['USUARIO'],
            $this->cfg['SENHA'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_TIMEOUT => 5]
        );
    }

    private static function conexaoExplicita(): array
    {
        $cfg = [];
        foreach (['HOST', 'PORTA', 'USUARIO', 'SENHA'] as $chave) {
            $valor = getenv('ASILOSOFT_TEST_DB_'.$chave);
            if ($valor === false) {
                throw new RuntimeException(
                    'Defina ASILOSOFT_TEST_DB_'.$chave.' (MySQL exclusivo de testes) ou use --conexao-da-aplicacao. Veja tests/README.md.'
                );
            }
            $cfg[$chave] = $valor;
        }
        return $cfg;
    }

    private static function conexaoDaAplicacao(): array
    {
        require_once dirname(__DIR__, 2).'/src/infra/Database/Database.php';
        $ler = new ReflectionMethod(PDOMysqli::class, 'envValue');
        return [
            'HOST' => $ler->invoke(null, ['MYSQLHOST', 'ASILOSOFT_DB_HOST'], null),
            'PORTA' => $ler->invoke(null, ['MYSQLPORT', 'ASILOSOFT_DB_PORTA'], '3306'),
            'USUARIO' => $ler->invoke(null, ['MYSQLUSER', 'ASILOSOFT_DB_USUARIO'], null),
            'SENHA' => $ler->invoke(null, ['MYSQLPASSWORD', 'MYSQL_ROOT_PASSWORD', 'ASILOSOFT_DB_SENHA'], ''),
        ];
    }
}

<?php
require_once dirname(dirname(__DIR__)) . '/core/Config/Environment.php';
class PDOMysqli
{
    private static ?PDO $conn = null;
    public static function connection(): PDO
    {
        if (self::$conn instanceof PDO) return self::$conn;
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', Environment::get('ASILOSOFT_DB_HOST','127.0.0.1'), Environment::get('ASILOSOFT_DB_PORTA','3306'), Environment::get('ASILOSOFT_DB_NOME','asilosoft'));
        self::$conn = new PDO($dsn, Environment::get('ASILOSOFT_DB_USUARIO','root'), Environment::get('ASILOSOFT_DB_SENHA'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
        return self::$conn;
    }
    public static function transactional(int $usuarioId, callable $operation): mixed
    {
        $pdo = self::connection();
        $pdo->beginTransaction();
        try {
            $statement = $pdo->prepare('SET @asilosoft_usuario_id = ?'); $statement->execute([$usuarioId]);
            $result = $operation($pdo);
            $pdo->commit(); $pdo->exec('SET @asilosoft_usuario_id = NULL');
            return $result;
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $pdo->exec('SET @asilosoft_usuario_id = NULL');
            throw $error;
        }
    }
}

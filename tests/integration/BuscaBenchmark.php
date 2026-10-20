<?php
// Medição local, não certifica RNF05. Requer base de teste já migrada.
foreach (['HOST','PORTA','NOME','USUARIO','SENHA'] as $key) {
    if (getenv('ASILOSOFT_TEST_DB_'.$key) === false) throw new RuntimeException('Defina ASILOSOFT_TEST_DB_'.$key);
}
$name=getenv('ASILOSOFT_TEST_DB_NOME');
if (!preg_match('/^asilosoft_test_[a-z0-9_]+$/D',$name)) throw new RuntimeException('Base de teste obrigatória.');
$pdo=new PDO('mysql:host='.getenv('ASILOSOFT_TEST_DB_HOST').';port='.getenv('ASILOSOFT_TEST_DB_PORTA').';dbname='.$name.';charset=utf8mb4',getenv('ASILOSOFT_TEST_DB_USUARIO'),getenv('ASILOSOFT_TEST_DB_SENHA'),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
require_once __DIR__.'/../../src/modules/idosos/repository/AsiloIdosoRepository.php';
$pdo->beginTransaction();
try {
    $insert=$pdo->prepare('INSERT INTO idosos(nome,cpf,identificador) VALUES (?,?,?)');
    for ($i=0;$i<1000;$i++) $insert->execute(['Benchmark residente '.sprintf('%04d',$i),(string)(90000000000+$i),'BENCH-'.$i]);
    $repo=new AsiloIdosoRepository($pdo);
    foreach (['residente','900.000.005-00','BENCH-500'] as $term) {
        $times=[];
        for ($i=0;$i<100;$i++) { $start=hrtime(true);$repo->pesquisar($term,true);$times[]=(hrtime(true)-$start)/1e6; }
        sort($times);
        printf("%s: mediana %.3f ms; p95 %.3f ms; máximo %.3f ms\n",$term,$times[49],$times[94],$times[99]);
    }
    echo 'Massa: '.$pdo->query('SELECT COUNT(*) FROM idosos')->fetchColumn()." idosos; 100 consultas por termo; PDO/MySQL local, sem carga simultânea.\n";
} finally { $pdo->rollBack(); }

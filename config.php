<?php
// Ajuste os dados abaixo conforme a configuração do MySQL no seu computador.
$host = 'sql103.infinityfree.com';
$banco = 'if0_43447803_dulcen';
$usuario = 'if0_43447803';
$senha = 'John1348094';

$dsn = "mysql:host={$host};dbname={$banco};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $usuario, $senha, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Erro ao conectar ao banco de dados. Confira o arquivo config.php e se o MySQL está ativo.');
}

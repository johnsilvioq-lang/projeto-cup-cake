<?php
// Configuração do banco de dados.
// Padrão do XAMPP/WAMP: usuário "root" sem senha. Ajuste conforme o seu ambiente
// (ou defina as variáveis de ambiente DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS).
return [
    'host'    => getenv('DB_HOST') ?: '127.0.0.1',
    'port'    => getenv('DB_PORT') ?: '3306',
    'dbname'  => getenv('DB_NAME') ?: 'dulcen',
    'user'    => getenv('DB_USER') ?: 'root',
    'pass'    => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'charset' => 'utf8mb4',
];

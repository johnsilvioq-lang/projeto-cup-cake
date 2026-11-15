<?php
// FRONT CONTROLLER / ROTAS: todas as requisições passam por aqui.
ini_set('display_errors', '0');
require_once __DIR__ . '/app/controllers/UsuarioController.php';

// Pasta base (funciona em http://localhost/ e em http://localhost/dulcen/)
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$caminho = trim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen($base)), '/');
$caminho = preg_replace('#^index\.php/?#', '', $caminho);
$metodo = $_SERVER['REQUEST_METHOD'];

function view(string $nome, string $base): void
{
    header('Content-Type: text/html; charset=utf-8');
    require __DIR__ . "/app/views/{$nome}.php";
}

if ($metodo === 'GET' && ($caminho === '' || $caminho === 'login')) {
    view('login', $base);
} elseif ($metodo === 'GET' && $caminho === 'cadastro') {
    view('cadastro', $base);
} elseif ($metodo === 'POST' && $caminho === 'api/usuarios') {
    UsuarioController::cadastrar();
} elseif ($metodo === 'POST' && $caminho === 'api/login') {
    UsuarioController::login();
} else {
    http_response_code(404);
    echo 'Página não encontrada.';
}

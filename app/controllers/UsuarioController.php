<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/Validacao.php';

// CONTROLLER: recebe a requisição, valida, chama o model e responde em JSON.
class UsuarioController
{
    private static function entrada(): array
    {
        $json = json_decode(file_get_contents('php://input'), true);
        return is_array($json) ? $json : [];
    }

    private static function responder(int $status, array $corpo): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($corpo, JSON_UNESCAPED_UNICODE);
    }

    // POST /api/usuarios
    public static function cadastrar(): void
    {
        [$erros, $dados] = Validacao::cadastro(self::entrada());
        if ($erros) {
            self::responder(400, ['ok' => false, 'erros' => $erros]);
            return;
        }

        try {
            if (Usuario::buscarPorEmail($dados['email'])) {
                $erros['email'] = 'Este e-mail já está cadastrado.';
            }
            if (Usuario::buscarPorCpf($dados['cpf'])) {
                $erros['cpf'] = 'Este CPF já está cadastrado.';
            }
            if ($erros) {
                self::responder(409, ['ok' => false, 'erros' => $erros]);
                return;
            }

            $id = Usuario::criar($dados);
            self::responder(201, ['ok' => true, 'id' => $id]);
        } catch (PDOException $e) {
            // 1062 = chave duplicada (corrida entre duas requisições)
            if (($e->errorInfo[1] ?? 0) === 1062) {
                self::responder(409, ['ok' => false, 'erros' => ['email' => 'E-mail ou CPF já cadastrado.']]);
                return;
            }
            error_log($e->getMessage());
            self::responder(500, ['ok' => false, 'mensagem' => 'Erro ao acessar o banco de dados.']);
        }
    }

    // POST /api/login
    public static function login(): void
    {
        $in = self::entrada();
        $email = mb_strtolower(trim((string) ($in['email'] ?? '')));
        $senha = (string) ($in['senha'] ?? '');

        try {
            $usuario = Usuario::buscarPorEmail($email);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            self::responder(500, ['ok' => false, 'mensagem' => 'Erro ao acessar o banco de dados.']);
            return;
        }

        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            self::responder(401, ['ok' => false, 'mensagem' => 'E-mail ou senha incorretos.']);
            return;
        }
        $primeiroNome = explode(' ', $usuario['nome_completo'])[0];
        self::responder(200, ['ok' => true, 'nome' => $primeiroNome]);
    }
}

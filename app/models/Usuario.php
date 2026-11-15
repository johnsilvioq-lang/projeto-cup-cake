<?php

require_once __DIR__ . '/../../config/Database.php';

// MODEL: único lugar que conversa com a tabela "usuarios".
// Cada campo do formulário de cadastro corresponde a uma coluna.
class Usuario
{
    public static function criar(array $d): int
    {
        $sql = 'INSERT INTO usuarios
                  (nome_completo, data_nascimento, cpf, email, telefone, rua, bairro, numero_casa, senha_hash)
                VALUES
                  (:nome, :nascimento, :cpf, :email, :telefone, :rua, :bairro, :numero, :senha_hash)';
        $stmt = Database::conectar()->prepare($sql);
        $stmt->execute([
            ':nome'       => $d['nome'],
            ':nascimento' => $d['nascimento'],
            ':cpf'        => $d['cpf'],
            ':email'      => $d['email'],
            ':telefone'   => $d['telefone'],
            ':rua'        => $d['rua'],
            ':bairro'     => $d['bairro'],
            ':numero'     => $d['numero'],
            ':senha_hash' => password_hash($d['senha'], PASSWORD_DEFAULT),
        ]);
        return (int) Database::conectar()->lastInsertId();
    }

    public static function buscarPorEmail(string $email): ?array
    {
        $stmt = Database::conectar()->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public static function buscarPorCpf(string $cpf): ?array
    {
        $stmt = Database::conectar()->prepare('SELECT id FROM usuarios WHERE cpf = :cpf LIMIT 1');
        $stmt->execute([':cpf' => $cpf]);
        return $stmt->fetch() ?: null;
    }
}

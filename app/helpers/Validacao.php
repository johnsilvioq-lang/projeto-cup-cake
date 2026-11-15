<?php

class Validacao
{
    public static function digitos(?string $v): string
    {
        return preg_replace('/\D/', '', (string) $v);
    }

    public static function cpfValido(string $valor): bool
    {
        $cpf = self::digitos($valor);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * ($t + 1 - $i);
            }
            if ((($soma * 10) % 11) % 10 !== (int) $cpf[$t]) {
                return false;
            }
        }
        return true;
    }

    // Valida os campos do formulário de cadastro.
    // As chaves de "erros" são os mesmos ids dos campos do formulário.
    // Retorna [erros, dados limpos].
    public static function cadastro(array $in): array
    {
        $erros = [];
        $txt = fn($k) => trim((string) ($in[$k] ?? ''));

        $nome = $txt('nome');
        if (count(preg_split('/\s+/', $nome, -1, PREG_SPLIT_NO_EMPTY)) < 2 || mb_strlen($nome) > 120) {
            $erros['nome'] = 'Digite seu nome completo.';
        }

        $nasc = $txt('nascimento');
        $partes = explode('-', $nasc);
        $dataOk = preg_match('/^\d{4}-\d{2}-\d{2}$/', $nasc)
            && checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])
            && $nasc <= date('Y-m-d') && $nasc >= '1900-01-01';
        if (!$dataOk) {
            $erros['nascimento'] = 'Informe uma data válida.';
        }

        $cpf = self::digitos($txt('cpf'));
        if (!self::cpfValido($cpf)) {
            $erros['cpf'] = 'CPF inválido.';
        }

        $email = mb_strtolower($txt('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 160) {
            $erros['email'] = 'Digite um e-mail válido.';
        } elseif (mb_strtolower($txt('email2')) !== $email) {
            $erros['email2'] = 'Os e-mails não coincidem.';
        }

        $tel = self::digitos($txt('telefone'));
        if (strlen($tel) !== 10 && strlen($tel) !== 11) {
            $erros['telefone'] = 'Digite um telefone com DDD.';
        }

        $rua = $txt('rua');
        if (mb_strlen($rua) < 3 || mb_strlen($rua) > 120) {
            $erros['rua'] = 'Informe a rua.';
        }

        $bairro = $txt('bairro');
        if (mb_strlen($bairro) < 2 || mb_strlen($bairro) > 80) {
            $erros['bairro'] = 'Informe o bairro.';
        }

        $numero = $txt('numero');
        if (mb_strlen($numero) < 1 || mb_strlen($numero) > 10) {
            $erros['numero'] = 'Informe o número.';
        }

        $senha = (string) ($in['senha'] ?? '');
        if (mb_strlen($senha) < 6 || mb_strlen($senha) > 100) {
            $erros['senha'] = 'A senha deve ter pelo menos 6 caracteres.';
        } elseif ((string) ($in['senha2'] ?? '') !== $senha) {
            $erros['senha2'] = 'As senhas não coincidem.';
        }

        return [$erros, [
            'nome' => $nome, 'nascimento' => $nasc, 'cpf' => $cpf, 'email' => $email,
            'telefone' => $tel, 'rua' => $rua, 'bairro' => $bairro, 'numero' => $numero,
            'senha' => $senha,
        ]];
    }
}

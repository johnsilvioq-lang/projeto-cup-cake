<?php
require_once __DIR__ . '/config.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = preg_replace('/\\D/', '', $_POST['cpf'] ?? '');
    $telefone = preg_replace('/\\D/', '', $_POST['telefone'] ?? '');
    $email = mb_strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if (count(preg_split('/\\s+/', $nome)) < 2) {
        $erro = 'Informe seu nome completo.';
    } elseif (!cpf_valido($cpf)) {
        $erro = 'CPF inválido.';
    } elseif (!in_array(strlen($telefone), [10, 11], true)) {
        $erro = 'Informe um telefone com DDD.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, cpf, telefone, email, senha) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$nome, $cpf, $telefone, $email, password_hash($senha, PASSWORD_DEFAULT)]);
            header('Location: loguin_page.php?cadastro=sucesso');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erro = 'Este CPF ou e-mail já está cadastrado.';
            } else {
                error_log($e->getMessage());
                $erro = 'Não foi possível concluir o cadastro. Tente novamente.';
            }
        }
    }
}
function cpf_valido(string $cpf): bool {
    if (strlen($cpf) !== 11 || preg_match('/^(.)\\1*$/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) $soma += (int)$cpf[$i] * ($t + 1 - $i);
        $digito = (($soma * 10) % 11) % 10;
        if ($digito !== (int)$cpf[$t]) return false;
    }
    return true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dulcen Cup Cakes | Cadastro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&amp;family=Nunito:wght@400;600;700&amp;display=swap"
      rel="stylesheet">
    <style>
  :root {
    --rosa: #f45b8d;
    --rosa-escuro: #d93f72;
    --rosa-claro: #ffe4ee;
    --creme: #fff8f0;
    --marrom: #6b3f2a;
    --texto: #4a2c20;
    --cinza: #8a7a72;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Nunito', sans-serif;
    color: var(--texto);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
    background:
      radial-gradient(circle at 15% 20%, #ffd1e3 0, transparent 40%),
      radial-gradient(circle at 85% 80%, #ffe9c7 0, transparent 40%),
      var(--creme);
  }
  .card {
    width: 100%;
    max-width: 400px;
    background: #fff;
    border-radius: 24px;
    padding: 32px 32px 28px;
    box-shadow: 0 20px 50px rgba(244, 91, 141, .18);
  }
  .logo { text-align: center; margin-bottom: 8px; }
  .logo-img { width: 120px; height: 120px; object-fit: contain; display: block; margin: 0 auto; }
  .logo p { color: var(--cinza); font-size: .9rem; margin-top: 4px; }
  h2 {
    font-family: 'Pacifico', cursive;
    font-weight: 400;
    font-size: 1.6rem;
    color: var(--rosa);
    text-align: center;
    margin: 8px 0 4px;
  }
  label { display: block; font-weight: 700; font-size: .9rem; margin: 14px 0 6px; }
  .campo { position: relative; }
  input {
    width: 100%;
    padding: 13px 14px;
    border: 2px solid var(--rosa-claro);
    border-radius: 12px;
    font: inherit;
    color: var(--texto);
    background: #fffafc;
    transition: border-color .2s, box-shadow .2s;
  }
  input:focus {
    outline: none;
    border-color: var(--rosa);
    box-shadow: 0 0 0 4px rgba(244, 91, 141, .15);
  }
  input.erro { border-color: #d93025; }
  .olho {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    font-size: 1.1rem; padding: 4px;
  }
  .msg-erro { color: #d93025; font-size: .8rem; margin-top: 4px; min-height: 1em; }
  a { color: var(--rosa-escuro); text-decoration: none; font-weight: 700; }
  a:hover { text-decoration: underline; }
  button.entrar {
    width: 100%;
    margin-top: 22px;
    padding: 14px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--rosa), var(--rosa-escuro));
    color: #fff;
    font: 700 1rem 'Nunito', sans-serif;
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
    box-shadow: 0 8px 18px rgba(244, 91, 141, .35);
  }
  button.entrar:hover { transform: translateY(-2px); }
  button.entrar:active { transform: translateY(0); }
  .cadastro { text-align: center; margin-top: 22px; font-size: .9rem; color: var(--cinza); }
  .sucesso {
    display: none; margin-top: 16px; padding: 12px; text-align: center;
    background: #e8f7ee; color: #1e7a45; border-radius: 12px; font-weight: 700; font-size: .9rem;
  }
</style> </head>
  <body>
    <main class="card">
      <div class="logo"> <img class="logo-img" src="https://i.ibb.co/5gYVr1PL/logo-t.png"
          alt="Logo Dulcen Cup Cakes">
        <p>Cupcakes artesanais feitos com amor</p>
      </div>
      <h2>Crie sua conta</h2>
      <form id="form-cadastro" method="POST" action="cadastro.php">

        <label for="nome">Nome</label>
        <input id="nome" placeholder="Seu nome completo" autocomplete="name" required type="text" name="nome">
        <div class="msg-erro" id="erro-nome"></div>

        <label for="cpf">CPF</label>
        <input id="cpf" placeholder="000.000.000-00" inputmode="numeric" maxlength="14" required type="text" name="cpf">
        <div class="msg-erro" id="erro-cpf"></div>

        <label for="telefone">Telefone</label>
        <input id="telefone" placeholder="(11) 91234-5678" autocomplete="tel" inputmode="tel" maxlength="15" required type="tel" name="telefone">
        <div class="msg-erro" id="erro-telefone"></div>

        <label for="email">E-mail</label>
        <input id="email" placeholder="voce@exemplo.com" autocomplete="email" required type="email" name="email">
        <div class="msg-erro" id="erro-email"></div>

        <label for="senha">Senha</label>
        <div class="campo"> <input id="senha" placeholder="Mínimo de 6 caracteres" autocomplete="new-password" required type="password" name="senha">
          <button type="button" class="olho" id="toggle" aria-label="Mostrar senha">👁️</button> </div>
        <div class="msg-erro" id="erro-senha"></div>

        <button type="submit" class="entrar">Cadastrar 🧁</button>
        <?php if (!empty($erro)): ?><div class="msg-erro" style="margin-top:12px"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
      </form>
      <p class="cadastro">Já tem uma conta? <a href="loguin_page.php">Entrar</a></p>
    </main>
    <script>
  const $ = (id) => document.getElementById(id);
  const form = $('form-cadastro');

  // Mostrar/ocultar senha
  $('toggle').addEventListener('click', () => {
    const oculto = $('senha').type === 'password';
    $('senha').type = oculto ? 'text' : 'password';
    $('toggle').textContent = oculto ? '🙈' : '👁️';
  });

  // Máscara de CPF
  $('cpf').addEventListener('input', (e) => {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11);
    v = v.replace(/(\d{3})(\d)/, '$1.$2')
         .replace(/(\d{3})(\d)/, '$1.$2')
         .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    e.target.value = v;
  });

  // Máscara de telefone
  $('telefone').addEventListener('input', (e) => {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11);
    if (v.length > 10) v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    else if (v.length > 6) v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    else if (v.length > 2) v = v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    e.target.value = v;
  });

  // Validação de CPF (dígitos verificadores)
  function cpfValido(cpf) {
    cpf = cpf.replace(/\D/g, '');
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
    for (let t = 9; t < 11; t++) {
      let soma = 0;
      for (let i = 0; i < t; i++) soma += Number(cpf[i]) * (t + 1 - i);
      const dig = ((soma * 10) % 11) % 10;
      if (dig !== Number(cpf[t])) return false;
    }
    return true;
  }

  function marcar(id, valido, msg) {
    $('erro-' + id).textContent = valido ? '' : msg;
    $(id).classList.toggle('erro', !valido);
    return valido;
  }

  form.addEventListener('submit', (e) => {
    const resultados = [];
    resultados.push(marcar('nome', $('nome').value.trim().split(/\s+/).length >= 2, 'Digite seu nome completo.'));
    resultados.push(marcar('cpf', cpfValido($('cpf').value), 'CPF inválido.'));
    const tel = $('telefone').value.replace(/\D/g, '');
    resultados.push(marcar('telefone', tel.length === 10 || tel.length === 11, 'Digite um telefone com DDD.'));
    resultados.push(marcar('email', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($('email').value.trim()), 'Digite um e-mail válido.'));
    resultados.push(marcar('senha', $('senha').value.length >= 6, 'A senha deve ter pelo menos 6 caracteres.'));
    if (!resultados.every(Boolean)) {
      e.preventDefault();
      const primeiro = form.querySelector('input.erro');
      if (primeiro) primeiro.focus();
    }
  });
</script> </body>
</html>

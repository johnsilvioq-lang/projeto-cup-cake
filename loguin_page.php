<?php
session_start();
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mb_strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
        $erro = 'Informe um e-mail e uma senha válidos.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nome, email, senha FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = [
                'id' => (int)$usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email']
            ];
            header('Location: index.php');
            exit;
        }
        $erro = 'E-mail ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dulcen Cup Cakes | Login</title>
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
    padding: 36px 32px 28px;
    box-shadow: 0 20px 50px rgba(244, 91, 141, .18);
  }
  .logo { text-align: center; margin-bottom: 24px; }
  .logo-img { width: 180px; height: 180px; object-fit: contain; display: block; margin: 0 auto; }
  .logo h1 { /* nao usado */
    font-family: 'Pacifico', cursive;
    font-weight: 400;
    font-size: 2rem;
    color: var(--rosa);
    line-height: 1.1;
    margin-top: 6px;
  }
  .logo p { color: var(--cinza); font-size: .9rem; margin-top: 4px; }
  label { display: block; font-weight: 700; font-size: .9rem; margin: 16px 0 6px; }
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
  .opcoes {
    display: flex; justify-content: space-between; align-items: center;
    margin: 14px 0 20px; font-size: .85rem;
  }
  .opcoes label { display: flex; align-items: center; gap: 6px; margin: 0; font-weight: 600; cursor: pointer; }
  .opcoes input { width: auto; accent-color: var(--rosa); }
  a { color: var(--rosa-escuro); text-decoration: none; font-weight: 700; }
  a:hover { text-decoration: underline; }
  button.entrar {
    width: 100%;
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
      <div class="logo">
        <a href="index.php">
          <img class="logo-img" src="https://i.ibb.co/5gYVr1PL/logo-t.png" alt="Logo Dulcen Cup Cakes">
        </a>
        <p>Cupcakes artesanais feitos com amor</p>
      </div>
      <form id="form-login" method="POST" action="loguin_page.php"> <label for="email">E-mail</label>
        <div class="campo"> <input id="email" placeholder="voce@exemplo.com" autocomplete="email"
            required="" type="email" name="email"> </div>
        <label for="senha">Senha</label>
        <div class="campo"> <input id="senha" placeholder="Digite sua senha" autocomplete="current-password"
            required="" type="password" name="senha"> <button type="button" class="olho" id="toggle"
            aria-label="Mostrar senha">👁️</button> </div>
        <div class="opcoes"> <label><input type="checkbox"> Lembrar de mim</label>
          <a href="#">Esqueci a senha</a> </div>
        <button type="submit" class="entrar">Entrar 🧁</button>
        <?php if (!empty($_GET['cadastro'])): ?><div class="sucesso" style="display:block">Cadastro realizado! Faça seu login 💖</div><?php endif; ?>
        <?php if (!empty($erro)): ?><div class="msg-erro" style="margin-top:12px"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
      </form>
      <p class="cadastro">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </main>
    <script>
  const form = document.getElementById('form-login');
  const email = document.getElementById('email');
  const senha = document.getElementById('senha');

  document.getElementById('toggle').addEventListener('click', () => {
    const oculto = senha.type === 'password';
    senha.type = oculto ? 'text' : 'password';
    document.getElementById('toggle').textContent = oculto ? '🙈' : '👁️';
  });

  form.addEventListener('submit', (e) => {
    let ok = true;
    const emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim());
    if (!emailValido) { email.classList.add('erro'); ok = false; }
    const senhaValida = senha.value.length >= 6;
    if (!senhaValida) { senha.classList.add('erro'); ok = false; }
    if (!ok) e.preventDefault();
  });
</script> </body>
</html>

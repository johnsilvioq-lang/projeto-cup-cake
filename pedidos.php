<?php
session_start();
require_once __DIR__ . '/config.php';

if (empty($_SESSION['usuario'])) {
    header('Location: loguin_page.php');
    exit;
}

$usuarioId = $_SESSION['usuario']['id'];

// Ação para cancelar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_pedido_id'])) {
    $pedidoIdCancelar = (int)$_POST['cancelar_pedido_id'];
    
    // Verifica se o pedido pertence ao usuário e está Pendente
    $stmtCheck = $pdo->prepare('SELECT id, status FROM pedidos WHERE id = ? AND usuario_id = ?');
    $stmtCheck->execute([$pedidoIdCancelar, $usuarioId]);
    $ped = $stmtCheck->fetch();
    
    if ($ped && $ped['status'] === 'Pendente') {
        $stmtUp = $pdo->prepare("UPDATE pedidos SET status = 'Cancelado' WHERE id = ?");
        $stmtUp->execute([$pedidoIdCancelar]);
    }
    header('Location: pedidos.php');
    exit;
}

// Buscar pedidos do usuário
$stmtPedidos = $pdo->prepare('SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY criado_em DESC');
$stmtPedidos->execute([$usuarioId]);
$pedidos = $stmtPedidos->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dulcen Cup Cakes | Meus Pedidos</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --fundo: #ffe9f1;
    --rosa: #ee7f96;
    --rosa-escuro: #d85f7b;
    --rosa-claro: #ffd3e1;
    --marrom: #4f4a3a;
    --texto: #4a2c3a;
    --cinza: #8a6f7b;
    --branco: #fff;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Nunito', sans-serif;
    background: var(--fundo);
    color: var(--texto);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }
  header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 24px;
    background: rgba(255, 233, 241, .92);
    backdrop-filter: blur(8px);
    border-bottom: 2px solid var(--rosa-claro);
  }
  .logo-img { width: 84px; height: 84px; object-fit: contain; display: block; }
  .acoes { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
  .btn {
    border: none; border-radius: 999px; padding: 10px 20px;
    font-weight: 800; font-size: .95rem; text-decoration: none; display: inline-block;
    transition: transform .15s, box-shadow .15s, background .15s; cursor: pointer;
  }
  .btn:hover { transform: translateY(-2px); }
  .btn-contorno { background: #fff; color: var(--rosa-escuro); border: 2px solid var(--rosa); }
  .btn-cancelar { background: #d93025; color: #fff; padding: 6px 12px; font-size: 0.85rem; border-radius: 8px; }
  .btn-cancelar:hover { background: #b5251b; }

  main { width: 100%; max-width: 900px; margin: 0 auto; padding: 32px 20px; flex: 1; }
  h2 { font-family: 'Pacifico', cursive; font-weight: 400; color: var(--rosa-escuro); font-size: 2rem; margin-bottom: 20px; }
  
  .pedido-card {
    background: #fff; border-radius: 20px; padding: 24px; margin-bottom: 20px;
    box-shadow: 0 8px 22px rgba(238,127,150,.16);
  }
  .pedido-header {
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 2px solid var(--rosa-claro); padding-bottom: 12px; margin-bottom: 12px;
    flex-wrap: wrap; gap: 10px;
  }
  .pedido-id { font-weight: 800; font-size: 1.1rem; color: var(--rosa-escuro); }
  .pedido-data { font-size: 0.9rem; color: var(--cinza); }
  .status-pendente { color: #d99b00; font-weight: 700; }
  .status-cancelado { color: #d93025; font-weight: 700; }
  .status-confirmado { color: #1e7a45; font-weight: 700; }

  .lista-itens { list-style: none; margin-bottom: 16px; }
  .lista-itens li {
    display: flex; justify-content: space-between; padding: 6px 0;
    border-bottom: 1px dashed #eee; font-size: 0.95rem;
  }
  .pedido-footer {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 12px; font-weight: 800; font-size: 1.1rem; flex-wrap: wrap; gap: 10px;
  }
  .vazio { text-align: center; color: var(--cinza); padding: 40px 0; background: #fff; border-radius: 20px; box-shadow: 0 8px 22px rgba(238,127,150,.16); }
</style>
</head>
<body>

<header>
  <a href="index.php"><img src="https://i.ibb.co/5gYVr1PL/logo-t.png" alt="logo t" width="84" height="84" class="logo-img"></a>
  <div class="acoes">
    <a class="btn btn-contorno" href="index.php">Voltar à Loja</a>
    <span style="font-weight: 700; font-size: .9rem; color: #4f4a3a;">Olá, <?= htmlspecialchars($_SESSION['usuario']['nome'], ENT_QUOTES, 'UTF-8') ?>!</span>
    <a class="btn btn-contorno" href="logout.php">Sair</a>
  </div>
</header>

<main>
  <h2>Meus Pedidos</h2>

  <?php if (empty($pedidos)): ?>
    <div class="vazio">Você ainda não realizou nenhum pedido 🧁</div>
  <?php else: ?>
    <?php foreach ($pedidos as $p): 
        $stmtItens = $pdo->prepare('SELECT * FROM itens_pedido WHERE pedido_id = ?');
        $stmtItens->execute([$p['id']]);
        $itensPedido = $stmtItens->fetchAll();
    ?>
      <div class="pedido-card">
        <div class="pedido-header">
          <div>
            <span class="pedido-id">Pedido #<?= $p['id'] ?></span>
            <div class="pedido-data">Realizado em: <?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></div>
          </div>
          <div>
            Status: 
            <?php if ($p['status'] === 'Pendente'): ?>
              <span class="status-pendente">Pendente</span>
            <?php elseif ($p['status'] === 'Cancelado'): ?>
              <span class="status-cancelado">Cancelado</span>
            <?php else: ?>
              <span class="status-confirmado"><?= htmlspecialchars($p['status']) ?></span>
            <?php endif; ?>
          </div>
        </div>

        <ul class="lista-itens">
          <?php foreach ($itensPedido as $item): ?>
            <li>
              <span><?= htmlspecialchars($item['nome_produto']) ?> (<?= $item['quantidade'] ?>x)</span>
              <span>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></span>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="pedido-footer">
          <div>Total: R$ <?= number_format($p['total'], 2, ',', '.') ?></div>
          <div>
            <?php if ($p['status'] === 'Pendente'): ?>
              <form method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente cancelar este pedido?');">
                <input type="hidden" name="cancelar_pedido_id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-cancelar">Cancelar Pedido</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</main>

</body>
</html>
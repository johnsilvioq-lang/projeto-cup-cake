<?php
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if (empty($_SESSION['usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$itens = $data['itens'] ?? [];

if (empty($itens)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Carrinho vazio']);
    exit;
}

try {
    $pdo->beginTransaction();

    $total = 0;
    foreach ($itens as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }

    $stmt = $pdo->prepare('INSERT INTO pedidos (usuario_id, total, status) VALUES (?, ?, ?)');
    $stmt->execute([$_SESSION['usuario']['id'], $total, 'Pendente']);
    $pedidoId = $pdo->lastInsertId();

    $stmtItem = $pdo->prepare('INSERT INTO itens_pedido (pedido_id, nome_produto, preco, quantidade) VALUES (?, ?, ?, ?)');
    foreach ($itens as $item) {
        $stmtItem->execute([$pedidoId, $item['nome'], $item['preco'], $item['quantidade']]);
    }

    $pdo->commit();
    echo json_encode(['sucesso' => true, 'pedido_id' => $pedidoId]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar pedido']);
}
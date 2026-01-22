<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $plan_id = $_POST['plan_id'] ?? 0;
    $product_name = $_POST['product_name'] ?? '';
    $plan_name = $_POST['plan_name'] ?? '';
    $price = $_POST['price'] ?? 0;

    if (!$plan_id) {
        echo json_encode(['success' => false, 'message' => 'Plano inválido.']);
        exit;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Verifica se já existe
    foreach ($_SESSION['cart'] as $item) {
        if ($item['plan_id'] == $plan_id) {
            echo json_encode(['success' => false, 'message' => 'Item já está no carrinho.']);
            exit;
        }
    }

    $_SESSION['cart'][] = [
        'plan_id' => $plan_id,
        'product_name' => $product_name,
        'plan_name' => $plan_name,
        'price' => $price
    ];

    echo json_encode(['success' => true, 'message' => 'Adicionado ao carrinho!']);

} elseif ($action === 'remove') {
    $index = $_POST['index'] ?? -1;

    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
        echo json_encode(['success' => true, 'message' => 'Removido com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item não encontrado.']);
    }

} elseif ($action === 'checkout') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Faça login para finalizar a compra.', 'redirect' => '/login.php']);
        exit;
    }

    if (empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Carrinho vazio.']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'];
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
        $stmt->bind_param("id", $user_id, $total);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, plan_id, price) VALUES (?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt_item->bind_param("iid", $order_id, $item['plan_id'], $item['price']);
            $stmt_item->execute();
        }

        $conn->commit();
        $_SESSION['cart'] = []; // Limpa carrinho
        echo json_encode(['success' => true, 'message' => 'Pedido realizado com sucesso!', 'order_id' => $order_id]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro ao processar pedido: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
}
?>

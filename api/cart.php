<?php
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if ($secure) {
    ini_set('session.cookie_secure', '1');
}
session_start();

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

require_once 'db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

function db_has_column(mysqli $conn, $table, $column)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
}

function db_has_table(mysqli $conn, $table)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
}

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

    $hasOrdersAffiliate = db_has_column($conn, 'orders', 'affiliate_user_id');
    $hasUsersReferredBy = db_has_column($conn, 'users', 'referred_by');
    $hasAffiliateCommissions = db_has_table($conn, 'affiliate_commissions') && db_has_table($conn, 'affiliate_accounts');

    $affiliate_user_id = (int)($_SESSION['affiliate_user_id'] ?? 0);
    if ($affiliate_user_id <= 0 && $hasUsersReferredBy) {
        $stmt = $conn->prepare("SELECT referred_by FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $affiliate_user_id = (int)($row['referred_by'] ?? 0);
    }

    $conn->begin_transaction();

    try {
        if ($hasOrdersAffiliate && $affiliate_user_id > 0 && $affiliate_user_id !== (int)$user_id) {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total, affiliate_user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("idi", $user_id, $total, $affiliate_user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
            $stmt->bind_param("id", $user_id, $total);
        }
        $stmt->execute();
        $order_id = $stmt->insert_id;

        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, plan_id, price) VALUES (?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt_item->bind_param("iid", $order_id, $item['plan_id'], $item['price']);
            $stmt_item->execute();
        }

        if ($hasAffiliateCommissions && $affiliate_user_id > 0 && $affiliate_user_id !== (int)$user_id) {
            $stmt = $conn->prepare("SELECT commission_rate FROM affiliate_accounts WHERE user_id = ? AND status = 'active' LIMIT 1");
            $stmt->bind_param("i", $affiliate_user_id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $rate = (float)($row['commission_rate'] ?? 0.0);
            if ($rate > 0) {
                $amount = round($total * $rate, 2);
                if ($amount > 0) {
                    $stmt = $conn->prepare("INSERT IGNORE INTO affiliate_commissions (affiliate_user_id, order_id, amount, status) VALUES (?, ?, ?, 'pending')");
                    $stmt->bind_param("iid", $affiliate_user_id, $order_id, $amount);
                    $stmt->execute();
                }
            }
        }

        $conn->commit();
        $_SESSION['cart'] = []; // Limpa carrinho
        echo json_encode(['success' => true, 'message' => 'Pedido realizado com sucesso!', 'order_id' => $order_id]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro ao processar pedido.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
}
?>

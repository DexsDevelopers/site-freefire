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

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

function ensure_cart()
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    foreach ($_SESSION['cart'] as $i => $item) {
        if (!is_array($item)) {
            unset($_SESSION['cart'][$i]);
            continue;
        }
        if (!isset($_SESSION['cart'][$i]['qty']) || (int)$_SESSION['cart'][$i]['qty'] < 1) {
            $_SESSION['cart'][$i]['qty'] = 1;
        }
        if (!isset($_SESSION['cart'][$i]['price'])) {
            $_SESSION['cart'][$i]['price'] = 0;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

function cart_totals()
{
    ensure_cart();
    $items = [];
    $total_qty = 0;
    $total = 0.0;
    foreach ($_SESSION['cart'] as $item) {
        $qty = max(1, (int)($item['qty'] ?? 1));
        $unit = (float)($item['price'] ?? 0);
        $subtotal = round($unit * $qty, 2);
        $items[] = [
            'plan_id' => (int)($item['plan_id'] ?? 0),
            'product_name' => (string)($item['product_name'] ?? ''),
            'product_image_url' => (string)($item['product_image_url'] ?? ''),
            'plan_name' => (string)($item['plan_name'] ?? ''),
            'unit_price' => $unit,
            'qty' => $qty,
            'subtotal' => $subtotal,
        ];
        $total_qty += $qty;
        $total += $subtotal;
    }
    return [
        'items' => $items,
        'total_qty' => $total_qty,
        'total' => round($total, 2),
    ];
}

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

function find_plan(mysqli $conn, int $plan_id)
{
    $stmt = $conn->prepare("SELECT pl.id AS plan_id, pl.name AS plan_name, pl.price, p.name AS product_name, p.image_url AS product_image_url FROM plans pl JOIN products p ON p.id = pl.product_id WHERE pl.id = ? LIMIT 1");
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        return null;
    }
    return [
        'plan_id' => (int)$row['plan_id'],
        'plan_name' => (string)$row['plan_name'],
        'price' => (float)$row['price'],
        'product_name' => (string)$row['product_name'],
        'product_image_url' => (string)($row['product_image_url'] ?? ''),
    ];
}

if ($action === 'list') {
    $data = cart_totals();
    echo json_encode(['success' => true] + $data);
    exit;
}

if ($action === 'add') {
    require_once 'db.php';
    $plan_id = (int)($_POST['plan_id'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 1);
    if ($qty < 1) {
        $qty = 1;
    }

    if (!$plan_id) {
        echo json_encode(['success' => false, 'message' => 'Plano inválido.']);
        exit;
    }

    $plan = find_plan($conn, $plan_id);
    if (!$plan) {
        echo json_encode(['success' => false, 'message' => 'Plano não encontrado.']);
        exit;
    }

    ensure_cart();

    foreach ($_SESSION['cart'] as $i => $item) {
        if ((int)($item['plan_id'] ?? 0) === $plan_id) {
            $_SESSION['cart'][$i]['qty'] = (int)($_SESSION['cart'][$i]['qty'] ?? 1) + $qty;
            $data = cart_totals();
            echo json_encode(['success' => true, 'message' => 'Quantidade atualizada.'] + $data);
            exit;
        }
    }

    $_SESSION['cart'][] = [
        'plan_id' => $plan['plan_id'],
        'product_name' => $plan['product_name'],
        'product_image_url' => $plan['product_image_url'],
        'plan_name' => $plan['plan_name'],
        'price' => $plan['price'],
        'qty' => $qty,
    ];

    $data = cart_totals();
    echo json_encode(['success' => true, 'message' => 'Adicionado ao carrinho!'] + $data);

} elseif ($action === 'remove') {
    ensure_cart();
    $index = (int)($_POST['index'] ?? -1);
    $plan_id = (int)($_POST['plan_id'] ?? 0);

    $removed = false;
    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
        $removed = true;
    } elseif ($plan_id > 0) {
        foreach ($_SESSION['cart'] as $i => $item) {
            if ((int)($item['plan_id'] ?? 0) === $plan_id) {
                array_splice($_SESSION['cart'], $i, 1);
                $removed = true;
                break;
            }
        }
    }

    if (!$removed) {
        echo json_encode(['success' => false, 'message' => 'Item não encontrado.']);
        exit;
    }

    $data = cart_totals();
    echo json_encode(['success' => true, 'message' => 'Removido com sucesso.'] + $data);

} elseif ($action === 'update_qty') {
    ensure_cart();
    $index = (int)($_POST['index'] ?? -1);
    $plan_id = (int)($_POST['plan_id'] ?? 0);
    $delta = (int)($_POST['delta'] ?? 0);
    $set = isset($_POST['qty']) ? (int)$_POST['qty'] : null;

    $targetIndex = -1;
    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        $targetIndex = $index;
    } elseif ($plan_id > 0) {
        foreach ($_SESSION['cart'] as $i => $item) {
            if ((int)($item['plan_id'] ?? 0) === $plan_id) {
                $targetIndex = $i;
                break;
            }
        }
    }

    if ($targetIndex < 0) {
        echo json_encode(['success' => false, 'message' => 'Item não encontrado.']);
        exit;
    }

    $current = (int)($_SESSION['cart'][$targetIndex]['qty'] ?? 1);
    $next = $current;
    if ($set !== null) {
        $next = $set;
    } elseif ($delta !== 0) {
        $next = $current + $delta;
    }

    if ($next < 1) {
        array_splice($_SESSION['cart'], $targetIndex, 1);
    } else {
        $_SESSION['cart'][$targetIndex]['qty'] = $next;
    }

    $data = cart_totals();
    echo json_encode(['success' => true] + $data);

} elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
    $data = cart_totals();
    echo json_encode(['success' => true] + $data);

} elseif ($action === 'checkout') {
    require_once 'db.php';
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Faça login para finalizar a compra.', 'redirect' => '/login.php']);
        exit;
    }

    if (empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Carrinho vazio.']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $totals = cart_totals();
    $total = (float)$totals['total'];

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
            $qty = max(1, (int)($item['qty'] ?? 1));
            for ($i = 0; $i < $qty; $i++) {
                $stmt_item->bind_param("iid", $order_id, $item['plan_id'], $item['price']);
                $stmt_item->execute();
            }
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

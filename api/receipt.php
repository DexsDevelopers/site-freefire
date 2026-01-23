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

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/pdf_simple.php';

function db_has_column(mysqli $conn, $table, $column)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
}

$orderId = (int)($_GET['order_id'] ?? 0);
if ($orderId <= 0) {
    http_response_code(404);
    echo "Pedido inválido.";
    exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);
$isAdmin = !empty($_SESSION['is_admin']);
if ($userId <= 0) {
    http_response_code(401);
    echo "Não autorizado.";
    exit;
}

if ($isAdmin) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $orderId);
} else {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->bind_param("ii", $orderId, $userId);
}
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) {
    http_response_code(404);
    echo "Pedido não encontrado.";
    exit;
}

$hasQty = db_has_column($conn, 'order_items', 'qty');
if ($hasQty) {
    $sqlItems = "SELECT p.name AS product_name, pl.name AS plan_name, oi.price, oi.qty
                 FROM order_items oi
                 JOIN plans pl ON pl.id = oi.plan_id
                 JOIN products p ON p.id = pl.product_id
                 WHERE oi.order_id = ?
                 ORDER BY oi.id ASC";
} else {
    $sqlItems = "SELECT p.name AS product_name, pl.name AS plan_name, oi.price, COUNT(*) AS qty
                 FROM order_items oi
                 JOIN plans pl ON pl.id = oi.plan_id
                 JOIN products p ON p.id = pl.product_id
                 WHERE oi.order_id = ?
                 GROUP BY oi.plan_id, oi.price, p.name, pl.name
                 ORDER BY MIN(oi.id) ASC";
}
$stmt = $conn->prepare($sqlItems);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$lines = [];
$lines[] = "Pedido #" . $orderId;
$lines[] = "Status: " . (string)($order['status'] ?? '');
$lines[] = "Total: R$ " . number_format((float)($order['total'] ?? 0), 2, ',', '.');
$customerName = (string)($order['customer_name'] ?? '');
if ($customerName !== '') $lines[] = "Cliente: " . $customerName;
$customerEmail = (string)($order['customer_email'] ?? '');
$deliveryEmail = (string)($order['delivery_email'] ?? $customerEmail);
if ($deliveryEmail !== '') $lines[] = "Gmail (entrega): " . $deliveryEmail;
$deliveryDiscord = (string)($order['delivery_discord'] ?? '');
if ($deliveryDiscord !== '') $lines[] = "Discord: " . $deliveryDiscord;
$lines[] = "Entrega: Digital (key e download do painel)";

$lines[] = " ";
$lines[] = "Itens:";
foreach ($items as $it) {
    $qty = (int)($it['qty'] ?? 1);
    $unit = (float)($it['price'] ?? 0);
    $sub = $unit * $qty;
    $lines[] = "- " . (string)($it['product_name'] ?? '') . " / " . (string)($it['plan_name'] ?? '') . " x" . $qty . " = R$ " . number_format($sub, 2, ',', '.');
}

$pdf = pdf_simple_from_lines("Comprovante Thunder Store", $lines);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="pedido-' . $orderId . '.pdf"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;

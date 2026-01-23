<?php
session_start();
require_once __DIR__ . '/api/db.php';
require_once __DIR__ . '/api/stripe_client.php';

function db_has_table(mysqli $conn, $table)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
}

function app_setting(mysqli $conn, $key, $default = null)
{
    if (!db_has_table($conn, 'app_settings')) return $default;
    $stmt = $conn->prepare("SELECT value FROM app_settings WHERE `key` = ? LIMIT 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? (string)$row['value'] : $default;
}

$orderId = (int)($_GET['order_id'] ?? 0);
$sessionId = trim((string)($_GET['session_id'] ?? ''));
if ($orderId <= 0 || $sessionId === '') {
    http_response_code(404);
    echo "Parâmetros inválidos.";
    exit;
}

$secret = (string)app_setting($conn, 'payment_secret_key', '');
if ($secret === '') {
    http_response_code(500);
    echo "Pagamento não configurado.";
    exit;
}

$resp = stripe_api_request($secret, 'GET', '/v1/checkout/sessions/' . rawurlencode($sessionId));
if (!$resp['ok']) {
    http_response_code(502);
    echo "Não foi possível validar o pagamento.";
    exit;
}

$session = $resp['data'];
$metaOrder = (int)($session['metadata']['order_id'] ?? 0);
$paymentStatus = (string)($session['payment_status'] ?? '');
if ($metaOrder !== $orderId) {
    http_response_code(400);
    echo "Sessão inválida.";
    exit;
}

if ($paymentStatus === 'paid') {
    $stmt = $conn->prepare("UPDATE orders SET status = 'Pago', payment_status = 'paid', payment_reference = ?, paid_at = NOW() WHERE id = ? LIMIT 1");
    $stmt->bind_param("si", $sessionId, $orderId);
    $stmt->execute();
}

header("Location: /pedido.php?id=" . $orderId);
exit;

<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/stripe_client.php';

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

function json_resp($code, $payload)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

$payload = file_get_contents('php://input');
$sigHeader = (string)($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');
$secret = (string)app_setting($conn, 'payment_webhook_secret', '');
if ($secret === '') {
    json_resp(400, ['success' => false, 'message' => 'Webhook secret ausente.']);
}

if (!stripe_verify_signature($payload, $sigHeader, $secret)) {
    json_resp(400, ['success' => false, 'message' => 'Assinatura inválida.']);
}

$event = json_decode($payload, true);
if (!is_array($event) || !isset($event['type'])) {
    json_resp(400, ['success' => false, 'message' => 'Evento inválido.']);
}

$type = (string)$event['type'];
$obj = $event['data']['object'] ?? null;
if (!is_array($obj)) {
    json_resp(200, ['success' => true]);
}

function update_order_status(mysqli $conn, $orderId, $status, $paymentStatus, $reference = null)
{
    $orderId = (int)$orderId;
    if ($orderId <= 0) return;
    $reference = $reference !== null ? (string)$reference : null;

    if ($reference !== null && $reference !== '') {
        $stmt = $conn->prepare("UPDATE orders SET status = ?, payment_status = ?, payment_reference = ?, paid_at = IF(?='paid', NOW(), paid_at) WHERE id = ? LIMIT 1");
        $stmt->bind_param("ssssi", $status, $paymentStatus, $reference, $paymentStatus, $orderId);
    } else {
        $stmt = $conn->prepare("UPDATE orders SET status = ?, payment_status = ?, paid_at = IF(?='paid', NOW(), paid_at) WHERE id = ? LIMIT 1");
        $stmt->bind_param("sssi", $status, $paymentStatus, $paymentStatus, $orderId);
    }
    $stmt->execute();
}

$orderId = 0;
$reference = null;

if ($type === 'checkout.session.completed') {
    $meta = $obj['metadata'] ?? [];
    $orderId = (int)($meta['order_id'] ?? 0);
    $reference = (string)($obj['id'] ?? '');
    $paymentStatus = (string)($obj['payment_status'] ?? '');
    if ($paymentStatus === 'paid') {
        update_order_status($conn, $orderId, 'Pago', 'paid', $reference);
    }
} elseif ($type === 'checkout.session.async_payment_failed') {
    $meta = $obj['metadata'] ?? [];
    $orderId = (int)($meta['order_id'] ?? 0);
    $reference = (string)($obj['id'] ?? '');
    update_order_status($conn, $orderId, 'Cancelado', 'failed', $reference);
} elseif ($type === 'payment_intent.succeeded') {
    $meta = $obj['metadata'] ?? [];
    $orderId = (int)($meta['order_id'] ?? 0);
    $reference = (string)($obj['id'] ?? '');
    update_order_status($conn, $orderId, 'Pago', 'paid', $reference);
} elseif ($type === 'payment_intent.payment_failed') {
    $meta = $obj['metadata'] ?? [];
    $orderId = (int)($meta['order_id'] ?? 0);
    $reference = (string)($obj['id'] ?? '');
    update_order_status($conn, $orderId, 'Cancelado', 'failed', $reference);
}

json_resp(200, ['success' => true]);

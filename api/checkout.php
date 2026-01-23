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

require_once 'db.php';

function db_has_table(mysqli $conn, $table)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
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

function app_setting(mysqli $conn, $key, $default = null)
{
    if (!db_has_table($conn, 'app_settings')) return $default;
    $stmt = $conn->prepare("SELECT value FROM app_settings WHERE `key` = ? LIMIT 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? (string)$row['value'] : $default;
}

function sanitize_cep($cep)
{
    $digits = preg_replace('/\D+/', '', (string)$cep);
    if (strlen($digits) !== 8) return null;
    return $digits;
}

function shipping_options($cepDigits, $cartTotal)
{
    $first = (int)substr($cepDigits, 0, 1);
    $base = 29.90;
    if ($first >= 0 && $first <= 3) $base = 19.90;
    if ($first >= 7) $base = 39.90;
    if ($cartTotal >= 200) $base = 0.00;

    $standard = ['id' => 'standard', 'label' => 'Padrão (5–8 dias)', 'price' => $base, 'eta' => '5–8 dias úteis'];
    $expressPrice = ($base <= 0) ? 19.90 : round($base + 15.00, 2);
    $express = ['id' => 'express', 'label' => 'Expresso (2–4 dias)', 'price' => $expressPrice, 'eta' => '2–4 dias úteis'];
    return [$standard, $express];
}

function cart_items()
{
    $items = $_SESSION['cart'] ?? [];
    if (!is_array($items)) return [];
    $out = [];
    foreach ($items as $item) {
        $planId = (int)($item['plan_id'] ?? 0);
        if ($planId <= 0) continue;
        $qty = max(1, (int)($item['qty'] ?? 1));
        $price = (float)($item['price'] ?? 0);
        $out[] = [
            'plan_id' => $planId,
            'price' => $price,
            'qty' => $qty,
        ];
    }
    return $out;
}

function cart_total($items)
{
    $total = 0.0;
    foreach ($items as $it) {
        $total += ((float)$it['price']) * ((int)$it['qty']);
    }
    return round($total, 2);
}

function ensure_checkout_schema(mysqli $conn)
{
    $adds = [];
    $orders = [
        "customer_name" => "ALTER TABLE orders ADD COLUMN customer_name VARCHAR(120) NULL",
        "customer_email" => "ALTER TABLE orders ADD COLUMN customer_email VARCHAR(120) NULL",
        "customer_phone" => "ALTER TABLE orders ADD COLUMN customer_phone VARCHAR(30) NULL",
        "shipping_zip" => "ALTER TABLE orders ADD COLUMN shipping_zip VARCHAR(15) NULL",
        "shipping_address" => "ALTER TABLE orders ADD COLUMN shipping_address VARCHAR(255) NULL",
        "shipping_cost" => "ALTER TABLE orders ADD COLUMN shipping_cost DECIMAL(10,2) NOT NULL DEFAULT 0",
        "shipping_method" => "ALTER TABLE orders ADD COLUMN shipping_method VARCHAR(50) NULL",
        "delivery_email" => "ALTER TABLE orders ADD COLUMN delivery_email VARCHAR(120) NULL",
        "delivery_discord" => "ALTER TABLE orders ADD COLUMN delivery_discord VARCHAR(120) NULL",
        "payment_method" => "ALTER TABLE orders ADD COLUMN payment_method VARCHAR(20) NOT NULL DEFAULT 'manual'",
        "payment_provider" => "ALTER TABLE orders ADD COLUMN payment_provider VARCHAR(40) NULL",
        "payment_status" => "ALTER TABLE orders ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'pending'",
        "payment_reference" => "ALTER TABLE orders ADD COLUMN payment_reference VARCHAR(120) NULL",
        "paid_at" => "ALTER TABLE orders ADD COLUMN paid_at TIMESTAMP NULL",
        "updated_at" => "ALTER TABLE orders ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    ];
    foreach ($orders as $col => $sql) {
        if (!db_has_column($conn, 'orders', $col)) {
            $adds[] = $sql;
        }
    }

    $items = [];
    if (!db_has_column($conn, 'order_items', 'qty')) {
        $items[] = "ALTER TABLE order_items ADD COLUMN qty INT NOT NULL DEFAULT 1";
    }

    foreach (array_merge($adds, $items) as $sql) {
        $conn->query($sql);
    }
}

function json_error($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

$action = (string)($_POST['action'] ?? $_GET['action'] ?? '');
if ($action !== 'create_order') {
    json_error('Ação inválida.');
}

if (empty($_SESSION['user_id'])) {
    json_error('Faça login para finalizar a compra.', 401);
}

$items = cart_items();
if (!$items) {
    json_error('Carrinho vazio.');
}

$customerName = trim((string)($_POST['customer_name'] ?? ''));
$customerEmail = trim((string)($_POST['customer_email'] ?? ''));
$customerPhone = trim((string)($_POST['customer_phone'] ?? ''));
$deliveryDiscord = trim((string)($_POST['delivery_discord'] ?? $_POST['customer_discord'] ?? ''));
$deliveryEmail = trim((string)($_POST['delivery_email'] ?? $customerEmail));

if ($customerName === '' || $customerEmail === '' || $customerPhone === '' || $deliveryDiscord === '') {
    json_error('Preencha nome, gmail, telefone e discord.');
}
if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    json_error('E-mail inválido.');
}
if (!filter_var($deliveryEmail, FILTER_VALIDATE_EMAIL)) {
    json_error('Gmail de entrega inválido.');
}

$cartSubtotal = cart_total($items);
$shippingCost = 0.0;
$shippingMethod = 'digital';
$shippingZip = '';
$addrFull = 'Entrega digital';
$grandTotal = round($cartSubtotal, 2);

$paymentMethod = (string)app_setting($conn, 'payment_method', 'manual');
if ($paymentMethod !== 'api' && $paymentMethod !== 'manual') $paymentMethod = 'manual';
$paymentProvider = (string)app_setting($conn, 'payment_provider', 'stripe');
if ($paymentProvider !== 'stripe') $paymentProvider = 'stripe';

ensure_checkout_schema($conn);

$userId = (int)$_SESSION['user_id'];
$status = 'Pendente';
$paymentStatus = 'pending';

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, customer_name, customer_email, customer_phone, delivery_email, delivery_discord, shipping_zip, shipping_address, shipping_cost, shipping_method, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssssssssdsss", $userId, $grandTotal, $status, $customerName, $customerEmail, $customerPhone, $deliveryEmail, $deliveryDiscord, $shippingZip, $addrFull, $shippingCost, $shippingMethod, $paymentMethod, $paymentStatus);
    $stmt->execute();
    $orderId = (int)$stmt->insert_id;

    $hasQty = db_has_column($conn, 'order_items', 'qty');
    if ($hasQty) {
        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, plan_id, price, qty) VALUES (?, ?, ?, ?)");
        foreach ($items as $it) {
            $planId = (int)$it['plan_id'];
            $price = (float)$it['price'];
            $qty = (int)$it['qty'];
            $stmtItem->bind_param("iidi", $orderId, $planId, $price, $qty);
            $stmtItem->execute();
        }
    } else {
        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, plan_id, price) VALUES (?, ?, ?)");
        foreach ($items as $it) {
            $planId = (int)$it['plan_id'];
            $price = (float)$it['price'];
            $qty = (int)$it['qty'];
            for ($i = 0; $i < $qty; $i++) {
                $stmtItem->bind_param("iid", $orderId, $planId, $price);
                $stmtItem->execute();
            }
        }
    }

    $redirectUrl = null;
    if ($paymentMethod === 'api') {
        require_once __DIR__ . '/stripe_client.php';
        $secret = (string)app_setting($conn, 'payment_secret_key', '');
        if ($secret === '') {
            throw new Exception('Secret key ausente.');
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
        $baseUrl = $scheme . '://' . $host;
        $successUrl = $baseUrl . '/payment_success.php?order_id=' . $orderId . '&session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $baseUrl . '/pedido.php?id=' . $orderId;

        $unitAmount = (int)round($grandTotal * 100);
        if ($unitAmount < 1) $unitAmount = 1;

        $params = [
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string)$orderId,
            'customer_email' => $customerEmail,
            'metadata[order_id]' => (string)$orderId,
            'payment_intent_data[metadata][order_id]' => (string)$orderId,
            'line_items[0][price_data][currency]' => 'brl',
            'line_items[0][price_data][unit_amount]' => (string)$unitAmount,
            'line_items[0][price_data][product_data][name]' => 'Pedido #' . $orderId . ' - Thunder Store',
            'line_items[0][quantity]' => '1',
        ];

        $resp = stripe_api_request($secret, 'POST', '/v1/checkout/sessions', $params);
        if (!$resp['ok']) {
            throw new Exception('Erro Stripe: ' . (string)($resp['error'] ?? ''));
        }

        $session = $resp['data'];
        $sessionId = (string)($session['id'] ?? '');
        $sessionUrl = (string)($session['url'] ?? '');
        if ($sessionId === '' || $sessionUrl === '') {
            throw new Exception('Resposta Stripe inválida.');
        }

        $stmtUp = $conn->prepare("UPDATE orders SET payment_provider = ?, payment_reference = ?, status = 'Aguardando pagamento' WHERE id = ? LIMIT 1");
        $stmtUp->bind_param("ssi", $paymentProvider, $sessionId, $orderId);
        $stmtUp->execute();

        $redirectUrl = $sessionUrl;
    }

    $conn->commit();
    $_SESSION['cart'] = [];

    $mailSent = null;
    if ($paymentMethod === 'manual') {
        require_once __DIR__ . '/pdf_simple.php';
        require_once __DIR__ . '/mailer_simple.php';

        $pixKey = (string)app_setting($conn, 'manual_pix_key', '');
        $instructions = (string)app_setting($conn, 'manual_instructions', 'Finalize o pagamento via PIX/transferência e envie o comprovante para o suporte.');
        $fromEmail = (string)app_setting($conn, 'mail_from', '');

        $lines = [];
        $lines[] = "Pedido #" . $orderId;
        $lines[] = "Total: R$ " . number_format($grandTotal, 2, ',', '.');
        $lines[] = "Cliente: " . $customerName;
        $lines[] = "Gmail (entrega): " . $deliveryEmail;
        $lines[] = "Discord: " . $deliveryDiscord;
        $lines[] = "Entrega: Digital (key e acesso ao painel)";
        $lines[] = " ";
        $lines[] = "Itens:";
        foreach ($items as $it) {
            $sub = ((float)$it['price']) * ((int)$it['qty']);
            $lines[] = "- PlanID " . (int)$it['plan_id'] . " x" . (int)$it['qty'] . " = R$ " . number_format($sub, 2, ',', '.');
        }
        if ($pixKey !== '') {
            $lines[] = " ";
            $lines[] = "Chave PIX: " . $pixKey;
        }
        $lines[] = " ";
        $lines[] = $instructions;

        $pdf = pdf_simple_from_lines("Comprovante Thunder Store", $lines);

        $html = '<div style="font-family:Arial,sans-serif;line-height:1.5">';
        $html .= '<h2>Pedido #' . (int)$orderId . '</h2>';
        $html .= '<p>Recebemos seu pedido. Total: <b>R$ ' . number_format($grandTotal, 2, ',', '.') . '</b></p>';
        $html .= '<p>' . htmlspecialchars($instructions) . '</p>';
        if ($pixKey !== '') {
            $html .= '<p><b>Chave PIX:</b> ' . htmlspecialchars($pixKey) . '</p>';
        }
        $html .= '<p>Seu comprovante em PDF vai anexado neste e-mail.</p>';
        $html .= '</div>';

        $mailSent = mail_send_with_pdf($deliveryEmail, "Thunder Store - Pedido #" . $orderId, $html, $pdf, "pedido-$orderId.pdf", $fromEmail ?: null);
    }

    if ($redirectUrl) {
        echo json_encode(['success' => true, 'order_id' => $orderId, 'redirect_url' => $redirectUrl]);
    } else {
        echo json_encode(['success' => true, 'order_id' => $orderId, 'mail_sent' => $mailSent]);
    }
} catch (Exception $e) {
    $conn->rollback();
    json_error('Erro ao criar pedido.', 500);
}

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

function sanitize_cep($cep)
{
    $digits = preg_replace('/\D+/', '', (string)$cep);
    if (strlen($digits) !== 8) return null;
    return $digits;
}

function cart_total()
{
    $total = 0.0;
    $items = $_SESSION['cart'] ?? [];
    if (!is_array($items)) return 0.0;
    foreach ($items as $item) {
        $qty = max(1, (int)($item['qty'] ?? 1));
        $unit = (float)($item['price'] ?? 0);
        $total += $unit * $qty;
    }
    return round($total, 2);
}

function shipping_options($cepDigits, $cartTotal)
{
    $first = (int)substr($cepDigits, 0, 1);
    $base = 29.90;
    if ($first >= 0 && $first <= 3) $base = 19.90;
    if ($first >= 7) $base = 39.90;

    if ($cartTotal >= 200) {
        $base = 0.00;
    }

    $standard = [
        'id' => 'standard',
        'label' => 'Padrão (5–8 dias)',
        'price' => $base,
        'eta' => '5–8 dias úteis',
    ];

    $expressPrice = ($base <= 0) ? 19.90 : round($base + 15.00, 2);
    $express = [
        'id' => 'express',
        'label' => 'Expresso (2–4 dias)',
        'price' => $expressPrice,
        'eta' => '2–4 dias úteis',
    ];

    return [$standard, $express];
}

$cep = sanitize_cep($_GET['cep'] ?? $_POST['cep'] ?? '');
if (!$cep) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'CEP inválido.']);
    exit;
}

$total = cart_total();
$options = shipping_options($cep, $total);
echo json_encode(['success' => true, 'cep' => $cep, 'cart_total' => $total, 'options' => $options]);

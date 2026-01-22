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

function json_error($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

if (empty($_SESSION['user_id'])) {
    json_error('Faça login para acessar.', 401);
}

$requiredTables = ['affiliate_accounts', 'affiliate_clicks', 'affiliate_referrals', 'affiliate_commissions', 'affiliate_payouts'];
foreach ($requiredTables as $t) {
    if (!db_has_table($conn, $t)) {
        json_error('Afiliados não está configurado. Rode /setup_db.php.', 500);
    }
}

$action = (string)($_POST['action'] ?? $_GET['action'] ?? 'get');
$userId = (int)$_SESSION['user_id'];

if ($action === 'enable') {
    $code = bin2hex(random_bytes(5));
    $rate = (float)app_setting($conn, 'affiliate_default_rate', '0.10');
    if ($rate <= 0 || $rate > 0.9) $rate = 0.10;
    $stmt = $conn->prepare("INSERT INTO affiliate_accounts (user_id, code, commission_rate, status) VALUES (?, ?, ?, 'active')
                            ON DUPLICATE KEY UPDATE status='active'");
    $stmt->bind_param("isd", $userId, $code, $rate);
    if (!$stmt->execute()) {
        json_error('Não foi possível ativar afiliado.', 500);
    }
    echo json_encode(['success' => true, 'message' => 'Afiliado ativado com sucesso.']);
    exit;
}

if ($action === 'request_payout') {
    $amount = (float)($_POST['amount'] ?? 0);
    $method = trim((string)($_POST['method'] ?? 'pix'));
    $destination = trim((string)($_POST['destination'] ?? ''));

    if ($amount <= 0 || $destination === '') {
        json_error('Informe valor e destino do saque.');
    }

    $min = (float)app_setting($conn, 'affiliate_payout_min', '50.00');
    if ($amount < $min) {
        json_error('Valor mínimo de saque: R$ ' . number_format($min, 2, ',', '.'));
    }

    $stmt = $conn->prepare("SELECT code, status FROM affiliate_accounts WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $acc = $stmt->get_result()->fetch_assoc();
    if (!$acc || ($acc['status'] ?? '') !== 'active') {
        json_error('Sua conta de afiliado não está ativa.');
    }

    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS s FROM affiliate_commissions WHERE affiliate_user_id = ? AND status = 'approved'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $approved = (float)($stmt->get_result()->fetch_assoc()['s'] ?? 0);

    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS s FROM affiliate_payouts WHERE affiliate_user_id = ? AND status IN ('approved','paid')");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $paid = (float)($stmt->get_result()->fetch_assoc()['s'] ?? 0);

    $available = max(0, round($approved - $paid, 2));
    if ($amount > $available) {
        json_error('Saldo insuficiente. Disponível: R$ ' . number_format($available, 2, ',', '.'));
    }

    $stmt = $conn->prepare("INSERT INTO affiliate_payouts (affiliate_user_id, amount, method, destination, status) VALUES (?, ?, ?, ?, 'requested')");
    $stmt->bind_param("idss", $userId, $amount, $method, $destination);
    if (!$stmt->execute()) {
        json_error('Não foi possível solicitar saque.', 500);
    }

    echo json_encode(['success' => true, 'message' => 'Saque solicitado com sucesso.']);
    exit;
}

$stmt = $conn->prepare("SELECT code, commission_rate, status, created_at FROM affiliate_accounts WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();

$enabled = (bool)$account;
$code = $account ? (string)$account['code'] : null;
$link = $code ? ("/r.php?c=" . urlencode($code)) : null;

$stmt = $conn->prepare("SELECT COUNT(*) AS c FROM affiliate_clicks WHERE affiliate_user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$clicks = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);

$stmt = $conn->prepare("SELECT COUNT(*) AS c FROM affiliate_referrals WHERE affiliate_user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$referrals = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);

$stmt = $conn->prepare("SELECT status, COALESCE(SUM(amount),0) AS s FROM affiliate_commissions WHERE affiliate_user_id = ? GROUP BY status");
$stmt->bind_param("i", $userId);
$stmt->execute();
$byStatus = [];
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $byStatus[(string)$r['status']] = (float)$r['s'];
}
$pending = (float)($byStatus['pending'] ?? 0);
$approved = (float)($byStatus['approved'] ?? 0);
$paidCommissions = (float)($byStatus['paid'] ?? 0);

$stmt = $conn->prepare("SELECT status, COALESCE(SUM(amount),0) AS s FROM affiliate_payouts WHERE affiliate_user_id = ? GROUP BY status");
$stmt->bind_param("i", $userId);
$stmt->execute();
$payoutByStatus = [];
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $payoutByStatus[(string)$r['status']] = (float)$r['s'];
}

$payoutApproved = (float)($payoutByStatus['approved'] ?? 0);
$payoutPaid = (float)($payoutByStatus['paid'] ?? 0);

$available = max(0, round($approved - ($payoutApproved + $payoutPaid), 2));

echo json_encode([
    'success' => true,
    'enabled' => $enabled,
    'account' => $account,
    'link' => $link,
    'stats' => [
        'clicks' => $clicks,
        'referrals' => $referrals,
        'commissions' => [
            'pending' => $pending,
            'approved' => $approved,
            'paid' => $paidCommissions,
            'available' => $available,
        ],
        'payouts' => $payoutByStatus,
    ],
    'settings' => [
        'payout_min' => (float)app_setting($conn, 'affiliate_payout_min', '50.00'),
    ]
]);

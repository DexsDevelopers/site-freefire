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

require_once __DIR__ . '/api/db.php';

$code = trim((string)($_GET['c'] ?? $_GET['ref'] ?? ''));
if ($code === '') {
    header("Location: /");
    exit;
}

$stmt = $conn->prepare("SELECT user_id FROM affiliate_accounts WHERE code = ? AND status = 'active' LIMIT 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    header("Location: /");
    exit;
}

$_SESSION['affiliate_code'] = $code;
$_SESSION['affiliate_user_id'] = (int)$row['user_id'];

$ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
$secret = (string)($db_pass ?? '');
$ip_hash = hash('sha256', $ip . '|' . $secret);
$ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
$ref = substr((string)($_SERVER['HTTP_REFERER'] ?? ''), 0, 255);

$stmt = $conn->prepare("INSERT INTO affiliate_clicks (affiliate_user_id, code, ip_hash, user_agent, referer) VALUES (?,?,?,?,?)");
$stmt->bind_param("issss", $_SESSION['affiliate_user_id'], $code, $ip_hash, $ua, $ref);
$stmt->execute();

header("Location: /");
exit;

<?php
try {
    require_once __DIR__ . '/api/db.php';
} catch (Throwable $e) {
    echo "Falha ao conectar no banco. Ajuste api/db.php para seu ambiente e rode /setup_db.php.\n";
    exit(0);
}

function has_table(mysqli $conn, $table)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
}

function has_column(mysqli $conn, $table, $column)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
}

$checks = [
    'products' => has_table($conn, 'products'),
    'plans' => has_table($conn, 'plans'),
    'users' => has_table($conn, 'users'),
    'orders' => has_table($conn, 'orders'),
    'order_items' => has_table($conn, 'order_items'),
    'users.role' => has_column($conn, 'users', 'role'),
    'users.referred_by' => has_column($conn, 'users', 'referred_by'),
    'orders.affiliate_user_id' => has_column($conn, 'orders', 'affiliate_user_id'),
    'affiliate_accounts' => has_table($conn, 'affiliate_accounts'),
    'affiliate_clicks' => has_table($conn, 'affiliate_clicks'),
    'affiliate_referrals' => has_table($conn, 'affiliate_referrals'),
    'affiliate_commissions' => has_table($conn, 'affiliate_commissions'),
    'affiliate_payouts' => has_table($conn, 'affiliate_payouts'),
    'app_settings' => has_table($conn, 'app_settings'),
];

foreach ($checks as $k => $v) {
    echo str_pad($k, 26) . ': ' . ($v ? 'OK' : 'MISSING') . PHP_EOL;
}

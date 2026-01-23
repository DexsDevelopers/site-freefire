<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . '/api/db.php';

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

$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) {
    http_response_code(404);
    echo "Pedido inválido.";
    exit;
}

$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ii", $orderId, $userId);
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

$paymentMethod = (string)($order['payment_method'] ?? 'manual');
$paymentStatus = (string)($order['payment_status'] ?? 'pending');
$shippingCost = (float)($order['shipping_cost'] ?? 0);
$shippingMethod = (string)($order['shipping_method'] ?? '');
$customerName = (string)($order['customer_name'] ?? ($_SESSION['user_name'] ?? ''));
$customerEmail = (string)($order['customer_email'] ?? '');
$deliveryEmail = (string)($order['delivery_email'] ?? $customerEmail);
$deliveryDiscord = (string)($order['delivery_discord'] ?? '');
$shippingZip = (string)($order['shipping_zip'] ?? '');
$shippingAddress = (string)($order['shipping_address'] ?? '');

$pixKey = (string)app_setting($conn, 'manual_pix_key', '');
$manualInstructions = (string)app_setting($conn, 'manual_instructions', 'Finalize o pagamento via PIX/transferência e envie o comprovante para o suporte.');

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Pedido #<?php echo (int)$orderId; ?> | Thunder Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ff-orange': '#FF9900',
                        'ff-black': '#111111',
                        'ff-gray': '#1F1F1F',
                        'ff-red': '#DC2626',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <link rel="stylesheet" href="/assets/popup.css" />
    <style>
        html, body { touch-action: pan-x pan-y; }
        body { background-color: #000; color: white; font-family: 'Inter', sans-serif; }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
    <script src="/assets/popup.js" defer></script>
</head>
<body class="bg-black text-white min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <div class="text-3xl md:text-5xl font-black uppercase tracking-wider">Pedido #<?php echo (int)$orderId; ?></div>
                <div class="text-white/60 font-semibold mt-2">Confira o resumo e conclua o pagamento.</div>
            </div>
            <div class="flex gap-2">
                <a href="/" class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-black text-sm">Voltar ao site</a>
                <a href="/carrinho.php" class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-black text-sm">Carrinho</a>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 rounded-2xl border border-white/10 bg-white/5 p-6">
                <div class="text-xl font-black">Itens</div>
                <div class="mt-5 space-y-3">
                    <?php foreach ($items as $it): ?>
                        <?php
                            $qty = (int)($it['qty'] ?? 1);
                            $unit = (float)($it['price'] ?? 0);
                            $sub = $unit * $qty;
                        ?>
                        <div class="rounded-2xl border border-white/10 bg-black/30 p-4 flex items-start justify-between gap-4">
                            <div>
                                <div class="font-black"><?php echo htmlspecialchars((string)$it['product_name']); ?></div>
                                <div class="text-white/60 text-sm font-semibold"><?php echo htmlspecialchars((string)$it['plan_name']); ?> • Qtd <?php echo $qty; ?></div>
                            </div>
                            <div class="font-black"><?php echo 'R$ ' . number_format($sub, 2, ',', '.'); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 h-fit">
                <div class="text-xl font-black">Resumo</div>
                <div class="mt-4 space-y-2 text-white/70 font-semibold">
                    <div class="flex justify-between"><span>Status</span><span class="text-white"><?php echo htmlspecialchars((string)($order['status'] ?? '')); ?></span></div>
                    <div class="flex justify-between"><span>Cliente</span><span class="text-white"><?php echo htmlspecialchars($customerName); ?></span></div>
                    <div class="pt-2">
                        <div class="text-xs text-white/50 font-bold tracking-wide uppercase">Entrega</div>
                        <div class="text-white mt-1">Digital (key e download do painel)</div>
                        <?php if ($deliveryEmail !== ''): ?>
                            <div class="text-white/60 text-sm font-semibold mt-2">Gmail: <?php echo htmlspecialchars($deliveryEmail); ?></div>
                        <?php endif; ?>
                        <?php if ($deliveryDiscord !== ''): ?>
                            <div class="text-white/60 text-sm font-semibold mt-1">Discord: <?php echo htmlspecialchars($deliveryDiscord); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="flex justify-between text-2xl font-black pt-2">
                        <span>Total</span>
                        <span class="text-red-500"><?php echo 'R$ ' . number_format((float)$order['total'], 2, ',', '.'); ?></span>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-black/30 p-5">
                    <div class="text-sm font-black tracking-wide uppercase text-white/80">Pagamento</div>
                    <?php if ($paymentMethod === 'manual'): ?>
                        <div class="mt-3 text-white/70 font-semibold"><?php echo htmlspecialchars($manualInstructions); ?></div>
                        <?php if ($pixKey !== ''): ?>
                            <div class="mt-4">
                                <div class="text-xs text-white/50 font-bold tracking-wide uppercase">Chave PIX</div>
                                <div class="mt-2 px-4 py-3 rounded-xl bg-white/5 border border-white/10 font-black break-all"><?php echo htmlspecialchars($pixKey); ?></div>
                            </div>
                        <?php endif; ?>
                        <div class="mt-4">
                            <a href="/api/receipt.php?order_id=<?php echo (int)$orderId; ?>" class="block text-center px-5 py-3 rounded-xl bg-ff-red hover:bg-red-700 font-black">Baixar comprovante (PDF)</a>
                            <div class="text-xs text-white/40 mt-2">O comprovante também é enviado por e-mail quando o servidor possui envio de e-mail configurado.</div>
                        </div>
                    <?php else: ?>
                        <?php if ($paymentStatus === 'paid'): ?>
                            <div class="mt-3 text-emerald-200 font-black">Pagamento confirmado.</div>
                            <div class="text-white/60 font-semibold mt-2">Seu pedido será processado.</div>
                        <?php else: ?>
                            <div class="mt-3 text-white/70 font-semibold">Pagamento via API em processamento. Se você acabou de pagar, aguarde alguns instantes e recarregue esta página.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            if (!window.ThunderPopup || typeof window.ThunderPopup.modal !== 'function') return;
            const orderId = <?php echo (int)$orderId; ?>;
            const paymentMethod = <?php echo json_encode($paymentMethod); ?>;
            const paymentStatus = <?php echo json_encode($paymentStatus); ?>;
            const key = 'tp_order_notice_' + String(orderId) + '_' + String(paymentMethod) + '_' + String(paymentStatus);
            if (localStorage.getItem(key) === '1') return;

            if (paymentMethod === 'api' && paymentStatus === 'paid') {
                localStorage.setItem(key, '1');
                window.ThunderPopup.modal({
                    type: 'success',
                    title: 'Pagamento confirmado',
                    message: 'Seu pagamento foi confirmado. Você vai receber a key e o download do painel no Gmail e Discord informados.',
                    primaryText: 'Entendi',
                    secondaryText: '',
                    danger: false
                });
                return;
            }

            if (paymentMethod === 'manual' && paymentStatus !== 'paid') {
                localStorage.setItem(key, '1');
                window.ThunderPopup.modal({
                    type: 'info',
                    title: 'Pedido criado',
                    message: 'Finalize o pagamento conforme as instruções. Assim que confirmar, a entrega digital será enviada no Gmail e Discord informados.',
                    primaryText: 'Ok',
                    secondaryText: '',
                    danger: false
                });
            }
        })();
    </script>
</body>
</html>

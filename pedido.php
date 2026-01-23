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
    <link rel="stylesheet" href="/assets/popup.css?v=20260123" />
    <style>
        html, body { touch-action: pan-x pan-y; }
        body { background-color: #000; color: white; font-family: 'Inter', sans-serif; }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
    <script src="/assets/popup.js?v=20260123" defer></script>
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

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-7 rounded-2xl border border-white/10 bg-white/5 p-6">
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

            <div class="lg:col-span-5 rounded-2xl border border-white/10 bg-white/5 p-6 h-fit">
                <div class="text-xl font-black">Resumo</div>
                <div class="mt-4 space-y-3 text-white/70 font-semibold">
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
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm font-black tracking-wide uppercase text-white/80">Pagamento</div>
                        <?php if ($paymentMethod === 'manual'): ?>
                            <div class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-200 text-xs font-black tracking-wide uppercase">PIX Manual</div>
                        <?php endif; ?>
                    </div>
                    <?php if ($paymentMethod === 'manual'): ?>
                        <div class="mt-3 text-white/70 font-semibold leading-relaxed"><?php echo htmlspecialchars($manualInstructions); ?></div>

                        <div class="mt-4 grid grid-cols-1 gap-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-white/50 font-black tracking-wide uppercase">Passo a passo</div>
                                <div class="mt-3 space-y-2 text-sm text-white/70 font-semibold leading-relaxed">
                                    <div class="flex gap-3">
                                        <div class="h-7 w-7 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white font-black">1</div>
                                        <div class="flex-1">Faça o PIX usando a chave abaixo.</div>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="h-7 w-7 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white font-black">2</div>
                                        <div class="flex-1">Pegue o comprovante no app do seu banco (ou tire print).</div>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="h-7 w-7 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white font-black">3</div>
                                        <div class="flex-1">Abra um ticket no Discord e envie o comprovante + o ID do pedido.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($pixKey !== ''): ?>
                            <div class="mt-4">
                                <div class="text-xs text-white/50 font-bold tracking-wide uppercase">Chave PIX</div>
                                <div class="mt-2 flex items-stretch gap-2">
                                    <div class="flex-1 px-4 py-3 rounded-xl bg-black/30 border border-white/10 font-black break-all"><?php echo htmlspecialchars($pixKey); ?></div>
                                    <button type="button" data-copy="<?php echo htmlspecialchars($pixKey); ?>" class="px-4 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-black text-sm">
                                        Copiar
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="mt-4">
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-white/10 bg-black/20 px-4 py-3">
                                <div>
                                    <div class="text-xs text-white/50 font-black tracking-wide uppercase">ID do pedido</div>
                                    <div class="font-black text-white">#<?php echo (int)$orderId; ?></div>
                                </div>
                                <button type="button" data-copy="<?php echo (int)$orderId; ?>" class="px-4 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-black text-sm">
                                    Copiar
                                </button>
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <a href="https://discord.gg/hpjCtT7CU7" target="_blank" rel="noopener" class="block text-center px-5 py-3 rounded-xl bg-ff-red hover:bg-red-700 font-black">
                                    Abrir ticket no Discord
                                </a>
                                <button type="button" data-copy="<?php echo htmlspecialchars('Pedido #' . (int)$orderId . ' - Segue comprovante PIX.'); ?>" class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-black">
                                    Copiar mensagem
                                </button>
                            </div>

                            <div class="mt-3 rounded-xl border border-amber-500/25 bg-amber-500/10 px-4 py-3 text-sm text-amber-200 font-semibold leading-relaxed">
                                Abra um ticket no Discord e envie o comprovante do PIX (do seu banco) + o ID do pedido. Sem o comprovante, a entrega pode atrasar.
                            </div>
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
    <script>
        (function () {
            const buttons = document.querySelectorAll('[data-copy]');
            if (!buttons.length) return;
            buttons.forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const value = btn.getAttribute('data-copy') || '';
                    if (!value) return;
                    try {
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            await navigator.clipboard.writeText(value);
                        } else {
                            const ta = document.createElement('textarea');
                            ta.value = value;
                            ta.style.position = 'fixed';
                            ta.style.left = '-9999px';
                            document.body.appendChild(ta);
                            ta.focus();
                            ta.select();
                            document.execCommand('copy');
                            ta.remove();
                        }
                        if (window.ThunderPopup && typeof window.ThunderPopup.toast === 'function') {
                            window.ThunderPopup.toast('success', 'Copiado.');
                        }
                    } catch (e) {
                        if (window.ThunderPopup && typeof window.ThunderPopup.toast === 'function') {
                            window.ThunderPopup.toast('error', 'Não foi possível copiar.');
                        }
                    }
                });
            });
        })();
    </script>
</body>
</html>

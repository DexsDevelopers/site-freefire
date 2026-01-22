<?php
require_once __DIR__ . '/_layout.php';
require_admin($conn);

$statusOptions = ['Pendente', 'Pago', 'Concluido', 'Cancelado', 'Reembolsado'];

$flash = ['type' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'update_status') {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $newStatus = (string)($_POST['status'] ?? '');

        if ($orderId <= 0 || !in_array($newStatus, $statusOptions, true)) {
            $flash = ['type' => 'error', 'message' => 'Dados inválidos para atualizar status.'];
        } else {
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $orderId);
            if ($stmt->execute()) {
                if (db_table_exists($conn, 'affiliate_commissions')) {
                    if (in_array($newStatus, ['Pago', 'Concluido'], true)) {
                        $stmt2 = $conn->prepare("UPDATE affiliate_commissions SET status = 'approved' WHERE order_id = ? AND status IN ('pending')");
                        $stmt2->bind_param("i", $orderId);
                        $stmt2->execute();
                    }
                    if (in_array($newStatus, ['Cancelado', 'Reembolsado'], true)) {
                        $stmt2 = $conn->prepare("UPDATE affiliate_commissions SET status = 'rejected' WHERE order_id = ? AND status IN ('pending','approved')");
                        $stmt2->bind_param("i", $orderId);
                        $stmt2->execute();
                    }
                }
                $flash = ['type' => 'success', 'message' => 'Status atualizado com sucesso.'];
            } else {
                $flash = ['type' => 'error', 'message' => 'Não foi possível atualizar o status.'];
            }
        }
    }
}

$q = trim((string)($_GET['q'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];
$types = '';

if ($status !== '' && in_array($status, $statusOptions, true)) {
    $where[] = "o.status = ?";
    $types .= 's';
    $params[] = $status;
}

if ($q !== '') {
    if (ctype_digit($q)) {
        $where[] = "o.id = ?";
        $types .= 'i';
        $params[] = (int)$q;
    } else {
        $where[] = "(u.email LIKE ? OR u.name LIKE ?)";
        $types .= 'ss';
        $params[] = '%' . $q . '%';
        $params[] = '%' . $q . '%';
    }
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$countSql = "SELECT COUNT(*) AS c FROM orders o INNER JOIN users u ON u.id = o.user_id $whereSql";
$stmt = $conn->prepare($countSql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$totalRows = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$listSql = "SELECT o.id, o.total, o.status, o.created_at, u.name AS user_name, u.email AS user_email
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            $whereSql
            ORDER BY o.id DESC
            LIMIT ? OFFSET ?";
$stmt = $conn->prepare($listSql);

if ($types === '') {
    $stmt->bind_param("ii", $perPage, $offset);
} else {
    $types2 = $types . 'ii';
    $params2 = array_merge($params, [$perPage, $offset]);
    $stmt->bind_param($types2, ...$params2);
}
$stmt->execute();
$orders = $stmt->get_result();

$viewId = (int)($_GET['view'] ?? 0);
$viewOrder = null;
$viewItems = [];

if ($viewId > 0) {
    $stmt = $conn->prepare("SELECT o.id, o.total, o.status, o.created_at, u.name AS user_name, u.email AS user_email
                            FROM orders o INNER JOIN users u ON u.id = o.user_id WHERE o.id = ? LIMIT 1");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $viewOrder = $stmt->get_result()->fetch_assoc();

    if ($viewOrder) {
        $stmt = $conn->prepare("SELECT oi.id, oi.price, p.name AS plan_name, pr.name AS product_name
                                FROM order_items oi
                                INNER JOIN plans p ON p.id = oi.plan_id
                                INNER JOIN products pr ON pr.id = p.product_id
                                WHERE oi.order_id = ?
                                ORDER BY oi.id ASC");
        $stmt->bind_param("i", $viewId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $viewItems[] = $r;
        }
    }
}

ob_start();
?>
<div class="flex flex-col xl:flex-row gap-4">
    <div class="flex-1 rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <div class="text-lg font-black">Vendas</div>
                <div class="text-xs text-white/50 mt-1"><?php echo h($totalRows); ?> pedidos encontrados</div>
            </div>
            <form method="get" class="flex flex-col sm:flex-row gap-3 sm:items-end">
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2">Busca (ID, nome ou email)</label>
                    <input name="q" value="<?php echo h($q); ?>" class="w-full sm:w-72 px-4 py-3 rounded-xl bg-black/30 border border-admin-border focus:outline-none focus:ring-2 focus:ring-admin-accent/40 focus:border-admin-accent/60" placeholder="ex: 1024 ou cliente@email.com">
                </div>
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2">Status</label>
                    <select name="status" class="w-full sm:w-56 px-4 py-3 rounded-xl bg-black/30 border border-admin-border focus:outline-none focus:ring-2 focus:ring-admin-accent/40 focus:border-admin-accent/60">
                        <option value="">Todos</option>
                        <?php foreach ($statusOptions as $s): ?>
                            <option value="<?php echo h($s); ?>" <?php echo ($status === $s) ? 'selected' : ''; ?>><?php echo h($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button class="px-5 py-3 rounded-xl bg-admin-accent hover:bg-red-700 font-black">Filtrar</button>
                    <a href="/admin/vendas.php" class="px-5 py-3 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-black">Limpar</a>
                </div>
            </form>
        </div>

        <?php if ($flash['message'] !== ''): ?>
            <?php
                $cls = $flash['type'] === 'success'
                    ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                    : 'border-admin-accent/40 bg-admin-accent/10 text-red-200';
            ?>
            <div class="mt-5 rounded-xl border px-4 py-3 text-sm <?php echo $cls; ?>">
                <?php echo h($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-white/60">
                <tr class="border-b border-admin-border">
                    <th class="text-left py-3 pr-4">#</th>
                    <th class="text-left py-3 pr-4">Cliente</th>
                    <th class="text-left py-3 pr-4">Email</th>
                    <th class="text-left py-3 pr-4">Total</th>
                    <th class="text-left py-3 pr-4">Status</th>
                    <th class="text-left py-3 pr-4">Criado</th>
                    <th class="text-right py-3">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                <?php if ($orders && $orders->num_rows > 0): ?>
                    <?php while ($row = $orders->fetch_assoc()): ?>
                        <tr>
                            <td class="py-3 pr-4 font-bold"><?php echo h($row['id']); ?></td>
                            <td class="py-3 pr-4"><?php echo h($row['user_name']); ?></td>
                            <td class="py-3 pr-4 text-white/70"><?php echo h($row['user_email']); ?></td>
                            <td class="py-3 pr-4 font-bold">R$ <?php echo h(number_format((float)$row['total'], 2, ',', '.')); ?></td>
                            <td class="py-3 pr-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border border-admin-border bg-black/30">
                                    <?php echo h($row['status']); ?>
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-white/70"><?php echo h($row['created_at']); ?></td>
                            <td class="py-3 text-right">
                                <a class="px-3 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black"
                                   href="<?php echo h('/admin/vendas.php?' . http_build_query(array_merge($_GET, ['view' => $row['id']]))); ?>">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="py-10 text-center text-white/60">Nenhum pedido encontrado.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex items-center justify-between gap-3 flex-wrap">
            <div class="text-xs text-white/50">Página <?php echo h($page); ?> de <?php echo h($totalPages); ?></div>
            <div class="flex items-center gap-2">
                <?php
                    $base = $_GET;
                    unset($base['page']);
                    $prev = max(1, $page - 1);
                    $next = min($totalPages, $page + 1);
                ?>
                <a class="px-4 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black <?php echo ($page <= 1) ? 'pointer-events-none opacity-40' : ''; ?>"
                   href="<?php echo h('/admin/vendas.php?' . http_build_query(array_merge($base, ['page' => $prev]))); ?>">
                    Anterior
                </a>
                <a class="px-4 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black <?php echo ($page >= $totalPages) ? 'pointer-events-none opacity-40' : ''; ?>"
                   href="<?php echo h('/admin/vendas.php?' . http_build_query(array_merge($base, ['page' => $next]))); ?>">
                    Próxima
                </a>
            </div>
        </div>
    </div>

    <div class="w-full xl:w-[420px] rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="text-lg font-black">Detalhes</div>
        <div class="text-xs text-white/50 mt-1">Selecione um pedido para ver itens e ações</div>

        <?php if ($viewOrder): ?>
            <div class="mt-5 rounded-2xl border border-admin-border bg-black/20 p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm text-white/60 font-bold">Pedido</div>
                        <div class="text-2xl font-black">#<?php echo h($viewOrder['id']); ?></div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border border-admin-border bg-black/30">
                        <?php echo h($viewOrder['status']); ?>
                    </span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs text-white/60 font-bold">Cliente</div>
                        <div class="font-bold"><?php echo h($viewOrder['user_name']); ?></div>
                        <div class="text-xs text-white/60"><?php echo h($viewOrder['user_email']); ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-white/60 font-bold">Total</div>
                        <div class="text-lg font-black">R$ <?php echo h(number_format((float)$viewOrder['total'], 2, ',', '.')); ?></div>
                        <div class="text-xs text-white/60"><?php echo h($viewOrder['created_at']); ?></div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="text-xs text-white/60 font-bold mb-2">Atualizar status</div>
                    <form method="post" class="flex gap-2">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" value="<?php echo h($viewOrder['id']); ?>">
                        <select name="status" class="flex-1 px-4 py-3 rounded-xl bg-black/30 border border-admin-border focus:outline-none focus:ring-2 focus:ring-admin-accent/40 focus:border-admin-accent/60 text-sm font-bold">
                            <?php foreach ($statusOptions as $s): ?>
                                <option value="<?php echo h($s); ?>" <?php echo (($viewOrder['status'] ?? '') === $s) ? 'selected' : ''; ?>>
                                    <?php echo h($s); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="px-4 py-3 rounded-xl bg-admin-accent hover:bg-red-700 font-black text-sm">
                            Salvar
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-5">
                <div class="text-xs text-white/60 font-bold mb-2">Itens</div>
                <div class="rounded-2xl border border-admin-border bg-black/20 overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="text-white/60 border-b border-admin-border">
                        <tr>
                            <th class="text-left py-3 px-4">Produto</th>
                            <th class="text-left py-3 px-4">Plano</th>
                            <th class="text-right py-3 px-4">Preço</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-admin-border">
                        <?php if (count($viewItems) > 0): ?>
                            <?php foreach ($viewItems as $it): ?>
                                <tr>
                                    <td class="py-3 px-4 font-bold"><?php echo h($it['product_name']); ?></td>
                                    <td class="py-3 px-4 text-white/80"><?php echo h($it['plan_name']); ?></td>
                                    <td class="py-3 px-4 text-right font-bold">R$ <?php echo h(number_format((float)$it['price'], 2, ',', '.')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="py-6 text-center text-white/60">Sem itens.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="mt-6 rounded-2xl border border-admin-border bg-black/20 p-5 text-sm text-white/60">
                Abra um pedido para ver os detalhes.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
render_admin_layout('Vendas', 'vendas', $content);

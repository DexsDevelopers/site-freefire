<?php
require_once __DIR__ . '/_layout.php';
require_admin($conn);

$metrics = [
    'orders_total' => 0,
    'revenue_total' => 0.0,
    'orders_pending' => 0,
    'users_total' => 0,
];

$res = $conn->query("SELECT COUNT(*) AS c FROM orders");
if ($res) $metrics['orders_total'] = (int)$res->fetch_assoc()['c'];

$res = $conn->query("SELECT COALESCE(SUM(total),0) AS s FROM orders WHERE status IN ('Pago','Concluido')");
if ($res) $metrics['revenue_total'] = (float)$res->fetch_assoc()['s'];

$res = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status = 'Pendente'");
if ($res) $metrics['orders_pending'] = (int)$res->fetch_assoc()['c'];

$res = $conn->query("SELECT COUNT(*) AS c FROM users");
if ($res) $metrics['users_total'] = (int)$res->fetch_assoc()['c'];

ob_start();
?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-2xl border border-admin-border bg-white/5 p-5">
        <div class="text-xs text-white/60 font-bold">Pedidos</div>
        <div class="mt-2 text-3xl font-black"><?php echo h($metrics['orders_total']); ?></div>
        <div class="mt-3 text-xs text-white/50">Total no sistema</div>
    </div>
    <div class="rounded-2xl border border-admin-border bg-white/5 p-5">
        <div class="text-xs text-white/60 font-bold">Faturamento</div>
        <div class="mt-2 text-3xl font-black">R$ <?php echo h(number_format($metrics['revenue_total'], 2, ',', '.')); ?></div>
        <div class="mt-3 text-xs text-white/50">Somente Pago/Concluído</div>
    </div>
    <div class="rounded-2xl border border-admin-border bg-white/5 p-5">
        <div class="text-xs text-white/60 font-bold">Pendentes</div>
        <div class="mt-2 text-3xl font-black"><?php echo h($metrics['orders_pending']); ?></div>
        <div class="mt-3 text-xs text-white/50">Aguardando pagamento</div>
    </div>
    <div class="rounded-2xl border border-admin-border bg-white/5 p-5">
        <div class="text-xs text-white/60 font-bold">Usuários</div>
        <div class="mt-2 text-3xl font-black"><?php echo h($metrics['users_total']); ?></div>
        <div class="mt-3 text-xs text-white/50">Cadastrados</div>
    </div>
</div>

<div class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="xl:col-span-2 rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="text-lg font-black">Últimos pedidos</div>
                <div class="text-xs text-white/50 mt-1">Visão rápida</div>
            </div>
            <a href="/admin/vendas.php" class="px-4 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-sm font-bold">
                Ver vendas
            </a>
        </div>
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-white/60">
                <tr class="border-b border-admin-border">
                    <th class="text-left py-3 pr-4">#</th>
                    <th class="text-left py-3 pr-4">Cliente</th>
                    <th class="text-left py-3 pr-4">Total</th>
                    <th class="text-left py-3 pr-4">Status</th>
                    <th class="text-left py-3 pr-4">Data</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                <?php
                    $sql = "SELECT o.id, o.total, o.status, o.created_at, u.name AS user_name
                            FROM orders o
                            INNER JOIN users u ON u.id = o.user_id
                            ORDER BY o.id DESC
                            LIMIT 8";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td class="py-3 pr-4 font-bold"><?php echo h($row['id']); ?></td>
                        <td class="py-3 pr-4"><?php echo h($row['user_name']); ?></td>
                        <td class="py-3 pr-4 font-bold">R$ <?php echo h(number_format((float)$row['total'], 2, ',', '.')); ?></td>
                        <td class="py-3 pr-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border border-admin-border bg-black/30">
                                <?php echo h($row['status']); ?>
                            </span>
                        </td>
                        <td class="py-3 pr-4 text-white/70"><?php echo h($row['created_at']); ?></td>
                    </tr>
                <?php
                        endwhile;
                    else:
                ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-white/60">Nenhum pedido ainda.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="text-lg font-black">Atalhos</div>
        <div class="mt-4 grid grid-cols-1 gap-3">
            <a class="px-4 py-3 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-bold" href="/admin/produtos.php">Gerenciar produtos</a>
            <a class="px-4 py-3 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-bold" href="/admin/vendas.php">Ver pedidos</a>
            <a class="px-4 py-3 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-bold" href="/admin/usuarios.php">Gerenciar usuários</a>
            <a class="px-4 py-3 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-bold" href="/admin/afiliados.php">Programa de afiliados</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
render_admin_layout('Dashboard', 'dashboard', $content);


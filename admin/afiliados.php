<?php
require_once __DIR__ . '/_layout.php';
require_admin($conn);

$required = ['affiliate_accounts', 'affiliate_clicks', 'affiliate_referrals', 'affiliate_commissions', 'affiliate_payouts', 'app_settings'];
foreach ($required as $t) {
    if (!db_table_exists($conn, $t)) {
        ob_start();
        ?>
        <div class="rounded-2xl border border-admin-border bg-white/5 p-6">
            <div class="text-lg font-black">Afiliados</div>
            <div class="text-sm text-white/60 mt-2">Afiliados não está configurado. Rode <span class="font-bold">/setup_db.php</span>.</div>
        </div>
        <?php
        $content = ob_get_clean();
        render_admin_layout('Afiliados', 'afiliados', $content);
        exit;
    }
}

$flash = ['type' => '', 'message' => ''];
$payoutStatusOptions = ['requested', 'approved', 'rejected', 'paid'];
$accountStatusOptions = ['active', 'paused'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'update_payout') {
        $payoutId = (int)($_POST['payout_id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        $note = trim((string)($_POST['note'] ?? ''));
        if ($payoutId <= 0 || !in_array($status, $payoutStatusOptions, true)) {
            $flash = ['type' => 'error', 'message' => 'Dados inválidos para saque.'];
        } else {
            if ($status === 'requested') {
                $stmt = $conn->prepare("UPDATE affiliate_payouts SET status = ?, note = ?, processed_at = NULL WHERE id = ?");
                $stmt->bind_param("ssi", $status, $note, $payoutId);
            } else {
                $stmt = $conn->prepare("UPDATE affiliate_payouts SET status = ?, note = ?, processed_at = NOW() WHERE id = ?");
                $stmt->bind_param("ssi", $status, $note, $payoutId);
            }
            if ($stmt->execute()) {
                $flash = ['type' => 'success', 'message' => 'Saque atualizado.'];
            } else {
                $flash = ['type' => 'error', 'message' => 'Não foi possível atualizar o saque.'];
            }
        }
    }

    if ($action === 'update_rate') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $rate = (float)($_POST['commission_rate'] ?? -1);
        if ($userId <= 0 || $rate < 0 || $rate > 0.9) {
            $flash = ['type' => 'error', 'message' => 'Comissão inválida.'];
        } else {
            $stmt = $conn->prepare("UPDATE affiliate_accounts SET commission_rate = ? WHERE user_id = ?");
            $stmt->bind_param("di", $rate, $userId);
            if ($stmt->execute()) {
                $flash = ['type' => 'success', 'message' => 'Comissão atualizada.'];
            } else {
                $flash = ['type' => 'error', 'message' => 'Não foi possível atualizar a comissão.'];
            }
        }
    }

    if ($action === 'toggle_status') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        if ($userId <= 0 || !in_array($status, $accountStatusOptions, true)) {
            $flash = ['type' => 'error', 'message' => 'Status inválido.'];
        } else {
            $stmt = $conn->prepare("UPDATE affiliate_accounts SET status = ? WHERE user_id = ?");
            $stmt->bind_param("si", $status, $userId);
            if ($stmt->execute()) {
                $flash = ['type' => 'success', 'message' => 'Status do afiliado atualizado.'];
            } else {
                $flash = ['type' => 'error', 'message' => 'Não foi possível atualizar o status.'];
            }
        }
    }
}

$res = $conn->query("SELECT COUNT(*) AS c FROM affiliate_accounts");
$affTotal = $res ? (int)$res->fetch_assoc()['c'] : 0;
$res = $conn->query("SELECT COALESCE(SUM(amount),0) AS s FROM affiliate_commissions WHERE status = 'pending'");
$commPending = $res ? (float)$res->fetch_assoc()['s'] : 0;
$res = $conn->query("SELECT COALESCE(SUM(amount),0) AS s FROM affiliate_payouts WHERE status = 'requested'");
$payoutRequested = $res ? (float)$res->fetch_assoc()['s'] : 0;

$affSql = "SELECT a.user_id, u.name, u.email, a.code, a.commission_rate, a.status, a.created_at,
    (SELECT COUNT(*) FROM affiliate_clicks c WHERE c.affiliate_user_id = a.user_id) AS clicks,
    (SELECT COUNT(*) FROM affiliate_referrals r WHERE r.affiliate_user_id = a.user_id) AS referrals,
    (SELECT COALESCE(SUM(amount),0) FROM affiliate_commissions cm WHERE cm.affiliate_user_id = a.user_id AND cm.status = 'approved') AS comm_approved,
    (SELECT COALESCE(SUM(amount),0) FROM affiliate_commissions cm WHERE cm.affiliate_user_id = a.user_id AND cm.status = 'pending') AS comm_pending,
    (SELECT COALESCE(SUM(amount),0) FROM affiliate_payouts p WHERE p.affiliate_user_id = a.user_id AND p.status IN ('approved','paid')) AS payout_paid
    FROM affiliate_accounts a
    INNER JOIN users u ON u.id = a.user_id
    ORDER BY a.created_at DESC
    LIMIT 200";
$affiliates = $conn->query($affSql);

$payouts = $conn->query("SELECT p.id, p.affiliate_user_id, p.amount, p.method, p.destination, p.status, p.note, p.created_at, p.processed_at, u.name, u.email
                         FROM affiliate_payouts p
                         INNER JOIN users u ON u.id = p.affiliate_user_id
                         ORDER BY p.id DESC
                         LIMIT 80");

ob_start();
?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="rounded-2xl border border-admin-border bg-white/5 p-5">
        <div class="text-xs text-white/60 font-bold">Afiliados</div>
        <div class="mt-2 text-3xl font-black"><?php echo h($affTotal); ?></div>
    </div>
    <div class="rounded-2xl border border-admin-border bg-white/5 p-5">
        <div class="text-xs text-white/60 font-bold">Comissões pendentes</div>
        <div class="mt-2 text-3xl font-black">R$ <?php echo h(number_format($commPending, 2, ',', '.')); ?></div>
    </div>
    <div class="rounded-2xl border border-admin-border bg-white/5 p-5">
        <div class="text-xs text-white/60 font-bold">Saques solicitados</div>
        <div class="mt-2 text-3xl font-black">R$ <?php echo h(number_format($payoutRequested, 2, ',', '.')); ?></div>
    </div>
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

<div class="mt-8 grid grid-cols-1 xl:grid-cols-2 gap-4">
    <div class="rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <div class="text-lg font-black">Saques</div>
                <div class="text-xs text-white/50 mt-1">Gerencie as solicitações e pagamentos</div>
            </div>
        </div>
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-white/60">
                <tr class="border-b border-admin-border">
                    <th class="text-left py-3 pr-4">#</th>
                    <th class="text-left py-3 pr-4">Afiliado</th>
                    <th class="text-left py-3 pr-4">Valor</th>
                    <th class="text-left py-3 pr-4">Destino</th>
                    <th class="text-left py-3 pr-4">Status</th>
                    <th class="text-right py-3">Ação</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                <?php if ($payouts && $payouts->num_rows > 0): ?>
                    <?php while ($p = $payouts->fetch_assoc()): ?>
                        <tr>
                            <td class="py-3 pr-4 font-bold"><?php echo h($p['id']); ?></td>
                            <td class="py-3 pr-4">
                                <div class="font-bold"><?php echo h($p['name']); ?></div>
                                <div class="text-xs text-white/60"><?php echo h($p['email']); ?></div>
                            </td>
                            <td class="py-3 pr-4 font-black">R$ <?php echo h(number_format((float)$p['amount'], 2, ',', '.')); ?></td>
                            <td class="py-3 pr-4 text-white/70">
                                <div class="font-bold uppercase text-xs"><?php echo h($p['method']); ?></div>
                                <div class="text-xs"><?php echo h($p['destination']); ?></div>
                            </td>
                            <td class="py-3 pr-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border border-admin-border bg-black/30"><?php echo h($p['status']); ?></span>
                            </td>
                            <td class="py-3 text-right">
                                <form method="post" class="flex flex-col gap-2 items-end">
                                    <?php echo csrf_input(); ?>
                                    <input type="hidden" name="action" value="update_payout">
                                    <input type="hidden" name="payout_id" value="<?php echo h($p['id']); ?>">
                                    <select name="status" class="w-44 px-3 py-2 rounded-xl bg-black/30 border border-admin-border text-xs font-bold">
                                        <?php foreach ($payoutStatusOptions as $st): ?>
                                            <option value="<?php echo h($st); ?>" <?php echo (($p['status'] ?? '') === $st) ? 'selected' : ''; ?>><?php echo h($st); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input name="note" value="<?php echo h($p['note']); ?>" class="w-44 px-3 py-2 rounded-xl bg-black/30 border border-admin-border text-xs font-bold" placeholder="Observação">
                                    <button class="w-44 px-4 py-2 rounded-xl bg-admin-accent hover:bg-red-700 text-xs font-black">Salvar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-white/60">Nenhum saque.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="text-lg font-black">Afiliados</div>
        <div class="text-xs text-white/50 mt-1">Códigos, comissão, desempenho e saldo</div>
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-white/60">
                <tr class="border-b border-admin-border">
                    <th class="text-left py-3 pr-4">Afiliado</th>
                    <th class="text-left py-3 pr-4">Código</th>
                    <th class="text-left py-3 pr-4">Cliques</th>
                    <th class="text-left py-3 pr-4">Cadastros</th>
                    <th class="text-left py-3 pr-4">Saldo</th>
                    <th class="text-right py-3">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                <?php if ($affiliates && $affiliates->num_rows > 0): ?>
                    <?php while ($a = $affiliates->fetch_assoc()): ?>
                        <?php
                            $approved = (float)($a['comm_approved'] ?? 0);
                            $paid = (float)($a['payout_paid'] ?? 0);
                            $available = max(0, round($approved - $paid, 2));
                            $ratePct = ((float)($a['commission_rate'] ?? 0)) * 100;
                        ?>
                        <tr>
                            <td class="py-3 pr-4">
                                <div class="font-bold"><?php echo h($a['name']); ?></div>
                                <div class="text-xs text-white/60"><?php echo h($a['email']); ?></div>
                                <div class="text-xs text-white/50 mt-1"><?php echo h($a['status']); ?> • <?php echo h(number_format($ratePct, 2, ',', '.')); ?>%</div>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="font-bold"><?php echo h($a['code']); ?></div>
                                <a class="text-xs text-white/60 hover:text-white" href="<?php echo h('/r.php?c=' . urlencode($a['code'])); ?>" target="_blank">/r.php?c=<?php echo h($a['code']); ?></a>
                            </td>
                            <td class="py-3 pr-4 font-bold"><?php echo h($a['clicks']); ?></td>
                            <td class="py-3 pr-4 font-bold"><?php echo h($a['referrals']); ?></td>
                            <td class="py-3 pr-4">
                                <div class="font-black">R$ <?php echo h(number_format($available, 2, ',', '.')); ?></div>
                                <div class="text-xs text-white/60">Aprovado: R$ <?php echo h(number_format($approved, 2, ',', '.')); ?></div>
                                <div class="text-xs text-white/60">Pendente: R$ <?php echo h(number_format((float)$a['comm_pending'], 2, ',', '.')); ?></div>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex flex-col gap-2 items-end">
                                    <form method="post" class="flex gap-2 items-center">
                                        <?php echo csrf_input(); ?>
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="user_id" value="<?php echo h($a['user_id']); ?>">
                                        <select name="status" class="px-3 py-2 rounded-xl bg-black/30 border border-admin-border text-xs font-black">
                                            <?php foreach ($accountStatusOptions as $st): ?>
                                                <option value="<?php echo h($st); ?>" <?php echo (($a['status'] ?? '') === $st) ? 'selected' : ''; ?>><?php echo h($st); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="px-4 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black">Status</button>
                                    </form>
                                    <form method="post" class="flex gap-2 items-center">
                                        <?php echo csrf_input(); ?>
                                        <input type="hidden" name="action" value="update_rate">
                                        <input type="hidden" name="user_id" value="<?php echo h($a['user_id']); ?>">
                                        <input name="commission_rate" value="<?php echo h((string)$a['commission_rate']); ?>" class="w-24 px-3 py-2 rounded-xl bg-black/30 border border-admin-border text-xs font-black" placeholder="0.10">
                                        <button class="px-4 py-2 rounded-xl bg-admin-accent hover:bg-red-700 text-xs font-black">Comissão</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-white/60">Nenhum afiliado ainda.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
render_admin_layout('Afiliados', 'afiliados', $content);

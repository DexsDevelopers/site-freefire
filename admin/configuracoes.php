<?php
require_once __DIR__ . '/_layout.php';
require_admin($conn);

if (!db_table_exists($conn, 'app_settings')) {
    ob_start();
    ?>
    <div class="rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="text-lg font-black">Configurações</div>
        <div class="text-sm text-white/60 mt-2">Banco desatualizado. Rode <span class="font-bold">/setup_db.php</span>.</div>
    </div>
    <?php
    $content = ob_get_clean();
    render_admin_layout('Configurações', 'config', $content);
    exit;
}

$flash = ['type' => '', 'message' => ''];

function get_setting(mysqli $conn, $key, $default)
{
    $stmt = $conn->prepare("SELECT value FROM app_settings WHERE `key` = ? LIMIT 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? (string)$row['value'] : (string)$default;
}

function set_setting(mysqli $conn, $key, $value)
{
    $stmt = $conn->prepare("INSERT INTO app_settings (`key`,`value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
    $stmt->bind_param("ss", $key, $value);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'save_settings') {
        $rate = (float)($_POST['affiliate_default_rate'] ?? 0.10);
        $min = (float)($_POST['affiliate_payout_min'] ?? 50.00);

        if ($rate <= 0 || $rate > 0.9) {
            $flash = ['type' => 'error', 'message' => 'Taxa padrão inválida (use 0.01 até 0.90).'];
        } elseif ($min < 0) {
            $flash = ['type' => 'error', 'message' => 'Mínimo de saque inválido.'];
        } else {
            $ok = true;
            $ok = $ok && set_setting($conn, 'affiliate_default_rate', number_format($rate, 4, '.', ''));
            $ok = $ok && set_setting($conn, 'affiliate_payout_min', number_format($min, 2, '.', ''));
            $flash = $ok ? ['type' => 'success', 'message' => 'Configurações salvas.'] : ['type' => 'error', 'message' => 'Não foi possível salvar.'];
        }
    }
}

$affiliate_default_rate = (float)get_setting($conn, 'affiliate_default_rate', '0.10');
$affiliate_payout_min = (float)get_setting($conn, 'affiliate_payout_min', '50.00');

ob_start();
?>
<?php if ($flash['message'] !== ''): ?>
    <?php
        $cls = $flash['type'] === 'success'
            ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
            : 'border-admin-accent/40 bg-admin-accent/10 text-red-200';
    ?>
    <div class="mb-5 rounded-xl border px-4 py-3 text-sm <?php echo $cls; ?>">
        <?php echo h($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="rounded-2xl border border-admin-border bg-white/5 p-6">
    <div class="text-lg font-black">Configurações</div>
    <div class="text-xs text-white/50 mt-1">Parâmetros globais do sistema</div>

    <div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-admin-border bg-black/20 p-6">
            <div class="text-sm font-black">Afiliados</div>
            <div class="text-xs text-white/60 mt-1">Taxas padrão e regras de saque</div>
            <form method="post" class="mt-5 space-y-4">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="action" value="save_settings">
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2">Taxa padrão (ex: 0.10 = 10%)</label>
                    <input name="affiliate_default_rate" type="number" step="0.0001" min="0" max="0.9"
                           value="<?php echo h(number_format($affiliate_default_rate, 4, '.', '')); ?>"
                           class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2">Mínimo de saque (R$)</label>
                    <input name="affiliate_payout_min" type="number" step="0.01" min="0"
                           value="<?php echo h(number_format($affiliate_payout_min, 2, '.', '')); ?>"
                           class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold">
                </div>
                <button class="px-5 py-3 rounded-xl bg-admin-accent hover:bg-red-700 font-black">Salvar</button>
            </form>
        </div>

        <div class="rounded-2xl border border-admin-border bg-black/20 p-6">
            <div class="text-sm font-black">Acesso</div>
            <div class="text-xs text-white/60 mt-1">Para logar no admin, o usuário precisa ter role=admin.</div>
            <div class="mt-5 text-sm text-white/70">
                <div class="rounded-xl border border-admin-border bg-black/30 p-4">
                    <div class="font-bold">URL do admin</div>
                    <div class="text-white/60">/admin/login.php</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
render_admin_layout('Configurações', 'config', $content);

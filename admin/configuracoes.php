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

    if ($action === 'save_payment_settings') {
        $method = (string)($_POST['payment_method'] ?? 'manual');
        if ($method !== 'api' && $method !== 'manual') {
            $method = 'manual';
        }

        $provider = (string)($_POST['payment_provider'] ?? 'stripe');
        if ($provider !== 'stripe') {
            $provider = 'stripe';
        }

        $mode = (string)($_POST['payment_mode'] ?? 'sandbox');
        if ($mode !== 'sandbox' && $mode !== 'production') {
            $mode = 'sandbox';
        }

        $publishable = trim((string)($_POST['payment_publishable_key'] ?? ''));
        $secret = trim((string)($_POST['payment_secret_key'] ?? ''));
        $webhookUrl = trim((string)($_POST['payment_webhook_url'] ?? ''));
        $webhookSecret = trim((string)($_POST['payment_webhook_secret'] ?? ''));

        $manualPixKey = trim((string)($_POST['manual_pix_key'] ?? ''));
        $manualBank = trim((string)($_POST['manual_bank'] ?? ''));
        $manualAgency = trim((string)($_POST['manual_agency'] ?? ''));
        $manualAccount = trim((string)($_POST['manual_account'] ?? ''));
        $manualBeneficiary = trim((string)($_POST['manual_beneficiary'] ?? ''));
        $manualInstructions = trim((string)($_POST['manual_instructions'] ?? ''));

        $mailFrom = trim((string)($_POST['mail_from'] ?? ''));

        if ($method === 'api' && ($publishable === '' || $secret === '')) {
            $flash = ['type' => 'error', 'message' => 'Informe Publishable Key e Secret Key para o pagamento via API.'];
        } else {
            $ok = true;
            $ok = $ok && set_setting($conn, 'payment_method', $method);
            $ok = $ok && set_setting($conn, 'payment_provider', $provider);
            $ok = $ok && set_setting($conn, 'payment_mode', $mode);
            $ok = $ok && set_setting($conn, 'payment_publishable_key', $publishable);
            $ok = $ok && set_setting($conn, 'payment_secret_key', $secret);
            $ok = $ok && set_setting($conn, 'payment_webhook_url', $webhookUrl);
            $ok = $ok && set_setting($conn, 'payment_webhook_secret', $webhookSecret);

            $ok = $ok && set_setting($conn, 'manual_pix_key', $manualPixKey);
            $ok = $ok && set_setting($conn, 'manual_bank', $manualBank);
            $ok = $ok && set_setting($conn, 'manual_agency', $manualAgency);
            $ok = $ok && set_setting($conn, 'manual_account', $manualAccount);
            $ok = $ok && set_setting($conn, 'manual_beneficiary', $manualBeneficiary);
            $ok = $ok && set_setting($conn, 'manual_instructions', $manualInstructions);

            $ok = $ok && set_setting($conn, 'mail_from', $mailFrom);
            $flash = $ok ? ['type' => 'success', 'message' => 'Configurações de pagamento salvas.'] : ['type' => 'error', 'message' => 'Não foi possível salvar pagamento.'];
        }
    }
}

$affiliate_default_rate = (float)get_setting($conn, 'affiliate_default_rate', '0.10');
$affiliate_payout_min = (float)get_setting($conn, 'affiliate_payout_min', '50.00');

$payment_method = (string)get_setting($conn, 'payment_method', 'manual');
$payment_provider = (string)get_setting($conn, 'payment_provider', 'stripe');
$payment_mode = (string)get_setting($conn, 'payment_mode', 'sandbox');
$payment_publishable_key = (string)get_setting($conn, 'payment_publishable_key', '');
$payment_secret_key = (string)get_setting($conn, 'payment_secret_key', '');
$payment_webhook_url = (string)get_setting($conn, 'payment_webhook_url', '');
$payment_webhook_secret = (string)get_setting($conn, 'payment_webhook_secret', '');

$manual_pix_key = (string)get_setting($conn, 'manual_pix_key', '');
$manual_bank = (string)get_setting($conn, 'manual_bank', '');
$manual_agency = (string)get_setting($conn, 'manual_agency', '');
$manual_account = (string)get_setting($conn, 'manual_account', '');
$manual_beneficiary = (string)get_setting($conn, 'manual_beneficiary', '');
$manual_instructions = (string)get_setting($conn, 'manual_instructions', 'Finalize o pagamento via PIX/transferência e envie o comprovante para o suporte.');

$mail_from = (string)get_setting($conn, 'mail_from', '');

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

    <div class="mt-6 rounded-2xl border border-admin-border bg-black/20 p-6">
        <div class="text-sm font-black">Pagamento</div>
        <div class="text-xs text-white/60 mt-1">Escolha entre API de pagamento ou pagamento manual</div>

        <form method="post" class="mt-5 space-y-6" id="paymentForm">
            <?php echo csrf_input(); ?>
            <input type="hidden" name="action" value="save_payment_settings">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <label class="rounded-xl border border-admin-border bg-black/30 p-4 flex items-start gap-3 cursor-pointer">
                    <input type="radio" name="payment_method" value="api" class="mt-1" <?php echo $payment_method === 'api' ? 'checked' : ''; ?>>
                    <div>
                        <div class="font-black">API de Pagamento</div>
                        <div class="text-xs text-white/60 mt-1">Stripe (cartão/pix conforme provedor)</div>
                    </div>
                </label>
                <label class="rounded-xl border border-admin-border bg-black/30 p-4 flex items-start gap-3 cursor-pointer">
                    <input type="radio" name="payment_method" value="manual" class="mt-1" <?php echo $payment_method !== 'api' ? 'checked' : ''; ?>>
                    <div>
                        <div class="font-black">Pagamento Manual</div>
                        <div class="text-xs text-white/60 mt-1">PIX/transferência com instruções</div>
                    </div>
                </label>
                <div class="rounded-xl border border-admin-border bg-black/30 p-4">
                    <div class="text-xs text-white/60 font-bold">E-mail (envio do PDF)</div>
                    <input name="mail_from" value="<?php echo h($mail_from); ?>" placeholder="no-reply@seudominio.com"
                           class="mt-2 w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                </div>
            </div>

            <div id="payApiBox" class="rounded-2xl border border-admin-border bg-black/30 p-6">
                <div class="text-sm font-black">API (Stripe)</div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Publishable Key</label>
                        <input name="payment_publishable_key" value="<?php echo h($payment_publishable_key); ?>" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Secret Key</label>
                        <input name="payment_secret_key" value="<?php echo h($payment_secret_key); ?>" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Modo</label>
                        <select name="payment_mode" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                            <option value="sandbox" <?php echo $payment_mode === 'sandbox' ? 'selected' : ''; ?>>Sandbox/Teste</option>
                            <option value="production" <?php echo $payment_mode === 'production' ? 'selected' : ''; ?>>Produção</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Provider</label>
                        <select name="payment_provider" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                            <option value="stripe" <?php echo $payment_provider === 'stripe' ? 'selected' : ''; ?>>Stripe</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-white/60 mb-2">Webhook URL (informativo)</label>
                        <input name="payment_webhook_url" value="<?php echo h($payment_webhook_url); ?>" placeholder="https://seudominio.com/api/webhook_stripe.php"
                               class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-white/60 mb-2">Webhook Secret (opcional)</label>
                        <input name="payment_webhook_secret" value="<?php echo h($payment_webhook_secret); ?>"
                               class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                    </div>
                </div>
            </div>

            <div id="payManualBox" class="rounded-2xl border border-admin-border bg-black/30 p-6">
                <div class="text-sm font-black">Manual</div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-white/60 mb-2">Chave PIX</label>
                        <input name="manual_pix_key" value="<?php echo h($manual_pix_key); ?>" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Banco</label>
                        <input name="manual_bank" value="<?php echo h($manual_bank); ?>" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Agência</label>
                        <input name="manual_agency" value="<?php echo h($manual_agency); ?>" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Conta</label>
                        <input name="manual_account" value="<?php echo h($manual_account); ?>" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Beneficiário</label>
                        <input name="manual_beneficiary" value="<?php echo h($manual_beneficiary); ?>" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-white/60 mb-2">Instruções</label>
                        <textarea name="manual_instructions" rows="4" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border font-bold text-sm"><?php echo h($manual_instructions); ?></textarea>
                    </div>
                </div>
            </div>

            <button class="px-5 py-3 rounded-xl bg-admin-accent hover:bg-red-700 font-black">Salvar pagamento</button>
        </form>
    </div>
</div>
<script>
    (function () {
        const form = document.getElementById('paymentForm');
        if (!form) return;
        const apiBox = document.getElementById('payApiBox');
        const manualBox = document.getElementById('payManualBox');
        const radios = Array.from(form.querySelectorAll('input[name="payment_method"]'));
        function update() {
            const selected = (radios.find(r => r.checked) || {}).value || 'manual';
            if (apiBox) apiBox.style.display = selected === 'api' ? '' : 'none';
            if (manualBox) manualBox.style.display = selected === 'manual' ? '' : 'none';
        }
        radios.forEach(r => r.addEventListener('change', update));
        update();
    })();
</script>
<?php
$content = ob_get_clean();
render_admin_layout('Configurações', 'config', $content);

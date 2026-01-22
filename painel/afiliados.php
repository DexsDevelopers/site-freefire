<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afiliados | Thunder Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { 'accent': '#dc2626', 'borderc': 'rgba(255,255,255,0.08)' }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
</head>
<body class="bg-black text-white min-h-screen font-sans">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <div class="text-3xl font-black">Programa de Afiliados</div>
                <div class="text-white/60 text-sm mt-2">Convide pessoas, ganhe comissão e solicite saques.</div>
            </div>
            <div class="flex gap-2">
                <a href="/painel" class="px-5 py-3 rounded-xl bg-white/5 border border-borderc hover:bg-white/10 font-black text-sm">Voltar</a>
                <button id="btnEnable" class="hidden px-5 py-3 rounded-xl bg-accent hover:bg-red-700 font-black text-sm">Ativar afiliado</button>
            </div>
        </div>

        <div id="alert" class="hidden mt-6 rounded-xl border px-4 py-3 text-sm"></div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-borderc bg-white/5 p-5">
                <div class="text-xs text-white/60 font-bold">Cliques</div>
                <div id="mClicks" class="mt-2 text-3xl font-black">—</div>
            </div>
            <div class="rounded-2xl border border-borderc bg-white/5 p-5">
                <div class="text-xs text-white/60 font-bold">Cadastros</div>
                <div id="mReferrals" class="mt-2 text-3xl font-black">—</div>
            </div>
            <div class="rounded-2xl border border-borderc bg-white/5 p-5">
                <div class="text-xs text-white/60 font-bold">Comissão aprovada</div>
                <div id="mApproved" class="mt-2 text-3xl font-black">—</div>
            </div>
            <div class="rounded-2xl border border-borderc bg-white/5 p-5">
                <div class="text-xs text-white/60 font-bold">Disponível p/ saque</div>
                <div id="mAvailable" class="mt-2 text-3xl font-black">—</div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2 rounded-2xl border border-borderc bg-white/5 p-6">
                <div class="text-lg font-black">Seu link</div>
                <div class="text-xs text-white/60 mt-1">Use este link para divulgar.</div>
                <div class="mt-5 flex flex-col sm:flex-row gap-3">
                    <input id="refLink" readonly class="flex-1 px-4 py-3 rounded-xl bg-black/30 border border-borderc font-bold text-sm" value="Carregando...">
                    <button id="btnCopy" class="px-5 py-3 rounded-xl bg-white/5 border border-borderc hover:bg-white/10 font-black text-sm">Copiar</button>
                </div>
                <div id="accInfo" class="mt-4 text-xs text-white/60"></div>
            </div>

            <div class="rounded-2xl border border-borderc bg-white/5 p-6">
                <div class="text-lg font-black">Solicitar saque</div>
                <div class="text-xs text-white/60 mt-1">Processado pelo administrador.</div>
                <form id="payoutForm" class="mt-5 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Valor</label>
                        <input name="amount" type="number" step="0.01" min="0" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-borderc focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent/60 font-bold" placeholder="ex: 50.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Método</label>
                        <select name="method" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-borderc focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent/60 font-bold">
                            <option value="pix">PIX</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Chave/Destino</label>
                        <input name="destination" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-borderc focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent/60 font-bold" placeholder="ex: email, CPF, telefone">
                    </div>
                    <button class="w-full px-5 py-3 rounded-xl bg-accent hover:bg-red-700 font-black text-sm">Enviar solicitação</button>
                    <div id="minInfo" class="text-xs text-white/50"></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const fmtMoney = (v) => 'R$ ' + Number(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function showAlert(type, message) {
            const el = document.getElementById('alert');
            el.classList.remove('hidden');
            el.className = 'mt-6 rounded-xl border px-4 py-3 text-sm ' + (type === 'success'
                ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                : 'border-accent/40 bg-accent/10 text-red-200');
            el.textContent = message;
        }

        async function loadAffiliate() {
            const res = await fetch('/api/affiliate.php?action=get', { credentials: 'same-origin' });
            const data = await res.json();
            if (!data.success) {
                showAlert('error', data.message || 'Erro ao carregar afiliado.');
                document.getElementById('refLink').value = 'Indisponível';
                return;
            }

            const enabled = !!data.enabled;
            document.getElementById('btnEnable').classList.toggle('hidden', enabled);

            document.getElementById('mClicks').textContent = String(data.stats?.clicks ?? 0);
            document.getElementById('mReferrals').textContent = String(data.stats?.referrals ?? 0);
            document.getElementById('mApproved').textContent = fmtMoney(data.stats?.commissions?.approved ?? 0);
            document.getElementById('mAvailable').textContent = fmtMoney(data.stats?.commissions?.available ?? 0);

            const link = data.link ? (location.origin + data.link) : 'Ative para gerar seu link';
            document.getElementById('refLink').value = link;

            const acc = data.account || null;
            document.getElementById('accInfo').textContent = acc
                ? `Código: ${acc.code} • Comissão: ${(Number(acc.commission_rate || 0) * 100).toFixed(1)}% • Status: ${acc.status}`
                : 'Conta de afiliado não ativada.';

            const min = Number(data.settings?.payout_min ?? 0);
            document.getElementById('minInfo').textContent = min > 0 ? `Mínimo de saque: ${fmtMoney(min)}` : '';
        }

        document.getElementById('btnCopy').addEventListener('click', async function () {
            const input = document.getElementById('refLink');
            input.select();
            input.setSelectionRange(0, 99999);
            try {
                await navigator.clipboard.writeText(input.value);
                showAlert('success', 'Link copiado.');
            } catch {
                showAlert('error', 'Não foi possível copiar. Copie manualmente.');
            }
        });

        document.getElementById('btnEnable').addEventListener('click', async function () {
            const form = new FormData();
            form.append('action', 'enable');
            const res = await fetch('/api/affiliate.php', { method: 'POST', body: form, credentials: 'same-origin' });
            const data = await res.json();
            if (!data.success) {
                showAlert('error', data.message || 'Erro ao ativar.');
                return;
            }
            showAlert('success', data.message);
            await loadAffiliate();
        });

        document.getElementById('payoutForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = new FormData(this);
            form.append('action', 'request_payout');
            const res = await fetch('/api/affiliate.php', { method: 'POST', body: form, credentials: 'same-origin' });
            const data = await res.json();
            if (!data.success) {
                showAlert('error', data.message || 'Erro ao solicitar saque.');
                return;
            }
            showAlert('success', data.message);
            this.reset();
            await loadAffiliate();
        });

        loadAffiliate().catch(() => showAlert('error', 'Erro ao carregar afiliados.'));
    </script>
</body>
</html>


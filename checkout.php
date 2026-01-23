<?php
session_start();
$isLogged = !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Finalizar Compra | Thunder Store</title>
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
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
    <script src="/assets/popup.js" defer></script>
</head>
<body class="bg-black text-white min-h-screen">
    <nav class="bg-black/80 backdrop-blur-md border-b border-white/10 fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-20 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <img src="/logo-thunder.png" alt="Thunder Store" class="h-10 w-10 rounded-xl border border-white/10 bg-black/50 object-contain">
                    <div class="leading-tight">
                        <div class="font-black tracking-wide">THUNDER STORE</div>
                        <div class="text-xs text-white/60 font-semibold">Finalizar compra</div>
                    </div>
                </a>
                <div class="flex items-center gap-3">
                    <a href="/carrinho.php" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-bold text-sm">Voltar ao carrinho</a>
                    <?php if ($isLogged): ?>
                        <a href="/painel" class="px-4 py-2 rounded-xl bg-ff-red hover:bg-red-700 font-black text-sm">Painel</a>
                    <?php else: ?>
                        <a href="/login.php" class="px-4 py-2 rounded-xl bg-ff-red hover:bg-red-700 font-black text-sm">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-28 pb-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-3xl md:text-5xl font-black uppercase tracking-wider">Finalizar Compra</h1>
                <p class="text-white/60 mt-3 font-semibold">Produto digital: receba key e download no Gmail e Discord.</p>
            </div>

            <?php if (!$isLogged): ?>
                <div class="max-w-2xl mx-auto rounded-2xl border border-white/10 bg-white/5 p-8">
                    <div class="text-xl font-black">Faça login para continuar</div>
                    <div class="text-white/60 mt-2 font-semibold">Para segurança e acompanhamento do pedido, é necessário estar logado.</div>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="/login.php" class="flex-1 text-center px-6 py-3 rounded-xl bg-ff-red hover:bg-red-700 font-black">Ir para Login</a>
                        <a href="/cadastro.php" class="flex-1 text-center px-6 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-black">Criar conta</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <section class="lg:col-span-7 rounded-2xl border border-white/10 bg-white/5 p-6">
                        <h2 class="text-xl font-black">Dados do Cliente</h2>
                        <form id="checkout-form" class="mt-6 space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-white/70 mb-2">Nome completo</label>
                                <input name="customer_name" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="Seu nome completo">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-white/70 mb-2">Gmail (Entrega)</label>
                                    <input name="customer_email" type="email" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="seugmail@gmail.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-white/70 mb-2">Telefone</label>
                                    <input name="customer_phone" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="(11) 99999-9999">
                                </div>
                            </div>

                            <h3 class="text-xl font-black pt-4">Entrega Digital</h3>
                            <div class="rounded-2xl border border-white/10 bg-black/30 p-4">
                                <div class="text-white/70 font-semibold">Key + download do painel serão enviados para o Gmail e Discord informados.</div>
                                <div class="text-white/40 text-sm font-semibold mt-1">Confira se está correto para evitar atraso na entrega.</div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-white/70 mb-2">Discord</label>
                                <input name="delivery_discord" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="usuario#0000 ou @usuario">
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-black/30 p-4">
                                <div class="text-xs text-white/50 font-black tracking-wide uppercase">Aviso de Segurança (PIX)</div>
                                <div class="mt-2 text-white/70 font-semibold leading-relaxed">
                                    Se o seu banco exibir um alerta de segurança/possível golpe, <span class="text-white">confira os dados do recebedor</span> antes de confirmar.
                                    Se estiver em dúvida, <span class="text-white">não transfira</span> e abra um ticket no nosso Discord para suporte.
                                </div>
                                <div class="mt-3">
                                    <a href="https://discord.gg/hpjCtT7CU7" target="_blank" rel="noopener" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-ff-red hover:bg-red-700 font-black text-sm">
                                        Abrir ticket no Discord
                                    </a>
                                </div>
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <button type="button"
                                            data-tp-image="/40f2de66-65cc-46ec-a72a-30f0ddb450f5.jpg"
                                            data-tp-image-title="Exemplo 1"
                                            data-tp-image-alt="Alerta de golpe no app do banco"
                                            data-tp-image-caption="Se exibido no app, confirme os dados e clique na opção indicada."
                                            class="block text-left rounded-2xl border border-white/10 bg-white/5 overflow-hidden hover:bg-white/10 transition">
                                        <img src="/40f2de66-65cc-46ec-a72a-30f0ddb450f5.jpg" alt="Exemplo de alerta de golpe no app do banco" class="w-full h-44 object-cover" loading="lazy">
                                        <div class="px-3 py-2 text-xs text-white/60 font-semibold">Exemplo 1</div>
                                    </button>
                                    <button type="button"
                                            data-tp-image="/25f05c2a-fae8-4c58-83d2-d713b43aa273.jpg"
                                            data-tp-image-title="Exemplo 2"
                                            data-tp-image-alt="Alerta de segurança ao pagar PIX"
                                            data-tp-image-caption="Se aparecer este aviso, clique na opção para pagar mesmo assim."
                                            class="block text-left rounded-2xl border border-white/10 bg-white/5 overflow-hidden hover:bg-white/10 transition">
                                        <img src="/25f05c2a-fae8-4c58-83d2-d713b43aa273.jpg" alt="Exemplo de alerta de segurança ao pagar PIX" class="w-full h-44 object-cover" loading="lazy">
                                        <div class="px-3 py-2 text-xs text-white/60 font-semibold">Exemplo 2</div>
                                    </button>
                                    <button type="button"
                                            data-tp-image="/8fd2d8fb-32f8-4f89-9f67-e939bd07337d.jpg"
                                            data-tp-image-title="Exemplo 3"
                                            data-tp-image-alt="Aviso de segurança antes de transferir"
                                            data-tp-image-caption="Se o banco alertar, conclua apenas se os dados estiverem corretos."
                                            class="block text-left rounded-2xl border border-white/10 bg-white/5 overflow-hidden hover:bg-white/10 transition">
                                        <img src="/8fd2d8fb-32f8-4f89-9f67-e939bd07337d.jpg" alt="Exemplo de aviso de segurança antes de transferir" class="w-full h-44 object-cover" loading="lazy">
                                        <div class="px-3 py-2 text-xs text-white/60 font-semibold">Exemplo 3</div>
                                    </button>
                                </div>
                                <div class="mt-3 text-xs text-white/40 font-semibold">
                                    Dica: clique em uma imagem para abrir em popup.
                                </div>
                            </div>

                            <div id="checkout-alert" class="hidden rounded-2xl border px-4 py-3 text-sm font-semibold"></div>

                            <button id="btn-finish" class="w-full mt-4 px-6 py-4 rounded-2xl bg-green-600 hover:bg-green-700 font-black tracking-wide text-lg shadow-[0_0_22px_rgba(22,163,74,0.35)]">
                                Pagar e concluir
                            </button>
                        </form>
                    </section>

                    <aside class="lg:col-span-5 rounded-2xl border border-white/10 bg-white/5 p-6 h-fit">
                        <h2 class="text-xl font-black">Revisão do Pedido</h2>
                        <div id="order-items" class="mt-5 space-y-3"></div>
                        <div class="mt-6 border-t border-white/10 pt-5 space-y-2">
                            <div class="flex items-center justify-between text-white/70 font-semibold">
                                <span>Subtotal</span>
                                <span id="sum-subtotal">—</span>
                            </div>
                            <div class="flex items-center justify-between text-2xl font-black">
                                <span>Total</span>
                                <span id="sum-total" class="text-red-500">—</span>
                            </div>
                        </div>
                    </aside>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        const fmtMoney = (v) => 'R$ ' + Number(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function showAlert(type, message) {
            const el = document.getElementById('checkout-alert');
            if (!el) return;
            el.classList.remove('hidden');
            el.className = 'rounded-2xl border px-4 py-3 text-sm font-semibold ' + (type === 'success'
                ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                : 'border-red-500/40 bg-red-500/10 text-red-200');
            el.textContent = message;
            if (window.ThunderPopup && typeof window.ThunderPopup.toast === 'function') {
                window.ThunderPopup.toast(type === 'success' ? 'success' : 'error', message);
            }
        }

        function renderOrder(items, subtotal) {
            const list = document.getElementById('order-items');
            if (!list) return;
            list.innerHTML = items.map((it) => `
                <div class="rounded-2xl border border-white/10 bg-black/30 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-black">${it.product_name}</div>
                            <div class="text-white/60 text-sm font-semibold">${it.plan_name} • Qtd ${it.qty}</div>
                        </div>
                        <div class="font-black text-white">${fmtMoney(it.subtotal)}</div>
                    </div>
                </div>
            `).join('');

            document.getElementById('sum-subtotal').textContent = fmtMoney(subtotal);
            document.getElementById('sum-total').textContent = fmtMoney(subtotal);
        }

        async function loadCart() {
            const res = await fetch('/api/cart.php?action=list', { credentials: 'same-origin' });
            return await res.json();
        }

        (async function init() {
            const form = document.getElementById('checkout-form');
            if (!form) return;

            try {
                let currentCart = await loadCart();
                if (!currentCart?.success || !(currentCart.items || []).length) {
                    showAlert('error', 'Seu carrinho está vazio.');
                    document.getElementById('btn-finish').disabled = true;
                } else {
                    renderOrder(currentCart.items, Number(currentCart.total || 0));
                }
            } catch (e) {
                console.error(e);
                showAlert('error', 'Não foi possível carregar o carrinho. Recarregue a página.');
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = document.getElementById('btn-finish');
                const originalText = btn ? btn.textContent : '';
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Processando...';
                }
                if (window.ThunderPopup && typeof window.ThunderPopup.toast === 'function') {
                    window.ThunderPopup.toast('info', 'Aguarde, estamos criando seu pedido...');
                }
                try {
                    const payload = new FormData(form);
                    payload.set('action', 'create_order');

                    const res = await fetch('/api/checkout.php', { method: 'POST', body: payload, credentials: 'same-origin' });
                    const data = await res.json();
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                        return;
                    }
                    if (!data.success) {
                        showAlert('error', data.message || 'Não foi possível finalizar.');
                        return;
                    }
                    if (data.order_id) {
                        window.location.href = '/pedido.php?id=' + encodeURIComponent(data.order_id);
                        return;
                    }
                    showAlert('error', 'Não foi possível finalizar.');
                } catch (err) {
                    console.error(err);
                    showAlert('error', 'Erro ao processar checkout.');
                } finally {
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = originalText || 'Pagar e concluir';
                    }
                }
            });
        })();
    </script>
</body>
</html>

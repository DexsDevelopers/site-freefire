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
    <style>
        html, body { touch-action: pan-x pan-y; }
        body { background-color: #000; color: white; font-family: 'Inter', sans-serif; }
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
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
                <p class="text-white/60 mt-3 font-semibold">Preencha seus dados, escolha o frete e finalize o pagamento.</p>
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
                                    <label class="block text-sm font-bold text-white/70 mb-2">E-mail</label>
                                    <input name="customer_email" type="email" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="voce@email.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-white/70 mb-2">Telefone</label>
                                    <input name="customer_phone" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="(11) 99999-9999">
                                </div>
                            </div>

                            <h3 class="text-xl font-black pt-4">Entrega</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-white/70 mb-2">CEP</label>
                                    <input name="shipping_zip" inputmode="numeric" pattern="[0-9]{8}" maxlength="9" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="00000-000">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-bold text-white/70 mb-2">Endereço</label>
                                    <input name="shipping_address" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="Rua, número, bairro, cidade/UF">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-white/70 mb-2">Número</label>
                                    <input name="shipping_number" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="123">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-white/70 mb-2">Complemento</label>
                                    <input name="shipping_complement" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="Apto, bloco... (opcional)">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-white/70 mb-2">Referência</label>
                                    <input name="shipping_reference" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 focus:outline-none focus:ring-2 focus:ring-ff-red/40 font-semibold" placeholder="Perto de... (opcional)">
                                </div>
                            </div>

                            <h3 class="text-xl font-black pt-4">Frete</h3>
                            <div id="shipping-box" class="rounded-2xl border border-white/10 bg-black/30 p-4">
                                <div class="text-white/60 font-semibold">Informe o CEP para calcular o frete.</div>
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
                            <div class="flex items-center justify-between text-white/70 font-semibold">
                                <span>Frete</span>
                                <span id="sum-shipping">—</span>
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
        const onlyDigits = (s) => String(s || '').replace(/\D+/g, '');

        function showAlert(type, message) {
            const el = document.getElementById('checkout-alert');
            if (!el) return;
            el.classList.remove('hidden');
            el.className = 'rounded-2xl border px-4 py-3 text-sm font-semibold ' + (type === 'success'
                ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                : 'border-red-500/40 bg-red-500/10 text-red-200');
            el.textContent = message;
        }

        function renderOrder(items, subtotal, shippingPrice) {
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
            document.getElementById('sum-shipping').textContent = fmtMoney(shippingPrice);
            document.getElementById('sum-total').textContent = fmtMoney(subtotal + shippingPrice);
        }

        async function loadCart() {
            const res = await fetch('/api/cart.php?action=list', { credentials: 'same-origin' });
            return await res.json();
        }

        async function loadShipping(cepDigits) {
            const res = await fetch('/api/shipping.php?cep=' + encodeURIComponent(cepDigits), { credentials: 'same-origin' });
            return await res.json();
        }

        function renderShipping(options) {
            const box = document.getElementById('shipping-box');
            if (!box) return;
            if (!options?.length) {
                box.innerHTML = `<div class="text-white/60 font-semibold">Nenhuma opção de frete disponível.</div>`;
                return;
            }
            box.innerHTML = `
                <div class="space-y-3">
                    ${options.map((o, idx) => `
                        <label class="flex items-center justify-between gap-4 p-4 rounded-xl border border-white/10 bg-white/5 cursor-pointer hover:bg-white/10">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_method" value="${o.id}" ${idx === 0 ? 'checked' : ''} class="h-5 w-5 accent-red-600">
                                <div>
                                    <div class="font-black">${o.label}</div>
                                    <div class="text-white/60 text-sm font-semibold">${o.eta}</div>
                                </div>
                            </div>
                            <div class="font-black text-white">${fmtMoney(o.price)}</div>
                        </label>
                    `).join('')}
                </div>
            `;
        }

        (async function init() {
            const form = document.getElementById('checkout-form');
            if (!form) return;

            let currentCart = await loadCart();
            if (!currentCart?.success || !(currentCart.items || []).length) {
                showAlert('error', 'Seu carrinho está vazio.');
                return;
            }

            let shippingOptions = [];
            let selectedShippingPrice = 0;
            renderShipping(null);
            renderOrder(currentCart.items, Number(currentCart.total || 0), 0);

            const cepInput = form.querySelector('input[name="shipping_zip"]');
            const shippingBox = document.getElementById('shipping-box');

            async function recalcShipping() {
                const cepDigits = onlyDigits(cepInput?.value || '');
                if (cepDigits.length !== 8) return;
                const data = await loadShipping(cepDigits);
                if (!data.success) {
                    showAlert('error', data.message || 'Não foi possível calcular frete.');
                    return;
                }
                shippingOptions = data.options || [];
                renderShipping(shippingOptions);
                selectedShippingPrice = Number(shippingOptions[0]?.price || 0);
                renderOrder(currentCart.items, Number(currentCart.total || 0), selectedShippingPrice);
            }

            if (cepInput) {
                cepInput.addEventListener('input', () => {
                    const digits = onlyDigits(cepInput.value);
                    if (digits.length === 8) recalcShipping();
                });
            }

            if (shippingBox) {
                shippingBox.addEventListener('change', (e) => {
                    const t = e.target;
                    if (t && t.name === 'shipping_method') {
                        const opt = shippingOptions.find(o => o.id === t.value);
                        selectedShippingPrice = Number(opt?.price || 0);
                        renderOrder(currentCart.items, Number(currentCart.total || 0), selectedShippingPrice);
                    }
                });
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                document.getElementById('btn-finish').disabled = true;
                try {
                    const cepDigits = onlyDigits(cepInput?.value || '');
                    if (cepDigits.length !== 8) {
                        showAlert('error', 'Informe um CEP válido.');
                        return;
                    }
                    const selectedMethod = (form.querySelector('input[name="shipping_method"]:checked') || {}).value || '';
                    if (!selectedMethod) {
                        showAlert('error', 'Selecione uma opção de frete.');
                        return;
                    }

                    const payload = new FormData(form);
                    payload.set('action', 'create_order');
                    payload.set('shipping_zip', cepDigits);
                    payload.set('shipping_method', selectedMethod);

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
                } catch (err) {
                    console.error(err);
                    showAlert('error', 'Erro ao processar checkout.');
                } finally {
                    document.getElementById('btn-finish').disabled = false;
                }
            });
        })();
    </script>
</body>
</html>

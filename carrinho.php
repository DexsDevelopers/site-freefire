<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Carrinho | Thunder Store</title>
    <!-- <link rel="stylesheet" href="/assets/index-R2RkWoEQ.css"> -->
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
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
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
<body class="bg-black text-white min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-black/80 backdrop-blur-md border-b border-white/10 fixed w-full z-50 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 pointer-events-none opacity-20"
             style="background-image: url('data:image/svg+xml,%3Csvg width=%2724%27 height=%2724%27 viewBox=%270 0 24 24%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cpath d=%27M11 11H9v2h2v2h2v-2h2v-2h-2V9h-2v2z%27 fill=%27%23ffffff%27 fill-rule=%27evenodd%27/%3E%3C/svg%3E'); background-size: 42px 42px;">
        </div>
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-b from-white/5 via-transparent to-transparent"></div>
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between h-20">
                <!-- Left: Icons + Logo -->
                <div class="flex items-center gap-6">
                    <!-- Icons -->
                    <div class="flex items-center gap-4 text-gray-400">
                        <a href="https://discord.gg/seuservidor" target="_blank" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037 14.12 14.12 0 0 0-.624 1.282 18.336 18.336 0 0 0-5.46 0 14.137 14.137 0 0 0-.623-1.282.074.074 0 0 0-.08-.037 19.782 19.782 0 0 0-4.885 1.515.066.066 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.086 2.157 2.419 0 1.334-.956 2.42-2.157 2.42zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.086 2.157 2.419 0 1.334-.946 2.42-2.157 2.42z"/></svg>
                        </a>
                        <a href="/carrinho.php" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10 relative text-white bg-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </a>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="/painel" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10" title="Perfil">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </a>
                        <?php else: ?>
                            <a href="/login.php" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10" title="Login">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                    <!-- Divider -->
                    <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
                    <!-- Logo -->
                    <a href="/" class="flex-shrink-0">
                         <img src="/logo-thunder.png" alt="Thunder Store" class="h-10 w-auto object-contain">
                    </a>
                </div>

                <!-- Center: Links -->
                <div class="hidden xl:flex items-center gap-6">
                    <a href="/" class="relative text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">INÍCIO</a>
                    <a href="/roleta.php" class="relative text-yellow-400 font-bold text-xs tracking-wider flex items-center gap-2 hover:text-yellow-300 transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-yellow-400 after:transition-all hover:after:w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                        ROLETA
                    </a>
                    <a href="/status.php" class="relative text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">STATUS</a>
                    <a href="/termos.php" class="relative text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">TERMOS</a>
                    <a href="/demo.php" class="relative text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">DEMONSTRAÇÃO</a>
                    <a href="/faq.php" class="relative text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">FAQ</a>
                    <a href="/mystery-box.php" class="relative text-purple-500 font-bold text-xs tracking-wider flex items-center gap-2 hover:text-purple-400 transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-purple-500 after:transition-all hover:after:w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        MYSTERY BOX
                    </a>
                    <span class="ml-2 text-[11px] text-gray-500 tracking-wide hidden 2xl:block">Free Fire Counter</span>
                </div>

                <!-- Right: Loja Button -->
                <div class="flex items-center">
                     <a href="/#produtos" class="hidden xl:flex bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 text-white font-black italic px-6 py-2 rounded-full items-center gap-2 shadow-[0_0_25px_rgba(255,165,0,0.35)] transform skew-x-[-10deg] hover:skew-x-[-10deg] hover:scale-105 transition-all border border-white/10 hover:border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform skew-x-[10deg]" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                        <span class="transform skew-x-[10deg] text-sm">LOJA</span>
                     </a>
                     
                     <!-- Mobile Menu Button -->
                     <button id="nav-mobile-toggle" class="ml-4 xl:hidden text-white bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-full p-2 transition-all" aria-expanded="false" aria-controls="nav-mobile">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                     </button>
                </div>
            </div>
            <div id="nav-mobile" class="xl:hidden hidden pb-4">
                <div class="mt-2 rounded-2xl border border-white/10 bg-black/60 backdrop-blur-md overflow-hidden">
                    <div class="grid grid-cols-2 gap-2 p-3">
                        <a href="/" class="px-4 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-white font-bold text-xs tracking-wider uppercase transition-colors">Início</a>
                        <a href="/#produtos" class="px-4 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-white font-bold text-xs tracking-wider uppercase transition-colors">Loja</a>
                        <a href="/roleta.php" class="px-4 py-3 rounded-xl bg-yellow-400/10 hover:bg-yellow-400/15 text-yellow-300 font-bold text-xs tracking-wider uppercase transition-colors flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                            Roleta
                        </a>
                        <a href="/mystery-box.php" class="px-4 py-3 rounded-xl bg-purple-500/10 hover:bg-purple-500/15 text-purple-300 font-bold text-xs tracking-wider uppercase transition-colors flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            Mystery Box
                        </a>
                        <a href="/status.php" class="px-4 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-bold text-xs tracking-wider uppercase transition-colors">Status</a>
                        <a href="/termos.php" class="px-4 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-bold text-xs tracking-wider uppercase transition-colors">Termos</a>
                        <a href="/demo.php" class="px-4 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-bold text-xs tracking-wider uppercase transition-colors">Demonstração</a>
                        <a href="/faq.php" class="px-4 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-bold text-xs tracking-wider uppercase transition-colors">FAQ</a>
                    </div>
                    <div class="px-3 pb-3">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="/painel" class="block px-4 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs tracking-wider uppercase transition-colors text-center">Acessar Painel</a>
                        <?php else: ?>
                            <a href="/login.php" class="block px-4 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs tracking-wider uppercase transition-colors text-center">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex-grow pt-24 px-4 max-w-7xl mx-auto w-full">
        <h1 class="text-4xl font-bold mb-8 text-center"><span class="text-red-600">SEU</span> CARRINHO</h1>
        <div id="cart-root"></div>
    </div>

    <script>
        const fmtMoney = (v) => 'R$ ' + Number(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function escapeHtml(value) {
            const s = String(value ?? '');
            return s
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function itemTemplate(item) {
            const img = item.product_image_url ? `<img src="${escapeHtml(item.product_image_url)}" alt="" class="h-16 w-16 rounded-lg object-cover border border-white/10 bg-white/5">` : `<div class="h-16 w-16 rounded-lg border border-white/10 bg-white/5"></div>`;
            return `
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-white/10 py-5 last:border-0">
                    <div class="flex items-center gap-4">
                        ${img}
                        <div>
                            <div class="text-lg font-extrabold text-white">${escapeHtml(item.product_name)}</div>
                            <div class="text-sm text-white/60 font-semibold">${escapeHtml(item.plan_name)}</div>
                            <div class="text-xs text-white/40 mt-1">Unitário: ${fmtMoney(item.unit_price)}</div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                        <div class="inline-flex items-center rounded-full border border-white/10 bg-white/5">
                            <button class="px-4 py-2 text-white/70 hover:text-white" data-action="dec" data-plan-id="${item.plan_id}" aria-label="Diminuir">−</button>
                            <div class="min-w-12 text-center font-black">${item.qty}</div>
                            <button class="px-4 py-2 text-white/70 hover:text-white" data-action="inc" data-plan-id="${item.plan_id}" aria-label="Aumentar">+</button>
                        </div>

                        <div class="text-right">
                            <div class="text-xs text-white/50 font-bold tracking-wide uppercase">Subtotal</div>
                            <div class="text-2xl font-black text-red-500">${fmtMoney(item.subtotal)}</div>
                        </div>

                        <button class="text-white/40 hover:text-red-400 transition-colors" data-action="remove" data-plan-id="${item.plan_id}" aria-label="Remover">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        }

        function renderCart(data) {
            const root = document.getElementById('cart-root');
            if (!root) return;

            const items = data?.items || [];
            if (!items.length) {
                root.innerHTML = `
                    <div class="text-center py-20">
                        <p class="text-xl text-gray-400 mb-6">Seu carrinho está vazio.</p>
                        <a href="/" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-[0_0_20px_rgba(220,38,38,0.4)]">
                            VER PRODUTOS
                        </a>
                    </div>
                `;
                return;
            }

            root.innerHTML = `
                <div class="bg-zinc-900/50 border border-red-900/20 rounded-xl p-6 mb-8">
                    ${items.map(itemTemplate).join('')}

                    <div class="mt-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 pt-6 border-t border-red-900/30">
                        <div class="text-3xl font-bold">
                            Total: <span class="text-red-500">${fmtMoney(data.total)}</span>
                            <div class="text-sm text-white/50 font-semibold mt-2">${data.total_qty} item(ns)</div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                            <a href="/" class="w-full sm:w-auto text-center bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold py-3 px-6 rounded-full transition-all">
                                CONTINUAR COMPRANDO
                            </a>
                            <a href="/checkout" class="w-full sm:w-auto text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-[0_0_20px_rgba(22,163,74,0.4)]">
                                FINALIZAR COMPRA
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        async function fetchCart() {
            const res = await fetch('/api/cart.php?action=list', { credentials: 'same-origin' });
            return await res.json();
        }

        async function updateQty(planId, delta) {
            const formData = new FormData();
            formData.append('action', 'update_qty');
            formData.append('plan_id', String(planId));
            formData.append('delta', String(delta));
            const res = await fetch('/api/cart.php', { method: 'POST', body: formData, credentials: 'same-origin' });
            return await res.json();
        }

        async function removePlan(planId) {
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('plan_id', String(planId));
            const res = await fetch('/api/cart.php', { method: 'POST', body: formData, credentials: 'same-origin' });
            return await res.json();
        }

        (async function initCart() {
            try {
                const data = await fetchCart();
                renderCart(data);
            } catch (e) {
                console.error(e);
            }
        })();

        document.addEventListener('click', async (event) => {
            const btn = event.target.closest('[data-action][data-plan-id]');
            if (!btn) return;

            const action = btn.getAttribute('data-action');
            const planId = Number(btn.getAttribute('data-plan-id'));
            if (!planId) return;

            btn.disabled = true;
            try {
                let data;
                if (action === 'inc') data = await updateQty(planId, +1);
                else if (action === 'dec') data = await updateQty(planId, -1);
                else if (action === 'remove') {
                    if (window.ThunderPopup && typeof window.ThunderPopup.confirm === 'function') {
                        const ok = await window.ThunderPopup.confirm({
                            title: 'Remover item',
                            message: 'Remover este item do carrinho?',
                            confirmText: 'Remover',
                            cancelText: 'Cancelar',
                            danger: true
                        });
                        if (!ok) return;
                    } else {
                        if (!confirm('Remover este item?')) return;
                    }
                    data = await removePlan(planId);
                }
                if (data?.success) {
                    renderCart(data);
                    if (action === 'remove' && window.ThunderPopup && typeof window.ThunderPopup.toast === 'function') {
                        window.ThunderPopup.toast('success', 'Item removido do carrinho.');
                    }
                } else if (data && !data.success && window.ThunderPopup && typeof window.ThunderPopup.toast === 'function') {
                    window.ThunderPopup.toast('error', data.message || 'Não foi possível atualizar o carrinho.');
                }
            } catch (e) {
                console.error(e);
                if (window.ThunderPopup && typeof window.ThunderPopup.toast === 'function') {
                    window.ThunderPopup.toast('error', 'Erro ao atualizar o carrinho.');
                }
            } finally {
                btn.disabled = false;
            }
        });

        (function () {
            const toggle = document.getElementById('nav-mobile-toggle');
            const menu = document.getElementById('nav-mobile');
            if (!toggle || !menu) return;

            toggle.addEventListener('click', function () {
                const isOpen = !menu.classList.contains('hidden');
                if (isOpen) {
                    menu.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    menu.classList.remove('hidden');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });

            menu.addEventListener('click', function (e) {
                const target = e.target;
                if (target && target.tagName === 'A') {
                    menu.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        })();
    </script>
</body>
</html>

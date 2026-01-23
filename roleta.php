<?php
session_start();
$today = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Roleta | Thunder Store</title>
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
    <style>
        html { scroll-behavior: smooth; }
        html, body { touch-action: pan-x pan-y; }
        body { background-color: #000; color: white; font-family: 'Inter', sans-serif; }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
</head>
<body class="bg-black text-white min-h-screen">
    <nav class="bg-black/80 backdrop-blur-md border-b border-white/10 fixed w-full z-50 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 pointer-events-none opacity-20"
             style="background-image: url('data:image/svg+xml,%3Csvg width=%2724%27 height=%2724%27 viewBox=%270 0 24 24%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cpath d=%27M11 11H9v2h2v2h2v-2h2v-2h-2V9h-2v2z%27 fill=%27%23ffffff%27 fill-rule=%27evenodd%27/%3E%3C/svg%3E'); background-size: 42px 42px;">
        </div>
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-b from-white/5 via-transparent to-transparent"></div>
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-4 text-gray-400">
                        <a href="https://discord.gg/hpjCtT7CU7" target="_blank" rel="noopener" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037 14.12 14.12 0 0 0-.624 1.282 18.336 18.336 0 0 0-5.46 0 14.137 14.137 0 0 0-.623-1.282.074.074 0 0 0-.08-.037 19.782 19.782 0 0 0-4.885 1.515.066.066 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.086 2.157 2.419 0 1.334-.956 2.42-2.157 2.42zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.086 2.157 2.419 0 1.334-.946 2.42-2.157 2.42z"/></svg>
                        </a>
                        <a href="/carrinho.php" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10 relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </a>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="/painel" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10" title="Perfil">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 0 0-7-7z" /></svg>
                            </a>
                        <?php else: ?>
                            <a href="/login.php" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10" title="Login">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 0 0-7-7z" /></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
                    <a href="/" class="flex-shrink-0">
                        <img src="/logo-thunder.png" alt="Thunder Store" class="h-10 w-auto object-contain">
                    </a>
                </div>

                <div class="hidden xl:flex items-center gap-6">
                    <a href="/" class="relative text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">INÍCIO</a>
                    <a href="/roleta.php" class="relative text-yellow-400 font-bold text-xs tracking-wider flex items-center gap-2 hover:text-yellow-300 transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-full after:bg-yellow-400 after:transition-all">
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

                <div class="flex items-center">
                    <a href="/#produtos" class="hidden xl:flex bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 text-white font-black italic px-6 py-2 rounded-full items-center gap-2 shadow-[0_0_25px_rgba(255,165,0,0.35)] transform skew-x-[-10deg] hover:skew-x-[-10deg] hover:scale-105 transition-all border border-white/10 hover:border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform skew-x-[10deg]" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                        <span class="transform skew-x-[10deg] text-sm">LOJA</span>
                    </a>
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

    <main class="pt-28 pb-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center text-center">
                <div class="inline-flex items-center gap-2 bg-yellow-400/10 border border-yellow-400/20 px-4 py-2 rounded-full mb-6">
                    <span class="text-yellow-300 font-bold text-xs tracking-widest uppercase">Roleta Diária</span>
                    <span class="text-gray-400 text-xs tracking-wide">Hoje: <?php echo htmlspecialchars($today); ?></span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-3">Gire e Ganhe Prêmios</h1>
                <p class="text-gray-400 max-w-2xl">A roleta é uma demonstração visual. Você pode girar 1 vez por dia (por navegador).</p>
                <div class="mt-6 w-full max-w-3xl rounded-2xl border border-yellow-400/20 bg-yellow-400/10 px-5 py-4 text-left">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="px-3 py-1 rounded-full bg-yellow-400/15 border border-yellow-400/25 text-yellow-300 text-xs font-black tracking-widest uppercase">Em Breve</div>
                            <div class="text-white font-bold">Roleta com prêmios reais e histórico</div>
                        </div>
                        <div class="text-xs text-gray-400 font-bold tracking-wider uppercase hidden sm:block">Atualização</div>
                    </div>
                    <div class="mt-2 text-sm text-gray-300 leading-relaxed">
                        Estamos finalizando a versão oficial da Roleta Diária. Por enquanto, esta página é apenas uma prévia visual.
                    </div>
                </div>
            </div>

            <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <div class="relative flex items-center justify-center">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
                        <div class="w-0 h-0 border-l-[14px] border-l-transparent border-r-[14px] border-r-transparent border-t-[22px] border-t-red-600 drop-shadow-[0_0_10px_rgba(220,38,38,0.5)]"></div>
                    </div>
                    <div class="relative w-[320px] h-[320px] sm:w-[380px] sm:h-[380px] rounded-full border border-white/10 bg-white/5 shadow-[0_0_50px_rgba(255,0,0,0.08)] overflow-hidden">
                        <div id="wheel" class="absolute inset-0 origin-center transition-transform duration-[3500ms] ease-out"></div>
                        <div class="absolute inset-6 rounded-full bg-black/60 border border-white/10"></div>
                    </div>
                </div>

                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-black tracking-wider uppercase">Painel</h2>
                        <div id="cooldown" class="text-xs font-bold text-gray-400"></div>
                    </div>
                    <div class="mt-6 space-y-4">
                        <button id="spin" class="w-full bg-red-600 hover:bg-red-700 disabled:bg-white/10 disabled:text-gray-500 text-white font-black py-4 rounded-xl transition-colors tracking-wider uppercase">
                            Girar Agora
                        </button>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-black/50 border border-white/10 rounded-xl p-4">
                                <div class="text-xs text-gray-400 font-bold tracking-widest uppercase">Último Prêmio</div>
                                <div id="lastPrize" class="mt-2 text-white font-black">—</div>
                            </div>
                            <div class="bg-black/50 border border-white/10 rounded-xl p-4">
                                <div class="text-xs text-gray-400 font-bold tracking-widest uppercase">Chances</div>
                                <div class="mt-2 text-white font-black">1 / dia</div>
                            </div>
                        </div>
                        <div class="bg-black/50 border border-white/10 rounded-xl p-4">
                            <div class="text-xs text-gray-400 font-bold tracking-widest uppercase mb-2">Prêmios Possíveis</div>
                            <div id="prizeList" class="flex flex-wrap gap-2"></div>
                        </div>
                        <a href="/#produtos" class="block text-center bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white font-bold py-3 rounded-xl transition-colors uppercase tracking-wider text-sm">
                            Ir para a Loja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        (function () {
            const toggle = document.getElementById('nav-mobile-toggle');
            const menu = document.getElementById('nav-mobile');
            if (toggle && menu) {
                toggle.addEventListener('click', function () {
                    const isOpen = !menu.classList.contains('hidden');
                    menu.classList.toggle('hidden', isOpen);
                    toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                });
                menu.addEventListener('click', function (e) {
                    const target = e.target;
                    if (target && target.tagName === 'A') {
                        menu.classList.add('hidden');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            const prizes = [
                { label: '💎 50 Diamantes', color: '#dc2626' },
                { label: '💰 5.000 Moedas', color: '#f59e0b' },
                { label: '🎁 Mystery Item', color: '#8b5cf6' },
                { label: '⚡ 10% OFF', color: '#22c55e' },
                { label: '🎯 Skin Random', color: '#0ea5e9' },
                { label: '🔥 1 Dia VIP', color: '#ef4444' },
                { label: '🛡️ Boost', color: '#a3a3a3' },
                { label: '💎 100 Diamantes', color: '#dc2626' },
            ];

            const wheel = document.getElementById('wheel');
            const spinBtn = document.getElementById('spin');
            const lastPrize = document.getElementById('lastPrize');
            const prizeList = document.getElementById('prizeList');
            const cooldown = document.getElementById('cooldown');

            if (prizeList) {
                prizes.forEach(p => {
                    const el = document.createElement('span');
                    el.className = 'px-3 py-1 rounded-full text-xs font-bold bg-white/5 border border-white/10 text-gray-200';
                    el.textContent = p.label;
                    prizeList.appendChild(el);
                });
            }

            function buildWheel() {
                const slices = prizes.length;
                const sliceDeg = 360 / slices;
                const parts = prizes.map((p, i) => {
                    const start = i * sliceDeg;
                    const end = (i + 1) * sliceDeg;
                    return `${p.color} ${start}deg ${end}deg`;
                }).join(', ');
                wheel.style.background = `conic-gradient(${parts})`;
                wheel.style.borderRadius = '9999px';
                wheel.style.filter = 'saturate(1.05) contrast(1.05)';

                const labels = document.createElement('div');
                labels.className = 'absolute inset-0';
                prizes.forEach((p, i) => {
                    const label = document.createElement('div');
                    label.className = 'absolute left-1/2 top-1/2 text-[11px] sm:text-xs font-black text-white drop-shadow-[0_0_10px_rgba(0,0,0,0.9)] whitespace-nowrap';
                    const angle = (i * sliceDeg) + sliceDeg / 2;
                    label.style.transform = `translate(-50%, -50%) rotate(${angle}deg) translate(0, -150px) rotate(90deg)`;
                    label.textContent = p.label.replace(/ .*/, '');
                    labels.appendChild(label);
                });
                wheel.appendChild(labels);
            }

            function getLocalKey() {
                return `thunder_roleta_last_spin_<?php echo htmlspecialchars($today); ?>`;
            }

            function isLocked() {
                return localStorage.getItem(getLocalKey()) === '1';
            }

            function updateCooldown() {
                if (!cooldown || !spinBtn) return;
                if (!isLocked()) {
                    cooldown.textContent = 'Disponível para girar';
                    spinBtn.disabled = false;
                    return;
                }
                cooldown.textContent = 'Volte amanhã para girar';
                spinBtn.disabled = true;
            }

            let currentRotation = 0;
            let spinning = false;

            function spin() {
                if (spinning || isLocked()) return;
                spinning = true;
                spinBtn.disabled = true;

                const slices = prizes.length;
                const sliceDeg = 360 / slices;
                const chosenIndex = Math.floor(Math.random() * slices);
                const randomOffset = Math.random() * (sliceDeg * 0.75) - (sliceDeg * 0.375);
                const target = (360 * 6) + (chosenIndex * sliceDeg) + (sliceDeg / 2) + randomOffset;
                currentRotation = currentRotation + target;
                wheel.style.transform = `rotate(${currentRotation}deg)`;

                setTimeout(() => {
                    const prize = prizes[chosenIndex];
                    lastPrize.textContent = prize.label;
                    localStorage.setItem(getLocalKey(), '1');
                    spinning = false;
                    updateCooldown();
                }, 3600);
            }

            if (spinBtn) spinBtn.addEventListener('click', spin);
            buildWheel();
            updateCooldown();
        })();
    </script>
</body>
</html>

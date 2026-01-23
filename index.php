<?php
session_start();

$dbOk = true;
try {
    require_once 'api/db.php';
} catch (Throwable $e) {
    $dbOk = false;
}

$products = [];

if ($dbOk && isset($conn)) {
    $sql = "
        SELECT
            p.*,
            COALESCE(MIN(pl.price), 0) AS min_price,
            COUNT(pl.id) AS plan_count
        FROM products p
        LEFT JOIN plans pl ON pl.product_id = p.id
        WHERE p.status = 'Ativo'
        GROUP BY p.id
        ORDER BY (p.slug = 'freefire') DESC, p.id DESC
    ";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
}

if (!$dbOk || count($products) === 0) {
    $products = [
        [
            'id' => 1,
            'slug' => 'freefire',
            'name' => 'FREE FIRE',
            'description' => 'Funções premium, suporte e atualizações constantes.',
            'image_url' => '/img/freefire.jpg',
            'status' => 'Ativo',
            'features' => 'Chams|Aimbot|No Recoil|AimFov|CameraHack',
            'min_price' => 15.00,
            'plan_count' => 4,
        ],
        [
            'id' => 2,
            'slug' => 'valorant',
            'name' => 'VALORANT',
            'description' => 'Monitor premium, performance e estabilidade.',
            'image_url' => '/img/valorantwall.jpg',
            'status' => 'Ativo',
            'features' => 'HGV ON/OFF|Indetectável|No Lag',
            'min_price' => 20.00,
            'plan_count' => 4,
        ],
        [
            'id' => 3,
            'slug' => 'cs2',
            'name' => 'COUNTER STRIKE 2',
            'description' => 'Recursos avançados e atualizações rápidas.',
            'image_url' => '/img/counterstrike2.png',
            'status' => 'Ativo',
            'features' => 'Wallhack|Aimbot|Triggerbot|Radar',
            'min_price' => 15.00,
            'plan_count' => 3,
        ],
    ];
}
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover" />
    <title>Thunder Store | Produtos</title>
    <meta name="description" content="Produtos para vários jogos: entrega rápida, segurança e suporte." />
    <!-- <link rel="stylesheet" crossorigin href="/assets/index-R2RkWoEQ.css"> -->
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
        .reveal { opacity: 0; transform: translateY(18px); filter: blur(6px); transition: opacity 700ms cubic-bezier(.2,.8,.2,1), transform 700ms cubic-bezier(.2,.8,.2,1), filter 700ms cubic-bezier(.2,.8,.2,1); }
        .reveal.is-in { opacity: 1; transform: translateY(0); filter: blur(0); }
        .shine {
            background: radial-gradient(900px 280px at var(--mx, 50%) var(--my, 50%), rgba(220,38,38,0.18), transparent 55%),
                        radial-gradient(900px 280px at calc(var(--mx, 50%) + 120px) calc(var(--my, 50%) + 80px), rgba(255,255,255,0.08), transparent 60%);
        }
        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1; transform: none; filter: none; transition: none; }
        }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
  </head>
  <body class="bg-black text-white min-h-screen">
    
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
                        <a href="https://discord.gg/hpjCtT7CU7" target="_blank" rel="noopener" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037 14.12 14.12 0 0 0-.624 1.282 18.336 18.336 0 0 0-5.46 0 14.137 14.137 0 0 0-.623-1.282.074.074 0 0 0-.08-.037 19.782 19.782 0 0 0-4.885 1.515.066.066 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.086 2.157 2.419 0 1.334-.956 2.42-2.157 2.42zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.086 2.157 2.419 0 1.334-.946 2.42-2.157 2.42z"/></svg>
                        </a>
                        <a href="/carrinho.php" class="hover:text-white transition-colors bg-white/5 p-2 rounded-full hover:bg-white/10 relative">
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
                    <a href="/" class="relative text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-full after:bg-white after:transition-all">INÍCIO</a>
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
                     <a href="#produtos" class="hidden xl:flex bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 text-white font-black italic px-6 py-2 rounded-full items-center gap-2 shadow-[0_0_25px_rgba(255,165,0,0.35)] transform skew-x-[-10deg] hover:skew-x-[-10deg] hover:scale-105 transition-all border border-white/10 hover:border-white/20">
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
                        <a href="#produtos" class="px-4 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-white font-bold text-xs tracking-wider uppercase transition-colors">Loja</a>
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

    <!-- Hero Section -->
    <div class="relative min-h-screen bg-black flex items-center overflow-hidden">
        <!-- Background Effect -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-gray-900/40 via-black to-black opacity-80"></div>
            <!-- Cross Pattern Background -->
            <div class="absolute top-0 left-0 w-full h-full opacity-20" 
                 style="background-image: url('data:image/svg+xml,%3Csvg width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M11 11H9v2h2v2h2v-2h2v-2h-2V9h-2v2z\' fill=\'%23ffffff\' fill-rule=\'evenodd\'/%3E%3C/svg%3E'); background-size: 40px 40px;">
            </div>
            <!-- White/Gray glow spots for Thunder theme -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/5 rounded-full blur-[128px]"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-red-600/5 rounded-full blur-[128px]"></div>
        </div>

        <div class="relative z-10 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col items-center text-center justify-center min-h-screen pt-20 pb-10">
            <!-- Content -->
            <div class="space-y-8 flex flex-col items-center max-w-4xl mx-auto">
                <!-- Image as Title -->
                <div class="relative flex justify-center w-full">
                    <img 
                        src="/logo-thunder.png" 
                        alt="THUNDER STORE" 
                        class="w-full max-w-3xl drop-shadow-[0_0_30px_rgba(255,255,255,0.1)] transform hover:scale-105 transition-transform duration-500"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                    />
                    <!-- Fallback Text if image not found -->
                    <h1 class="text-6xl md:text-8xl font-black tracking-tighter leading-none hidden">
                        <span class="block text-white">THUNDER</span>
                        <span class="block text-gray-400 drop-shadow-[0_0_15px_rgba(255,255,255,0.3)]">STORE</span>
                    </h1>
                </div>
                
                <p class="text-gray-400 text-lg md:text-2xl max-w-2xl font-medium leading-relaxed">
                    A melhor loja de Free Fire do cenário.
                    <br />
                    Produtos exclusivos, entrega rápida e segurança total.
                </p>

                <div class="flex flex-col sm:flex-row gap-6 justify-center w-full">
                    <a href="#produtos" class="bg-white text-black hover:bg-gray-200 font-bold py-4 px-10 rounded-full flex items-center justify-center gap-2 transition-all uppercase tracking-wide text-sm sm:text-base hover:-translate-y-1 shadow-lg group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        Ver Produtos
                    </a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="/painel" class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-10 rounded-full flex items-center justify-center gap-2 shadow-[0_0_20px_rgba(255,0,51,0.4)] hover:shadow-[0_0_30px_rgba(255,0,51,0.6)] transition-all uppercase tracking-wide text-sm sm:text-base hover:-translate-y-1">
                            Acessar Painel
                        </a>
                    <?php else: ?>
                        <a href="/login.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-10 rounded-full flex items-center justify-center gap-2 shadow-[0_0_20px_rgba(255,0,51,0.4)] hover:shadow-[0_0_30px_rgba(255,0,51,0.6)] transition-all uppercase tracking-wide text-sm sm:text-base hover:-translate-y-1">
                            Acessar Painel
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Support Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <a href="https://discord.gg/hpjCtT7CU7" target="_blank" rel="noopener" aria-label="Abrir suporte no Discord" class="bg-red-600 hover:bg-red-700 text-white p-4 rounded-full shadow-[0_0_20px_rgba(220,38,38,0.5)] hover:shadow-[0_0_30px_rgba(220,38,38,0.8)] transition-all transform hover:-translate-y-1 hover:scale-110 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>
        </a>
    </div>

    <!-- Promo Section (Roleta & Features) -->
    <div class="bg-black relative z-10 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-20">
            
            <!-- Roleta Banner -->
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-400 p-8 md:p-12 shadow-[0_0_40px_rgba(255,165,0,0.3)] transform hover:scale-[1.01] transition-transform duration-300">
                <!-- Decorative Stars -->
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute top-4 left-4 text-white/40 w-8 h-8 animate-pulse" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6-4.8-6 4.8 2.4-7.2-6-4.8h7.6z"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute bottom-4 right-4 text-white/40 w-6 h-6 animate-pulse delay-75" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6-4.8-6 4.8 2.4-7.2-6-4.8h7.6z"/></svg>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6-4.8-6 4.8 2.4-7.2-6-4.8h7.6z"/></svg>
                            <span class="text-xs font-bold text-white uppercase tracking-wider">Nova Funcionalidade</span>
                        </div>
                        
                        <div>
                            <h2 class="text-4xl md:text-5xl font-black text-white mb-2">Roleta Diária</h2>
                            <h3 class="text-2xl font-bold text-white/90">Gire e Ganhe Prêmios!</h3>
                        </div>
                        
                        <p class="text-white/80 font-medium max-w-md">
                            Teste sua sorte todos os dias e ganhe moedas, diamantes e itens exclusivos gratuitamente!
                        </p>
                        
                        <a href="/roleta.php" class="inline-flex bg-white text-orange-600 hover:bg-orange-50 font-bold py-3 px-8 rounded-full items-center gap-2 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 cursor-pointer w-fit">
                            Experimentar Agora 
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    </div>
                    
                    <!-- Roulette Image/Illustration -->
                    <div class="relative hidden md:block">
                        <div class="absolute -right-12 -top-12 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full transform rotate-12 z-20 shadow-lg border-2 border-white">
                            NOVO!
                        </div>
                        <div class="bg-black/80 rounded-xl p-2 shadow-2xl border border-white/10 transform rotate-2 hover:rotate-0 transition-all duration-500">
                            <img 
                                src="https://images.unsplash.com/photo-1596838132731-3301c3fd4317?q=80&w=500&auto=format&fit=crop" 
                                alt="Roleta Interface" 
                                class="rounded-lg w-full h-48 object-cover opacity-90"
                            />
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="flex gap-2">
                                    <div class="w-20 h-24 bg-gray-800 rounded border border-gray-600 flex items-center justify-center">
                                        <span class="text-2xl">💎</span>
                                    </div>
                                    <div class="w-24 h-28 bg-gray-700 rounded border-2 border-orange-500 flex items-center justify-center shadow-[0_0_15px_rgba(255,165,0,0.5)] transform -translate-y-2">
                                        <span class="text-4xl">💰</span>
                                    </div>
                                    <div class="w-20 h-24 bg-gray-800 rounded border border-gray-600 flex items-center justify-center">
                                        <span class="text-2xl">🔫</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alta Performance Section -->
            <div class="relative">
                <!-- Background Mesh Effect -->
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_rgba(255,0,0,0.05)_0%,_transparent_70%)] pointer-events-none"></div>

                <div class="text-center mb-16 relative z-10">
                    <h2 class="text-3xl md:text-5xl font-black uppercase tracking-wider text-white">
                        ALTA <span class="relative inline-block text-red-600 after:content-[''] after:absolute after:-bottom-2 after:left-0 after:w-full after:h-1 after:bg-red-600">PERFORMANCE</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto relative z-10">
                    <!-- Velocidade Card -->
                    <div class="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                        <div class="mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-wide mb-3">VELOCIDADE</h3>
                        <p class="text-gray-400 font-medium">Entrega instantânea via PIX.</p>
                    </div>

                    <!-- Segurança Card -->
                    <div class="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                        <div class="mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 11c0 3-1.5 5.5-3.5 7.5S14 21 14 21s1.5-4.5 3.5-6.5S22 8 22 11Z"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-wide mb-3">SEGURANÇA</h3>
                        <p class="text-gray-400 font-medium">Tecnologia de proteção Triple-Layer.</p>
                    </div>

                    <!-- Suporte Card -->
                    <a href="https://discord.gg/hpjCtT7CU7" target="_blank" rel="noopener" class="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                        <div class="mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-wide mb-3">SUPORTE</h3>
                        <p class="text-gray-400 font-medium">Nossa equipe entra via AnyDesk.</p>
                    </a>

                    <!-- Comunidade VIP Card -->
                    <a href="https://discord.gg/hpjCtT7CU7" target="_blank" rel="noopener" class="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                        <div class="mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-wide mb-3">COMUNIDADE VIP</h3>
                        <p class="text-gray-400 font-medium">Acesso exclusivo ao Discord.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div id="produtos" class="py-16 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-10">
                <div class="reveal" data-reveal>
                    <h2 class="text-3xl md:text-4xl font-black uppercase tracking-wider">
                        Produtos <span class="text-red-600">Premium</span>
                    </h2>
                    <p class="text-gray-400 mt-2 max-w-2xl">Filtre por jogo, veja recursos, compare preços e escolha o plano ideal.</p>
                </div>

                <div class="reveal w-full lg:w-auto" data-reveal>
                    <div class="relative w-full lg:w-[420px]">
                        <div class="absolute inset-0 rounded-2xl blur-2xl opacity-30 bg-red-600/20"></div>
                        <div class="relative flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md px-4 py-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                            <input id="productSearch" class="w-full bg-transparent outline-none text-sm font-semibold placeholder:text-white/40" placeholder="Buscar jogo, recurso, nome..." />
                            <button id="clearSearch" class="hidden px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-black">Limpar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php $i = 0; foreach ($products as $product): ?>
                <?php
                    $featuresRaw = (string)($product['features'] ?? '');
                    $features = $featuresRaw !== '' ? array_values(array_filter(array_map('trim', explode('|', $featuresRaw)))) : [];
                    $featuresTop = array_slice($features, 0, 6);
                    $minPrice = (float)($product['min_price'] ?? 0);
                    $planCount = (int)($product['plan_count'] ?? 0);
                ?>
                <div
                    class="product-card reveal group rounded-2xl overflow-hidden border border-white/10 bg-white/5 backdrop-blur-md transition-all duration-300 hover:-translate-y-2 hover:border-red-600/40 hover:shadow-[0_0_45px_rgba(220,38,38,0.18)]"
                    data-reveal
                    data-name="<?php echo htmlspecialchars($product['name']); ?>"
                    data-slug="<?php echo htmlspecialchars($product['slug']); ?>"
                    data-features="<?php echo htmlspecialchars(implode(' ', $features)); ?>"
                    style="transition-delay: <?php echo (int)(($i % 9) * 45); ?>ms;"
                >
                    <div class="relative h-52 overflow-hidden shine">
                        <img
                            src="<?php echo htmlspecialchars($product['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
                        <div class="absolute top-4 left-4 flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-black tracking-wide bg-green-500/10 border border-green-500/20 text-green-300">
                                ATIVO
                            </span>
                            <?php if ($planCount > 0): ?>
                                <span class="px-3 py-1 rounded-full text-xs font-black tracking-wide bg-white/5 border border-white/10 text-white/80">
                                    <?php echo $planCount; ?> planos
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <h3 class="text-2xl font-black text-white tracking-tight leading-tight">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h3>
                                    <div class="text-xs text-white/60 font-semibold mt-1"><?php echo htmlspecialchars($product['slug']); ?></div>
                                </div>
                                <?php if ($minPrice > 0): ?>
                                    <div class="text-right">
                                        <div class="text-[10px] uppercase tracking-widest text-white/50 font-bold">a partir de</div>
                                        <div class="text-lg font-black text-white">R$ <?php echo number_format($minPrice, 2, ',', '.'); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-gray-300/80 text-sm leading-relaxed line-clamp-3">
                            <?php echo htmlspecialchars((string)$product['description']); ?>
                        </p>

                        <?php if (count($featuresTop) > 0): ?>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <?php foreach ($featuresTop as $f): ?>
                                    <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wide bg-black/40 border border-white/10 text-white/80">
                                        <?php echo htmlspecialchars($f); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mt-6 flex items-center gap-3">
                            <a
                                href="/comprar.php?game=<?php echo $product['slug']; ?>"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black py-3 transition-all shadow-[0_10px_25px_rgba(220,38,38,0.22)] hover:shadow-[0_14px_34px_rgba(220,38,38,0.30)]"
                            >
                                Ver planos
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="m13 5 7 7-7 7"></path>
                                </svg>
                            </a>
                            <a
                                href="/comprar.php?game=<?php echo $product['slug']; ?>"
                                class="min-w-12 min-h-12 inline-flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition"
                                title="Detalhes"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                            </a>
                        </div>
                        <a href="/teste-gratis.php?game=<?php echo $product['slug']; ?>" class="mt-3 block text-center px-4 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-black text-sm">
                            Conseguir key grátis (2h) para testar
                        </a>
                    </div>
                </div>
                <?php $i++; endforeach; ?>
            </div>

            <div id="noProducts" class="hidden mt-10 rounded-2xl border border-white/10 bg-white/5 p-8 text-center">
                <div class="text-xl font-black">Nada encontrado</div>
                <div class="text-sm text-white/60 mt-2">Tente buscar por outro nome ou recurso.</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black border-t border-red-900/20 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <img src="/logo-thunder.png" alt="Logo" class="h-12 w-auto mx-auto mb-6 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
            <p class="text-gray-500 text-sm">© 2024 Thunder Store. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        async function logout() {
            const formData = new FormData();
            formData.append('action', 'logout');
            await fetch('/api/auth.php', { method: 'POST', body: formData });
            location.reload();
        }

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

        (function () {
            const items = document.querySelectorAll('[data-reveal]');
            const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (!items.length) return;

            if (prefersReduced || !('IntersectionObserver' in window)) {
                items.forEach(el => el.classList.add('is-in'));
                return;
            }
            const io = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting) {
                        e.target.classList.add('is-in');
                        io.unobserve(e.target);
                    }
                });
            }, { threshold: 0.18 });
            items.forEach(el => io.observe(el));
        })();

        (function () {
            const search = document.getElementById('productSearch');
            const clear = document.getElementById('clearSearch');
            const cards = Array.from(document.querySelectorAll('.product-card'));
            const empty = document.getElementById('noProducts');
            if (!search || !cards.length) return;

            function applyFilter() {
                const q = (search.value || '').trim().toLowerCase();
                let visible = 0;
                cards.forEach((c) => {
                    const hay = (c.dataset.name + ' ' + c.dataset.slug + ' ' + c.dataset.features).toLowerCase();
                    const ok = q === '' || hay.includes(q);
                    c.classList.toggle('hidden', !ok);
                    if (ok) visible++;
                });
                if (clear) clear.classList.toggle('hidden', q === '');
                if (empty) empty.classList.toggle('hidden', visible !== 0);
            }

            search.addEventListener('input', applyFilter);
            if (clear) clear.addEventListener('click', () => { search.value = ''; applyFilter(); search.focus(); });

            cards.forEach((card) => {
                const onMove = (e) => {
                    const r = card.getBoundingClientRect();
                    const x = ((e.clientX - r.left) / r.width) * 100;
                    const y = ((e.clientY - r.top) / r.height) * 100;
                    card.style.setProperty('--mx', x + '%');
                    card.style.setProperty('--my', y + '%');
                };
                card.addEventListener('mousemove', onMove);
                card.addEventListener('mouseleave', () => {
                    card.style.removeProperty('--mx');
                    card.style.removeProperty('--my');
                });
            });
        })();
    </script>
  </body>
</html>

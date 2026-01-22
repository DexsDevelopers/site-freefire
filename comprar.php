<?php
session_start();

$dbOk = true;
try {
    require_once 'api/db.php';
} catch (Throwable $e) {
    $dbOk = false;
}

// Obtém o slug do produto da URL (ex: comprar.php?game=freefire)
$slug = isset($_GET['game']) ? trim((string)$_GET['game']) : '';

if (empty($slug)) {
    // Redireciona para a home se não houver produto especificado
    header("Location: /");
    exit;
}

$product = null;
$plans = [];

if ($dbOk && isset($conn)) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE slug = ? LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result_product = $stmt->get_result();

    if ($result_product && $result_product->num_rows > 0) {
        $product = $result_product->fetch_assoc();

        $product_id = (int)$product['id'];
        $stmt = $conn->prepare("SELECT * FROM plans WHERE product_id = ? ORDER BY price ASC");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result_plans = $stmt->get_result();
        while ($row = $result_plans->fetch_assoc()) {
            $plans[] = $row;
        }
    }
}

if (!$product) {
    $fallback = [
        'freefire' => [
            'product' => [
                'id' => 1,
                'slug' => 'freefire',
                'name' => 'FREE FIRE',
                'description' => 'Funções premium, suporte e atualizações constantes.',
                'image_url' => '/img/freefire.jpg',
                'features' => 'Chams|Aimbot|No Recoil|AimFov|CameraHack',
            ],
            'plans' => [
                ['id' => 101, 'product_id' => 1, 'name' => 'Diário', 'price' => 15.00],
                ['id' => 102, 'product_id' => 1, 'name' => 'Semanal', 'price' => 30.00],
                ['id' => 103, 'product_id' => 1, 'name' => 'Mensal', 'price' => 60.00],
                ['id' => 104, 'product_id' => 1, 'name' => 'Permanente', 'price' => 160.00],
            ],
        ],
        'valorant' => [
            'product' => [
                'id' => 2,
                'slug' => 'valorant',
                'name' => 'VALORANT',
                'description' => 'Monitor premium, performance e estabilidade.',
                'image_url' => '/img/valorantwall.jpg',
                'features' => 'HGV ON/OFF|Indetectável|No Lag',
            ],
            'plans' => [
                ['id' => 201, 'product_id' => 2, 'name' => 'Diário', 'price' => 20.00],
                ['id' => 202, 'product_id' => 2, 'name' => 'Semanal', 'price' => 50.00],
                ['id' => 203, 'product_id' => 2, 'name' => 'Mensal', 'price' => 90.00],
                ['id' => 204, 'product_id' => 2, 'name' => 'Permanente', 'price' => 250.00],
            ],
        ],
    ];

    if (!isset($fallback[$slug])) {
        http_response_code(404);
        echo "Produto não encontrado.";
        exit;
    }

    $product = $fallback[$slug]['product'];
    $plans = $fallback[$slug]['plans'];
}

$bestPlanId = 0;
foreach ($plans as $pl) {
    $n = mb_strtolower((string)($pl['name'] ?? ''));
    if (str_contains($n, 'mensal')) {
        $bestPlanId = (int)$pl['id'];
        break;
    }
}
if ($bestPlanId === 0 && count($plans) >= 2) {
    $bestPlanId = (int)$plans[1]['id'];
}
if ($bestPlanId === 0 && count($plans) === 1) {
    $bestPlanId = (int)$plans[0]['id'];
}

$featuresRaw = (string)($product['features'] ?? '');
$featuresList = $featuresRaw !== '' ? array_values(array_filter(array_map('trim', explode('|', $featuresRaw)))) : [];
$featuresTop = array_slice($featuresList, 0, 10);
$minPrice = null;
foreach ($plans as $pl) {
    $p = (float)($pl['price'] ?? 0);
    if ($p > 0 && ($minPrice === null || $p < $minPrice)) $minPrice = $p;
}
$planCount = count($plans);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Comprar <?php echo htmlspecialchars($product['name']); ?> - Thunder Store</title>
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
        html, body { touch-action: pan-x pan-y; }
        .reveal { opacity: 0; transform: translateY(18px); filter: blur(6px); transition: opacity 700ms cubic-bezier(.2,.8,.2,1), transform 700ms cubic-bezier(.2,.8,.2,1), filter 700ms cubic-bezier(.2,.8,.2,1); }
        .reveal.is-in { opacity: 1; transform: translateY(0); filter: blur(0); }
        .glass { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.10); }
        .glow { box-shadow: 0 0 0 1px rgba(220,38,38,0.15), 0 18px 55px rgba(220,38,38,0.10); }
        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1; transform: none; filter: none; transition: none; }
        }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
</head>
<body class="bg-black text-white font-sans antialiased">
    <div id="root">
        <div class="min-h-screen flex flex-col bg-black text-white selection:bg-ff-red selection:text-white overflow-x-hidden">
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

            <!-- Main Content -->
            <div class="pt-28 pb-16 px-4 sm:px-6 lg:px-8 flex-grow">
                <div class="max-w-6xl mx-auto w-full">
                    <div class="reveal" data-reveal>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-white/60 font-bold">
                            <a href="/" class="hover:text-white transition">Início</a>
                            <span class="text-white/30">/</span>
                            <a href="/#produtos" class="hover:text-white transition">Produtos</a>
                            <span class="text-white/30">/</span>
                            <span class="text-white"><?php echo htmlspecialchars($product['name']); ?></span>
                        </div>
                        <div class="mt-4 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                            <div>
                                <h1 class="text-4xl md:text-5xl font-black tracking-tight uppercase"><?php echo htmlspecialchars($product['name']); ?></h1>
                                <p class="text-white/60 mt-2 max-w-2xl">Escolha o melhor plano e finalize em segundos.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php if ($planCount > 0 && $minPrice !== null): ?>
                                    <div class="px-4 py-2 rounded-2xl glass">
                                        <div class="text-[10px] uppercase tracking-[0.22em] text-white/50 font-black">a partir de</div>
                                        <div class="text-lg font-black">R$ <?php echo number_format((float)$minPrice, 2, ',', '.'); ?></div>
                                    </div>
                                <?php endif; ?>
                                <div class="px-4 py-2 rounded-2xl glass">
                                    <div class="text-[10px] uppercase tracking-[0.22em] text-white/50 font-black">planos</div>
                                    <div class="text-lg font-black"><?php echo (int)$planCount; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-6">
                        <div class="xl:col-span-2 rounded-3xl glass glow overflow-hidden relative reveal" data-reveal>
                            <div class="absolute -top-16 -right-16 w-80 h-80 bg-ff-red/10 rounded-full blur-[120px] pointer-events-none"></div>
                            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white/5 rounded-full blur-[120px] pointer-events-none"></div>

                            <div class="p-6 md:p-8">
                                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">
                                    <div class="lg:col-span-2">
                                        <div class="rounded-2xl overflow-hidden border border-white/10 bg-black/40">
                                            <div class="relative h-56 lg:h-80">
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover" loading="lazy">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/30 to-transparent"></div>
                                                <div class="absolute top-4 left-4 flex items-center gap-2">
                                                    <span class="px-3 py-1 rounded-full text-xs font-black tracking-wide bg-green-500/10 border border-green-500/20 text-green-300">
                                                        ATIVO
                                                    </span>
                                                    <span class="px-3 py-1 rounded-full text-xs font-black tracking-wide bg-white/5 border border-white/10 text-white/80">
                                                        Entrega rápida
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lg:col-span-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-black tracking-wide bg-ff-red/10 border border-ff-red/30 text-red-200">Premium</span>
                                            <span class="px-3 py-1 rounded-full text-xs font-black tracking-wide bg-white/5 border border-white/10 text-white/70">Suporte</span>
                                            <span class="px-3 py-1 rounded-full text-xs font-black tracking-wide bg-white/5 border border-white/10 text-white/70">Atualizações</span>
                                        </div>

                                        <p class="mt-5 text-white/75 leading-relaxed">
                                            <?php echo nl2br(htmlspecialchars((string)$product['description'])); ?>
                                        </p>

                                        <?php if (count($featuresTop) > 0): ?>
                                            <div class="mt-6">
                                                <div class="text-xs font-black uppercase tracking-[0.22em] text-white/50 mb-3">Recursos</div>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php foreach ($featuresTop as $f): ?>
                                                        <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wide bg-black/40 border border-white/10 text-white/80">
                                                            <?php echo htmlspecialchars($f); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mt-7 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div class="rounded-2xl border border-white/10 bg-black/30 p-4">
                                                <div class="text-xs text-white/60 font-black uppercase tracking-[0.22em]">Segurança</div>
                                                <div class="mt-1 font-black">Protegido</div>
                                            </div>
                                            <div class="rounded-2xl border border-white/10 bg-black/30 p-4">
                                                <div class="text-xs text-white/60 font-black uppercase tracking-[0.22em]">Suporte</div>
                                                <div class="mt-1 font-black">Assistido</div>
                                            </div>
                                            <div class="rounded-2xl border border-white/10 bg-black/30 p-4">
                                                <div class="text-xs text-white/60 font-black uppercase tracking-[0.22em]">Acesso</div>
                                                <div class="mt-1 font-black">Imediato</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl glass glow p-6 md:p-7 reveal" data-reveal>
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <div class="text-lg font-black uppercase tracking-wide">Escolha seu plano</div>
                                    <div class="text-xs text-white/50 mt-1">Clique em um plano para selecionar</div>
                                </div>
                                <?php if ($bestPlanId): ?>
                                    <div class="px-3 py-1 rounded-full text-xs font-black bg-ff-red/10 border border-ff-red/30 text-red-200">Recomendado</div>
                                <?php endif; ?>
                            </div>

                            <form id="addToCartForm" method="POST" class="mt-5">
                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <?php foreach ($plans as $idx => $plan): ?>
                                        <?php
                                            $pid = (int)$plan['id'];
                                            $checked = ($pid === $bestPlanId) || ($bestPlanId === 0 && $idx === 0);
                                            $isBest = ($pid === $bestPlanId);
                                        ?>
                                        <label class="block cursor-pointer group">
                                            <input
                                                type="radio"
                                                name="plan_id"
                                                value="<?php echo $pid; ?>"
                                                data-name="<?php echo htmlspecialchars((string)$plan['name']); ?>"
                                                data-price="<?php echo htmlspecialchars((string)$plan['price']); ?>"
                                                class="peer sr-only"
                                                <?php echo $checked ? 'checked' : ''; ?>
                                            >
                                            <div class="rounded-2xl border border-white/10 bg-black/30 p-4 transition-all duration-300 group-hover:border-white/20 group-hover:-translate-y-0.5 peer-checked:border-ff-red/60 peer-checked:bg-ff-red/10">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <div class="text-sm font-black"><?php echo htmlspecialchars((string)$plan['name']); ?></div>
                                                        <?php if ($isBest): ?>
                                                            <div class="mt-1 text-[10px] font-black uppercase tracking-[0.22em] text-red-200">Mais escolhido</div>
                                                        <?php else: ?>
                                                            <div class="mt-1 text-[10px] font-black uppercase tracking-[0.22em] text-white/40">Plano</div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-[10px] font-black uppercase tracking-[0.22em] text-white/50">Preço</div>
                                                        <div class="text-base font-black text-white">R$ <?php echo number_format((float)$plan['price'], 2, ',', '.'); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mt-5 rounded-2xl border border-white/10 bg-black/30 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <div class="text-[10px] font-black uppercase tracking-[0.22em] text-white/50">Selecionado</div>
                                            <div id="selectedPlanName" class="text-sm font-black">—</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-[10px] font-black uppercase tracking-[0.22em] text-white/50">Total</div>
                                            <div id="selectedPlanPrice" class="text-lg font-black">R$ 0,00</div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="mt-5 w-full bg-ff-red text-white font-black uppercase py-4 rounded-2xl hover:bg-red-700 transition-colors tracking-wider shadow-[0_10px_30px_rgba(255,0,0,0.22)] hover:shadow-[0_14px_40px_rgba(255,0,0,0.32)] flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                    Adicionar ao carrinho
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="rounded-3xl glass p-6 reveal" data-reveal>
                            <div class="text-sm font-black uppercase tracking-wide">Entrega</div>
                            <div class="text-white/60 text-sm mt-2">Após o pagamento, o acesso é liberado rapidamente.</div>
                        </div>
                        <div class="rounded-3xl glass p-6 reveal" data-reveal>
                            <div class="text-sm font-black uppercase tracking-wide">Suporte</div>
                            <div class="text-white/60 text-sm mt-2">Atendimento via Discord e suporte assistido quando necessário.</div>
                        </div>
                        <div class="rounded-3xl glass p-6 reveal" data-reveal>
                            <div class="text-sm font-black uppercase tracking-wide">Atualizações</div>
                            <div class="text-white/60 text-sm mt-2">Atualizações e manutenção para manter o produto estável.</div>
                        </div>
                    </div>

                    <div class="mt-10 rounded-3xl glass p-6 md:p-8 reveal" data-reveal>
                        <div class="text-lg font-black uppercase tracking-wide">Perguntas rápidas</div>
                        <div class="mt-5 space-y-3">
                            <button class="w-full text-left rounded-2xl border border-white/10 bg-black/30 px-4 py-4 hover:bg-black/40 transition" data-acc-btn>
                                <div class="flex items-center justify-between gap-4">
                                    <div class="font-black">Como recebo o produto?</div>
                                    <div class="text-white/50 font-black">+</div>
                                </div>
                                <div class="hidden mt-3 text-white/60 text-sm leading-relaxed" data-acc-panel>
                                    Após a compra, você recebe instruções e acesso no canal de suporte/Discord (ou método definido pela loja).
                                </div>
                            </button>
                            <button class="w-full text-left rounded-2xl border border-white/10 bg-black/30 px-4 py-4 hover:bg-black/40 transition" data-acc-btn>
                                <div class="flex items-center justify-between gap-4">
                                    <div class="font-black">Posso trocar de plano depois?</div>
                                    <div class="text-white/50 font-black">+</div>
                                </div>
                                <div class="hidden mt-3 text-white/60 text-sm leading-relaxed" data-acc-panel>
                                    Sim. Você pode comprar um plano diferente quando quiser, conforme disponibilidade do produto.
                                </div>
                            </button>
                            <button class="w-full text-left rounded-2xl border border-white/10 bg-black/30 px-4 py-4 hover:bg-black/40 transition" data-acc-btn>
                                <div class="flex items-center justify-between gap-4">
                                    <div class="font-black">O que está incluso?</div>
                                    <div class="text-white/50 font-black">+</div>
                                </div>
                                <div class="hidden mt-3 text-white/60 text-sm leading-relaxed" data-acc-panel>
                                    Os recursos inclusos aparecem na lista “Recursos” acima. Em caso de dúvidas, chame o suporte.
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-black border-t border-white/10 py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <p class="text-gray-500 text-sm">© 2024 Thunder Store. Todos os direitos reservados.</p>
                </div>
            </footer>
        </div>
    </div>
    <script>
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
            const form = document.getElementById('addToCartForm');
            const nameEl = document.getElementById('selectedPlanName');
            const priceEl = document.getElementById('selectedPlanPrice');
            if (!form) return;

            const fmtMoney = (v) => 'R$ ' + Number(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            function updateSelected() {
                const selected = form.querySelector('input[name="plan_id"]:checked');
                if (!selected) return;
                if (nameEl) nameEl.textContent = selected.dataset.name || '—';
                if (priceEl) priceEl.textContent = fmtMoney(selected.dataset.price || 0);
            }

            form.addEventListener('change', function (e) {
                const t = e.target;
                if (t && t.name === 'plan_id') updateSelected();
            });

            updateSelected();

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(form);
                formData.append('action', 'add');

                const selected = form.querySelector('input[name="plan_id"]:checked');
                if (selected) {
                    formData.append('plan_name', selected.dataset.name || '');
                    formData.append('price', selected.dataset.price || '0');
                }

                try {
                    const response = await fetch('/api/cart.php', { method: 'POST', body: formData });
                    const data = await response.json();
                    if (data.success) {
                        if (confirm('Produto adicionado ao carrinho! Ir para o carrinho?')) {
                            window.location.href = '/carrinho.php';
                        }
                    } else {
                        alert(data.message || 'Não foi possível adicionar ao carrinho.');
                    }
                } catch (error) {
                    console.error(error);
                    alert('Erro ao adicionar ao carrinho.');
                }
            });
        })();

        (function () {
            const buttons = Array.from(document.querySelectorAll('[data-acc-btn]'));
            if (!buttons.length) return;

            buttons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const panel = btn.querySelector('[data-acc-panel]');
                    const sign = btn.querySelector('div.text-white\\/50');
                    if (!panel) return;
                    const isOpen = !panel.classList.contains('hidden');
                    panel.classList.toggle('hidden', isOpen);
                    if (sign) sign.textContent = isOpen ? '+' : '—';
                });
            });
        })();
    </script>
</body>
</html>
<?php if (isset($conn)) { $conn->close(); } ?>

<?php
session_start();
$cart_items = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <style>
        body { background-color: #000; color: white; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-black/90 backdrop-blur-sm border-b border-white/5 fixed w-full z-50 transition-all duration-300">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
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
                    <a href="/" class="text-white font-bold text-xs tracking-wider hover:text-red-500 transition-colors">INÍCIO</a>
                    <a href="#" class="text-yellow-400 font-bold text-xs tracking-wider flex items-center gap-2 hover:text-yellow-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                        ROLETA
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors">STATUS</a>
                    <a href="#" class="text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors">TERMOS</a>
                    <a href="#" class="text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors">DEMONSTRAÇÃO</a>
                    <a href="#" class="text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors">FAQ</a>
                    <a href="#" class="text-purple-500 font-bold text-xs tracking-wider flex items-center gap-2 hover:text-purple-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        MYSTERY BOX
                    </a>
                </div>

                <!-- Right: Loja Button -->
                <div class="flex items-center">
                     <a href="/#produtos" class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 text-white font-black italic px-6 py-2 rounded-full flex items-center gap-2 shadow-[0_0_15px_rgba(255,165,0,0.3)] transform skew-x-[-10deg] hover:skew-x-[-10deg] hover:scale-105 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform skew-x-[10deg]" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                        <span class="transform skew-x-[10deg] text-sm">LOJA</span>
                     </a>
                     
                     <!-- Mobile Menu Button -->
                     <button class="ml-4 xl:hidden text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                     </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex-grow pt-24 px-4 max-w-7xl mx-auto w-full">
        <h1 class="text-4xl font-bold mb-8 text-center"><span class="text-red-600">SEU</span> CARRINHO</h1>

        <?php if (empty($cart_items)): ?>
            <div class="text-center py-20">
                <p class="text-xl text-gray-400 mb-6">Seu carrinho está vazio.</p>
                <a href="/" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-[0_0_20px_rgba(220,38,38,0.4)]">
                    VER PRODUTOS
                </a>
            </div>
        <?php else: ?>
            <div class="bg-zinc-900/50 border border-red-900/20 rounded-xl p-6 mb-8">
                <?php foreach ($cart_items as $index => $item): ?>
                    <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-800 py-4 last:border-0">
                        <div class="flex items-center gap-4 mb-4 md:mb-0">
                            <div>
                                <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <p class="text-gray-400"><?php echo htmlspecialchars($item['plan_name']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <span class="text-2xl font-bold text-red-500">R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></span>
                            <button onclick="removeItem(<?php echo $index; ?>)" class="text-gray-500 hover:text-red-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mt-8 flex flex-col md:flex-row justify-between items-center pt-6 border-t border-red-900/30">
                    <div class="text-3xl font-bold mb-4 md:mb-0">
                        Total: <span class="text-red-500">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                    </div>
                    <button onclick="checkout()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-[0_0_20px_rgba(22,163,74,0.4)]">
                        FINALIZAR COMPRA
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        async function removeItem(index) {
            if (!confirm('Remover este item?')) return;
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('index', index);

            try {
                const response = await fetch('/api/cart.php', { method: 'POST', body: formData });
                const data = await response.json();
                if (data.success) location.reload();
            } catch (error) { console.error(error); }
        }

        async function checkout() {
            const formData = new FormData();
            formData.append('action', 'checkout');

            try {
                const response = await fetch('/api/cart.php', { method: 'POST', body: formData });
                const data = await response.json();
                
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.success) {
                    alert('Pedido #' + data.order_id + ' realizado com sucesso!');
                    location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) { console.error(error); }
        }

        async function logout() {
            const formData = new FormData();
            formData.append('action', 'logout');
            await fetch('/api/auth.php', { method: 'POST', body: formData });
            location.reload();
        }
    </script>
</body>
</html>

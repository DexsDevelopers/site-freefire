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
    <nav class="bg-black/90 backdrop-blur-sm border-b border-red-900/30 fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0 flex items-center gap-3 cursor-pointer" onclick="window.location.href='/'">
                    <img src="/logo-thunder.png" alt="Thunder Store" class="h-10 w-auto drop-shadow-[0_0_10px_rgba(220,38,38,0.5)]">
                    <span class="font-bold text-2xl tracking-tighter text-white">
                        THUNDER <span class="text-red-600 drop-shadow-[0_0_10px_rgba(220,38,38,0.8)]">STORE</span>
                    </span>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="/" class="text-gray-300 hover:text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">INÍCIO</a>
                        <a href="/#produtos" class="text-gray-300 hover:text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">PRODUTOS</a>
                        <a href="/carrinho.php" class="text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">CARRINHO</a>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <span class="text-gray-300 text-sm">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <a href="#" onclick="logout()" class="text-gray-300 hover:text-red-500 text-sm">Sair</a>
                        <?php else: ?>
                            <a href="/login.php" class="text-gray-300 hover:text-red-500 text-sm">Login</a>
                        <?php endif; ?>
                    </div>
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

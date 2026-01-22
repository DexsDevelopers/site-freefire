<?php
require_once 'api/db.php';

// Obtém o slug do produto da URL (ex: comprar.php?game=freefire)
$slug = isset($_GET['game']) ? $conn->real_escape_string($_GET['game']) : '';

if (empty($slug)) {
    // Redireciona para a home se não houver produto especificado
    header("Location: /");
    exit;
}

// Busca informações do produto
$sql_product = "SELECT * FROM products WHERE slug = '$slug' LIMIT 1";
$result_product = $conn->query($sql_product);

if ($result_product->num_rows == 0) {
    echo "Produto não encontrado.";
    exit;
}

$product = $result_product->fetch_assoc();

// Busca planos do produto
$product_id = $product['id'];
$sql_plans = "SELECT * FROM plans WHERE product_id = $product_id ORDER BY price ASC";
$result_plans = $conn->query($sql_plans);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</head>
<body class="bg-black text-white font-sans antialiased">
    <div id="root">
        <div class="min-h-screen flex flex-col bg-black text-white selection:bg-ff-red selection:text-white overflow-x-hidden">
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

            <!-- Main Content -->
            <div class="pt-32 pb-16 px-4 sm:px-6 lg:px-8 flex-grow flex items-center justify-center">
                <div class="max-w-4xl w-full bg-[#111] border border-white/10 rounded-2xl p-8 md:p-12 shadow-[0_0_40px_rgba(255,0,0,0.1)] relative overflow-hidden">
                    <!-- Background Effects -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-ff-red/10 rounded-full blur-[100px] pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-ff-red/5 rounded-full blur-[100px] pointer-events-none"></div>

                    <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                        <!-- Product Info -->
                        <div class="text-left">
                            <h1 class="text-4xl md:text-5xl font-black uppercase mb-4 tracking-wider"><?php echo htmlspecialchars($product['name']); ?></h1>
                            
                            <div class="inline-block bg-green-500/10 border border-green-500/20 px-4 py-1.5 rounded-full mb-6">
                                <span class="text-green-500 font-bold text-sm tracking-wide uppercase flex items-center gap-2">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_#22c55e]"></span>
                                    INDETECTADO
                                </span>
                            </div>

                            <p class="text-gray-400 text-lg leading-relaxed mb-8">
                                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                            </p>

                            <!-- Features List (Optional, if you want to parse the pipe-separated features) -->
                            <?php if (!empty($product['features'])): ?>
                            <div class="flex flex-wrap gap-2 mb-8">
                                <?php 
                                $features = explode('|', $product['features']);
                                foreach ($features as $feature): 
                                ?>
                                    <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full text-xs font-bold text-gray-300 uppercase tracking-wide">
                                        <?php echo htmlspecialchars($feature); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Purchase Options -->
                        <div class="bg-[#0a0a0a] border border-white/5 rounded-xl p-6 shadow-inner">
                            <h3 class="text-xl font-bold mb-6 text-white uppercase tracking-wide border-b border-white/10 pb-4">Escolha seu Plano</h3>
                            
                            <form id="addToCartForm" method="POST">
                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="space-y-3 mb-8">
                                    <?php 
                                    $first = true;
                                    while ($plan = $result_plans->fetch_assoc()): 
                                        $checked = $first ? 'checked' : '';
                                        $first = false;
                                    ?>
                                    <label class="block relative cursor-pointer group">
                                        <input type="radio" name="plan_id" value="<?php echo $plan['id']; ?>" 
                                               data-name="<?php echo htmlspecialchars($plan['name']); ?>" 
                                               data-price="<?php echo $plan['price']; ?>"
                                               class="peer sr-only" <?php echo $checked; ?>>
                                        <div class="p-4 rounded-lg bg-[#151515] border border-white/5 peer-checked:border-ff-red peer-checked:bg-ff-red/10 transition-all duration-300 flex justify-between items-center group-hover:border-white/20">
                                            <span class="font-medium text-gray-300 peer-checked:text-white"><?php echo htmlspecialchars($plan['name']); ?></span>
                                            <span class="font-bold text-white peer-checked:text-ff-red">R$ <?php echo number_format($plan['price'], 2, ',', '.'); ?></span>
                                        </div>
                                    </label>
                                    <?php endwhile; ?>
                                </div>

                                <button type="submit" class="w-full bg-ff-red text-white font-black uppercase py-4 rounded-lg hover:bg-red-700 transition-colors tracking-wider shadow-[0_4px_14px_rgba(255,0,0,0.4)] hover:shadow-[0_6px_20px_rgba(255,0,0,0.6)] flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                    ADICIONAR AO CARRINHO
                                </button>
                            </form>
                            <script>
                                document.getElementById('addToCartForm').addEventListener('submit', async function(e) {
                                    e.preventDefault();
                                    const formData = new FormData(this);
                                    formData.append('action', 'add');
                                    
                                    // Pega os dados do plano selecionado
                                    const selected = document.querySelector('input[name="plan_id"]:checked');
                                    if(selected) {
                                        formData.append('plan_name', selected.dataset.name);
                                        formData.append('price', selected.dataset.price);
                                    }

                                    try {
                                        const response = await fetch('/api/cart.php', { method: 'POST', body: formData });
                                        const data = await response.json();
                                        if (data.success) {
                                            if(confirm('Produto adicionado ao carrinho! Ir para o carrinho?')) {
                                                window.location.href = '/carrinho.php';
                                            }
                                        } else {
                                            alert(data.message);
                                        }
                                    } catch (error) { console.error(error); }
                                });
                            </script>
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
</body>
</html>
<?php $conn->close(); ?>
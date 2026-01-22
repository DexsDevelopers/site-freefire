<?php
session_start();
require_once 'api/db.php';

// Busca todos os produtos
$sql = "SELECT * FROM products WHERE status = 'Ativo'";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Thunder Store | Loja de Free Fire</title>
    <meta name="description" content="Sua loja confiável para produtos de Free Fire. Diamantes, contas e muito mais com entrega imediata." />
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
        body { background-color: #000; color: white; font-family: 'Inter', sans-serif; }
    </style>
  </head>
  <body class="bg-black text-white min-h-screen">
    
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
                        <a href="#produtos" class="text-gray-300 hover:text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">PRODUTOS</a>
                        <a href="/carrinho.php" class="text-gray-300 hover:text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">CARRINHO</a>
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

    <!-- Hero Section -->
    <div class="relative min-h-screen bg-black flex items-center overflow-hidden">
        <!-- Background Effect -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-gray-800/20 via-black to-black opacity-50"></div>
            <div class="absolute top-0 left-0 w-full h-full opacity-10" 
                 style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
            </div>
            <!-- White/Gray glow spots for Thunder theme -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/5 rounded-full blur-[128px]"></div>
        </div>

        <div class="relative z-10 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col items-center text-center justify-center min-h-screen pt-20 pb-10">
            <!-- Content -->
            <div class="space-y-8 flex flex-col items-center max-w-4xl mx-auto">
                <!-- Image as Title -->
                <div class="relative flex justify-center w-full">
                    <img 
                        src="/logo-thunder.png" 
                        alt="THUNDER STORE" 
                        class="w-full max-w-4xl drop-shadow-[0_0_30px_rgba(255,255,255,0.15)] transform hover:scale-105 transition-transform duration-500"
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
                    <a href="#produtos" class="bg-white text-black hover:bg-gray-200 font-bold py-4 px-10 rounded-full flex items-center justify-center gap-2 transition-all uppercase tracking-wide text-sm sm:text-base hover:-translate-y-1 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
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
                        
                        <a href="#" class="inline-flex bg-white text-orange-600 hover:bg-orange-50 font-bold py-3 px-8 rounded-full items-center gap-2 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 cursor-pointer w-fit">
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
                    <div class="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                        <div class="mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-wide mb-3">SUPORTE</h3>
                        <p class="text-gray-400 font-medium">Nossa equipe entra via AnyDesk.</p>
                    </div>

                    <!-- Comunidade VIP Card -->
                    <div class="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                        <div class="mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-wide mb-3">COMUNIDADE VIP</h3>
                        <p class="text-gray-400 font-medium">Acesso exclusivo ao Discord.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div id="produtos" class="py-16 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12 uppercase tracking-wider">
                ESCOLHA O <span class="text-red-600 border-b-4 border-red-600">SEU JOGO</span>
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while($product = $result->fetch_assoc()): ?>
                <div class="group bg-zinc-900/50 border border-zinc-800 hover:border-red-600/50 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-[0_0_30px_rgba(220,38,38,0.2)] hover:-translate-y-2">
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80"></div>
                        <div class="absolute bottom-4 left-4">
                            <h3 class="text-xl font-bold text-white group-hover:text-red-500 transition-colors"><?php echo htmlspecialchars($product['name']); ?></h3>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex text-yellow-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <span class="text-xs text-gray-500 font-medium">5.0 (2k+ reviews)</span>
                        </div>
                        
                        <p class="text-gray-400 text-sm mb-6 line-clamp-3">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                        
                        <a href="/comprar.php?game=<?php echo $product['slug']; ?>" class="block w-full text-center bg-white/5 hover:bg-red-600 text-white font-bold py-3 rounded-lg border border-white/10 hover:border-red-500 transition-all duration-300">
                            VER PLANOS
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
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
    </script>
  </body>
</html>

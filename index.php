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
                    <a href="#" class="relative text-yellow-400 font-bold text-xs tracking-wider flex items-center gap-2 hover:text-yellow-300 transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-yellow-400 after:transition-all hover:after:w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                        ROLETA
                    </a>
                    <a href="#" class="relative text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">STATUS</a>
                    <a href="#" class="relative text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">TERMOS</a>
                    <a href="#" class="relative text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">DEMONSTRAÇÃO</a>
                    <a href="#" class="relative text-gray-300 hover:text-white font-bold text-xs tracking-wider transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-white after:transition-all hover:after:w-full">FAQ</a>
                    <a href="#" class="relative text-purple-500 font-bold text-xs tracking-wider flex items-center gap-2 hover:text-purple-400 transition-colors after:content-[''] after:absolute after:-bottom-2 after:left-0 after:h-[2px] after:w-0 after:bg-purple-500 after:transition-all hover:after:w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        MYSTERY BOX
                    </a>
                    <span class="ml-2 text-[11px] text-gray-500 tracking-wide hidden 2xl:block">Free Fire Counter</span>
                </div>

                <!-- Right: Loja Button -->
                <div class="flex items-center">
                     <a href="#produtos" class="bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 text-white font-black italic px-6 py-2 rounded-full flex items-center gap-2 shadow-[0_0_25px_rgba(255,165,0,0.35)] transform skew-x-[-10deg] hover:skew-x-[-10deg] hover:scale-105 transition-all border border-white/10 hover:border-white/20">
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
        <a href="#" class="bg-red-600 hover:bg-red-700 text-white p-4 rounded-full shadow-[0_0_20px_rgba(220,38,38,0.5)] hover:shadow-[0_0_30px_rgba(220,38,38,0.8)] transition-all transform hover:-translate-y-1 hover:scale-110 flex items-center justify-center">
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

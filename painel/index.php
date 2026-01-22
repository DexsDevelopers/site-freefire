<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel VIP | Thunder Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cheat-black': '#050505',
                        'cheat-dark': '#0a0a0a',
                        'cheat-red': '#ff0000',
                        'cheat-border': '#ff0000',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['Rajdhani', 'sans-serif'], // Usando Rajdhani para o visual "gamer/tech"
                    },
                    boxShadow: {
                        'glow': '0 0 10px rgba(255, 0, 0, 0.5)',
                        'glow-white': '0 0 15px rgba(255, 255, 255, 0.8)',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Checkbox Styling */
        .cheat-checkbox input:checked + div {
            background-color: #ff0000;
            border-color: #ff0000;
        }
        .cheat-checkbox input:checked + div::after {
            content: '';
            display: block;
            width: 10px;
            height: 10px;
            background-color: white; /* ou checkmark svg */
            /* Simples quadrado branco interno ou check */
        }
        
        /* Scrollbar customizada */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #000; 
        }
        ::-webkit-scrollbar-thumb {
            background: #333; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ff0000; 
        }
    </style>
</head>
<body class="bg-black min-h-screen flex items-center justify-center font-sans select-none overflow-hidden">
    
    <!-- Main Container (The Cheat Menu Window) -->
    <div class="w-[900px] h-[600px] bg-black border border-cheat-red relative flex shadow-[0_0_50px_rgba(255,0,0,0.1)]">
        
        <!-- Sidebar -->
        <div class="w-1/4 h-full border-r border-cheat-red/30 flex flex-col justify-between p-6 bg-black relative z-10">
            
            <div>
                <!-- Header -->
                <div class="mb-10">
                    <h1 class="text-white font-mono font-bold text-xl tracking-wider">
                        THUNDER <span class="text-cheat-red">STORE</span>
                    </h1>
                </div>

                <!-- Navigation -->
                <div class="space-y-4">
                    <!-- Active Button (White Glow) -->
                    <button class="w-full bg-white text-cheat-red font-bold font-mono py-3 rounded shadow-glow-white tracking-widest text-lg hover:scale-105 transition-transform uppercase">
                        AIM
                    </button>
                    
                    <!-- Inactive Buttons (Red) -->
                    <button class="w-full bg-cheat-red text-white font-bold font-mono py-3 rounded shadow-glow tracking-widest text-lg hover:bg-red-700 transition-colors uppercase">
                        AIM 2
                    </button>
                    
                    <button class="w-full bg-cheat-red text-white font-bold font-mono py-3 rounded shadow-glow tracking-widest text-lg hover:bg-red-700 transition-colors uppercase">
                        VISUALS
                    </button>
                    
                    <button class="w-full bg-cheat-red text-white font-bold font-mono py-3 rounded shadow-glow tracking-widest text-lg hover:bg-red-700 transition-colors uppercase">
                        MISC
                    </button>
                </div>
            </div>

            <!-- Footer / Status -->
            <div>
                <div class="text-white font-mono font-bold text-lg">
                    Status
                </div>
                <div class="text-green-500 text-xs mt-1 animate-pulse">
                    ● UNDETECTED
                </div>
                
                <a href="/api/auth.php?logout=true" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-gray-500 hover:text-white text-xs mt-4 block">
                    Sair
                </a>
                <form id="logout-form" action="/api/auth.php" method="POST" style="display: none;">
                    <input type="hidden" name="action" value="logout">
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-3/4 h-full p-12 relative bg-[#050505]">
            <!-- Red border container effect inside content (optional, based on image border) -->
            <div class="absolute inset-4 border border-cheat-red/20 pointer-events-none"></div>

            <!-- Header Section -->
            <div class="mb-12">
                <div class="inline-block border-l-4 border-cheat-red pl-4 py-2 bg-[#0a0a0a] min-w-[200px]">
                    <h2 class="text-white font-mono font-bold text-2xl tracking-[0.2em] uppercase">GENERAL</h2>
                </div>
            </div>

            <!-- Checkbox Grid -->
            <div class="grid grid-cols-2 gap-y-8 gap-x-12 px-4">
                
                <!-- Coluna 1 -->
                <div class="space-y-6">
                    <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                        <input type="checkbox" class="hidden" checked>
                        <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500">
                            <!-- Inner check indicator handled by CSS -->
                        </div>
                        <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Aimbot Head</span>
                    </label>

                    <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                        <input type="checkbox" class="hidden">
                        <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                        <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Aimbot Legit</span>
                    </label>

                    <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                        <input type="checkbox" class="hidden">
                        <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                        <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">No Recoil</span>
                    </label>

                    <div class="h-4"></div> <!-- Spacer -->

                    <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                        <input type="checkbox" class="hidden">
                        <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                        <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Fly Hack</span>
                    </label>

                    <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                        <input type="checkbox" class="hidden">
                        <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                        <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Precision++ (BlackList)</span>
                    </label>
                </div>

                <!-- Coluna 2 -->
                <div class="space-y-6">
                    <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                        <input type="checkbox" class="hidden">
                        <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                        <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Scope 2x</span>
                    </label>

                    <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                        <input type="checkbox" class="hidden">
                        <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                        <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Scope 4x</span>
                    </label>

                    <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                        <input type="checkbox" class="hidden">
                        <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                        <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Atravessar</span>
                    </label>
                </div>

            </div>

        </div>
    </div>

    <!-- Background Decoration -->
    <div class="fixed inset-0 pointer-events-none z-[-1]">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1200px] h-[800px] bg-red-600/5 rounded-full blur-[150px]"></div>
    </div>

</body>
</html>

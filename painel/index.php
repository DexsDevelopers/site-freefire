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
                        mono: ['Rajdhani', 'sans-serif'],
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
            background-color: white;
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

        .tab-btn.active {
            background-color: white;
            color: #ff0000;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.8);
        }
        .tab-btn.inactive {
            background-color: #ff0000;
            color: white;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
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
                    <button onclick="switchTab('aim')" id="btn-aim" class="tab-btn active w-full font-bold font-mono py-3 rounded tracking-widest text-lg hover:scale-105 transition-transform uppercase">
                        AIM
                    </button>
                    
                    <button onclick="switchTab('aim2')" id="btn-aim2" class="tab-btn inactive w-full font-bold font-mono py-3 rounded tracking-widest text-lg hover:scale-105 transition-transform uppercase">
                        AIM 2
                    </button>
                    
                    <button onclick="switchTab('visuals')" id="btn-visuals" class="tab-btn inactive w-full font-bold font-mono py-3 rounded tracking-widest text-lg hover:scale-105 transition-transform uppercase">
                        VISUALS
                    </button>
                    
                    <button onclick="switchTab('misc')" id="btn-misc" class="tab-btn inactive w-full font-bold font-mono py-3 rounded tracking-widest text-lg hover:scale-105 transition-transform uppercase">
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
            <!-- Red border container effect inside content -->
            <div class="absolute inset-4 border border-cheat-red/20 pointer-events-none"></div>

            <!-- TAB: AIM -->
            <div id="tab-aim" class="tab-content h-full">
                <div class="mb-12">
                    <div class="inline-block border-l-4 border-cheat-red pl-4 py-2 bg-[#0a0a0a] min-w-[200px]">
                        <h2 class="text-white font-mono font-bold text-2xl tracking-[0.2em] uppercase">GENERAL</h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-8 gap-x-12 px-4">
                    <!-- Coluna 1 -->
                    <div class="space-y-6">
                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden" checked>
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
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

                        <div class="h-4"></div>

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

            <!-- TAB: AIM 2 -->
            <div id="tab-aim2" class="tab-content h-full hidden">
                <div class="mb-12">
                    <div class="inline-block border-l-4 border-cheat-red pl-4 py-2 bg-[#0a0a0a] min-w-[200px]">
                        <h2 class="text-white font-mono font-bold text-2xl tracking-[0.2em] uppercase">GENERAL</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-y-8 px-4">
                    <div class="space-y-6">
                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Aimbot M82B</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Aimbot M24</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Aimbot AWM-Y</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Aimbot AWM</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Aimbot VSK</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Switch AWM</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- TAB: VISUALS -->
            <div id="tab-visuals" class="tab-content h-full hidden">
                <div class="mb-12">
                    <div class="inline-block border-l-4 border-cheat-red pl-4 py-2 bg-[#0a0a0a] min-w-[200px]">
                        <h2 class="text-white font-mono font-bold text-2xl tracking-[0.2em] uppercase">VISUALS</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-y-8 px-4">
                    <div class="space-y-6">
                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Camera Hack</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Chams</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Security</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Stream Mode</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- TAB: MISC -->
            <div id="tab-misc" class="tab-content h-full hidden">
                <div class="mb-8">
                    <div class="inline-block border-l-4 border-cheat-red pl-4 py-2 bg-[#0a0a0a] min-w-[200px]">
                        <h2 class="text-white font-mono font-bold text-2xl tracking-[0.2em] uppercase">MISC</h2>
                    </div>
                </div>
                
                <div class="mb-8 px-4">
                     <p class="text-white font-mono font-bold text-sm tracking-wide">Tempo Restante: <span class="text-gray-400">999 days</span></p>
                </div>

                <div class="grid grid-cols-1 gap-y-8 px-4">
                    <div class="space-y-6">
                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Speed 2x</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">AimFOV</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Criticals</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Bypass Mobile</span>
                        </label>

                        <label class="flex items-center gap-4 cursor-pointer group cheat-checkbox">
                            <input type="checkbox" class="hidden">
                            <div class="w-6 h-6 border-2 border-cheat-red flex items-center justify-center transition-colors bg-transparent group-hover:border-red-500"></div>
                            <span class="text-white font-mono text-lg tracking-wide group-hover:text-cheat-red transition-colors">Hide Taskbar</span>
                        </label>
                    </div>
                    
                    <div class="mt-4">
                        <button class="bg-[#111] hover:bg-[#222] text-gray-400 font-mono font-bold py-3 px-8 rounded-full border border-white/10 hover:border-white/20 transition-all shadow-lg uppercase tracking-wider">
                            Cleanner
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Background Decoration -->
    <div class="fixed inset-0 pointer-events-none z-[-1]">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1200px] h-[800px] bg-red-600/5 rounded-full blur-[150px]"></div>
    </div>

    <script>
        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Deactivate all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('inactive');
            });
            
            // Show selected tab content
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            
            // Activate selected button
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.remove('inactive');
            activeBtn.classList.add('active');
        }
    </script>

</body>
</html>

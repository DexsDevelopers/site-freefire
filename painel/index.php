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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Painel VIP | Thunder Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d'
                        },
                        neutral: {
                            0: '#ffffff',
                            50: '#fafafa',
                            100: '#f5f5f5',
                            200: '#e5e5e5',
                            300: '#d4d4d4',
                            400: '#a3a3a3',
                            500: '#737373',
                            600: '#525252',
                            700: '#404040',
                            800: '#262626',
                            900: '#171717',
                            950: '#0a0a0a'
                        },
                        success: {
                            500: '#22c55e',
                            600: '#16a34a'
                        },
                        warning: {
                            500: '#f59e0b',
                            600: '#d97706'
                        },
                        danger: {
                            500: '#ef4444',
                            600: '#dc2626'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['Rajdhani', 'sans-serif'],
                    },
                    fontSize: {
                        title: ['1.75rem', { lineHeight: '2.125rem', letterSpacing: '-0.01em' }],
                        subtitle: ['1.25rem', { lineHeight: '1.75rem', letterSpacing: '-0.01em' }],
                        body: ['1rem', { lineHeight: '1.5rem' }],
                        secondary: ['0.875rem', { lineHeight: '1.25rem' }]
                    },
                    boxShadow: {
                        e1: '0 1px 2px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.04)',
                        e2: '0 8px 24px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.06)',
                        e3: '0 20px 48px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.08)',
                    }
                }
            }
        }
    </script>
    <style>
        html, body { touch-action: pan-x pan-y; }
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0a0a0a;
        }
        ::-webkit-scrollbar-thumb {
            background: #262626;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ef4444;
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
    <link rel="stylesheet" href="/assets/popup.css" />
    <script src="/assets/popup.js" defer></script>
</head>
<body class="bg-neutral-950 text-neutral-50 min-h-dvh font-sans antialiased">
    <div class="fixed inset-0 pointer-events-none -z-10">
        <div class="absolute inset-0 bg-[radial-gradient(1000px_700px_at_50%_0%,rgba(239,68,68,0.10),transparent_55%)]"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-neutral-950/0 via-neutral-950/0 to-neutral-950"></div>
    </div>

    <div class="min-h-dvh flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-6xl rounded-2xl overflow-hidden bg-black/60 border border-primary-600 shadow-[0_0_60px_rgba(239,68,68,0.15)]">
            <div class="flex flex-col md:flex-row">
                <aside class="w-full md:w-80 bg-black/50 border-b md:border-b-0 md:border-r border-primary-600/40 p-6 flex flex-col">
                    <div class="flex items-center justify-between gap-4">
                        <div class="font-mono font-bold tracking-[0.2em] text-sm">
                            <span class="text-neutral-200">COMBO</span>
                            <span class="text-primary-500 ml-2">X3REC4</span>
                        </div>
                        <a href="/api/auth.php?logout=true" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 text-xs font-bold tracking-wider">
                            SAIR
                        </a>
                        <form id="logout-form" action="/api/auth.php" method="POST" style="display: none;">
                            <input type="hidden" name="action" value="logout">
                        </form>
                    </div>

                    <div class="mt-8 grid grid-cols-2 md:grid-cols-1 gap-4">
                        <button type="button" data-tab="aim" class="tab-btn h-14 rounded-lg font-black tracking-[0.2em] bg-white text-primary-600 shadow-[0_0_22px_rgba(255,255,255,0.35)]">
                            AIM
                        </button>
                        <button type="button" data-tab="aim2" class="tab-btn h-14 rounded-lg font-black tracking-[0.2em] bg-primary-600 text-white shadow-[0_0_22px_rgba(239,68,68,0.25)] hover:bg-primary-500">
                            AIM 2
                        </button>
                        <button type="button" data-tab="visuals" class="tab-btn h-14 rounded-lg font-black tracking-[0.2em] bg-primary-600 text-white shadow-[0_0_22px_rgba(239,68,68,0.25)] hover:bg-primary-500">
                            VISUALS
                        </button>
                        <button type="button" data-tab="misc" class="tab-btn h-14 rounded-lg font-black tracking-[0.2em] bg-primary-600 text-white shadow-[0_0_22px_rgba(239,68,68,0.25)] hover:bg-primary-500">
                            MISC
                        </button>
                    </div>

                    <div class="mt-10 pt-6 border-t border-white/10 flex items-center justify-between">
                        <div class="text-sm font-bold tracking-wider">Status</div>
                        <div class="px-3 py-1 rounded-full bg-success-500/10 text-success-500 text-xs font-bold tracking-wider">Online</div>
                    </div>
                </aside>

                <main class="flex-1 p-6 md:p-12">
                    <div class="flex items-center justify-center md:justify-start">
                        <div class="relative inline-flex items-center justify-center px-10 md:px-16 py-4 bg-neutral-900/50 border border-neutral-800 rounded-xl">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary-600 rounded-l-xl"></div>
                            <div class="font-black tracking-[0.35em] text-lg">GENERAL</div>
                        </div>
                    </div>

                    <div id="tab-aim" class="tab-content mt-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-24 gap-y-10">
                            <div class="space-y-6">
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only" checked>
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Aimbot Head</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Aimbot Legit</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">No Recoil</span>
                                </label>
                                <div class="h-6"></div>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Fly Hack</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Precision++ (BlackList)</span>
                                </label>
                            </div>

                            <div class="space-y-6">
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Scope 2x</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Scope 4x</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Atravessar</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="tab-aim2" class="tab-content hidden mt-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-24 gap-y-10">
                            <div class="space-y-6">
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Aimbot M82B</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Aimbot M24</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Aimbot AWM-Y</span>
                                </label>
                            </div>
                            <div class="space-y-6">
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Aimbot AWM</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Aimbot VSK</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Switch AWM</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="tab-visuals" class="tab-content hidden mt-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-24 gap-y-10">
                            <div class="space-y-6">
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Camera Hack</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Chams</span>
                                </label>
                            </div>
                            <div class="space-y-6">
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Security</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Stream Mode</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="tab-misc" class="tab-content hidden mt-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-24 gap-y-10">
                            <div class="space-y-6">
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Speed 2x</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">AimFOV</span>
                                </label>
                            </div>
                            <div class="space-y-6">
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Criticals</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer select-none">
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="h-6 w-6 border-2 border-primary-600 peer-checked:bg-primary-600 peer-checked:shadow-[0_0_14px_rgba(239,68,68,0.45)]"></span>
                                    <span class="font-bold tracking-wider text-lg">Bypass Mobile</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script>
        const tabButtons = Array.from(document.querySelectorAll('.tab-btn'));
        const tabContents = Array.from(document.querySelectorAll('.tab-content'));

        function setActiveTab(tabId) {
            tabContents.forEach(el => el.classList.add('hidden'));
            const active = document.getElementById('tab-' + tabId);
            if (active) active.classList.remove('hidden');

            tabButtons.forEach(btn => {
                const isActive = btn.dataset.tab === tabId;
                if (isActive) {
                    btn.classList.remove('bg-primary-600', 'text-white', 'hover:bg-primary-500');
                    btn.classList.add('bg-white', 'text-primary-600', 'shadow-[0_0_22px_rgba(255,255,255,0.35)]');
                } else {
                    btn.classList.remove('bg-white', 'text-primary-600', 'shadow-[0_0_22px_rgba(255,255,255,0.35)]');
                    btn.classList.add('bg-primary-600', 'text-white', 'hover:bg-primary-500', 'shadow-[0_0_22px_rgba(239,68,68,0.25)]');
                }
            });
        }

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => setActiveTab(btn.dataset.tab));
        });

        setActiveTab('aim');
    </script>
</body>
</html>

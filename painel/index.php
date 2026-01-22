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
</head>
<body class="bg-neutral-950 text-neutral-50 min-h-dvh font-sans antialiased">
    <div class="fixed inset-0 pointer-events-none -z-10">
        <div class="absolute inset-0 bg-[radial-gradient(1200px_800px_at_50%_0%,rgba(239,68,68,0.10),transparent_55%)]"></div>
        <div class="absolute inset-0 opacity-[0.16]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2724%27 height=%2724%27 viewBox=%270 0 24 24%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cpath d=%27M11 11H9v2h2v2h2v-2h2v-2h-2V9h-2v2z%27 fill=%27%23ffffff%27 fill-rule=%27evenodd%27/%3E%3C/svg%3E'); background-size: 40px 40px;"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-neutral-950/0 via-neutral-950/0 to-neutral-950"></div>
    </div>

    <div class="min-h-dvh flex flex-col">
        <header class="sticky top-0 z-40 bg-neutral-950/80 backdrop-blur-md border-b border-neutral-800">
            <div class="mx-auto max-w-[440px] px-4 py-4 flex items-center justify-between gap-2">
                <a href="/" class="min-h-12 flex items-center gap-2 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                    <img src="/logo-thunder.png" alt="Thunder Store" class="h-10 w-10 rounded-lg object-contain" decoding="async" fetchpriority="high" />
                    <div class="leading-tight">
                        <div class="font-extrabold text-[18px] tracking-tight">Thunder Store</div>
                        <div class="text-secondary text-neutral-400">Painel VIP</div>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    <div class="min-h-12 px-4 py-2 rounded-full bg-success-500/10 text-success-500 flex items-center gap-2 shadow-e1">
                        <span class="h-2 w-2 rounded-full bg-success-500 animate-pulse"></span>
                        <span class="text-secondary font-semibold">Undetected</span>
                    </div>

                    <button id="menu-btn" class="min-h-12 min-w-12 inline-flex items-center justify-center rounded-lg bg-neutral-900/60 hover:bg-neutral-900 border border-neutral-800 shadow-e1 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" aria-expanded="false" aria-controls="menu-panel" aria-label="Abrir menu">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="menu-panel" class="hidden border-t border-neutral-800 bg-neutral-950/80 backdrop-blur-md">
                <div class="mx-auto max-w-[440px] px-4 py-4 flex items-center justify-between gap-2">
                    <div class="text-secondary text-neutral-400">Conta logada</div>
                    <a href="/api/auth.php?logout=true" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="min-h-12 px-4 inline-flex items-center justify-center rounded-lg bg-primary-600 hover:bg-primary-500 text-neutral-0 font-bold tracking-wide transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-300">
                        Sair
                    </a>
                    <form id="logout-form" action="/api/auth.php" method="POST" style="display: none;">
                        <input type="hidden" name="action" value="logout">
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-[440px] px-4 pt-4 pb-[calc(7rem+env(safe-area-inset-bottom))]">
                <div class="rounded-lg border border-neutral-800 bg-neutral-950/40 shadow-e2 overflow-hidden">
                    <div class="px-4 py-4 border-b border-neutral-800">
                        <h1 class="text-title font-extrabold">Configurações</h1>
                        <p class="text-secondary text-neutral-400 mt-2">Ajuste recursos e preferências do seu painel.</p>
                    </div>

                    <div class="p-4">
                        <div id="tab-aim" class="tab-content" role="tabpanel" aria-labelledby="btn-aim" tabindex="0">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <h2 class="text-subtitle font-bold">AIM</h2>
                                    <p class="text-secondary text-neutral-400 mt-2">Configurações gerais de mira.</p>
                                </div>
                                <span class="text-secondary font-semibold text-primary-400">Geral</span>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-2 min-[390px]:grid-cols-2">
                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Aimbot Head</span>
                                    <input type="checkbox" class="peer sr-only" checked>
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Aimbot Legit</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">No Recoil</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Fly Hack</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50 min-[390px]:col-span-2">
                                    <div>
                                        <div class="text-body font-semibold">Precision++</div>
                                        <div class="text-secondary text-neutral-400 mt-2">BlackList</div>
                                    </div>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Scope 2x</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Scope 4x</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Atravessar</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div id="tab-aim2" class="tab-content hidden" role="tabpanel" aria-labelledby="btn-aim2" tabindex="0">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <h2 class="text-subtitle font-bold">AIM 2</h2>
                                    <p class="text-secondary text-neutral-400 mt-2">Configurações por arma.</p>
                                </div>
                                <span class="text-secondary font-semibold text-primary-400">Armas</span>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-2">
                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Aimbot M82B</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Aimbot M24</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Aimbot AWM-Y</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Aimbot AWM</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Aimbot VSK</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Switch AWM</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div id="tab-visuals" class="tab-content hidden" role="tabpanel" aria-labelledby="btn-visuals" tabindex="0">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <h2 class="text-subtitle font-bold">Visuals</h2>
                                    <p class="text-secondary text-neutral-400 mt-2">Ajustes visuais e segurança.</p>
                                </div>
                                <span class="text-secondary font-semibold text-primary-400">Visual</span>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-2">
                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Camera Hack</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Chams</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Security</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Stream Mode</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div id="tab-misc" class="tab-content hidden" role="tabpanel" aria-labelledby="btn-misc" tabindex="0">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <h2 class="text-subtitle font-bold">Misc</h2>
                                    <p class="text-secondary text-neutral-400 mt-2">Ferramentas e utilitários.</p>
                                </div>
                                <span class="text-secondary font-semibold text-primary-400">Extra</span>
                            </div>

                            <div class="mt-4 rounded-lg border border-neutral-800 bg-neutral-900/30 p-4 shadow-e1">
                                <div class="text-secondary text-neutral-400">Tempo restante</div>
                                <div class="mt-2 text-body font-bold">999 dias</div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-2">
                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Speed 2x</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">AimFOV</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Criticals</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Bypass Mobile</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>

                                <label class="min-h-12 p-4 rounded-lg bg-neutral-900/40 border border-neutral-800 shadow-e1 flex items-center justify-between gap-4 cursor-pointer active:scale-[0.99] transition-transform duration-200 focus-within:ring-2 focus-within:ring-primary-500/50">
                                    <span class="text-body font-semibold">Hide Taskbar</span>
                                    <input type="checkbox" class="peer sr-only">
                                    <span class="relative inline-flex h-7 w-12 items-center rounded-full bg-neutral-800 border border-neutral-700 transition-colors duration-200 peer-checked:bg-primary-600/40 peer-checked:border-primary-600/40 peer-checked:[&>.thumb]:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500">
                                        <span class="thumb absolute left-1 h-5 w-5 rounded-full bg-neutral-0 shadow-e1 transition-transform duration-200"></span>
                                    </span>
                                </label>
                            </div>

                            <div class="mt-4">
                                <button class="min-h-12 w-full px-4 rounded-lg bg-neutral-900/60 hover:bg-neutral-900 border border-neutral-800 shadow-e1 text-neutral-200 font-bold tracking-wide transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                                    Cleaner
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <nav class="fixed inset-x-0 bottom-0 z-50 bg-neutral-950/90 backdrop-blur-md border-t border-neutral-800">
            <div class="mx-auto max-w-[440px] px-2 pt-2 pb-[calc(0.5rem+env(safe-area-inset-bottom))]">
                <div class="grid grid-cols-4 gap-2" role="tablist" aria-label="Navegação do painel">
                    <button onclick="switchTab('aim')" id="btn-aim" class="tab-btn min-h-12 rounded-lg px-2 py-2 text-secondary font-semibold bg-primary-600/15 text-primary-200 hover:bg-neutral-900/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-colors duration-200" role="tab" aria-controls="tab-aim" aria-selected="true">
                        <div class="flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="2" x2="12" y2="6"></line>
                                <line x1="12" y1="18" x2="12" y2="22"></line>
                                <line x1="2" y1="12" x2="6" y2="12"></line>
                                <line x1="18" y1="12" x2="22" y2="12"></line>
                            </svg>
                            <span>AIM</span>
                        </div>
                    </button>

                    <button onclick="switchTab('aim2')" id="btn-aim2" class="tab-btn min-h-12 rounded-lg px-2 py-2 text-secondary font-semibold text-neutral-300 hover:bg-neutral-900/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-colors duration-200" role="tab" aria-controls="tab-aim2" aria-selected="false">
                        <div class="flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 9l-2-2-2 2"></path>
                                <path d="M12 7v10"></path>
                                <path d="M19 12a7 7 0 1 1-14 0a7 7 0 0 1 14 0Z"></path>
                            </svg>
                            <span>AIM 2</span>
                        </div>
                    </button>

                    <button onclick="switchTab('visuals')" id="btn-visuals" class="tab-btn min-h-12 rounded-lg px-2 py-2 text-secondary font-semibold text-neutral-300 hover:bg-neutral-900/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-colors duration-200" role="tab" aria-controls="tab-visuals" aria-selected="false">
                        <div class="flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Visual</span>
                        </div>
                    </button>

                    <button onclick="switchTab('misc')" id="btn-misc" class="tab-btn min-h-12 rounded-lg px-2 py-2 text-secondary font-semibold text-neutral-300 hover:bg-neutral-900/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-colors duration-200" role="tab" aria-controls="tab-misc" aria-selected="false">
                        <div class="flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 1v3"></path>
                                <path d="M12 20v3"></path>
                                <path d="M4.22 4.22l2.12 2.12"></path>
                                <path d="M17.66 17.66l2.12 2.12"></path>
                                <path d="M1 12h3"></path>
                                <path d="M20 12h3"></path>
                                <path d="M4.22 19.78l2.12-2.12"></path>
                                <path d="M17.66 6.34l2.12-2.12"></path>
                                <circle cx="12" cy="12" r="4"></circle>
                            </svg>
                            <span>Misc</span>
                        </div>
                    </button>
                </div>
            </div>
        </nav>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-primary-600/15', 'text-primary-200');
                btn.classList.add('text-neutral-300');
                btn.setAttribute('aria-selected', 'false');
            });

            const tab = document.getElementById('tab-' + tabId);
            if (tab) tab.classList.remove('hidden');

            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.add('bg-primary-600/15', 'text-primary-200');
                activeBtn.classList.remove('text-neutral-300');
                activeBtn.setAttribute('aria-selected', 'true');
            }
        }

        const menuBtn = document.getElementById('menu-btn');
        const menuPanel = document.getElementById('menu-panel');

        if (menuBtn && menuPanel) {
            menuBtn.addEventListener('click', () => {
                const isOpen = !menuPanel.classList.contains('hidden');
                menuPanel.classList.toggle('hidden');
                menuBtn.setAttribute('aria-expanded', String(!isOpen));
            });
        }
    </script>

</body>
</html>

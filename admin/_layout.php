<?php
require_once __DIR__ . '/_init.php';

function admin_nav_items()
{
    return [
        ['href' => '/admin/index.php', 'key' => 'dashboard', 'label' => 'Dashboard'],
        ['href' => '/admin/vendas.php', 'key' => 'vendas', 'label' => 'Vendas'],
        ['href' => '/admin/produtos.php', 'key' => 'produtos', 'label' => 'Produtos'],
        ['href' => '/admin/afiliados.php', 'key' => 'afiliados', 'label' => 'Afiliados'],
        ['href' => '/admin/usuarios.php', 'key' => 'usuarios', 'label' => 'Usuários'],
        ['href' => '/admin/configuracoes.php', 'key' => 'config', 'label' => 'Configurações'],
    ];
}

function render_admin_layout($pageTitle, $activeKey, $contentHtml)
{
    $userName = $_SESSION['user_name'] ?? 'Admin';
    $nav = admin_nav_items();
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo h($pageTitle); ?> | Admin</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: { sans: ['Inter', 'sans-serif'] },
                        colors: {
                            'admin-bg': '#050505',
                            'admin-card': '#0b0b0b',
                            'admin-border': 'rgba(255,255,255,0.08)',
                            'admin-accent': '#dc2626'
                        }
                    }
                }
            }
        </script>
        <link rel="icon" type="image/png" href="/logo-thunder.png" />
    </head>
    <body class="bg-admin-bg text-white min-h-screen">
        <div class="flex min-h-screen">
            <aside class="hidden lg:flex w-72 flex-col border-r border-admin-border bg-black/40 backdrop-blur-md">
                <div class="px-6 py-6 border-b border-admin-border">
                    <a href="/admin/index.php" class="flex items-center gap-3">
                        <img src="/logo-thunder.png" alt="Thunder Store" class="h-10 w-10 rounded-xl border border-admin-border bg-black/60">
                        <div class="leading-tight">
                            <div class="font-black tracking-wide">THUNDER</div>
                            <div class="text-xs text-white/60 font-semibold">Painel Admin</div>
                        </div>
                    </a>
                </div>
                <nav class="px-4 py-4 space-y-2 flex-1 overflow-y-auto">
                    <?php foreach ($nav as $item): ?>
                        <?php
                            $active = ($item['key'] === $activeKey);
                            $cls = $active
                                ? 'bg-admin-accent/15 border-admin-accent text-white'
                                : 'bg-white/0 border-transparent text-white/70 hover:text-white hover:bg-white/5';
                        ?>
                        <a href="<?php echo h($item['href']); ?>"
                           class="block px-4 py-3 rounded-xl border <?php echo $cls; ?> transition">
                            <div class="text-sm font-bold"><?php echo h($item['label']); ?></div>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <div class="px-6 py-5 border-t border-admin-border">
                    <div class="text-xs text-white/60">Logado como</div>
                    <div class="font-bold"><?php echo h($userName); ?></div>
                    <form method="post" action="/admin/logout.php" class="mt-4">
                        <?php echo csrf_input(); ?>
                        <button class="w-full px-4 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-bold text-sm">
                            Sair
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex-1 flex flex-col">
                <header class="sticky top-0 z-30 border-b border-admin-border bg-black/40 backdrop-blur-md">
                    <div class="px-4 sm:px-6 lg:px-10 py-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <button id="mobileMenuBtn" class="lg:hidden p-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="3" y1="12" x2="21" y2="12"></line>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <line x1="3" y1="18" x2="21" y2="18"></line>
                                </svg>
                            </button>
                            <div>
                                <div class="text-lg font-black"><?php echo h($pageTitle); ?></div>
                                <div class="text-xs text-white/60">Admin • Thunder Store</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="/" class="px-4 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-sm font-bold">
                                Ver site
                            </a>
                            <div class="hidden sm:flex items-center gap-3 px-4 py-2 rounded-xl bg-white/5 border border-admin-border">
                                <div class="h-8 w-8 rounded-lg bg-admin-accent/15 border border-admin-border flex items-center justify-center font-black">
                                    <?php echo h(mb_strtoupper(mb_substr($userName, 0, 1))); ?>
                                </div>
                                <div class="leading-tight">
                                    <div class="text-sm font-bold"><?php echo h($userName); ?></div>
                                    <div class="text-xs text-white/60">Administrador</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <div id="mobileMenu" class="lg:hidden hidden border-b border-admin-border bg-black/70 backdrop-blur-md">
                    <div class="px-4 py-4 space-y-2">
                        <?php foreach ($nav as $item): ?>
                            <?php
                                $active = ($item['key'] === $activeKey);
                                $cls = $active
                                    ? 'bg-admin-accent/15 border-admin-accent text-white'
                                    : 'bg-white/0 border-admin-border text-white/80 hover:text-white hover:bg-white/5';
                            ?>
                            <a href="<?php echo h($item['href']); ?>"
                               class="block px-4 py-3 rounded-xl border <?php echo $cls; ?> transition">
                                <div class="text-sm font-bold"><?php echo h($item['label']); ?></div>
                            </a>
                        <?php endforeach; ?>
                        <form method="post" action="/admin/logout.php" class="pt-2">
                            <?php echo csrf_input(); ?>
                            <button class="w-full px-4 py-3 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-bold text-sm">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>

                <main class="flex-1 px-4 sm:px-6 lg:px-10 py-8">
                    <?php echo $contentHtml; ?>
                </main>

                <footer class="px-4 sm:px-6 lg:px-10 py-6 border-t border-admin-border text-xs text-white/50">
                    Thunder Store • Admin
                </footer>
            </div>
        </div>

        <script>
            (function () {
                const btn = document.getElementById('mobileMenuBtn');
                const menu = document.getElementById('mobileMenu');
                if (!btn || !menu) return;
                btn.addEventListener('click', function () {
                    menu.classList.toggle('hidden');
                });
            })();
        </script>
    </body>
    </html>
    <?php
}


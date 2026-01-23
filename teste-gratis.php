<?php
session_start();
require_once __DIR__ . '/api/db.php';

function db_has_table(mysqli $conn, $table)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
}

function app_setting(mysqli $conn, $key, $default = null)
{
    if (!db_has_table($conn, 'app_settings')) return $default;
    $stmt = $conn->prepare("SELECT value FROM app_settings WHERE `key` = ? LIMIT 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? (string)$row['value'] : $default;
}

$slug = trim((string)($_GET['game'] ?? ''));
$productName = '';
if ($slug !== '') {
    $stmt = $conn->prepare("SELECT name FROM products WHERE slug = ? LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $productName = (string)($row['name'] ?? '');
}

$discordInvite = (string)app_setting($conn, 'discord_invite', 'https://discord.gg/hpjCtT7CU7');
$youtubeUrl = 'https://www.youtube.com/@zJohann/videos';

$backUrl = $slug !== '' ? ('/comprar.php?game=' . rawurlencode($slug)) : '/';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Key Grátis (2h) | Thunder Store</title>
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
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <style>
        html, body { touch-action: pan-x pan-y; }
        body { background-color: #000; color: white; font-family: 'Inter', sans-serif; }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
</head>
<body class="bg-black text-white min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <div class="text-3xl md:text-5xl font-black uppercase tracking-wider">Key grátis (2h)</div>
                <div class="text-white/60 font-semibold mt-2">
                    <?php if ($productName !== ''): ?>
                        Teste por 2 horas: <?php echo htmlspecialchars($productName); ?>
                    <?php elseif ($slug !== ''): ?>
                        Teste por 2 horas: <?php echo htmlspecialchars($slug); ?>
                    <?php else: ?>
                        Teste grátis por 2 horas.
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo htmlspecialchars($backUrl); ?>" class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-black text-sm">Voltar</a>
                <a href="/" class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 font-black text-sm">Início</a>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-7 rounded-2xl border border-white/10 bg-white/5 p-6">
                <div class="text-xl font-black">Como conseguir sua key de teste</div>
                <div class="mt-4 rounded-2xl border border-white/10 bg-black/30 p-5">
                    <div class="text-sm font-black tracking-wide uppercase text-white/80">Passo a passo</div>
                    <div class="mt-4 space-y-3 text-white/70 font-semibold leading-relaxed">
                        <div class="flex gap-3">
                            <div class="h-8 w-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white font-black">1</div>
                            <div class="flex-1">
                                Clique no botão do YouTube e <span class="text-white">se inscreva no canal</span>.
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="h-8 w-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white font-black">2</div>
                            <div class="flex-1">
                                Tire um <span class="text-white">print comprovando a inscrição</span>.
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="h-8 w-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white font-black">3</div>
                            <div class="flex-1">
                                Entre no nosso Discord, <span class="text-white">abra um ticket</span> e envie o print no ticket.
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="h-8 w-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white font-black">4</div>
                            <div class="flex-1">
                                Nossa equipe vai validar e enviar sua <span class="text-white">key de 2h</span>.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-amber-500/25 bg-amber-500/10 p-5">
                    <div class="text-amber-200 font-black">Importante</div>
                    <div class="mt-2 text-amber-100/80 font-semibold leading-relaxed">
                        A key de teste é limitada (2h) e é enviada somente após você mandar o print da inscrição no ticket.
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 rounded-2xl border border-white/10 bg-white/5 p-6 h-fit">
                <div class="text-xl font-black">Acessos</div>
                <div class="mt-4 space-y-3">
                    <a href="<?php echo htmlspecialchars($youtubeUrl); ?>" target="_blank" rel="noopener" class="block text-center px-6 py-4 rounded-2xl bg-white text-black hover:bg-gray-200 font-black shadow-[0_0_24px_rgba(255,255,255,0.25)]">
                        Ir para o YouTube (inscrever)
                    </a>
                    <a href="<?php echo htmlspecialchars($discordInvite); ?>" target="_blank" rel="noopener" class="block text-center px-6 py-4 rounded-2xl bg-ff-red hover:bg-red-700 font-black shadow-[0_0_24px_rgba(220,38,38,0.25)]">
                        Ir para o Discord (abrir ticket)
                    </a>
                </div>

                <div class="mt-5 rounded-2xl border border-white/10 bg-black/30 p-5">
                    <div class="text-xs text-white/50 font-black tracking-wide uppercase">Dica</div>
                    <div class="mt-2 text-sm text-white/70 font-semibold leading-relaxed">
                        No ticket, envie o print e informe qual produto você quer testar.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

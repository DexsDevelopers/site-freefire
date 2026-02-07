<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

require_once 'api/db.php';

$userId = (int) $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, username FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: /login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Meu Perfil | Thunder Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap"
        rel="stylesheet">
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
                        sans: ['Chakra Petch', 'sans-serif'],
                        display: ['Rajdhani', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <link rel="stylesheet" href="/assets/popup.css" />
    <style>
        html,
        body {
            touch-action: pan-x pan-y;
        }

        body {
            background-color: #000;
            color: white;
            font-family: 'Inter', sans-serif;
        }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
    <script src="/assets/popup.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="/assets/3d-bg.js" defer></script>
</head>

<body class="bg-black text-white min-h-screen flex flex-col relative">
    <div id="canvas-3d" class="fixed inset-0 w-full h-full z-0 opacity-40 pointer-events-none"></div>
    <!-- Navbar -->
    <nav
        class="bg-black/80 backdrop-blur-md border-b border-white/10 fixed w-full z-50 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 pointer-events-none opacity-20"
            style="background-image: url('data:image/svg+xml,%3Csvg width=%2724%27 height=%2724%27 viewBox=%270 0 24 24%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cpath d=%27M11 11H9v2h2v2h2v-2h2v-2h-2V9h-2v2z%27 fill=%27%23ffffff%27 fill-rule=%27evenodd%27/%3E%3C/svg%3E'); background-size: 42px 42px;">
        </div>
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-b from-white/5 via-transparent to-transparent">
        </div>
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-6">
                    <a href="/"
                        class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-sm font-bold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Voltar
                    </a>
                    <a href="/" class="flex-shrink-0">
                        <img src="/logo-thunder.png" alt="Thunder Store" class="h-10 w-auto object-contain">
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/painel"
                        class="hidden sm:block px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider">Acessar
                        Painel</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex-grow flex items-center justify-center pt-24 px-4 pb-12">
        <div class="max-w-2xl w-full">
            <div
                class="bg-zinc-900/80 border border-white/10 rounded-2xl p-8 shadow-2xl backdrop-blur-md relative overflow-hidden">
                <!-- Decor -->
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-red-600/10 rounded-full blur-[80px] pointer-events-none">
                </div>

                <div class="relative z-10">
                    <div class="flex flex-col items-center mb-8">
                        <div
                            class="w-24 h-24 bg-gradient-to-br from-red-600 to-orange-600 rounded-full flex items-center justify-center text-4xl font-black shadow-[0_0_30px_rgba(220,38,38,0.4)] mb-4">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                        <h1 class="text-3xl font-black">
                            <?php echo htmlspecialchars($user['name']); ?>
                        </h1>
                        <p class="text-gray-400 text-sm font-mono mt-1">@
                            <?php echo htmlspecialchars($user['username'] ?? 'usuario'); ?>
                        </p>
                    </div>

                    <form id="profileForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-400 mb-2">Nome Completo</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"
                                    required
                                    class="w-full px-4 py-3 bg-black/50 border border-white/10 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-400 mb-2">Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                                    required
                                    class="w-full px-4 py-3 bg-black/50 border border-white/10 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                            </div>
                        </div>

                        <div class="pt-6 border-t border-white/5">
                            <h3 class="text-lg font-bold text-white mb-4">Alterar Senha</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-400 mb-2">Nova Senha</label>
                                    <input type="password" name="password" placeholder="Deixe em branco para manter"
                                        class="w-full px-4 py-3 bg-black/50 border border-white/10 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-400 mb-2">Confirmar Senha</label>
                                    <input type="password" name="password_confirm" placeholder="Repita a nova senha"
                                        class="w-full px-4 py-3 bg-black/50 border border-white/10 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 flex justify-end gap-3">
                            <button type="submit"
                                class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-black rounded-xl shadow-lg hover:shadow-red-900/20 transition-all transform hover:-translate-y-1">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('profileForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const pass = formData.get('password');
            const confirm = formData.get('password_confirm');

            if (pass && pass !== confirm) {
                if (window.ThunderPopup) window.ThunderPopup.toast('error', 'As senhas não coincidem.');
                else alert('As senhas não coincidem.');
                return;
            }

            formData.append('action', 'update_profile');

            try {
                const response = await fetch('/api/auth.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    if (window.ThunderPopup) window.ThunderPopup.toast('success', data.message);
                    else alert(data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    if (window.ThunderPopup) window.ThunderPopup.toast('error', data.message);
                    else alert(data.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                if (window.ThunderPopup) window.ThunderPopup.toast('error', 'Erro de conexão.');
            }
        });
    </script>
</body>

</html>
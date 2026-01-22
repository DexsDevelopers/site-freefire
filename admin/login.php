<?php
require_once __DIR__ . '/_init.php';

if (!empty($_SESSION['is_admin']) && !empty($_SESSION['user_id'])) {
    redirect_to('/admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    if (!db_table_has_column($conn, 'users', 'role')) {
        $error = 'Banco desatualizado. Rode /setup_db.php para atualizar o schema.';
    } else {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $error = 'Preencha email e senha.';
        } else {
            $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();

            if (!$row || !password_verify($password, $row['password'])) {
                $error = 'Credenciais inválidas.';
            } elseif (($row['role'] ?? '') !== 'admin') {
                $error = 'Este usuário não tem permissão de administrador.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$row['id'];
                $_SESSION['user_name'] = (string)$row['name'];
                $_SESSION['user_role'] = 'admin';
                $_SESSION['is_admin'] = true;
                redirect_to('/admin/index.php');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Thunder Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { 'admin-accent': '#dc2626', 'admin-border': 'rgba(255,255,255,0.08)' }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="rounded-2xl border border-admin-border bg-white/5 backdrop-blur-md p-8 shadow-2xl">
            <div class="flex items-center gap-3 mb-8">
                <img src="/logo-thunder.png" alt="Thunder Store" class="h-12 w-12 rounded-xl border border-admin-border bg-black/40">
                <div class="leading-tight">
                    <div class="text-xl font-black">Painel Admin</div>
                    <div class="text-xs text-white/60 font-semibold">Acesso restrito</div>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="mb-5 rounded-xl border border-admin-accent/40 bg-admin-accent/10 px-4 py-3 text-sm text-red-200">
                    <?php echo h($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-5">
                <?php echo csrf_input(); ?>
                <div>
                    <label class="block text-sm font-bold text-white/80 mb-2">Email</label>
                    <input name="email" type="email" required autocomplete="email"
                           class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border focus:outline-none focus:ring-2 focus:ring-admin-accent/40 focus:border-admin-accent/60"
                           placeholder="admin@dominio.com">
                </div>
                <div>
                    <label class="block text-sm font-bold text-white/80 mb-2">Senha</label>
                    <input name="password" type="password" required autocomplete="current-password"
                           class="w-full px-4 py-3 rounded-xl bg-black/40 border border-admin-border focus:outline-none focus:ring-2 focus:ring-admin-accent/40 focus:border-admin-accent/60"
                           placeholder="••••••••">
                </div>
                <button class="w-full px-4 py-3 rounded-xl bg-admin-accent hover:bg-red-700 font-black tracking-wide">
                    Entrar
                </button>
                <div class="text-xs text-white/50">
                    Dica: o usuário precisa estar com role=admin no banco.
                </div>
            </form>
        </div>
        <div class="mt-6 text-center text-xs text-white/40">
            <a href="/" class="hover:text-white">Voltar ao site</a>
        </div>
    </div>
</body>
</html>


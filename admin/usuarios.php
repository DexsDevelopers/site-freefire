<?php
require_once __DIR__ . '/_layout.php';
require_admin($conn);

if (!db_table_has_column($conn, 'users', 'role')) {
    ob_start();
    ?>
    <div class="rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="text-lg font-black">Usuários</div>
        <div class="text-sm text-white/60 mt-2">Banco desatualizado. Rode <span class="font-bold">/setup_db.php</span>.</div>
    </div>
    <?php
    $content = ob_get_clean();
    render_admin_layout('Usuários', 'usuarios', $content);
    exit;
}

$flash = ['type' => '', 'message' => ''];
$roles = ['user', 'admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'update_role') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = (string)($_POST['role'] ?? '');

        if ($userId <= 0 || !in_array($role, $roles, true)) {
            $flash = ['type' => 'error', 'message' => 'Dados inválidos.'];
        } else {
            $stmt = $conn->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $cur = $stmt->get_result()->fetch_assoc();
            $curRole = (string)($cur['role'] ?? 'user');

            if ($curRole === 'admin' && $role !== 'admin') {
                $res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'admin'");
                $admins = $res ? (int)$res->fetch_assoc()['c'] : 0;
                if ($admins <= 1) {
                    $flash = ['type' => 'error', 'message' => 'Não é possível remover o último admin.'];
                }
            }

            if ($flash['message'] === '') {
                $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->bind_param("si", $role, $userId);
                if ($stmt->execute()) {
                    $flash = ['type' => 'success', 'message' => 'Permissão atualizada.'];
                } else {
                    $flash = ['type' => 'error', 'message' => 'Não foi possível atualizar.'];
                }
            }
        }
    }
}

$q = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;

$whereSql = '';
$types = '';
$params = [];
if ($q !== '') {
    if (ctype_digit($q)) {
        $whereSql = "WHERE u.id = ?";
        $types = 'i';
        $params = [(int)$q];
    } else {
        $whereSql = "WHERE (u.email LIKE ? OR u.name LIKE ?)";
        $types = 'ss';
        $params = ['%' . $q . '%', '%' . $q . '%'];
    }
}

$countSql = "SELECT COUNT(*) AS c FROM users u $whereSql";
$stmt = $conn->prepare($countSql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$totalRows = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$hasReferredBy = db_table_has_column($conn, 'users', 'referred_by');
$selectReferred = $hasReferredBy ? 'u.referred_by' : 'NULL AS referred_by';

$listSql = "SELECT u.id, u.name, u.email, u.role, $selectReferred, u.created_at,
            (SELECT COUNT(*) FROM affiliate_accounts a WHERE a.user_id = u.id) AS is_affiliate
            FROM users u
            $whereSql
            ORDER BY u.id DESC
            LIMIT ? OFFSET ?";
$stmt = $conn->prepare($listSql);
if ($types === '') {
    $stmt->bind_param("ii", $perPage, $offset);
} else {
    $types2 = $types . 'ii';
    $params2 = array_merge($params, [$perPage, $offset]);
    $stmt->bind_param($types2, ...$params2);
}
$stmt->execute();
$users = $stmt->get_result();

ob_start();
?>
<div class="rounded-2xl border border-admin-border bg-white/5 p-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <div class="text-lg font-black">Usuários</div>
            <div class="text-xs text-white/50 mt-1"><?php echo h($totalRows); ?> usuários encontrados</div>
        </div>
        <form method="get" class="flex flex-col sm:flex-row gap-3 sm:items-end">
            <div>
                <label class="block text-xs font-bold text-white/60 mb-2">Busca (ID, nome ou email)</label>
                <input name="q" value="<?php echo h($q); ?>" class="w-full sm:w-72 px-4 py-3 rounded-xl bg-black/30 border border-admin-border focus:outline-none focus:ring-2 focus:ring-admin-accent/40 focus:border-admin-accent/60" placeholder="ex: 12 ou email">
            </div>
            <div class="flex gap-2">
                <button class="px-5 py-3 rounded-xl bg-admin-accent hover:bg-red-700 font-black">Filtrar</button>
                <a href="/admin/usuarios.php" class="px-5 py-3 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-black">Limpar</a>
            </div>
        </form>
    </div>

    <?php if ($flash['message'] !== ''): ?>
        <?php
            $cls = $flash['type'] === 'success'
                ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                : 'border-admin-accent/40 bg-admin-accent/10 text-red-200';
        ?>
        <div class="mt-5 rounded-xl border px-4 py-3 text-sm <?php echo $cls; ?>">
            <?php echo h($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-white/60">
            <tr class="border-b border-admin-border">
                <th class="text-left py-3 pr-4">#</th>
                <th class="text-left py-3 pr-4">Nome</th>
                <th class="text-left py-3 pr-4">Email</th>
                <th class="text-left py-3 pr-4">Role</th>
                <th class="text-left py-3 pr-4">Afiliado</th>
                <th class="text-left py-3 pr-4">Criado</th>
                <th class="text-right py-3">Ações</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-admin-border">
            <?php if ($users && $users->num_rows > 0): ?>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td class="py-3 pr-4 font-bold"><?php echo h($u['id']); ?></td>
                        <td class="py-3 pr-4 font-bold"><?php echo h($u['name']); ?></td>
                        <td class="py-3 pr-4 text-white/70"><?php echo h($u['email']); ?></td>
                        <td class="py-3 pr-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border border-admin-border bg-black/30"><?php echo h($u['role']); ?></span>
                        </td>
                        <td class="py-3 pr-4">
                            <?php if ((int)$u['is_affiliate'] > 0): ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold border border-admin-border bg-emerald-500/10 text-emerald-200">Sim</span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold border border-admin-border bg-black/30 text-white/70">Não</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 pr-4 text-white/70"><?php echo h($u['created_at']); ?></td>
                        <td class="py-3 text-right">
                            <form method="post" class="inline-flex items-center gap-2">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="action" value="update_role">
                                <input type="hidden" name="user_id" value="<?php echo h($u['id']); ?>">
                                <select name="role" class="px-3 py-2 rounded-xl bg-black/30 border border-admin-border text-xs font-black">
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?php echo h($r); ?>" <?php echo (($u['role'] ?? 'user') === $r) ? 'selected' : ''; ?>><?php echo h($r); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="px-4 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black">Salvar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="py-10 text-center text-white/60">Nenhum usuário.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex items-center justify-between gap-3 flex-wrap">
        <div class="text-xs text-white/50">Página <?php echo h($page); ?> de <?php echo h($totalPages); ?></div>
        <div class="flex items-center gap-2">
            <?php
                $base = $_GET;
                unset($base['page']);
                $prev = max(1, $page - 1);
                $next = min($totalPages, $page + 1);
            ?>
            <a class="px-4 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black <?php echo ($page <= 1) ? 'pointer-events-none opacity-40' : ''; ?>"
               href="<?php echo h('/admin/usuarios.php?' . http_build_query(array_merge($base, ['page' => $prev]))); ?>">
                Anterior
            </a>
            <a class="px-4 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black <?php echo ($page >= $totalPages) ? 'pointer-events-none opacity-40' : ''; ?>"
               href="<?php echo h('/admin/usuarios.php?' . http_build_query(array_merge($base, ['page' => $next]))); ?>">
                Próxima
            </a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
render_admin_layout('Usuários', 'usuarios', $content);

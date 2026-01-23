<?php
require_once __DIR__ . '/_layout.php';
require_admin($conn);

$productStatusOptions = ['Ativo', 'Manutencao', 'Inativo'];
$flash = ['type' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'save_product') {
        $id = (int)($_POST['id'] ?? 0);
        $slug = trim((string)($_POST['slug'] ?? ''));
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $image_url = trim((string)($_POST['image_url'] ?? ''));
        $status = trim((string)($_POST['status'] ?? 'Ativo'));
        $features = trim((string)($_POST['features'] ?? ''));

        if ($slug === '' || $name === '' || !preg_match('/^[a-z0-9\-]{2,50}$/', $slug) || !in_array($status, $productStatusOptions, true)) {
            $flash = ['type' => 'error', 'message' => 'Preencha slug (a-z, 0-9, -), nome e status válidos.'];
        } else {
            if ($id > 0) {
                $stmt = $conn->prepare("UPDATE products SET slug=?, name=?, description=?, image_url=?, status=?, features=? WHERE id=?");
                $stmt->bind_param("ssssssi", $slug, $name, $description, $image_url, $status, $features, $id);
                $ok = $stmt->execute();
                $flash = $ok ? ['type' => 'success', 'message' => 'Produto atualizado.'] : ['type' => 'error', 'message' => 'Não foi possível atualizar o produto.'];
            } else {
                $stmt = $conn->prepare("INSERT INTO products (slug,name,description,image_url,status,features) VALUES (?,?,?,?,?,?)");
                $stmt->bind_param("ssssss", $slug, $name, $description, $image_url, $status, $features);
                $ok = $stmt->execute();
                $flash = $ok ? ['type' => 'success', 'message' => 'Produto criado.'] : ['type' => 'error', 'message' => 'Não foi possível criar o produto (slug pode estar duplicado).'];
            }
        }
    }

    if ($action === 'delete_product') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $flash = ['type' => 'error', 'message' => 'Produto inválido.'];
        } else {
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $id);
            $ok = $stmt->execute();
            $flash = $ok ? ['type' => 'success', 'message' => 'Produto removido.'] : ['type' => 'error', 'message' => 'Não foi possível remover o produto.'];
        }
    }

    if ($action === 'save_plan') {
        $id = (int)($_POST['id'] ?? 0);
        $product_id = (int)($_POST['product_id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $price = (float)($_POST['price'] ?? 0);

        if ($product_id <= 0 || $name === '' || $price <= 0) {
            $flash = ['type' => 'error', 'message' => 'Preencha plano e preço válidos.'];
        } else {
            if ($id > 0) {
                $stmt = $conn->prepare("UPDATE plans SET name=?, price=? WHERE id=? AND product_id=?");
                $stmt->bind_param("sdii", $name, $price, $id, $product_id);
                $ok = $stmt->execute();
                $flash = $ok ? ['type' => 'success', 'message' => 'Plano atualizado.'] : ['type' => 'error', 'message' => 'Não foi possível atualizar o plano.'];
            } else {
                $stmt = $conn->prepare("INSERT INTO plans (product_id,name,price) VALUES (?,?,?)");
                $stmt->bind_param("isd", $product_id, $name, $price);
                $ok = $stmt->execute();
                $flash = $ok ? ['type' => 'success', 'message' => 'Plano criado.'] : ['type' => 'error', 'message' => 'Não foi possível criar o plano (nome pode estar duplicado no produto).'];
            }
        }
    }

    if ($action === 'delete_plan') {
        $id = (int)($_POST['id'] ?? 0);
        $product_id = (int)($_POST['product_id'] ?? 0);
        if ($id <= 0 || $product_id <= 0) {
            $flash = ['type' => 'error', 'message' => 'Plano inválido.'];
        } else {
            $stmt = $conn->prepare("DELETE FROM plans WHERE id=? AND product_id=?");
            $stmt->bind_param("ii", $id, $product_id);
            $ok = $stmt->execute();
            $flash = $ok ? ['type' => 'success', 'message' => 'Plano removido.'] : ['type' => 'error', 'message' => 'Não foi possível remover o plano.'];
        }
    }
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");

$editProductId = (int)($_GET['edit_product'] ?? 0);
$activeProductId = (int)($_GET['product_id'] ?? 0);
$editingProduct = null;
$activeProduct = null;
$plans = null;
$editPlanId = (int)($_GET['edit_plan'] ?? 0);
$editingPlan = null;

if ($editProductId > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $editProductId);
    $stmt->execute();
    $editingProduct = $stmt->get_result()->fetch_assoc();
}

if ($activeProductId > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $activeProductId);
    $stmt->execute();
    $activeProduct = $stmt->get_result()->fetch_assoc();

    $stmt = $conn->prepare("SELECT * FROM plans WHERE product_id = ? ORDER BY price ASC");
    $stmt->bind_param("i", $activeProductId);
    $stmt->execute();
    $plans = $stmt->get_result();

    if ($editPlanId > 0) {
        $stmt = $conn->prepare("SELECT * FROM plans WHERE id = ? AND product_id = ? LIMIT 1");
        $stmt->bind_param("ii", $editPlanId, $activeProductId);
        $stmt->execute();
        $editingPlan = $stmt->get_result()->fetch_assoc();
    }
}

ob_start();
?>
<?php if ($flash['message'] !== ''): ?>
    <?php
        $cls = $flash['type'] === 'success'
            ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
            : 'border-admin-accent/40 bg-admin-accent/10 text-red-200';
    ?>
    <div class="mb-5 rounded-xl border px-4 py-3 text-sm <?php echo $cls; ?>">
        <?php echo h($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
    <div class="rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <div class="text-lg font-black">Produtos</div>
                <div class="text-xs text-white/50 mt-1">Crie e edite produtos (slug, status, imagem e features)</div>
            </div>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-white/60">
                <tr class="border-b border-admin-border">
                    <th class="text-left py-3 pr-4">Nome</th>
                    <th class="text-left py-3 pr-4">Slug</th>
                    <th class="text-left py-3 pr-4">Status</th>
                    <th class="text-right py-3">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                <?php if ($products && $products->num_rows > 0): ?>
                    <?php while ($p = $products->fetch_assoc()): ?>
                        <tr>
                            <td class="py-3 pr-4 font-bold"><?php echo h($p['name']); ?></td>
                            <td class="py-3 pr-4 text-white/70"><?php echo h($p['slug']); ?></td>
                            <td class="py-3 pr-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border border-admin-border bg-black/30"><?php echo h($p['status']); ?></span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a class="px-3 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black"
                                       href="<?php echo h('/admin/produtos.php?' . http_build_query(['edit_product' => $p['id'], 'product_id' => $p['id']])); ?>">
                                        Editar
                                    </a>
                                    <a class="px-3 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black"
                                       href="<?php echo h('/admin/produtos.php?' . http_build_query(['product_id' => $p['id']])); ?>">
                                        Planos
                                    </a>
                                    <form method="post"
                                          data-confirm="Remover produto e todos os planos?"
                                          data-confirm-title="Excluir produto"
                                          data-confirm-ok="Excluir"
                                          data-confirm-cancel="Cancelar"
                                          data-confirm-danger="1">
                                        <?php echo csrf_input(); ?>
                                        <input type="hidden" name="action" value="delete_product">
                                        <input type="hidden" name="id" value="<?php echo h($p['id']); ?>">
                                        <button class="px-3 py-2 rounded-xl bg-admin-accent hover:bg-red-700 text-xs font-black">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="py-8 text-center text-white/60">Nenhum produto.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-8 border-t border-admin-border pt-6">
            <div class="text-sm font-black"><?php echo $editingProduct ? 'Editar produto' : 'Novo produto'; ?></div>
            <form method="post" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="action" value="save_product">
                <input type="hidden" name="id" value="<?php echo h($editingProduct['id'] ?? 0); ?>">
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2">Slug</label>
                    <input name="slug" value="<?php echo h($editingProduct['slug'] ?? ''); ?>" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold" placeholder="ex: freefire">
                </div>
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2">Nome</label>
                    <input name="name" value="<?php echo h($editingProduct['name'] ?? ''); ?>" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold" placeholder="ex: FREE FIRE">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-white/60 mb-2">Descrição</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold"><?php echo h($editingProduct['description'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2">Imagem (URL)</label>
                    <input name="image_url" value="<?php echo h($editingProduct['image_url'] ?? ''); ?>" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold" placeholder="/img/freefire.jpg">
                </div>
                <div>
                    <label class="block text-xs font-bold text-white/60 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold">
                        <?php foreach ($productStatusOptions as $s): ?>
                            <option value="<?php echo h($s); ?>" <?php echo (($editingProduct['status'] ?? 'Ativo') === $s) ? 'selected' : ''; ?>><?php echo h($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-white/60 mb-2">Features (separado por |)</label>
                    <textarea name="features" rows="2" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold" placeholder="Chams|Aimbot|No Recoil"><?php echo h($editingProduct['features'] ?? ''); ?></textarea>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button class="px-5 py-3 rounded-xl bg-admin-accent hover:bg-red-700 font-black"><?php echo $editingProduct ? 'Salvar alterações' : 'Criar produto'; ?></button>
                    <a href="/admin/produtos.php" class="px-5 py-3 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-black">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-admin-border bg-white/5 p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <div class="text-lg font-black">Planos</div>
                <div class="text-xs text-white/50 mt-1">
                    <?php echo $activeProduct ? ('Gerenciando: ' . h($activeProduct['name'])) : 'Selecione um produto para gerenciar planos'; ?>
                </div>
            </div>
        </div>

        <?php if ($activeProduct): ?>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-white/60">
                    <tr class="border-b border-admin-border">
                        <th class="text-left py-3 pr-4">Nome</th>
                        <th class="text-left py-3 pr-4">Preço</th>
                        <th class="text-right py-3">Ações</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-admin-border">
                    <?php if ($plans && $plans->num_rows > 0): ?>
                        <?php while ($pl = $plans->fetch_assoc()): ?>
                            <tr>
                                <td class="py-3 pr-4 font-bold"><?php echo h($pl['name']); ?></td>
                                <td class="py-3 pr-4 font-black">R$ <?php echo h(number_format((float)$pl['price'], 2, ',', '.')); ?></td>
                                <td class="py-3 text-right">
                                    <div class="flex gap-2 justify-end">
                                        <a class="px-3 py-2 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 text-xs font-black"
                                           href="<?php echo h('/admin/produtos.php?' . http_build_query(['product_id' => $activeProductId, 'edit_plan' => $pl['id']])); ?>">
                                            Editar
                                        </a>
                                        <form method="post"
                                              data-confirm="Remover plano?"
                                              data-confirm-title="Excluir plano"
                                              data-confirm-ok="Excluir"
                                              data-confirm-cancel="Cancelar"
                                              data-confirm-danger="1">
                                            <?php echo csrf_input(); ?>
                                            <input type="hidden" name="action" value="delete_plan">
                                            <input type="hidden" name="product_id" value="<?php echo h($activeProductId); ?>">
                                            <input type="hidden" name="id" value="<?php echo h($pl['id']); ?>">
                                            <button class="px-3 py-2 rounded-xl bg-admin-accent hover:bg-red-700 text-xs font-black">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="py-8 text-center text-white/60">Nenhum plano.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 border-t border-admin-border pt-6">
                <div class="text-sm font-black"><?php echo $editingPlan ? 'Editar plano' : 'Novo plano'; ?></div>
                <form method="post" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="action" value="save_plan">
                    <input type="hidden" name="product_id" value="<?php echo h($activeProductId); ?>">
                    <input type="hidden" name="id" value="<?php echo h($editingPlan['id'] ?? 0); ?>">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-white/60 mb-2">Nome</label>
                        <input name="name" value="<?php echo h($editingPlan['name'] ?? ''); ?>" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold" placeholder="ex: Mensal">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-white/60 mb-2">Preço</label>
                        <input name="price" type="number" step="0.01" min="0" value="<?php echo h($editingPlan['price'] ?? ''); ?>" class="w-full px-4 py-3 rounded-xl bg-black/30 border border-admin-border font-bold" placeholder="ex: 60.00">
                    </div>
                    <div class="md:col-span-3 flex gap-2">
                        <button class="px-5 py-3 rounded-xl bg-admin-accent hover:bg-red-700 font-black"><?php echo $editingPlan ? 'Salvar alterações' : 'Criar plano'; ?></button>
                        <a href="<?php echo h('/admin/produtos.php?' . http_build_query(['product_id' => $activeProductId])); ?>" class="px-5 py-3 rounded-xl bg-white/5 border border-admin-border hover:bg-white/10 font-black">Limpar</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="mt-6 rounded-2xl border border-admin-border bg-black/20 p-5 text-sm text-white/60">
                Abra um produto na coluna “Produtos” para gerenciar os planos.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
render_admin_layout('Produtos', 'produtos', $content);

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Buffer de saída para capturar erros antes do HTML
$debug_log = [];
$debug_log[] = "Script iniciado.";

// Verifica extensões
if (!extension_loaded('mysqli')) {
    die("ERRO CRÍTICO: A extensão MySQLi do PHP não está carregada.");
}

// Tenta incluir a conexão
try {
    if (!file_exists('api/db.php')) {
        throw new Exception("Arquivo api/db.php não encontrado.");
    }
    require_once 'api/db.php';
    $debug_log[] = "Conexão com banco de dados estabelecida.";
} catch (Exception $e) {
    die("Erro na conexão: " . $e->getMessage());
}

$sql_file = 'database.sql';
$import_status = "";
$import_details = "";
$generated_admin = null;

function db_has_column(mysqli $conn, $table, $column)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
}

function db_has_table(mysqli $conn, $table)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)($row['c'] ?? 0)) > 0;
}

function ensure_admin_schema(mysqli $conn)
{
    if (db_has_column($conn, 'users', 'role') === false) {
        $conn->query("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user'");
    }
    if (db_has_column($conn, 'users', 'referred_by') === false) {
        $conn->query("ALTER TABLE users ADD COLUMN referred_by INT NULL");
        $conn->query("ALTER TABLE users ADD CONSTRAINT fk_users_referred_by FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL");
    }
    if (db_has_column($conn, 'orders', 'affiliate_user_id') === false) {
        $conn->query("ALTER TABLE orders ADD COLUMN affiliate_user_id INT NULL");
        $conn->query("ALTER TABLE orders ADD CONSTRAINT fk_orders_affiliate_user FOREIGN KEY (affiliate_user_id) REFERENCES users(id) ON DELETE SET NULL");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS app_settings (
        `key` VARCHAR(100) PRIMARY KEY,
        `value` TEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS affiliate_accounts (
        user_id INT PRIMARY KEY,
        code VARCHAR(32) NOT NULL UNIQUE,
        commission_rate DECIMAL(5,4) NOT NULL DEFAULT 0.1000,
        status VARCHAR(20) NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS affiliate_clicks (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        affiliate_user_id INT NOT NULL,
        code VARCHAR(32) NOT NULL,
        ip_hash CHAR(64) NOT NULL,
        user_agent VARCHAR(255) NULL,
        referer VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_aff_clicks_user (affiliate_user_id),
        INDEX idx_aff_clicks_code (code),
        FOREIGN KEY (affiliate_user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS affiliate_referrals (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        affiliate_user_id INT NOT NULL,
        referred_user_id INT NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_aff_ref_user (affiliate_user_id),
        FOREIGN KEY (affiliate_user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (referred_user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS affiliate_commissions (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        affiliate_user_id INT NOT NULL,
        order_id INT NOT NULL UNIQUE,
        amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_aff_comm_user (affiliate_user_id),
        FOREIGN KEY (affiliate_user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS affiliate_payouts (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        affiliate_user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        method VARCHAR(50) NOT NULL DEFAULT 'pix',
        destination VARCHAR(255) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'requested',
        note VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        processed_at TIMESTAMP NULL,
        INDEX idx_aff_payout_user (affiliate_user_id),
        INDEX idx_aff_payout_status (status),
        FOREIGN KEY (affiliate_user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $defaults = [
        ['affiliate_default_rate', '0.10'],
        ['affiliate_payout_min', '50.00'],
    ];
    foreach ($defaults as $row) {
        $stmt = $conn->prepare("INSERT IGNORE INTO app_settings (`key`,`value`) VALUES (?,?)");
        $stmt->bind_param("ss", $row[0], $row[1]);
        $stmt->execute();
    }
}

function ensure_first_admin(mysqli $conn)
{
    if (!db_has_column($conn, 'users', 'role')) {
        return null;
    }
    $res = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        return null;
    }
    $email = 'admin@thunder.local';
    $password_plain = bin2hex(random_bytes(6));
    $name = 'Administrador';
    $hash = password_hash($password_plain, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,'admin') ON DUPLICATE KEY UPDATE role='admin'");
    $stmt->bind_param("sss", $name, $email, $hash);
    $stmt->execute();
    return ['email' => $email, 'password' => $password_plain];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['run'])) {
    if (!file_exists($sql_file)) {
        $import_status = "error";
        $import_details = "Arquivo $sql_file não encontrado.";
    } else {
        $sql = file_get_contents($sql_file);
        if (!$sql) {
            $import_status = "error";
            $import_details = "Arquivo $sql_file está vazio ou não pode ser lido.";
        } else {
            // Limpa comentários do SQL para evitar problemas
            $lines = explode("\n", $sql);
            $clean_sql = "";
            foreach ($lines as $line) {
                if (substr(trim($line), 0, 2) == '--' || $line == '') continue;
                $clean_sql .= $line . "\n";
            }
            
            // Tenta executar
            if ($conn->multi_query($sql)) {
                $results = 0;
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                    $results++;
                } while ($conn->more_results() && $conn->next_result());
                
                $import_status = "success";
                ensure_admin_schema($conn);
                $generated_admin = ensure_first_admin($conn);
                $import_details = "Importação concluída! $results comandos processados.";
                if ($generated_admin) {
                    $import_details .= " Admin criado: " . $generated_admin['email'] . " senha: " . $generated_admin['password'];
                }
            } else {
                $import_status = "error";
                $import_details = "Erro MySQL: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Setup Database | Thunder Store</title>
    <style>
        html, body { touch-action: pan-x pan-y; }
        body { background-color: #111; color: #eee; font-family: monospace; padding: 20px; line-height: 1.5; }
        .container { max-width: 800px; margin: 0 auto; background: #222; padding: 20px; border-radius: 8px; border: 1px solid #333; }
        h1 { color: #fff; border-bottom: 1px solid #444; padding-bottom: 10px; }
        .btn { display: inline-block; padding: 10px 20px; background: #dc2626; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; border: none; cursor: pointer; }
        .btn:hover { background: #b91c1c; }
        .status { padding: 15px; margin: 20px 0; border-radius: 4px; }
        .success { background: #064e3b; color: #a7f3d0; border: 1px solid #059669; }
        .error { background: #7f1d1d; color: #fecaca; border: 1px solid #dc2626; }
        .debug { background: #333; padding: 10px; font-size: 12px; border: 1px solid #444; margin-top: 20px; }
        #php-check { color: red; font-weight: bold; font-size: 1.2em; border: 2px dashed red; padding: 20px; text-align: center; background: #fff; }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
</head>
<body>
    
    <!-- Este elemento só aparece se o PHP NÃO rodar -->
    <div id="php-check">
        ERRO: O PHP NÃO ESTÁ SENDO EXECUTADO!<br>
        Se você está vendo esta mensagem, seu servidor não está processando arquivos .php.<br>
        Verifique se você está acessando via http://localhost/... ou pelo domínio do site, e não abrindo o arquivo direto no navegador.
    </div>
    
    <!-- Esconde o alerta acima se o PHP rodar -->
    <?php echo '<style>#php-check { display: none !important; }</style>'; ?>

    <div class="container">
        <h1>Configuração do Banco de Dados</h1>
        
        <p>Este script irá importar as tabelas e dados iniciais para o banco de dados configurado em <code>api/db.php</code>.</p>
        
        <?php if ($import_status === 'success'): ?>
            <div class="status success">
                <strong>SUCESSO:</strong> <?php echo htmlspecialchars($import_details); ?>
            </div>
            <p>Agora você pode apagar este arquivo e acessar o site.</p>
            <a href="/" class="btn" style="background: #2563eb;">Ir para o Site</a>
        <?php elseif ($import_status === 'error'): ?>
            <div class="status error">
                <strong>ERRO:</strong> <?php echo htmlspecialchars($import_details); ?>
            </div>
            <form method="post">
                <button type="submit" class="btn">Tentar Novamente</button>
            </form>
        <?php else: ?>
            <form method="post">
                <button type="submit" class="btn">Iniciar Importação</button>
            </form>
        <?php endif; ?>

        <div class="debug">
            <strong>Log de Debug:</strong><br>
            <?php foreach($debug_log as $log): ?>
                - <?php echo htmlspecialchars($log); ?><br>
            <?php endforeach; ?>
            - DB Host: <?php echo htmlspecialchars($db_host); ?><br>
            - DB User: <?php echo htmlspecialchars($db_user); ?><br>
            - DB Name: <?php echo htmlspecialchars($db_name); ?><br>
        </div>
    </div>
</body>
</html>

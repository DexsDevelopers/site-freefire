<?php
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if ($secure) {
    ini_set('session.cookie_secure', '1');
}
session_start();

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

require_once 'db.php';

header('Content-Type: application/json');

$hasRole = false;
$hasReferredBy = false;
$hasAffiliateTables = false;

function db_has_column(mysqli $conn, $table, $column)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int) ($row['c'] ?? 0)) > 0;
}

function db_has_table(mysqli $conn, $table)
{
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int) ($row['c'] ?? 0)) > 0;
}

$hasRole = db_has_column($conn, 'users', 'role');
$hasReferredBy = db_has_column($conn, 'users', 'referred_by');
$hasAffiliateTables = db_has_table($conn, 'affiliate_accounts') && db_has_table($conn, 'affiliate_referrals');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'register') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
            exit;
        }

        // Verifica se email já existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email já cadastrado.']);
            exit;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $affiliate_user_id = (int) ($_SESSION['affiliate_user_id'] ?? 0);

        // Verifica existência da coluna username
        $hasUsername = db_has_column($conn, 'users', 'username');

        // Contrução dinâmica do INSERT
        $cols = ['name', 'email', 'password'];
        $params = [$name, $email, $password_hash];
        $types = 'sss';

        if ($hasUsername) {
            $cols[] = 'username';
            // Gera username único baseado no email
            $baseUser = explode('@', $email)[0];
            // Remove caracteres especiais
            $baseUser = preg_replace('/[^a-zA-Z0-9]/', '', $baseUser);
            // Adiciona sufixo aleatório curto para garantir unicidade
            $username = $baseUser . rand(100, 999);

            $params[] = $username;
            $types .= 's';
        }

        if ($hasReferredBy && $affiliate_user_id > 0) {
            $cols[] = 'referred_by';
            $params[] = $affiliate_user_id;
            $types .= 'i';
        }

        $sql = "INSERT INTO users (" . implode(', ', $cols) . ") VALUES (" . implode(', ', array_fill(0, count($cols), '?')) . ")";
        $stmt = $conn->prepare($sql);

        // Bind dinâmico
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            session_regenerate_id(true);
            $newUserId = (int) $stmt->insert_id;
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['user_name'] = $name;
            if ($hasUsername) {
                $_SESSION['user_username'] = $username;
            }
            if ($hasRole) {
                $_SESSION['user_role'] = 'user';
                $_SESSION['is_admin'] = false;
            }

            if ($hasAffiliateTables && $affiliate_user_id > 0 && $affiliate_user_id !== $newUserId) {
                $stmt2 = $conn->prepare("INSERT IGNORE INTO affiliate_referrals (affiliate_user_id, referred_user_id) VALUES (?, ?)");
                $stmt2->bind_param("ii", $affiliate_user_id, $newUserId);
                $stmt2->execute();
            }

            echo json_encode(['success' => true, 'message' => 'Cadastro realizado com sucesso!']);
        } else {
            // Tratamento específico para erro de duplicidade que possa ter escapado
            if ($conn->errno == 1062) {
                echo json_encode(['success' => false, 'message' => 'Erro: Dados duplicados (Email ou usuário já existem).']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $stmt->error]);
            }
        }
    } elseif ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
            exit;
        }

        $stmt = $hasRole
            ? $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?")
            : $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                if ($hasRole) {
                    $_SESSION['user_role'] = (string) ($row['role'] ?? 'user');
                    $_SESSION['is_admin'] = ($_SESSION['user_role'] === 'admin');
                }
                echo json_encode(['success' => true, 'message' => 'Login realizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Senha incorreta.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
        }
    } elseif ($action === 'logout') {
        session_destroy();
        header("Location: /login.php");
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}
?>
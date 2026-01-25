<?php
// Configurações do banco de dados
$db_host = 'localhost';
$db_user = 'u853242961_only2';
$db_pass = 'Lucastav8012@';
$db_name = 'u853242961_only';

// Conexão com o banco de dados
mysqli_report(MYSQLI_REPORT_OFF); // Disable default exception handling to handle it manually (optional, but safer for legacy style checks, or use try-catch)

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Verifica a conexão (caso exceptions estejam desligadas ou para garantir)
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Falha na conexão com o banco de dados: ' . $e->getMessage()]);
    exit;
}

$conn->set_charset("utf8mb4");
?>
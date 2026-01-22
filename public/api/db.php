<?php
// Configurações do Banco de Dados
// ATENÇÃO: Preencha com os dados reais do seu banco na Hostinger
define('DB_HOST', 'localhost');
define('DB_USER', 'u853242961_only2');
define('DB_PASS', 'Lucastav8012@'); // Senha fornecida
define('DB_NAME', 'u853242961_only');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode([
        'success' => false, 
        'message' => 'Erro de conexão com o banco de dados: ' . $e->getMessage()
    ]));
}
?>

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
                $import_details = "Importação concluída! $results comandos processados.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database | Thunder Store</title>
    <style>
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

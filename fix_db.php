<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$log = [];

// Função para logar mensagens
function logger($msg) {
    global $log;
    $log[] = $msg;
}

$status = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!file_exists('api/db.php')) {
            throw new Exception("Arquivo api/db.php não encontrado.");
        }
        require_once 'api/db.php';
        logger("Conexão com banco de dados estabelecida.");

        // 1. Remove duplicatas na tabela plans
        // Mantém o ID menor (o mais antigo) e remove os outros com mesmo product_id e name
        $sql_clean = "DELETE p1 FROM plans p1 INNER JOIN plans p2 WHERE p1.id > p2.id AND p1.product_id = p2.product_id AND p1.name = p2.name";
        if ($conn->query($sql_clean)) {
            $affected = $conn->affected_rows;
            logger("Limpeza de duplicatas: $affected registros removidos da tabela 'plans'.");
        } else {
            throw new Exception("Erro ao remover duplicatas: " . $conn->error);
        }

        // 2. Adiciona índice único para prevenir futuras duplicatas
        // Verifica se o índice já existe para evitar erro
        $check_index = "SHOW INDEX FROM plans WHERE Key_name = 'unique_plan'";
        $result = $conn->query($check_index);

        if ($result && $result->num_rows == 0) {
            $sql_alter = "ALTER TABLE plans ADD UNIQUE KEY unique_plan (product_id, name)";
            if ($conn->query($sql_alter)) {
                logger("Índice único 'unique_plan' adicionado à tabela 'plans'.");
            } else {
                throw new Exception("Erro ao adicionar índice único: " . $conn->error);
            }
        } else {
            logger("Índice único 'unique_plan' já existe. Nenhuma alteração necessária na estrutura.");
        }

        $status = 'success';
        $message = 'Banco de dados corrigido com sucesso!';

    } catch (Exception $e) {
        $status = 'error';
        $message = $e->getMessage();
        logger("ERRO: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Correção de Duplicatas | Thunder Store</title>
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
        .log { background: #333; padding: 10px; font-size: 12px; border: 1px solid #444; margin-top: 20px; max-height: 300px; overflow-y: auto; }
    </style>
    <script src="/assets/no-zoom.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Correção de Planos Duplicados</h1>
        
        <p>Este script irá remover planos duplicados da tabela <code>plans</code> e adicionar uma restrição única para evitar que o problema retorne.</p>
        
        <?php if ($status === 'success'): ?>
            <div class="status success">
                <strong>SUCESSO:</strong> <?php echo htmlspecialchars($message); ?>
            </div>
            <p>Seu banco de dados está limpo. Você pode apagar este arquivo agora.</p>
            <a href="/" class="btn" style="background: #2563eb;">Voltar para o Site</a>
        <?php elseif ($status === 'error'): ?>
            <div class="status error">
                <strong>ERRO:</strong> <?php echo htmlspecialchars($message); ?>
            </div>
            <form method="post">
                <button type="submit" class="btn">Tentar Novamente</button>
            </form>
        <?php else: ?>
            <form method="post">
                <button type="submit" class="btn">Corrigir Banco de Dados Agora</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($log)): ?>
        <div class="log">
            <strong>Log de Execução:</strong><br>
            <?php foreach($log as $l): ?>
                - <?php echo htmlspecialchars($l); ?><br>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
require_once __DIR__ . '/../config/config.php';

try {
    // Ler o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // Dividir em comandos individuais
    $commands = array_filter(array_map('trim', explode(';', $sql)));
    
    // Executar cada comando
    foreach ($commands as $command) {
        if (!empty($command)) {
            $conn->exec($command);
        }
    }
    
    echo "Schema importado com sucesso!\n";
} catch (PDOException $e) {
    die("Erro ao importar schema: " . $e->getMessage() . "\n");
} 
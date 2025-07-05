<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Configuração GymForge</h1>";

// 1. Verificar se consegue encontrar o config.php
$config_file = __DIR__ . '/config/config.php';
echo "<p>Procurando config.php em: " . $config_file . "</p>";
echo "<p>O arquivo existe? " . (file_exists($config_file) ? "SIM" : "NÃO") . "</p>";

// 2. Tentar carregar o config
if (file_exists($config_file)) {
    require_once $config_file;
    echo "<p>Config.php foi carregado</p>";
} else {
    echo "<p style='color: red'>ERRO: Config.php não encontrado!</p>";
}

// 3. Verificar constantes
echo "<p>BASE_URL está definida? " . (defined('BASE_URL') ? "SIM" : "NÃO") . "</p>";
if (defined('BASE_URL')) {
    echo "<p>Valor de BASE_URL: " . BASE_URL . "</p>";
}

// 4. Verificar ambiente
echo "<h2>Informações do Ambiente:</h2>";
echo "<pre>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "</pre>";
?>
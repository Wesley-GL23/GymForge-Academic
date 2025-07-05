<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de BASE_URL</h1>";

echo "<h2>Antes de incluir config.php:</h2>";
echo "BASE_URL está definida? " . (defined('BASE_URL') ? "SIM" : "NÃO") . "<br>";

echo "<h2>Incluindo config.php:</h2>";
require_once __DIR__ . '/config/config.php';

echo "<h2>Depois de incluir config.php:</h2>";
echo "BASE_URL está definida? " . (defined('BASE_URL') ? "SIM" : "NÃO") . "<br>";
if (defined('BASE_URL')) {
    echo "Valor de BASE_URL: " . BASE_URL . "<br>";
}

echo "<h2>Informações do arquivo:</h2>";
echo "Diretório atual: " . __DIR__ . "<br>";
echo "Caminho do config.php: " . __DIR__ . '/config/config.php' . "<br>";
echo "O arquivo config.php existe? " . (file_exists(__DIR__ . '/config/config.php') ? "SIM" : "NÃO") . "<br>";

echo "<h2>Debug backtrace:</h2>";
echo "<pre>";
debug_print_backtrace();
echo "</pre>";
?> 
<?php
require_once 'config/config.php';
require_once 'includes/auth_functions.php';

echo "<h1>Teste de Ambiente GymForge</h1>";

// Testar BASE_URL
echo "<h2>Configuração de URL</h2>";
echo "BASE_URL: " . BASE_URL . "<br>";

// Testar Sessão
echo "<h2>Sessão</h2>";
echo "Status da Sessão: " . session_status() . "<br>";
echo "ID da Sessão: " . session_id() . "<br>";

// Testar Banco de Dados
echo "<h2>Banco de Dados</h2>";
try {
    require_once 'config/conexao.php';
    echo "Conexão com banco de dados: OK<br>";
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "Total de usuários: " . $result['total'] . "<br>";
} catch (Exception $e) {
    echo "Erro no banco de dados: " . $e->getMessage() . "<br>";
}

// Testar Funções de Autenticação
echo "<h2>Funções de Autenticação</h2>";
echo "estaLogado(): " . (estaLogado() ? "true" : "false") . "<br>";
echo "validateCsrfToken está definida: " . (function_exists('validateCsrfToken') ? "sim" : "não") . "<br>";

// Testar diretórios
echo "<h2>Permissões de Diretório</h2>";
$dirs = ['assets', 'config', 'includes', 'views'];
foreach ($dirs as $dir) {
    echo "$dir é gravável: " . (is_writable($dir) ? "sim" : "não") . "<br>";
} 
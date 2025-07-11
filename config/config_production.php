<?php
// Configuração para PRODUÇÃO
// Defina a BASE_URL de acordo com seu ambiente de produção
if (!defined('BASE_URL')) {
    define('BASE_URL', 'https://seudominio.com/GymForge-Academic');
}

// Configurações de Debug - DESABILITADO em produção
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', false);
}

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'gymforge_php');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações de Sessão - Iniciar apenas uma vez
if (session_status() === PHP_SESSION_NONE) {
    // Configurar cookies seguros para produção
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => true, // true para produção
        'httponly' => true,
        'samesite' => 'Strict' // Strict para produção
    ]);
    session_start();
}

// Incluir funções de mensagem
if (file_exists(__DIR__ . '/../includes/message_functions.php')) {
    require_once __DIR__ . '/../includes/message_functions.php';
}

// Criar conexão PDO global
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $conn = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
} catch (PDOException $e) {
    // Em produção, não mostrar detalhes do erro
    error_log('Erro ao conectar ao banco de dados: ' . $e->getMessage());
    die('Erro interno do servidor');
} 
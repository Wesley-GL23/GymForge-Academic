<?php
require_once __DIR__ . '/../config/config.php';
global $conn;
if (!isset($conn) || !$conn) {
    // Força a criação da conexão se não existir
    require_once __DIR__ . '/../config/config.php';
}
require_once __DIR__ . '/Auth.php';

// Iniciar sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    // Configurar cookies seguros antes de iniciar a sessão
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Instanciar classe Auth
$auth = new Auth($conn);

// Funções de Usuário
function cadastrarUsuario($nome, $email, $senha) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return false;
        }
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, nivel, data_cadastro) VALUES (?, ?, ?, 'cliente', NOW())");
        $stmt->execute([$nome, $email, $hash]);
        return true;
    } catch (Exception $e) {
        error_log("Erro no cadastro: " . $e->getMessage());
        return false;
    }
}

function fazerLogout() {
    global $auth;
    limparTokenLembrar(); // Limpar token de lembrar
    $auth->logout();
    header('Location: /GymForge-Academic/forms/usuario/login.php');
    exit;
}

function estaLogado() {
    return isset($_SESSION['user_id']);
}

function usuarioAtual() {
    if (!estaLogado()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'nome' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'nivel' => $_SESSION['user_level']
    ];
}

function conectarBD() {
    global $conn;
    return $conn;
}

// Funções de CSRF
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Funções auxiliares
function redirectWithMessage($url, $tipo, $mensagem) {
    $_SESSION['mensagem'] = [
        'tipo' => $tipo,
        'texto' => $mensagem
    ];
    header('Location: ' . BASE_URL . $url);
    exit;
}

function requireAuth() {
    if (!estaLogado()) {
        header('Location: /forms/usuario/login.php');
        exit;
    }
}

function requireAdmin() {
    if (!estaLogado() || $_SESSION['user_level'] !== 'admin') {
        header('Location: /403.php');
        exit;
    }
}

function fazerLogin($email, $senha, $lembrar = false) {
    global $auth;
    $result = $auth->login($email, $senha);
    
    // Se o login foi bem-sucedido e o usuário marcou "lembrar"
    if (is_array($result) && $result['success'] && $lembrar) {
        // Criar um token de "lembrar" válido por 30 dias
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        try {
            global $conn;
            $stmt = $conn->prepare("
                INSERT INTO tokens_lembrar (usuario_id, token, expira) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE token = VALUES(token), expira = VALUES(expira)
            ");
            $stmt->execute([$_SESSION['user_id'], $token, $expira]);
            
            // Definir cookie seguro
            setcookie(
                'lembrar_token',
                $token,
                [
                    'expires' => time() + (30 * 24 * 60 * 60), // 30 dias
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]
            );
        } catch (Exception $e) {
            error_log("Erro ao salvar token de lembrar: " . $e->getMessage());
        }
    }
    
    // Compatibilidade: retorna true/false
    if (is_array($result)) {
        return $result['success'] ?? false;
    }
    return (bool)$result;
}

function verificarTokenLembrar() {
    if (estaLogado()) {
        return; // Já está logado
    }
    
    if (!isset($_COOKIE['lembrar_token'])) {
        return; // Não tem cookie
    }
    
    $token = $_COOKIE['lembrar_token'];
    
    try {
        global $conn;
        $stmt = $conn->prepare("
            SELECT u.id, u.nome, u.email, u.nivel, t.expira
            FROM tokens_lembrar t
            JOIN usuarios u ON t.usuario_id = u.id
            WHERE t.token = ? AND t.expira > NOW()
        ");
        $stmt->execute([$token]);
        $resultado = $stmt->fetch();
        
        if ($resultado) {
            // Login automático
            $_SESSION['user_id'] = $resultado['id'];
            $_SESSION['user_name'] = $resultado['nome'];
            $_SESSION['user_email'] = $resultado['email'];
            $_SESSION['user_level'] = $resultado['nivel'];
            
            // Renovar o token
            $novo_token = bin2hex(random_bytes(32));
            $nova_expira = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $stmt = $conn->prepare("
                UPDATE tokens_lembrar 
                SET token = ?, expira = ? 
                WHERE usuario_id = ?
            ");
            $stmt->execute([$novo_token, $nova_expira, $resultado['id']]);
            
            // Atualizar cookie
            setcookie(
                'lembrar_token',
                $novo_token,
                [
                    'expires' => time() + (30 * 24 * 60 * 60),
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]
            );
        } else {
            // Token inválido, remover cookie
            setcookie('lembrar_token', '', time() - 3600, '/');
        }
    } catch (Exception $e) {
        error_log("Erro ao verificar token de lembrar: " . $e->getMessage());
    }
}

function limparTokenLembrar() {
    if (isset($_COOKIE['lembrar_token'])) {
        try {
            global $conn;
            $stmt = $conn->prepare("DELETE FROM tokens_lembrar WHERE token = ?");
            $stmt->execute([$_COOKIE['lembrar_token']]);
        } catch (Exception $e) {
            error_log("Erro ao limpar token de lembrar: " . $e->getMessage());
        }
        
        setcookie('lembrar_token', '', time() - 3600, '/');
    }
}

// Verificar token de lembrar automaticamente
verificarTokenLembrar();

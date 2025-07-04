<?php
require_once __DIR__ . '/../config/conexao.php';

// Configurações de Segurança
session_start();
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Funções de Usuário
function cadastrarUsuario($nome, $email, $senha) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
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

function fazerLogin($email, $senha) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nome'];
            $_SESSION['user_email'] = $usuario['email'];
            $_SESSION['user_level'] = $usuario['nivel'];
            return true;
        }
        return false;
    } catch (Exception $e) {
        error_log("Erro no login: " . $e->getMessage());
        return false;
    }
}

function fazerLogout() {
    session_destroy();
    header('Location: /forms/usuario/login.php');
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

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
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

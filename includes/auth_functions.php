<?php
// Não precisa do session_start() aqui pois já está no config.php
require_once __DIR__ . '/../config/config.php';

// Verifica se o usuário está logado
function estaLogado() {
    return isset($_SESSION['user_id']);
}

// Verifica o nível de acesso do usuário
function temNivelAcesso($nivelRequerido) {
    if (!estaLogado()) return false;
    return isset($_SESSION['user_nivel']) && $_SESSION['user_nivel'] === $nivelRequerido;
}

// Verifica se o usuário é admin
function eAdmin() {
    return temNivelAcesso(NIVEL_ADMIN);
}

// Verifica se o usuário é cliente
function eCliente() {
    return temNivelAcesso(NIVEL_CLIENTE);
}

// Redireciona usuário não logado
function requireLogin() {
    if (!estaLogado()) {
        $_SESSION['flash_message'] = 'Por favor, faça login para acessar esta página';
        $_SESSION['flash_type'] = 'warning';
        header('Location: ' . BASE_URL . '/forms/usuario/login.php');
        exit;
    }
}

// Redireciona usuário não admin
function requireAdmin() {
    requireLogin();
    if (!eAdmin()) {
        $_SESSION['flash_message'] = 'Acesso restrito a administradores';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

// Redireciona usuário não cliente
function requireCliente() {
    requireLogin();
    if (!eCliente()) {
        $_SESSION['flash_message'] = 'Acesso restrito a clientes';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

// Função para logout
function logout() {
    session_start();
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['nivel'] === 'admin';
}

function isCliente() {
    return isLoggedIn() && $_SESSION['nivel'] === 'cliente';
}
?> 
<?php
// Incluir funções de autenticação
require_once __DIR__ . '/auth_functions.php';

// Função para verificar login
function requireLogin() {
    if (!estaLogado()) {
        header('Location: ' . BASE_URL . '/forms/usuario/login.php');
        exit;
    }
}

// Função para verificar nível de acesso
function requireNivel($nivel) {
    requireLogin();
    $usuario = usuarioAtual();
    if ($usuario['nivel'] !== $nivel) {
        header('Location: ' . BASE_URL . '/403.php');
        exit;
    }
} 
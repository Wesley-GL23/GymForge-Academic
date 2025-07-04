<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth_functions.php';

// Verifica se o usuário está logado e é admin
requireLogin();
requireNivel('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'] ?? '';
    $hash = $_POST['hash'] ?? '';
    
    if (empty($senha) || empty($hash)) {
        echo json_encode(['erro' => 'Senha e hash são obrigatórios']);
        exit;
    }
    
    $resultado = password_verify($senha, $hash);
    echo json_encode(['match' => $resultado]);
    exit;
}

// Se não for POST, retorna erro
echo json_encode(['erro' => 'Método não permitido']);
exit; 
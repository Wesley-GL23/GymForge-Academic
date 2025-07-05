<?php
require_once __DIR__ . '/../../config/config.php';
require_once INCLUDES_DIR . '/auth_functions.php';

// Verificar método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithMessage('/forms/usuario/login.php', 'danger', 'Método não permitido');
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    redirectWithMessage('/forms/usuario/login.php', 'danger', 'Token inválido');
}

try {
    // Validar e sanitizar inputs
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    $senha = $_POST['senha'] ?? '';
    if (empty($senha)) {
        throw new Exception('Senha não fornecida');
    }

    // Tentar fazer login
    $result = $auth->login($email, $senha);

    if ($result['success']) {
        // Login bem sucedido
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => 'Login realizado com sucesso!'
        ];
        
        // Registrar último login
        $stmt = $conn->prepare("
            UPDATE usuarios 
            SET ultimo_login = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);

        // Verificar e criar personagem se não existir
        $stmt = $conn->prepare("
            SELECT id FROM forge_characters 
            WHERE user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        
        if (!$stmt->fetch()) {
            $stmt = $conn->prepare("
                INSERT INTO forge_characters (user_id) 
                VALUES (?)
            ");
            $stmt->execute([$_SESSION['user_id']]);
            
            $char_id = $conn->lastInsertId();
            
            // Criar atributos iniciais
            $stmt = $conn->prepare("
                INSERT INTO forge_attributes (character_id) 
                VALUES (?)
            ");
            $stmt->execute([$char_id]);
        }
        
        // Redirecionar com base no nível do usuário
        $redirect = $_SESSION['user_level'] === 'admin' 
            ? '/views/dashboard/admin.php'
            : '/views/dashboard/cliente.php';
            
        // Registrar no log do sistema
        $stmt = $conn->prepare("
            INSERT INTO system_logs 
            (user_id, action_type, description, ip_address, user_agent) 
            VALUES (?, 'login', 'Login bem-sucedido', ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
        
        header('Location: ' . BASE_URL . $redirect);
        exit;
        
    } else {
        throw new Exception($result['message']);
    }
    
} catch (Exception $e) {
    redirectWithMessage('/forms/usuario/login.php', 'danger', $e->getMessage());
} 
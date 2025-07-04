<?php
require_once '../../config/conexao.php';
require_once '../../includes/auth_functions.php';

// Verificar método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Método não permitido'
    ];
    header('Location: ' . BASE_URL . '/forms/usuario/recuperar_senha.php');
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    http_response_code(403);
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Token inválido'
    ];
    header('Location: ' . BASE_URL . '/forms/usuario/recuperar_senha.php');
    exit;
}

try {
    // Validar e sanitizar inputs
    $token = filter_var($_POST['token'] ?? '', FILTER_SANITIZE_STRING);
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';
    
    if (empty($token)) {
        throw new Exception('Token não fornecido');
    }
    
    // Validar força da senha
    if (strlen($senha) < 8) {
        throw new Exception('A senha deve ter pelo menos 8 caracteres');
    }
    
    if (!preg_match('/[A-Z]/', $senha)) {
        throw new Exception('A senha deve conter pelo menos uma letra maiúscula');
    }
    
    if (!preg_match('/[a-z]/', $senha)) {
        throw new Exception('A senha deve conter pelo menos uma letra minúscula');
    }
    
    if (!preg_match('/[0-9]/', $senha)) {
        throw new Exception('A senha deve conter pelo menos um número');
    }
    
    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $senha)) {
        throw new Exception('A senha deve conter pelo menos um caractere especial');
    }
    
    if ($senha !== $confirmarSenha) {
        throw new Exception('As senhas não coincidem');
    }
    
    // Verificar se o token existe e não expirou
    $stmt = $conn->prepare("
        SELECT id, email 
        FROM usuarios 
        WHERE token_recuperacao = ? 
        AND token_expiracao > NOW()
        AND (bloqueado_ate IS NULL OR bloqueado_ate < NOW())
    ");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        throw new Exception('Token inválido ou expirado. Solicite um novo link de recuperação.');
    }
    
    // Redefinir a senha
    $result = $auth->resetPassword($token, $senha);
    
    if ($result['success']) {
        // Registrar no log do sistema
        $stmt = $conn->prepare("
            INSERT INTO system_logs 
            (user_id, action_type, description, ip_address, user_agent) 
            VALUES (?, 'password_reset', 'Senha redefinida com sucesso', ?, ?)
        ");
        $stmt->execute([
            $usuario['id'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);
        
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => 'Senha redefinida com sucesso! Faça login com sua nova senha.'
        ];
        header('Location: ' . BASE_URL . '/forms/usuario/login.php');
        
    } else {
        throw new Exception($result['message']);
    }
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => $e->getMessage()
    ];
    if (isset($token)) {
        header('Location: ' . BASE_URL . '/forms/usuario/redefinir_senha.php?token=' . urlencode($token));
    } else {
        header('Location: ' . BASE_URL . '/forms/usuario/recuperar_senha.php');
    }
}
exit;
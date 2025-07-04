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
    // Validar e sanitizar email
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Verificar se o email existe e não está bloqueado
    $stmt = $conn->prepare("
        SELECT id, nome, bloqueado_ate 
        FROM usuarios 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        // Por segurança, não informamos se o email existe ou não
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => 'Se o email existir, você receberá as instruções de recuperação.'
        ];
        header('Location: ' . BASE_URL . '/forms/usuario/recuperar_senha.php');
        exit;
    }

    // Verificar se a conta está bloqueada
    if ($usuario['bloqueado_ate'] && strtotime($usuario['bloqueado_ate']) > time()) {
        throw new Exception('Conta temporariamente bloqueada. Tente novamente mais tarde.');
    }

    // Gerar token e salvar
    $result = $auth->requestPasswordReset($email);
    
    if ($result['success']) {
        // Em um ambiente de produção, enviar email
        // Por enquanto, mostrar link direto (apenas para desenvolvimento)
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            $_SESSION['mensagem'] = [
                'tipo' => 'success',
                'texto' => "Link de recuperação (apenas dev): <a href='{$result['link']}'>Redefinir senha</a>"
            ];
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'success',
                'texto' => 'Instruções de recuperação foram enviadas para seu email.'
            ];
        }

        // Registrar no log do sistema
        $stmt = $conn->prepare("
            INSERT INTO system_logs 
            (user_id, action_type, description, ip_address, user_agent) 
            VALUES (?, 'password_reset_request', 'Solicitação de recuperação de senha', ?, ?)
        ");
        $stmt->execute([
            $usuario['id'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);
        
    } else {
        throw new Exception($result['message']);
    }
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => $e->getMessage()
    ];
}

header('Location: ' . BASE_URL . '/forms/usuario/recuperar_senha.php');
exit;
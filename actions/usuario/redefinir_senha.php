<?php
require_once '../../config/config.php';
require_once '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    $senha = filter_input(INPUT_POST, 'senha');
    $confirmar_senha = filter_input(INPUT_POST, 'confirmar_senha');
    
    // Validações básicas
    if (!$token || !$senha || !$confirmar_senha) {
        $_SESSION['flash_message'] = 'Todos os campos são obrigatórios.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../forms/usuario/redefinir_senha.php?token=' . urlencode($token));
        exit;
    }
    
    if ($senha !== $confirmar_senha) {
        $_SESSION['flash_message'] = 'As senhas não coincidem.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../forms/usuario/redefinir_senha.php?token=' . urlencode($token));
        exit;
    }
    
    if (strlen($senha) < 6) {
        $_SESSION['flash_message'] = 'A senha deve ter no mínimo 6 caracteres.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../forms/usuario/redefinir_senha.php?token=' . urlencode($token));
        exit;
    }
    
    // Verifica se o token é válido
    $stmt = $conn->prepare("
        SELECT usuario_id 
        FROM recuperacao_senha 
        WHERE token = ? 
        AND expira > NOW() 
        AND usado = 0
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['flash_message'] = 'Link inválido ou expirado. Por favor, solicite um novo link de recuperação.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../forms/usuario/recuperar_senha.php');
        exit;
    }
    
    $usuario = $result->fetch_assoc();
    
    // Atualiza a senha do usuário
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->bind_param("si", $senha_hash, $usuario['usuario_id']);
    
    if (!$stmt->execute()) {
        $_SESSION['flash_message'] = 'Erro ao atualizar a senha. Tente novamente.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../forms/usuario/redefinir_senha.php?token=' . urlencode($token));
        exit;
    }
    
    // Marca o token como usado
    $stmt = $conn->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    
    $_SESSION['flash_message'] = 'Senha atualizada com sucesso! Você já pode fazer login com sua nova senha.';
    $_SESSION['flash_type'] = 'success';
    header('Location: ../../forms/usuario/login.php');
    exit;
}

header('Location: ../../forms/usuario/recuperar_senha.php');
exit; 
<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth_functions.php';

// Verifica se o usuário está logado e é admin
requireAuth();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';
    
    // Validações
    $erros = [];
    
    if (empty($senhaAtual)) {
        $erros[] = "Senha atual é obrigatória";
    }
    
    if (strlen($novaSenha) < 8) {
        $erros[] = "A nova senha deve ter pelo menos 8 caracteres";
    }
    
    if ($novaSenha !== $confirmarSenha) {
        $erros[] = "As senhas não coincidem";
    }
    
    if (!empty($erros)) {
        echo json_encode(['erro' => implode(", ", $erros)]);
        exit;
    }
    
    // Verifica a senha atual
    $conn = conectarBD();
    $userId = $_SESSION['user_id'];
    
    try {
        $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($senhaAtual, $usuario['senha'])) {
            // Atualiza a senha
            $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            if ($stmt->execute([$novaSenhaHash, $userId])) {
                echo json_encode(['sucesso' => 'Senha atualizada com sucesso']);
            } else {
                echo json_encode(['erro' => 'Erro ao atualizar senha']);
            }
        } else {
            echo json_encode(['erro' => 'Senha atual incorreta']);
        }
    } catch (Exception $e) {
        error_log("Erro ao atualizar senha: " . $e->getMessage());
        echo json_encode(['erro' => 'Erro ao processar solicitação']);
    }
    exit;
}

// Se não for POST, retorna erro
echo json_encode(['erro' => 'Método não permitido']);
exit; 
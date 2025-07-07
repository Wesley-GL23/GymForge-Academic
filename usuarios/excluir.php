<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

// Verificar se é administrador
verificarAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    // Não permitir excluir o próprio usuário
    if ($id === $_SESSION['usuario_id']) {
        $_SESSION['mensagem'] = 'Você não pode excluir seu próprio usuário.';
        header('Location: index.php');
        exit();
    }
    
    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['mensagem'] = 'Usuário excluído com sucesso!';
            } else {
                $_SESSION['mensagem'] = 'Erro ao excluir usuário.';
            }
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = 'Erro ao excluir usuário: ' . $e->getMessage();
        }
    }
}

header('Location: index.php');
exit(); 
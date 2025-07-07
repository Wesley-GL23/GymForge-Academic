<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

// Verificar se é administrador
verificarAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM exercicios WHERE id = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['mensagem'] = 'Exercício excluído com sucesso!';
            } else {
                $_SESSION['mensagem'] = 'Erro ao excluir exercício.';
            }
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = 'Erro ao excluir exercício: ' . $e->getMessage();
        }
    }
}

header('Location: index.php');
exit(); 
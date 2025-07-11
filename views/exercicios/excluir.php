<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/exercise_functions.php';

// Verifica se é administrador
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    $_SESSION['erro'] = "Acesso negado.";
    header('Location: biblioteca.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        
        if (!$id) {
            throw new Exception("ID inválido");
        }
        
        if (excluirExercicio($id)) {
            $_SESSION['mensagem'] = "Exercício excluído com sucesso!";
        } else {
            throw new Exception("Erro ao excluir exercício");
        }
        
    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao excluir exercício: " . $e->getMessage();
    }
}

header('Location: biblioteca.php');
exit; 
<?php
session_start();
require_once '../../config/conexao.php';
require_once '../../includes/auth_functions.php';

// Verifica se é admin
requireAdmin();

// Verifica se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['msg'] = 'Método não permitido';
    $_SESSION['msg_type'] = 'danger';
    header('Location: ../../views/treino/listar.php');
    exit;
}

// Verifica se o ID foi fornecido
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['msg'] = 'ID do treino não fornecido';
    $_SESSION['msg_type'] = 'danger';
    header('Location: ../../views/treino/listar.php');
    exit;
}

$treino_id = (int)$_POST['id'];

// Conexão com o banco
$conn = conectarBD();

try {
    // Inicia a transação
    $conn->begin_transaction();

    // Remove os exercícios do treino
    $stmt = $conn->prepare("DELETE FROM treino_exercicios WHERE treino_id = ?");
    $stmt->bind_param("i", $treino_id);
    $stmt->execute();

    // Remove o treino
    $stmt = $conn->prepare("DELETE FROM treinos WHERE id = ?");
    $stmt->bind_param("i", $treino_id);
    $stmt->execute();

    // Commit da transação
    $conn->commit();

    $_SESSION['msg'] = 'Treino excluído com sucesso!';
    $_SESSION['msg_type'] = 'success';

} catch (Exception $e) {
    // Rollback em caso de erro
    $conn->rollback();
    
    $_SESSION['msg'] = 'Erro ao excluir treino: ' . $e->getMessage();
    $_SESSION['msg_type'] = 'danger';
} finally {
    fecharConexao($conn);
    header('Location: ../../views/treino/listar.php');
}
?> 
<?php
require_once '../../includes/header.php';
requireAdmin();

// Verifica se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMessage('ID do exercício não fornecido', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
    exit();
}

$conn = conectarBD();
$id = (int)$_GET['id'];

// Buscar exercício para remover o GIF se existir
$sql = "SELECT gif_url FROM exercicios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$exercicio = $stmt->get_result()->fetch_assoc();

if (!$exercicio) {
    setMessage('Exercício não encontrado', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
    exit();
}

// Remover GIF se existir
if ($exercicio['gif_url']) {
    $gif_path = str_replace(BASE_URL, '../../', $exercicio['gif_url']);
    if (file_exists($gif_path)) {
        unlink($gif_path);
    }
}

// Excluir do banco
$sql = "DELETE FROM exercicios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    setMessage('Exercício excluído com sucesso', 'success');
} else {
    setMessage('Erro ao excluir exercício: ' . $conn->error, 'danger');
}

fecharConexao($conn);
header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
exit(); 
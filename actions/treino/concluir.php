<?php
require_once '../../config/config.php';
require_once '../../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../forms/usuario/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $treino_id = filter_input(INPUT_POST, 'treino_id', FILTER_SANITIZE_NUMBER_INT);
    $data_conclusao = filter_input(INPUT_POST, 'data_conclusao');
    $duracao_minutos = filter_input(INPUT_POST, 'duracao_minutos', FILTER_SANITIZE_NUMBER_INT);
    $nivel_dificuldade = filter_input(INPUT_POST, 'nivel_dificuldade', FILTER_SANITIZE_NUMBER_INT);
    $observacoes = filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_STRING);
    
    // Validações básicas
    if (!$treino_id || !$data_conclusao || !$duracao_minutos || !$nivel_dificuldade) {
        $_SESSION['flash_message'] = 'Todos os campos obrigatórios devem ser preenchidos.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../views/treino/concluir.php?id=' . $treino_id);
        exit;
    }
    
    // Verifica se o treino pertence ao usuário
    $stmt = $conn->prepare("SELECT id FROM treinos WHERE id = ? AND aluno_id = ?");
    $stmt->bind_param("ii", $treino_id, $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['flash_message'] = 'Treino não encontrado.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../views/treino/meus_treinos.php');
        exit;
    }
    
    // Converte a data para o formato do MySQL
    $data_conclusao = date('Y-m-d H:i:s', strtotime($data_conclusao));
    
    // Insere o registro de conclusão
    $stmt = $conn->prepare("
        INSERT INTO treinos_concluidos 
        (usuario_id, treino_id, data_conclusao, duracao_minutos, nivel_dificuldade, observacoes) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisiss", 
        $_SESSION['usuario_id'], 
        $treino_id, 
        $data_conclusao, 
        $duracao_minutos, 
        $nivel_dificuldade, 
        $observacoes
    );
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = 'Treino marcado como concluído com sucesso!';
        $_SESSION['flash_type'] = 'success';
        header('Location: ../../views/treino/meus_treinos.php');
        exit;
    } else {
        $_SESSION['flash_message'] = 'Erro ao registrar a conclusão do treino. Tente novamente.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../views/treino/concluir.php?id=' . $treino_id);
        exit;
    }
}

header('Location: ../../views/treino/meus_treinos.php');
exit; 
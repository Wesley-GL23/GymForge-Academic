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

// Validação dos campos obrigatórios
$campos_obrigatorios = ['usuario_id', 'nome', 'data'];
foreach ($campos_obrigatorios as $campo) {
    if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
        $_SESSION['msg'] = 'Todos os campos obrigatórios devem ser preenchidos';
        $_SESSION['msg_type'] = 'danger';
        header('Location: ../../views/treino/cadastro.php');
        exit;
    }
}

// Validação dos exercícios
if (!isset($_POST['exercicios']) || empty($_POST['exercicios'])) {
    $_SESSION['msg'] = 'Selecione pelo menos um exercício';
    $_SESSION['msg_type'] = 'danger';
    header('Location: ../../views/treino/cadastro.php');
    exit;
}

// Conexão com o banco
$conn = conectarBD();

try {
    // Inicia a transação
    $conn->begin_transaction();

    // Insere o treino
    $stmt = $conn->prepare("INSERT INTO treinos (usuario_id, nome, descricao, data) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", 
        $_POST['usuario_id'],
        $_POST['nome'],
        $_POST['descricao'],
        $_POST['data']
    );
    $stmt->execute();
    $treino_id = $conn->insert_id;

    // Insere os exercícios do treino
    $stmt = $conn->prepare("INSERT INTO treino_exercicios (treino_id, exercicio_id, series, repeticoes, observacoes) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($_POST['exercicios'] as $key => $exercicio_id) {
        $series = $_POST['series'][$key];
        $repeticoes = $_POST['repeticoes'][$key];
        $observacoes = $_POST['observacoes'][$key] ?? '';

        $stmt->bind_param("iiiis", 
            $treino_id,
            $exercicio_id,
            $series,
            $repeticoes,
            $observacoes
        );
        $stmt->execute();
    }

    // Commit da transação
    $conn->commit();

    $_SESSION['msg'] = 'Treino cadastrado com sucesso!';
    $_SESSION['msg_type'] = 'success';
    header('Location: ../../views/treino/listar.php');

} catch (Exception $e) {
    // Rollback em caso de erro
    $conn->rollback();
    
    $_SESSION['msg'] = 'Erro ao cadastrar treino: ' . $e->getMessage();
    $_SESSION['msg_type'] = 'danger';
    header('Location: ../../views/treino/cadastro.php');
} finally {
    fecharConexao($conn);
}
?> 
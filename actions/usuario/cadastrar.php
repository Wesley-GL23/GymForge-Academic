<?php
session_start();
require_once '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = conectarBD();
    
    // Limpa e obtém os dados do formulário
    $nome = limparString($conn, $_POST['nome']);
    $email = limparString($conn, $_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validações
    $erros = [];
    
    // Validar nome
    if (empty($nome) || strlen($nome) < 3) {
        $erros[] = "Nome deve ter no mínimo 3 caracteres.";
    }
    
    // Validar email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido.";
    }
    
    // Verificar se email já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $erros[] = "Este e-mail já está cadastrado.";
    }
    $stmt->close();
    
    // Validar senha
    if (empty($senha) || strlen($senha) < 6) {
        $erros[] = "Senha deve ter no mínimo 6 caracteres.";
    }
    
    // Validar confirmação de senha
    if ($senha !== $confirmar_senha) {
        $erros[] = "As senhas não conferem.";
    }
    
    // Se houver erros
    if (!empty($erros)) {
        $_SESSION['msg'] = implode("<br>", $erros);
        $_SESSION['msg_type'] = "danger";
        header("Location: ../../forms/usuario/cadastro.php");
        exit();
    }
    
    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Inserir usuário
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, 'cliente')");
    $stmt->bind_param("sss", $nome, $email, $senha_hash);
    
    if ($stmt->execute()) {
        $_SESSION['msg'] = "Cadastro realizado com sucesso! Faça login para continuar.";
        $_SESSION['msg_type'] = "success";
        header("Location: ../../forms/usuario/login.php");
    } else {
        $_SESSION['msg'] = "Erro ao cadastrar. Por favor, tente novamente.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../../forms/usuario/cadastro.php");
    }
    
    $stmt->close();
    fecharConexao($conn);
    exit();
}

// Se alguém tentar acessar diretamente o arquivo
header("Location: ../../forms/usuario/cadastro.php");
exit(); 
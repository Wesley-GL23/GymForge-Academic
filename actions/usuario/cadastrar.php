<?php
require_once '../../config/config.php';
require_once '../../includes/auth_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmar_senha'];
    
    // Validações
    $erros = [];
    
    if (empty($nome) || strlen($nome) < 3) {
        $erros[] = "Nome inválido";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido";
    }
    
    if (strlen($senha) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres";
    }
    
    if ($senha !== $confirmarSenha) {
        $erros[] = "As senhas não coincidem";
    }
    
    if (!empty($erros)) {
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => implode(", ", $erros)
        ];
        header('Location: ' . BASE_URL . '/forms/usuario/cadastro.php');
        exit;
    }
    
    // Tenta cadastrar o usuário
    if (cadastrarUsuario($nome, $email, $senha)) {
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => 'Cadastro realizado com sucesso! Faça login para continuar.'
        ];
        header('Location: ' . BASE_URL . '/forms/usuario/login.php');
    } else {
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => 'Este email já está cadastrado!'
        ];
        header('Location: ' . BASE_URL . '/forms/usuario/cadastro.php');
    }
    exit;
} else {
    // Acesso direto ao arquivo
    header('Location: ' . BASE_URL . '/forms/usuario/cadastro.php');
    exit;
} 
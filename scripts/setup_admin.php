<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_functions.php';

try {
    // Verifica se já existe um admin
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nivel = 'admin'");
    $stmt->execute();
    
    if ($stmt->fetch()) {
        echo "Já existe um usuário administrador no sistema.\n";
        echo "Email: admin@gymforge.com\n";
        echo "Senha: password\n";
    } else {
        // Criar usuário admin
        $senha = 'password';
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nome, email, senha, nivel)
            VALUES ('Administrador', 'admin@gymforge.com', ?, 'admin')
        ");
        
        if ($stmt->execute([$senha_hash])) {
            echo "Usuário administrador criado com sucesso!\n";
            echo "Email: admin@gymforge.com\n";
            echo "Senha: password\n";
        } else {
            echo "Erro ao criar usuário administrador.\n";
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
} 
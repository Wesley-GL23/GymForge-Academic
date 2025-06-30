<?php
require_once 'config/config.php';
require_once 'config/conexao.php';

$conn = conectarBD();

// Busca o usuário admin
$sql = "SELECT * FROM usuarios WHERE email = 'admin@gymforge.com'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    echo "Usuário encontrado:\n";
    echo "ID: " . $usuario['id'] . "\n";
    echo "Nome: " . $usuario['nome'] . "\n";
    echo "Email: " . $usuario['email'] . "\n";
    echo "Nível: " . $usuario['nivel'] . "\n";
    echo "Hash da senha: " . $usuario['senha'] . "\n";
} else {
    echo "Usuário admin não encontrado!\n";
    
    // Vamos criar o usuário admin
    $senha = 'admin123';
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO usuarios (nome, email, senha, nivel) VALUES ('Administrador', 'admin@gymforge.com', ?, 'admin')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $hash);
    
    if ($stmt->execute()) {
        echo "\nUsuário admin criado com sucesso!\n";
        echo "Email: admin@gymforge.com\n";
        echo "Senha: admin123\n";
    } else {
        echo "\nErro ao criar usuário: " . $stmt->error . "\n";
    }
}

fecharConexao($conn);
?> 
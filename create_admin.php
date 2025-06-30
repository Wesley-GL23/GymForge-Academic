<?php
require_once 'config/config.php';

$senha = 'admin123';
$hash = password_hash($senha, PASSWORD_DEFAULT);
$conn = conectarBD();

$sql = "INSERT INTO usuarios (nome, email, senha, nivel) VALUES ('Administrador', 'admin@gymforge.com', ?, 'admin')";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $hash);

if ($stmt->execute()) {
    echo "Usuário admin criado com sucesso!\n";
    echo "Email: admin@gymforge.com\n";
    echo "Senha: admin123\n";
    echo "Hash gerado: " . $hash . "\n";
} else {
    echo "Erro ao criar usuário: " . $stmt->error . "\n";
}

$stmt->close();
fecharConexao($conn);
?> 
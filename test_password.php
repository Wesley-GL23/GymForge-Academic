<?php
require_once 'config/config.php';
require_once 'config/conexao.php';

// Nova senha para o admin
$nova_senha = 'admin123';
$hash = password_hash($nova_senha, PASSWORD_DEFAULT);

$conn = conectarBD();

// Atualiza a senha do usuário admin
$sql = "UPDATE usuarios SET senha = ? WHERE email = 'admin@gymforge.com'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $hash);

if ($stmt->execute()) {
    echo "Senha do admin atualizada com sucesso!\n";
    echo "Email: admin@gymforge.com\n";
    echo "Nova senha: admin123\n";
    echo "Hash gerado: " . $hash . "\n";
} else {
    echo "Erro ao atualizar senha: " . $stmt->error . "\n";
}

$stmt->close();
fecharConexao($conn);
?> 
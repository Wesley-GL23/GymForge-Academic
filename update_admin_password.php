<?php
require_once 'config/config.php';
require_once 'config/conexao.php';

$conn = conectarBD();

$senha = 'admin123';
$hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "UPDATE usuarios SET senha = ? WHERE email = 'admin@gymforge.com'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $hash);

if ($stmt->execute()) {
    echo "Senha do admin atualizada com sucesso!\n";
    echo "Email: admin@gymforge.com\n";
    echo "Senha: admin123\n";
} else {
    echo "Erro ao atualizar senha: " . $stmt->error;
}

$stmt->close();
fecharConexao($conn);
?> 
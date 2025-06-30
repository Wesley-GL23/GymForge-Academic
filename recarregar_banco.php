<?php
require_once 'config/config.php';
require_once 'config/conexao.php';

// Conectar ao MySQL sem selecionar banco
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Ler o arquivo SQL
$sql = file_get_contents('database/gymforge.sql');

// Executar os comandos SQL
if ($conn->multi_query($sql)) {
    echo "Banco de dados recarregado com sucesso!\n";
    echo "Agora você pode fazer login com:\n";
    echo "Email: admin@gymforge.com\n";
    echo "Senha: admin123\n";
} else {
    echo "Erro ao recarregar o banco: " . $conn->error;
}

$conn->close();
?> 
<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once CONFIG_DIR . '/conexao.php';

// Carregar usuários padrão
$default_users = require CONFIG_DIR . '/default_users.php';

// Função para criar usuário
function createUser($conn, $user) {
    try {
        // Verificar se usuário já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$user['email']]);
        if ($stmt->fetch()) {
            echo "Usuário {$user['email']} já existe.\n";
            return;
        }

        // Hash da senha
        $hash = password_hash($user['senha'], PASSWORD_DEFAULT);

        // Inserir usuário
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nome, email, senha, nivel, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $user['nome'],
            $user['email'],
            $hash,
            $user['nivel']
        ]);

        echo "Usuário {$user['email']} criado com sucesso!\n";
    } catch (Exception $e) {
        echo "Erro ao criar usuário {$user['email']}: " . $e->getMessage() . "\n";
    }
}

// Criar usuários padrão
foreach ($default_users as $user) {
    createUser($conn, $user);
} 
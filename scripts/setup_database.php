<?php
echo "Iniciando setup do banco de dados...\n";

try {
    // Conectar ao MySQL sem selecionar banco
    $pdo = new PDO(
        "mysql:host=localhost;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Conectado ao MySQL com sucesso!\n";

    // Ler o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/../database/gymforge.sql');
    
    // Executar os comandos SQL
    $pdo->exec($sql);
    
    echo "Banco de dados importado com sucesso!\n";
    
    // Inserir usuário admin padrão
    $pdo->exec("USE gymforge_php");
    
    // Verificar se já existe um admin
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE nivel = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        // Criar usuário admin
        $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, nivel)
            VALUES ('Administrador', 'admin@gymforge.local', ?, 'admin')
        ");
        $stmt->execute([$senha_hash]);
        echo "Usuário admin criado com sucesso!\n";
        echo "Email: admin@gymforge.local\n";
        echo "Senha: admin123\n";
    } else {
        echo "Usuário admin já existe.\n";
    }

    echo "\nSetup concluído com sucesso!\n";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}
<?php
// Configurações do Banco de Dados
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Conectar ao MySQL sem selecionar um banco de dados
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        throw new Exception("Erro na conexão: " . $conn->connect_error);
    }
    
    echo "Conectado ao MySQL com sucesso!\n";
    
    // Criar o banco de dados se não existir
    $sql = "CREATE DATABASE IF NOT EXISTS gymforge_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (!$conn->query($sql)) {
        throw new Exception("Erro ao criar banco de dados: " . $conn->error);
    }
    
    echo "Banco de dados criado/verificado com sucesso!\n";
    
    // Selecionar o banco de dados
    if (!$conn->select_db('gymforge_php')) {
        throw new Exception("Erro ao selecionar banco de dados: " . $conn->error);
    }
    
    echo "Banco de dados selecionado com sucesso!\n";
    
    // Criar tabela de usuários se não existir
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        nivel ENUM('admin', 'cliente', 'visitante') DEFAULT 'visitante',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (!$conn->query($sql)) {
        throw new Exception("Erro ao criar tabela de usuários: " . $conn->error);
    }
    
    echo "Tabela de usuários criada/verificada com sucesso!\n";
    
    // Verificar se já existe um admin
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nivel = 'admin' AND email = 'admin@gymforge.com' LIMIT 1");
    if (!$stmt) {
        throw new Exception("Erro ao preparar consulta: " . $conn->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Criar usuário admin
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Erro ao preparar inserção: " . $conn->error);
        }
        
        $nome = 'Administrador';
        $email = 'admin@gymforge.com';
        $senha = password_hash('admin123', PASSWORD_DEFAULT);
        $nivel = 'admin';
        
        $stmt->bind_param('ssss', $nome, $email, $senha, $nivel);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao criar usuário admin: " . $stmt->error);
        }
        
        echo "Usuário admin criado com sucesso!\n";
        echo "Email: admin@gymforge.com\n";
        echo "Senha: admin123\n";
    } else {
        echo "Usuário admin já existe.\n";
    }
    
    // Criar outras tabelas necessárias
    $tables = [
        "exercicios" => "CREATE TABLE IF NOT EXISTS exercicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            categoria VARCHAR(50) NOT NULL,
            gif_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "treinos" => "CREATE TABLE IF NOT EXISTS treinos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            data DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "treino_exercicios" => "CREATE TABLE IF NOT EXISTS treino_exercicios (
            treino_id INT NOT NULL,
            exercicio_id INT NOT NULL,
            series INT NOT NULL,
            repeticoes INT NOT NULL,
            observacoes TEXT,
            PRIMARY KEY (treino_id, exercicio_id),
            FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE,
            FOREIGN KEY (exercicio_id) REFERENCES exercicios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($tables as $name => $sql) {
        if (!$conn->query($sql)) {
            throw new Exception("Erro ao criar tabela $name: " . $conn->error);
        }
        echo "Tabela $name criada/verificada com sucesso!\n";
    }
    
    echo "\nConfiguração do banco de dados concluída com sucesso!\n";
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage() . "\n");
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 
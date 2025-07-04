-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS gymforge_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gymforge_php;

-- Configurações de Segurança
SET SESSION sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Tabela de Usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'cliente', 'visitante') DEFAULT 'visitante',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

-- Tabela de Exercícios
CREATE TABLE exercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(50) NOT NULL,
    grupo_muscular VARCHAR(50) NOT NULL,
    nivel_dificuldade ENUM('iniciante', 'intermediario', 'avancado') DEFAULT 'iniciante',
    video_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categoria_nivel (categoria, nivel_dificuldade),
    INDEX idx_grupo_muscular (grupo_muscular)
);

-- Tabela de Treinos
CREATE TABLE treinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    nivel_dificuldade ENUM('iniciante', 'intermediario', 'avancado') DEFAULT 'iniciante',
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    status ENUM('ativo', 'concluido', 'arquivado') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_status (usuario_id, status)
);

-- Tabela de Relação Treino-Exercícios
CREATE TABLE treino_exercicios (
    treino_id INT NOT NULL,
    exercicio_id INT NOT NULL,
    ordem INT NOT NULL,
    series INT NOT NULL,
    repeticoes INT NOT NULL,
    observacoes TEXT,
    PRIMARY KEY (treino_id, exercicio_id, ordem),
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE,
    FOREIGN KEY (exercicio_id) REFERENCES exercicios(id) ON DELETE CASCADE
);

-- Inserir usuário admin padrão
INSERT INTO usuarios (nome, email, senha, nivel) VALUES 
('Administrador', 'admin@gymforge.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Senha: password

-- Inserir alguns exercícios de exemplo
INSERT INTO exercicios (nome, descricao, categoria, grupo_muscular, nivel_dificuldade) VALUES
('Supino Reto', 'Exercício clássico para peitoral', 'musculacao', 'peitoral', 'iniciante'),
('Agachamento', 'Exercício fundamental para pernas', 'musculacao', 'pernas', 'iniciante'),
('Barra Fixa', 'Puxada na barra para costas', 'calistenia', 'costas', 'intermediario'); 
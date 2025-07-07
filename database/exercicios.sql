CREATE TABLE IF NOT EXISTS exercicios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    grupo_muscular VARCHAR(50) NOT NULL,
    nivel_dificuldade ENUM('iniciante', 'intermediario', 'avancado') NOT NULL,
    equipamento VARCHAR(100),
    video_url VARCHAR(255),
    imagem_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir alguns exercícios de exemplo
INSERT INTO exercicios (nome, descricao, grupo_muscular, nivel_dificuldade, equipamento) VALUES
('Supino Reto', 'Exercício clássico para peitoral', 'peito', 'intermediario', 'Barra e banco'),
('Agachamento', 'Exercício fundamental para pernas', 'pernas', 'intermediario', 'Barra ou peso livre'),
('Barra Fixa', 'Puxada na barra para costas', 'costas', 'avancado', 'Barra fixa'),
('Rosca Direta', 'Exercício isolado para bíceps', 'bracos', 'iniciante', 'Barra W ou halteres'),
('Prancha', 'Exercício isométrico para core', 'abdomen', 'iniciante', 'Nenhum');

-- Criar banco de dados se não existir
CREATE DATABASE IF NOT EXISTS gymforge_php;
USE gymforge_php;

-- Tabela de exercícios
CREATE TABLE IF NOT EXISTS exercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    categoria ENUM('musculacao', 'cardio', 'funcional', 'alongamento', 'yoga', 'pilates') NOT NULL,
    descricao TEXT,
    nivel_dificuldade ENUM('iniciante', 'intermediario', 'avancado') NOT NULL,
    video_url VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir alguns exercícios de exemplo
INSERT INTO exercicios (nome, categoria, descricao, nivel_dificuldade) VALUES
('Agachamento', 'musculacao', 'Exercício básico para membros inferiores', 'iniciante'),
('Corrida', 'cardio', 'Exercício aeróbico para condicionamento', 'iniciante'),
('Prancha', 'funcional', 'Exercício para core e estabilidade', 'intermediario'),
('Alongamento de Isquiotibiais', 'alongamento', 'Alongamento para parte posterior da coxa', 'iniciante'),
('Postura do Guerreiro', 'yoga', 'Postura para força e equilíbrio', 'intermediario'),
('Exercício de Respiração', 'pilates', 'Técnica básica de respiração', 'iniciante'); 
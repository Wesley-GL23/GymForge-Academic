-- Configurações do MySQL
SET SESSION sql_mode = '';

-- Criação do banco de dados
DROP DATABASE IF EXISTS gymforge_php;
CREATE DATABASE gymforge_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gymforge_php;

-- Tabela de Usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'cliente', 'visitante') DEFAULT 'visitante',
    ultimo_login TIMESTAMP NULL DEFAULT NULL,
    token_recuperacao VARCHAR(255) NULL,
    token_expiracao TIMESTAMP NULL DEFAULT NULL,
    tentativas_login INT DEFAULT 0,
    bloqueado_ate TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token_recuperacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Eventos da Forja
CREATE TABLE forge_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    event_type ENUM('daily', 'weekly', 'special') NOT NULL,
    start_time TIMESTAMP NULL DEFAULT NULL,
    end_time TIMESTAMP NULL DEFAULT NULL,
    status ENUM('upcoming', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming',
    min_level INT DEFAULT 1,
    max_participants INT NULL,
    requirements TEXT,
    rewards TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_status (status, start_time, end_time),
    INDEX idx_event_type (event_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Participação em Eventos
CREATE TABLE event_participation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    character_id INT NOT NULL,
    guild_id INT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progress INT NOT NULL DEFAULT 0,
    completed BOOLEAN NOT NULL DEFAULT FALSE,
    rewards_claimed BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (event_id) REFERENCES forge_events(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    FOREIGN KEY (guild_id) REFERENCES forge_guilds(id) ON DELETE SET NULL,
    UNIQUE KEY unique_participation (event_id, character_id),
    INDEX idx_progress_completion (progress, completed, rewards_claimed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 
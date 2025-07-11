-- Tabela de Notificacoes
CREATE TABLE IF NOT EXISTS forge_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    character_id INT NOT NULL,
    type ENUM('level_up', 'achievement', 'tempering', 'guild', 'event', 'challenge') NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    color VARCHAR(20) NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    INDEX idx_notifications (character_id, read_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

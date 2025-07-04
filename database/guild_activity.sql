-- Tabela de Atividades da Guilda
CREATE TABLE IF NOT EXISTS guild_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guild_id INT NOT NULL,
    character_id INT NOT NULL,
    activity_type ENUM('workout', 'achievement', 'challenge', 'event', 'contribution') NOT NULL,
    points INT NOT NULL DEFAULT 0,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guild_id) REFERENCES forge_guilds(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    INDEX idx_guild_activities (guild_id, activity_type, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 
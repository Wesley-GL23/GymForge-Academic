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

-- Tabela de Conquistas da Guilda
CREATE TABLE IF NOT EXISTS guild_achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guild_id INT NOT NULL,
    achievement_code VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    required_points INT NOT NULL,
    reward_type ENUM('xp', 'perk', 'title', 'emblem') NOT NULL,
    reward_value TEXT NOT NULL,
    unlocked_at TIMESTAMP NULL,
    progress INT NOT NULL DEFAULT 0,
    FOREIGN KEY (guild_id) REFERENCES forge_guilds(id) ON DELETE CASCADE,
    UNIQUE KEY unique_guild_achievement (guild_id, achievement_code),
    INDEX idx_guild_achievements (guild_id, unlocked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Desafios da Guilda
CREATE TABLE IF NOT EXISTS guild_challenges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guild_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    challenge_type ENUM('workout', 'participation', 'recruitment', 'event') NOT NULL,
    goal_value INT NOT NULL,
    current_value INT NOT NULL DEFAULT 0,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,
    reward_type ENUM('xp', 'points', 'achievement') NOT NULL,
    reward_value INT NOT NULL,
    status ENUM('active', 'completed', 'failed') NOT NULL DEFAULT 'active',
    FOREIGN KEY (guild_id) REFERENCES forge_guilds(id) ON DELETE CASCADE,
    INDEX idx_guild_challenges (guild_id, status, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Participação em Desafios
CREATE TABLE IF NOT EXISTS challenge_participation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    challenge_id INT NOT NULL,
    character_id INT NOT NULL,
    contribution_value INT NOT NULL DEFAULT 0,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (challenge_id) REFERENCES guild_challenges(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    UNIQUE KEY unique_challenge_participant (challenge_id, character_id),
    INDEX idx_challenge_participation (challenge_id, contribution_value)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 
-- Tabela de Personagens
CREATE TABLE IF NOT EXISTS forge_characters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    current_rank VARCHAR(50) NOT NULL DEFAULT 'novice_forger',
    level INT NOT NULL DEFAULT 1,
    total_xp BIGINT NOT NULL DEFAULT 0,
    total_workouts INT NOT NULL DEFAULT 0,
    total_exercises INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Atributos do Personagem
CREATE TABLE IF NOT EXISTS forge_attributes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    character_id INT NOT NULL,
    strength INT NOT NULL DEFAULT 1,
    endurance INT NOT NULL DEFAULT 1,
    technique INT NOT NULL DEFAULT 1,
    wisdom INT NOT NULL DEFAULT 1,
    attribute_points INT NOT NULL DEFAULT 0,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Têmpera dos Músculos
CREATE TABLE IF NOT EXISTS muscle_tempering (
    id INT PRIMARY KEY AUTO_INCREMENT,
    character_id INT NOT NULL,
    muscle_group VARCHAR(50) NOT NULL,
    current_level INT NOT NULL DEFAULT 0,
    total_exercises INT NOT NULL DEFAULT 0,
    last_exercise_date TIMESTAMP NULL,
    visual_stage VARCHAR(50) NOT NULL DEFAULT 'bronze_cold',
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    UNIQUE KEY unique_muscle_character (character_id, muscle_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Conquistas
CREATE TABLE IF NOT EXISTS forge_achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    character_id INT NOT NULL,
    achievement_code VARCHAR(50) NOT NULL,
    unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progress INT NOT NULL DEFAULT 0,
    completed BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    UNIQUE KEY unique_achievement_character (character_id, achievement_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Guildas
CREATE TABLE IF NOT EXISTS forge_guilds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    leader_id INT NOT NULL,
    level INT NOT NULL DEFAULT 1,
    total_xp BIGINT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    emblem_url VARCHAR(255),
    FOREIGN KEY (leader_id) REFERENCES forge_characters(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Membros da Guilda
CREATE TABLE IF NOT EXISTS guild_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guild_id INT NOT NULL,
    character_id INT NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    contribution_points INT NOT NULL DEFAULT 0,
    FOREIGN KEY (guild_id) REFERENCES forge_guilds(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    UNIQUE KEY unique_member_guild (guild_id, character_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Eventos da Forja
CREATE TABLE IF NOT EXISTS forge_events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_type VARCHAR(50) NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    requirements TEXT,
    rewards TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- Tabela de Participação em Eventos
CREATE TABLE IF NOT EXISTS event_participation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    character_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progress INT NOT NULL DEFAULT 0,
    completed BOOLEAN NOT NULL DEFAULT FALSE,
    rewards_claimed BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (event_id) REFERENCES forge_events(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    UNIQUE KEY unique_participation (event_id, character_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- Tabela de Notificações
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

-- Índices para otimização
CREATE INDEX idx_character_rank ON forge_characters(current_rank);
CREATE INDEX idx_character_level ON forge_characters(level);
CREATE INDEX idx_muscle_level ON muscle_tempering(current_level);
CREATE INDEX idx_achievement_code ON forge_achievements(achievement_code);
CREATE INDEX idx_event_dates ON forge_events(start_time, end_time);
CREATE INDEX idx_guild_level ON forge_guilds(level); 
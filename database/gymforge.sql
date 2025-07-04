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
    ultimo_login TIMESTAMP NULL,
    token_recuperacao VARCHAR(255) NULL,
    token_expiracao TIMESTAMP NULL,
    tentativas_login INT DEFAULT 0,
    bloqueado_ate TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token_recuperacao)
);

-- Tabela de Exercícios
CREATE TABLE exercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(50) NOT NULL,
    grupo_muscular VARCHAR(50) NOT NULL,
    nivel_dificuldade ENUM('iniciante', 'intermediario', 'avancado') DEFAULT 'iniciante',
    gif_url VARCHAR(255),
    video_url VARCHAR(255),
    instrucoes TEXT,
    dicas_seguranca TEXT,
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
    tipo ENUM('normal', 'desafio', 'evento') DEFAULT 'normal',
    nivel_dificuldade ENUM('iniciante', 'intermediario', 'avancado') DEFAULT 'iniciante',
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    status ENUM('ativo', 'concluido', 'arquivado') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_status (usuario_id, status),
    INDEX idx_datas (data_inicio, data_fim)
);

-- Tabela de Relação Treino-Exercícios
CREATE TABLE treino_exercicios (
    treino_id INT NOT NULL,
    exercicio_id INT NOT NULL,
    ordem INT NOT NULL,
    series INT NOT NULL,
    repeticoes INT NOT NULL,
    peso DECIMAL(5,2) NULL,
    tempo_descanso INT NULL COMMENT 'em segundos',
    observacoes TEXT,
    PRIMARY KEY (treino_id, exercicio_id, ordem),
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE,
    FOREIGN KEY (exercicio_id) REFERENCES exercicios(id) ON DELETE CASCADE
);

-- Tabela de Progresso dos Treinos
CREATE TABLE progresso_treinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    treino_id INT NOT NULL,
    exercicio_id INT NOT NULL,
    usuario_id INT NOT NULL,
    data_execucao TIMESTAMP NOT NULL,
    series_completadas INT NOT NULL,
    peso_utilizado DECIMAL(5,2) NULL,
    dificuldade_percebida INT NULL COMMENT '1-10',
    observacoes TEXT,
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE,
    FOREIGN KEY (exercicio_id) REFERENCES exercicios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_data (usuario_id, data_execucao)
);

-- Tabela de Personagens
CREATE TABLE forge_characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    current_rank VARCHAR(50) NOT NULL DEFAULT 'novice_forger',
    level INT NOT NULL DEFAULT 1,
    total_xp BIGINT NOT NULL DEFAULT 0,
    total_workouts INT NOT NULL DEFAULT 0,
    total_exercises INT NOT NULL DEFAULT 0,
    last_daily_reward TIMESTAMP NULL,
    streak_days INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_rank_level (current_rank, level)
);

-- Tabela de Atributos do Personagem
CREATE TABLE forge_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    character_id INT NOT NULL,
    strength INT NOT NULL DEFAULT 1,
    endurance INT NOT NULL DEFAULT 1,
    technique INT NOT NULL DEFAULT 1,
    wisdom INT NOT NULL DEFAULT 1,
    attribute_points INT NOT NULL DEFAULT 0,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE
);

-- Tabela de Têmpera dos Músculos
CREATE TABLE muscle_tempering (
    id INT AUTO_INCREMENT PRIMARY KEY,
    character_id INT NOT NULL,
    muscle_group VARCHAR(50) NOT NULL,
    current_level INT NOT NULL DEFAULT 0,
    total_exercises INT NOT NULL DEFAULT 0,
    last_exercise_date TIMESTAMP NULL,
    visual_stage VARCHAR(50) NOT NULL DEFAULT 'bronze_cold',
    heat_points INT NOT NULL DEFAULT 0,
    cooldown_rate INT NOT NULL DEFAULT 1,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    UNIQUE KEY unique_muscle_character (character_id, muscle_group),
    INDEX idx_visual_stage (visual_stage)
);

-- Tabela de Conquistas
CREATE TABLE forge_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    character_id INT NOT NULL,
    achievement_code VARCHAR(50) NOT NULL,
    unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progress INT NOT NULL DEFAULT 0,
    completed BOOLEAN NOT NULL DEFAULT FALSE,
    rewards_claimed BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    UNIQUE KEY unique_achievement_character (character_id, achievement_code),
    INDEX idx_completed_claimed (completed, rewards_claimed)
);

-- Tabela de Guildas
CREATE TABLE forge_guilds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    leader_id INT NOT NULL,
    level INT NOT NULL DEFAULT 1,
    total_xp BIGINT NOT NULL DEFAULT 0,
    member_count INT NOT NULL DEFAULT 1,
    max_members INT NOT NULL DEFAULT 50,
    weekly_activity_points INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    emblem_url VARCHAR(255),
    banner_url VARCHAR(255),
    recruitment_status ENUM('open', 'closed', 'invite_only') DEFAULT 'open',
    min_level_required INT DEFAULT 1,
    FOREIGN KEY (leader_id) REFERENCES forge_characters(id),
    INDEX idx_recruitment (recruitment_status, min_level_required)
);

-- Tabela de Membros da Guilda
CREATE TABLE guild_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guild_id INT NOT NULL,
    character_id INT NOT NULL,
    role ENUM('leader', 'officer', 'member') NOT NULL DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    contribution_points INT NOT NULL DEFAULT 0,
    last_activity TIMESTAMP NULL,
    FOREIGN KEY (guild_id) REFERENCES forge_guilds(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    UNIQUE KEY unique_member_guild (guild_id, character_id),
    INDEX idx_role_points (role, contribution_points)
);

-- Tabela de Solicitações de Entrada
CREATE TABLE guild_join_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guild_id INT NOT NULL,
    character_id INT NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    response_date TIMESTAMP NULL,
    responded_by INT NULL,
    message TEXT,
    FOREIGN KEY (guild_id) REFERENCES forge_guilds(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    FOREIGN KEY (responded_by) REFERENCES forge_characters(id) ON DELETE SET NULL,
    INDEX idx_guild_status (guild_id, status),
    INDEX idx_request_dates (request_date, response_date)
);

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
);

-- Tabela de Logs do Sistema
CREATE TABLE system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_action_date (action_type, created_at)
);

-- Configurações do Sistema
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT NULL,
    FOREIGN KEY (updated_by) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Inserir configurações iniciais do sistema
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('max_login_attempts', '5', 'Número máximo de tentativas de login antes do bloqueio'),
('login_block_duration', '900', 'Duração do bloqueio de login em segundos (15 minutos)'),
('password_reset_expiration', '3600', 'Tempo de expiração do token de redefinição de senha em segundos (1 hora)'),
('maintenance_mode', 'false', 'Sistema em modo de manutenção'),
('guild_creation_min_level', '10', 'Nível mínimo para criar uma guilda'),
('daily_reset_time', '00:00:00', 'Horário de reset das atividades diárias');

-- Remover senha do admin do SQL e deixar para ser definida durante a instalação
-- INSERT INTO usuarios (nome, email, senha, nivel) VALUES 
-- ('Administrador', 'admin@gymforge.com', '[SENHA_REMOVIDA]', 'admin');

-- Inserir categorias de exercícios iniciais
INSERT INTO exercicios (nome, categoria, grupo_muscular, nivel_dificuldade, descricao) VALUES 
('Supino Reto', 'Musculação', 'Peito', 'intermediario', 'Exercício clássico para desenvolvimento do peitoral'),
('Agachamento', 'Musculação', 'Pernas', 'intermediario', 'Exercício composto para desenvolvimento das pernas'),
('Barra Fixa', 'Calistenia', 'Costas', 'avancado', 'Exercício para desenvolvimento das costas e braços'),
('Flexão de Braço', 'Calistenia', 'Peito', 'iniciante', 'Exercício básico para peitoral e tríceps'),
('Prancha', 'Core', 'Abdômen', 'iniciante', 'Exercício isométrico para fortalecimento do core');

-- Tabela de Jobs do Sistema
CREATE TABLE system_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_type VARCHAR(50) NOT NULL,
    start_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    status ENUM('running', 'completed', 'failed') NOT NULL DEFAULT 'running',
    error_message TEXT NULL,
    stats JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_job_type_status (job_type, status),
    INDEX idx_start_time (start_time)
);

-- Tabela de Erros do Sistema
CREATE TABLE system_errors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NULL,
    error_type VARCHAR(50) NOT NULL,
    error_message TEXT NOT NULL,
    character_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES system_jobs(id) ON DELETE SET NULL,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE SET NULL,
    INDEX idx_error_type (error_type),
    INDEX idx_created_at (created_at)
);
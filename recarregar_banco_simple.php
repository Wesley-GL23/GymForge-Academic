<?php
/**
 * Script simples para recriar o banco de dados
 * ATENÇÃO: Este script irá apagar todos os dados existentes!
 */

try {
    // Conecta ao MySQL sem selecionar um banco de dados
    $conn = new PDO(
        "mysql:host=localhost;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );

    // Apaga o banco de dados se existir
    $conn->exec("DROP DATABASE IF EXISTS gymforge_php");
    echo "Banco de dados antigo removido.\n";

    // Cria o banco de dados
    $conn->exec("CREATE DATABASE IF NOT EXISTS gymforge_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->exec("USE gymforge_php");
    echo "Banco de dados criado.\n";

    // Configura o modo SQL
    $conn->exec("SET SESSION sql_mode = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION'");

    // Cria as tabelas básicas
    $tables = [
        // Tabela de Usuários
        "CREATE TABLE usuarios (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Exercícios
        "CREATE TABLE exercicios (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Personagens
        "CREATE TABLE forge_characters (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            current_rank VARCHAR(50) NOT NULL DEFAULT 'novice_forger',
            level INT NOT NULL DEFAULT 1,
            total_xp BIGINT NOT NULL DEFAULT 0,
            total_workouts INT NOT NULL DEFAULT 0,
            total_exercises INT NOT NULL DEFAULT 0,
            last_daily_reward TIMESTAMP NULL DEFAULT NULL,
            streak_days INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_rank_level (current_rank, level)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Atributos do Personagem
        "CREATE TABLE forge_attributes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            character_id INT NOT NULL,
            strength INT NOT NULL DEFAULT 1,
            endurance INT NOT NULL DEFAULT 1,
            technique INT NOT NULL DEFAULT 1,
            wisdom INT NOT NULL DEFAULT 1,
            attribute_points INT NOT NULL DEFAULT 0,
            FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Têmpera dos Músculos
        "CREATE TABLE muscle_tempering (
            id INT AUTO_INCREMENT PRIMARY KEY,
            character_id INT NOT NULL,
            muscle_group VARCHAR(50) NOT NULL,
            current_level INT NOT NULL DEFAULT 0,
            total_exercises INT NOT NULL DEFAULT 0,
            last_exercise_date TIMESTAMP NULL DEFAULT NULL,
            visual_stage VARCHAR(50) NOT NULL DEFAULT 'bronze_cold',
            heat_points INT NOT NULL DEFAULT 0,
            cooldown_rate INT NOT NULL DEFAULT 1,
            FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
            UNIQUE KEY unique_muscle_character (character_id, muscle_group),
            INDEX idx_visual_stage (visual_stage)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Conquistas
        "CREATE TABLE forge_achievements (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Guildas
        "CREATE TABLE forge_guilds (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Membros da Guilda
        "CREATE TABLE guild_members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            guild_id INT NOT NULL,
            character_id INT NOT NULL,
            role ENUM('leader', 'officer', 'member') NOT NULL DEFAULT 'member',
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            contribution_points INT NOT NULL DEFAULT 0,
            last_activity TIMESTAMP NULL DEFAULT NULL,
            FOREIGN KEY (guild_id) REFERENCES forge_guilds(id) ON DELETE CASCADE,
            FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
            UNIQUE KEY unique_member_guild (guild_id, character_id),
            INDEX idx_role_points (role, contribution_points)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Eventos da Forja
        "CREATE TABLE forge_events (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Participação em Eventos
        "CREATE TABLE event_participation (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Logs do Sistema
        "CREATE TABLE system_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            action_type VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            INDEX idx_action_date (action_type, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Configurações do Sistema
        "CREATE TABLE system_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT NOT NULL,
            setting_type ENUM('string', 'integer', 'boolean', 'json') NOT NULL DEFAULT 'string',
            description TEXT,
            is_public BOOLEAN NOT NULL DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_setting_key (setting_key),
            INDEX idx_public_settings (is_public)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Jobs do Sistema
        "CREATE TABLE system_jobs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            job_type VARCHAR(50) NOT NULL,
            job_data JSON NULL,
            status ENUM('pending', 'running', 'completed', 'failed') NOT NULL DEFAULT 'pending',
            priority INT NOT NULL DEFAULT 0,
            attempts INT NOT NULL DEFAULT 0,
            max_attempts INT NOT NULL DEFAULT 3,
            scheduled_at TIMESTAMP NULL DEFAULT NULL,
            started_at TIMESTAMP NULL DEFAULT NULL,
            completed_at TIMESTAMP NULL DEFAULT NULL,
            error_message TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_job_status (status, priority),
            INDEX idx_job_scheduled (scheduled_at, status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Tabela de Erros do Sistema
        "CREATE TABLE system_errors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            error_type VARCHAR(50) NOT NULL,
            error_message TEXT NOT NULL,
            error_code VARCHAR(50) NULL,
            file_path VARCHAR(255) NULL,
            line_number INT NULL,
            stack_trace TEXT NULL,
            user_id INT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            request_data JSON NULL,
            resolved BOOLEAN NOT NULL DEFAULT FALSE,
            resolved_at TIMESTAMP NULL DEFAULT NULL,
            resolved_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            FOREIGN KEY (resolved_by) REFERENCES usuarios(id) ON DELETE SET NULL,
            INDEX idx_error_type (error_type),
            INDEX idx_error_resolved (resolved, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];

    // Executa cada comando de criação de tabela
    foreach ($tables as $table) {
        try {
            $conn->exec($table);
            echo "Tabela criada com sucesso.\n";
        } catch (PDOException $e) {
            echo "Erro ao criar tabela:\n{$table}\n\nErro: " . $e->getMessage() . "\n\n";
        }
    }

    echo "Processo concluído com sucesso!\n";

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
} 
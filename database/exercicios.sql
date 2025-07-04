CREATE TABLE IF NOT EXISTS exercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    grupo_muscular VARCHAR(50) NOT NULL,
    nivel_dificuldade VARCHAR(20) NOT NULL DEFAULT 'iniciante',
    gif_url VARCHAR(255),
    video_url VARCHAR(255),
    instrucoes TEXT NOT NULL,
    dicas_seguranca TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

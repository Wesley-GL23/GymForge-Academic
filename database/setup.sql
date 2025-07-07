-- Criar banco de dados se não existir
CREATE DATABASE IF NOT EXISTS gymforge_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gymforge_php;

-- Importar tabelas do sistema
SOURCE forge_tables.sql;
SOURCE usuarios.sql;

-- Criar índices adicionais
CREATE INDEX idx_user_email ON usuarios(email);
CREATE INDEX idx_user_nivel ON usuarios(nivel);

-- Inserir dados iniciais se necessário
INSERT INTO usuarios (nome, email, senha, nivel) 
VALUES ('Admin', 'admin@gymforge.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador')
ON DUPLICATE KEY UPDATE id=id; 
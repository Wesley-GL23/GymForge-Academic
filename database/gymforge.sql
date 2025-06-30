-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS gymforge_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gymforge_php;

-- Tabela de Usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'cliente', 'visitante') DEFAULT 'visitante',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de Exercícios
CREATE TABLE exercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(50) NOT NULL,
    gif_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de Treinos
CREATE TABLE treinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    data DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Relação Treino-Exercícios
CREATE TABLE treino_exercicios (
    treino_id INT NOT NULL,
    exercicio_id INT NOT NULL,
    series INT NOT NULL,
    repeticoes INT NOT NULL,
    observacoes TEXT,
    PRIMARY KEY (treino_id, exercicio_id),
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE,
    FOREIGN KEY (exercicio_id) REFERENCES exercicios(id) ON DELETE CASCADE
);

-- Tabela de Notificações
CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Dicas
CREATE TABLE dicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    conteudo TEXT NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela para recuperação de senha
CREATE TABLE IF NOT EXISTS recuperacao_senha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expira DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela para registrar treinos concluídos
CREATE TABLE IF NOT EXISTS treinos_concluidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    treino_id INT NOT NULL,
    data_conclusao DATETIME NOT NULL,
    duracao_minutos INT,
    nivel_dificuldade ENUM('1', '2', '3', '4', '5') NOT NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (treino_id) REFERENCES treinos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela para registrar medidas do usuário
CREATE TABLE IF NOT EXISTS medidas_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_registro DATE NOT NULL,
    peso DECIMAL(5,2),
    altura DECIMAL(3,2),
    imc DECIMAL(4,2),
    circunferencia_braco DECIMAL(4,2),
    circunferencia_antebraco DECIMAL(4,2),
    circunferencia_peito DECIMAL(4,2),
    circunferencia_cintura DECIMAL(4,2),
    circunferencia_quadril DECIMAL(4,2),
    circunferencia_coxa DECIMAL(4,2),
    circunferencia_panturrilha DECIMAL(4,2),
    percentual_gordura DECIMAL(4,2),
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir usuário admin padrão (senha: admin123)
INSERT INTO usuarios (nome, email, senha, nivel) VALUES 
('Administrador', 'admin@gymforge.com', '$2y$10$8tDjcgyF.lqRa.dXn7QO5O5wh.qZy0XkCkuuE1qXk8vGz6D0Et6Hy', 'admin');

-- Inserir algumas categorias de exercícios iniciais
INSERT INTO exercicios (nome, categoria, descricao) VALUES 
('Supino Reto', 'Peito', 'Exercício clássico para desenvolvimento do peitoral'),
('Agachamento', 'Pernas', 'Exercício composto para desenvolvimento das pernas'),
('Barra Fixa', 'Costas', 'Exercício para desenvolvimento das costas e braços');

-- Inserir algumas dicas iniciais
INSERT INTO dicas (titulo, categoria, conteudo) VALUES 
('Importância da Hidratação', 'Saúde', 'Beber água durante o treino é fundamental para manter o corpo hidratado e garantir um bom desempenho. Mantenha sempre uma garrafa de água por perto.'),
('Descanso entre Séries', 'Treino', 'O tempo ideal de descanso entre séries varia de acordo com seu objetivo. Para hipertrofia, 1-2 minutos. Para força, 2-3 minutos.'); 
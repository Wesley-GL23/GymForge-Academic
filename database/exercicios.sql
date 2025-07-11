CREATE TABLE IF NOT EXISTS exercicios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(50) NOT NULL,
    grupo_muscular VARCHAR(50) NOT NULL,
    nivel_dificuldade ENUM('iniciante', 'intermediario', 'avancado') NOT NULL,
    equipamento VARCHAR(100),
    video_url VARCHAR(255),
    imagem_url VARCHAR(255),
    instrucoes TEXT,
    dicas_seguranca TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir alguns exercícios de exemplo
INSERT INTO exercicios (nome, descricao, categoria, grupo_muscular, nivel_dificuldade, equipamento, instrucoes, dicas_seguranca) VALUES
('Supino Reto', 'Exercício clássico para desenvolvimento do peitoral', 'musculacao', 'peito', 'intermediario', 'Barra e banco', 'Deite-se no banco com os pés apoiados no chão. Segure a barra com as mãos um pouco mais abertas que a largura dos ombros. Desça a barra controladamente até tocar levemente o peito e empurre de volta à posição inicial.', 'Mantenha os cotovelos em um ângulo de 45-90 graus. Não arquee as costas. Respire corretamente: inspire na descida e expire na subida.'),

('Agachamento', 'Exercício fundamental para fortalecimento das pernas', 'musculacao', 'pernas', 'intermediario', 'Barra ou peso livre', 'Posicione a barra nos ombros, pés na largura dos quadris. Desça como se fosse sentar em uma cadeira, mantendo o peito erguido e os joelhos alinhados com os pés. Retorne à posição inicial.', 'Mantenha a coluna neutra. Não deixe os joelhos passarem a ponta dos pés. Respire corretamente: inspire na descida e expire na subida.'),

('Barra Fixa', 'Exercício completo para costas e braços', 'musculacao', 'costas', 'avancado', 'Barra fixa', 'Segure a barra com as palmas das mãos voltadas para frente, um pouco mais abertas que a largura dos ombros. Puxe o corpo para cima até que o queixo ultrapasse a barra. Desça controladamente.', 'Evite balançar o corpo. Mantenha o core contraído. Se necessário, use faixas elásticas para assistência.'),

('Rosca Direta', 'Exercício isolado para bíceps', 'musculacao', 'bracos', 'iniciante', 'Barra W ou halteres', 'Em pé, segure os pesos com as palmas voltadas para cima. Mantenha os cotovelos junto ao corpo e levante os pesos até a altura dos ombros. Desça controladamente.', 'Não balance o corpo. Mantenha os cotovelos fixos. Controle a velocidade do movimento.'),

('Prancha', 'Exercício isométrico para fortalecimento do core', 'funcional', 'abdomen', 'iniciante', 'Nenhum', 'Apoie os antebraços no chão, alinhados com os ombros. Estenda as pernas para trás, apoiando-se nas pontas dos pés. Mantenha o corpo em linha reta e o abdômen contraído.', 'Não deixe o quadril cair. Mantenha a respiração constante. Comece com 20 segundos e aumente gradualmente.');

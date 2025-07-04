-- Tabela de Solicitações de Entrada
CREATE TABLE IF NOT EXISTS guild_join_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guild_id INT NOT NULL,
    character_id INT NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    response_date TIMESTAMP NULL,
    responded_by INT NULL,
    FOREIGN KEY (guild_id) REFERENCES forge_guilds(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES forge_characters(id) ON DELETE CASCADE,
    FOREIGN KEY (responded_by) REFERENCES forge_characters(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices para otimização
CREATE INDEX idx_guild_requests ON guild_join_requests(guild_id, status);
CREATE INDEX idx_character_requests ON guild_join_requests(character_id, status);
CREATE INDEX idx_request_dates ON guild_join_requests(request_date, response_date); 
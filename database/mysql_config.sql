-- Configurações do MySQL para permitir TIMESTAMP
SET SESSION sql_mode = '';
SET SESSION explicit_defaults_for_timestamp = 0;

-- Criação do banco de dados
DROP DATABASE IF EXISTS gymforge_php;
CREATE DATABASE IF NOT EXISTS gymforge_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gymforge_php; 
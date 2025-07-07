-- Tabela para tokens de "lembrar senha"
CREATE TABLE IF NOT EXISTS `tokens_lembrar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expira` datetime NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_id` (`usuario_id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_expira` (`expira`),
  CONSTRAINT `fk_tokens_lembrar_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índice para limpeza automática de tokens expirados
CREATE INDEX `idx_tokens_lembrar_limpeza` ON `tokens_lembrar` (`expira`) WHERE `expira` < NOW(); 
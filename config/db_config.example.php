<?php
// Configurações do Banco de Dados
define('DB_HOST', 'localhost');     // Host do banco de dados
define('DB_NAME', 'gymforge');      // Nome do banco de dados
define('DB_USER', 'root');         // Usuário do banco de dados
define('DB_PASS', '');             // Senha do banco de dados

// Configurações do Site
define('SITE_URL', 'http://gymforge.local');  // URL base do site
define('SITE_NAME', 'GymForge');              // Nome do site
define('SITE_EMAIL', 'gymforge.team@gmail.com'); // Email principal

// Configurações de Sessão
define('SESSION_NAME', 'GYMFORGE_SESSION');
define('SESSION_LIFETIME', 7200);  // 2 horas em segundos

// Configurações de Upload
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 10485760);  // 10MB em bytes

// Configurações de Debug
define('DEBUG_MODE', true);  // Mudar para false em produção
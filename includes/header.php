<?php
// Ativar exibição de erros para debug apenas em desenvolvimento
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configurações e constantes
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

// Funções de autenticação
require_once __DIR__ . '/auth_functions.php';

// Sessão já foi iniciada no config.php, não precisa iniciar novamente
// session_start(); // REMOVIDO - já iniciado no config.php

// Informações do usuário logado
$usuario_atual = usuarioAtual();
$is_logged = estaLogado();
$user_level = $usuario_atual ? $usuario_atual['nivel'] : null;

// Título padrão se não definido
if (!isset($titulo)) {
    $titulo = 'GymForge';
}

// Forçar HTTPS apenas em produção
if (!DEBUG_MODE && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: " . $redirect);
    exit;
}

// Debug - Mostrar diretório atual apenas em debug
if (DEBUG_MODE) {
    echo "<!-- Debug: Current Directory = " . __DIR__ . " -->";
    echo "<!-- Debug: BASE_URL = " . BASE_URL . " -->";
}

// Verificar se BASE_URL está definida
if (!defined('BASE_URL')) {
    die('Erro: BASE_URL não está definida. Verifique se o arquivo config.php foi carregado.');
}

// Incluir funções de mensagem se ainda não foram incluídas
if (!function_exists('setMessage')) {
    require_once __DIR__ . '/message_functions.php';
}

// Definir página atual
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymForge - <?php echo $titulo ?? 'Forjando sua melhor versão'; ?></title>
    
    <!-- Meta Tags -->
    <meta name="description" content="GymForge - Plataforma de treinos personalizados com gamificação. Transforme seu corpo e mente com treinos eficientes e divertidos.">
    <meta name="keywords" content="academia, treino, exercícios, fitness, musculação, gamificação">
    <meta name="author" content="GymForge">
    <meta name="theme-color" content="#FF6B00">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo BASE_URL; ?>">
    <meta property="og:title" content="GymForge - <?php echo $titulo ?? 'Forjando sua melhor versão'; ?>">
    <meta property="og:description" content="Transforme seu corpo e mente com o GymForge. Treinos personalizados e gamificação para sua evolução.">
    <meta property="og:image" content="<?php echo BASE_URL; ?>assets/img/gymforge-badge.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo BASE_URL; ?>">
    <meta property="twitter:title" content="GymForge - <?php echo $titulo ?? 'Forjando sua melhor versão'; ?>">
    <meta property="twitter:description" content="Transforme seu corpo e mente com o GymForge. Treinos personalizados e gamificação para sua evolução.">
    <meta property="twitter:image" content="<?php echo BASE_URL; ?>assets/img/gymforge-badge.png">
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/GymForge-Academic/assets/img/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/GymForge-Academic/assets/img/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/GymForge-Academic/assets/img/favicon.png">
    <link rel="manifest" href="/GymForge-Academic/site.webmanifest">
    
    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="/GymForge-Academic/assets/css/gymforge.css">
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark glass-effect fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/GymForge-Academic/" data-aos="fade-right">
                <img src="/GymForge-Academic/assets/img/logo-white.png" alt="GymForge" height="40" class="d-inline-block align-text-top">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars text-forge-accent"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item" data-aos="fade-down" data-aos-delay="100">
                        <a class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" href="/GymForge-Academic/views/dashboard/">
                            <i class="fas fa-chart-line me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item" data-aos="fade-down" data-aos-delay="200">
                        <a class="nav-link <?php echo $current_page === 'biblioteca' ? 'active' : ''; ?>" href="/GymForge-Academic/views/exercicios/biblioteca.php">
                            <i class="fas fa-dumbbell me-2"></i>Exercícios
                        </a>
                    </li>
                    <li class="nav-item" data-aos="fade-down" data-aos-delay="300">
                        <a class="nav-link <?php echo $current_page === 'treinos' ? 'active' : ''; ?>" href="/GymForge-Academic/views/treinos/">
                            <i class="fas fa-running me-2"></i>Meus Treinos
                        </a>
                    </li>
                    <?php if (estaLogado() && isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin'): ?>
                    <li class="nav-item" data-aos="fade-down" data-aos-delay="400">
                        <a class="nav-link <?php echo $current_page === 'admin' ? 'active' : ''; ?>" href="/GymForge-Academic/views/admin/">
                            <i class="fas fa-shield-alt me-2"></i>Admin
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <?php if (estaLogado()): ?>
                <div class="d-flex align-items-center" data-aos="fade-left">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle fa-lg text-forge-accent me-2"></i>
                            <span class="text-forge-accent"><?php echo $_SESSION['user_name'] ?? 'Usuário'; ?></span>
                            <?php if (isset($_SESSION['user_level'])): ?>
                            <span class="badge bg-forge-accent text-dark ms-2"><?php echo ucfirst($_SESSION['user_level']); ?></span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu glass-effect-light dropdown-menu-end">
                            <li>
                                <a class="dropdown-item text-white" href="/GymForge-Academic/views/perfil/">
                                    <i class="fas fa-user me-2"></i>Meu Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-white" href="/GymForge-Academic/views/configuracoes/">
                                    <i class="fas fa-cog me-2"></i>Configurações
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-forge-accent" href="/GymForge-Academic/actions/usuario/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php else: ?>
                <div class="d-flex gap-2" data-aos="fade-left">
                    <a href="/GymForge-Academic/login.php" class="btn btn-outline-accent">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <a href="/GymForge-Academic/cadastro.php" class="btn btn-primary forge-glow">
                        <i class="fas fa-user-plus me-2"></i>Cadastre-se
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="flash-messages-container">
        <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success fade-in glass-effect-light" role="alert" data-aos="fade-down">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
            echo $_SESSION['sucesso'];
            unset($_SESSION['sucesso']);
            ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger fade-in glass-effect-light" role="alert" data-aos="fade-down">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php 
            echo $_SESSION['erro'];
            unset($_SESSION['erro']);
            ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Main Container -->
    <main class="forge-main">
        <div class="forge-container">
    <!-- Scripts serão carregados no footer.php -->
</body>
</html> 
<?php
// Ativar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações e constantes
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

// Funções de autenticação
require_once __DIR__ . '/auth_functions.php';

// Iniciar ou recuperar sessão
session_start();

// Informações do usuário logado
$usuario_atual = usuarioAtual();
$is_logged = estaLogado();
$user_level = $usuario_atual ? $usuario_atual['nivel'] : null;

// Título padrão se não definido
if (!isset($titulo)) {
    $titulo = 'GymForge';
}

// Forçar HTTPS
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: " . $redirect);
    exit;
}

// Debug - Mostrar diretório atual
echo "<!-- Debug: Current Directory = " . __DIR__ . " -->";

// Verificar se BASE_URL está definida
if (!defined('BASE_URL')) {
    die('Erro: BASE_URL não está definida. Verifique se o arquivo config.php foi carregado.');
}

// Debug - mostrar valor da BASE_URL
echo "<!-- Debug: BASE_URL = " . BASE_URL . " -->";

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
    <title><?php echo $titulo; ?> - GymForge</title>
    
    <!-- Meta Tags SEO -->
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'GymForge - Plataforma completa para gerenciamento de academias e acompanhamento de treinos. Transforme sua jornada fitness.'; ?>">
    <meta name="keywords" content="academia, treinos, exercícios, fitness, saúde, GymForge">
    <meta name="author" content="GymForge">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>assets/img/gymforge-badge.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>assets/img/gymforge-badge.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>assets/img/gymforge-badge.png">
    <link rel="manifest" href="<?php echo BASE_URL; ?>site.webmanifest">
    <meta name="theme-color" content="#1A1A1A">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>GymForge">
    <meta property="og:description" content="<?php echo isset($page_description) ? $page_description : 'Transforme sua jornada fitness com o GymForge.'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:image" content="<?php echo BASE_URL; ?>assets/img/gymforge-logo.jpeg">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/forge.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
    
    <!-- Preload de fontes críticas -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap"></noscript>
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "GymForge",
        "description": "Plataforma completa para gerenciamento de academias e acompanhamento de treinos",
        "url": "<?php echo BASE_URL; ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo BASE_URL; ?>search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark forge-navbar">
        <div class="container">
            <a class="navbar-brand forge-brand" href="<?php echo BASE_URL; ?>">
                <div class="brand-logo">
                    <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="GymForge" class="brand-image">
                </div>
                <span class="brand-text">GymForge</span>
            </a>
            
            <button class="navbar-toggler forge-button" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link forge-nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>">
                            <i class="bi bi-house-door"></i>
                            <span>Início</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link forge-nav-link <?php echo $current_page === 'biblioteca' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>views/exercicios/biblioteca.php">
                            <i class="bi bi-journal-text"></i>
                            <span>Exercícios</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link forge-nav-link <?php echo $current_page === 'treinos' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>views/treinos/">
                            <i class="bi bi-lightning-charge"></i>
                            <span>Treinos</span>
                        </a>
                    </li>
                    <?php if ($is_logged): ?>
                    <li class="nav-item">
                        <a class="nav-link forge-nav-link <?php echo $current_page === 'character' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>views/forge/character.php">
                            <i class="bi bi-person-badge"></i>
                            <span>Meu Personagem</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link forge-nav-link <?php echo $current_page === 'guilds' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>views/forge/guilds.php">
                            <i class="bi bi-people"></i>
                            <span>Guildas</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if ($is_logged): ?>
                        <?php if ($user_level === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link forge-nav-link" href="<?php echo BASE_URL; ?>views/admin/">
                                <i class="bi bi-gear"></i>
                                <span>Admin</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link forge-nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="user-avatar">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <span class="d-none d-lg-inline"><?php echo $usuario_atual['nome']; ?></span>
                            </a>
                            <ul class="dropdown-menu forge-dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>views/dashboard/">
                                        <i class="bi bi-speedometer2"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>views/perfil/">
                                        <i class="bi bi-person"></i>
                                        <span>Meu Perfil</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>actions/usuario/logout.php">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span>Sair</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="forge-button forge-button--secondary me-2" href="<?php echo BASE_URL; ?>forms/usuario/login.php">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Entrar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="forge-button forge-button--primary" href="<?php echo BASE_URL; ?>forms/usuario/cadastro.php">
                                <i class="bi bi-person-plus"></i>
                                <span>Cadastrar</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert forge-alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show" role="alert">
        <div class="alert-icon">
            <?php
            $icon = 'info-circle';
            switch ($_SESSION['mensagem']['tipo']) {
                case 'success':
                    $icon = 'check-circle';
                    break;
                case 'danger':
                    $icon = 'exclamation-circle';
                    break;
                case 'warning':
                    $icon = 'exclamation-triangle';
                    break;
            }
            ?>
            <i class="bi bi-<?php echo $icon; ?>"></i>
        </div>
        <div class="alert-content">
            <?php echo $_SESSION['mensagem']['texto']; ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['mensagem']); endif; ?>

    <!-- Espaçamento para navbar fixa -->
    <div class="forge-navbar-spacer"></div>
    
    <!-- Modais de Login/Registro -->
    <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Modal de Login -->
        <div class="modal fade forge-modal" id="loginModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-box-arrow-in-right text-primary me-2"></i>
                            Entrar no GymForge
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form data-validate>
                            <div class="mb-3">
                                <label class="form-label" for="loginEmail">Email</label>
                                <input type="email" class="forge-input" id="loginEmail" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="loginPassword">Senha</label>
                                <div class="input-group">
                                    <input type="password" class="forge-input" id="loginPassword" required>
                                    <button type="button" class="forge-button forge-button--secondary" onclick="togglePassword('loginPassword')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Lembrar de mim</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="forge-button forge-button--primary">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    <span>Entrar</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        
        const icon = input.nextElementSibling.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    }
    </script>

    <!-- Main Container -->
    <main class="forge-main">
        <div class="forge-container">
    <!-- Scripts serão carregados no footer.php -->
</body>
</html> 
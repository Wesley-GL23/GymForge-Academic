<?php
// Ativar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carregar configurações
require_once dirname(__DIR__) . '/config/config.php';

// Verificar autenticação
require_once INCLUDES_DIR . '/auth_check.php';

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
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>GymForge - Forjando sua melhor versão</title>
    
    <!-- Meta Tags SEO -->
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'GymForge - Plataforma completa para gerenciamento de academias e acompanhamento de treinos. Transforme sua jornada fitness.'; ?>">
    <meta name="keywords" content="academia, treinos, exercícios, fitness, saúde, GymForge">
    <meta name="author" content="GymForge">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>GymForge">
    <meta property="og:description" content="<?php echo isset($page_description) ? $page_description : 'Transforme sua jornada fitness com o GymForge.'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:image" content="<?php echo BASE_URL; ?>assets/img/og-image.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/img/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>assets/img/apple-touch-icon.png">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/styles.css" rel="stylesheet">
    
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
<body>
    <!-- Navbar Profissional -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="bi bi-lightning-charge-fill text-accent"></i>
                <span class="ms-2">GYMFORGE</span>
            </a>
            
            <!-- Botão Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Menu Principal -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>">
                            <i class="bi bi-house-door me-1"></i>Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'exercises.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>exercises.php">
                            <i class="bi bi-collection me-1"></i>Exercícios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'workouts.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>workouts.php">
                            <i class="bi bi-activity me-1"></i>Treinos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>about.php">
                            <i class="bi bi-info-circle me-1"></i>Sobre
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>contact.php">
                            <i class="bi bi-envelope me-1"></i>Contato
                        </a>
                    </li>
                </ul>
                
                <!-- Área do Usuário -->
                <div class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Usuário Logado -->
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="avatar me-2">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>dashboard.php">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>profile.php">
                                        <i class="bi bi-person me-2"></i>Perfil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>settings.php">
                                        <i class="bi bi-gear me-2"></i>Configurações
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sair
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Usuário Não Logado -->
                        <div class="nav-item me-2">
                            <a class="btn btn-outline-light btn-sm" href="#" data-modal="loginModal">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="btn btn-accent btn-sm" href="#" data-modal="registerModal">
                                <i class="bi bi-person-plus me-1"></i>Cadastrar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Espaçamento para navbar fixa -->
    <div style="height: 80px;"></div>
    
    <!-- Modais de Login/Registro -->
    <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Modal de Login -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="loginModalLabel">
                            <i class="bi bi-box-arrow-in-right text-primary me-2"></i>Entrar no GymForge
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form data-validate>
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="loginEmail" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="loginPassword" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Lembrar de mim</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="#" class="text-decoration-none">Esqueceu sua senha?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal de Registro -->
        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="registerModalLabel">
                            <i class="bi bi-person-plus text-accent me-2"></i>Criar Conta
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form data-validate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="registerName" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="registerName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="registerLastName" class="form-label">Sobrenome</label>
                                    <input type="text" class="form-control" id="registerLastName" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="registerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="registerEmail" required>
                            </div>
                            <div class="mb-3">
                                <label for="registerPassword" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="registerPassword" required>
                            </div>
                            <div class="mb-3">
                                <label for="registerConfirmPassword" class="form-label">Confirmar Senha</label>
                                <input type="password" class="form-control" id="registerConfirmPassword" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    Concordo com os <a href="#" class="text-decoration-none">Termos de Uso</a>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-accent w-100">
                                <i class="bi bi-person-plus me-2"></i>Criar Conta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Conteúdo Principal -->
    <main class="container py-4">
    <!-- Scripts serão carregados no footer.php -->
</body>
</html> 
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYMFORGE™ - Forjando sua melhor versão</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/img/favicon/logo_small.png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Overlay para o background -->
    <div class="overlay"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/">
                <img src="<?php echo BASE_URL; ?>/assets/img/logo_small.png" alt="GYMFORGE™">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/#recursos">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/#galeria">Galeria</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/#como-funciona">Como Funciona</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/#planos">Planos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/#depoimentos">Depoimentos</a>
                    </li>
                </ul>
                
                <div class="d-flex gap-2">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>/views/dashboard/" class="btn btn-primary">
                            Dashboard
                        </a>
                        <a href="<?php echo BASE_URL; ?>/actions/usuario/logout.php" class="btn btn-outline-light">
                            Sair
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/forms/usuario/login.php" class="btn btn-outline-light">
                            Entrar
                        </a>
                        <a href="<?php echo BASE_URL; ?>/forms/usuario/cadastro.php" class="btn btn-primary">
                            Criar Conta
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['flash_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    </div>
    <?php endif; ?>

    <!-- Scripts no final do body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/GymForge-PHP/assets/js/script.js"></script>
</body>
</html> 
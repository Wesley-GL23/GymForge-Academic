<?php
// Ativar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar se o arquivo config existe
$config_file = __DIR__ . '/../config/config.php';
if (!file_exists($config_file)) {
    die('Erro: Arquivo config.php não encontrado');
}

// Tentar carregar o config
require_once $config_file;

// Verificar se BASE_URL foi definida
if (!defined('BASE_URL')) {
    die('Erro: BASE_URL não foi definida no config.php');
}

// Debug - remover depois
echo "<!-- Debug: BASE_URL = " . BASE_URL . " -->";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYMFORGE - Seu treino. Sua força. Sua evolução.</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/img/logo_small.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/">
                <img src="<?php echo BASE_URL; ?>/assets/img/logo_small.png" alt="GYMFORGE" height="30">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#recursos">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#galeria">Galeria</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#como-funciona">Como Funciona</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#planos">Planos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#depoimentos">Depoimentos</a>
                    </li>
                </ul>
                <div class="nav-buttons">
                    <a href="<?php echo BASE_URL; ?>/forms/usuario/cadastro.php" class="btn btn-primary">Começar Agora</a>
                    <a href="<?php echo BASE_URL; ?>/forms/usuario/login.php" class="btn btn-outline-light">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mensagens de Feedback -->
    <?php
    $mensagem = getMessage();
    if ($mensagem): ?>
        <div class="container mt-3">
            <div class="alert alert-<?php echo $mensagem['tipo']; ?> alert-dismissible fade show">
                <?php echo $mensagem['texto']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Conteúdo Principal -->
    <main class="container py-4"> 
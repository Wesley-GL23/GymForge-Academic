<?php
require_once 'config/config.php';
global $conn;
require_once 'includes/auth.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // Validações server-side
    if (empty($nome)) {
        $erro = 'Nome é obrigatório';
    } elseif (!$email) {
        $erro = 'Email inválido';
    } elseif (empty($senha)) {
        $erro = 'Senha é obrigatória';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não conferem';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $erro = 'Este email já está cadastrado';
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, 'cliente')");
                if ($stmt->execute([$nome, $email, $senha_hash])) {
                    $sucesso = 'Cadastro realizado com sucesso! Faça login para continuar.';
                } else {
                    $erro = 'Erro ao cadastrar usuário';
                }
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao cadastrar usuário';
            error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - GymForge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GymForge-Academic/assets/css/gymforge.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body.bg-cadastro {
            background-image: url('/GymForge-Academic/assets/img/gymforge-bg-2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            position: relative;
        }
        body.bg-cadastro::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(26,26,26,0.85);
            z-index: 0;
        }
        .cadastro-container {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-cadastro">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center py-5 cadastro-container">
        <div class="row justify-content-center w-100">
            <div class="col-md-7 col-lg-5">
                <div class="text-center mb-4">
                    <img src="/GymForge-Academic/assets/img/logo-white.png" alt="GymForge" class="img-fluid mb-4" style="max-width: 200px;">
                </div>
                <div class="glass-effect">
                    <h2 class="text-center mb-4">CRIE SUA CONTA</h2>
                    <?php if ($erro): ?>
                        <div class="alert alert-danger bg-transparent text-forge-accent border border-forge-accent">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($erro); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success bg-transparent text-forge-accent border border-forge-accent">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($sucesso); ?>
                        </div>
                        <div class="text-center">
                            <a href="login.php" class="btn btn-primary btn-lg forge-glow">
                                <i class="fas fa-sign-in-alt me-2"></i> Ir para Login
                            </a>
                        </div>
                    <?php else: ?>
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-forge-accent">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control bg-transparent border-0 border-bottom" 
                                    id="nome" name="nome" placeholder="Seu nome completo" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-forge-accent">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control bg-transparent border-0 border-bottom" 
                                    id="email" name="email" placeholder="Seu melhor email" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-forge-accent">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control bg-transparent border-0 border-bottom" 
                                    id="senha" name="senha" placeholder="Crie uma senha forte" required minlength="6">
                            </div>
                            <small class="text-forge-accent">
                                <i class="fas fa-info-circle me-1"></i> Mínimo de 6 caracteres
                            </small>
                        </div>
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-forge-accent">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <input type="password" class="form-control bg-transparent border-0 border-bottom" 
                                    id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg forge-glow">
                                <i class="fas fa-user-plus me-2"></i> Criar Conta
                            </button>
                            <a href="login.php" class="btn btn-accent btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> Já tenho uma conta
                            </a>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="index.php" class="text-white text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i> Voltar para a página inicial
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                // Validação adicional de senha
                var senha = document.getElementById('senha')
                var confirmar_senha = document.getElementById('confirmar_senha')
                if (senha.value !== confirmar_senha.value) {
                    confirmar_senha.setCustomValidity('As senhas não conferem')
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    confirmar_senha.setCustomValidity('')
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</body>
</html> 
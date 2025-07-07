<?php
require_once 'config/config.php';
require_once 'includes/auth_functions.php';
require_once 'includes/auth.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $lembrar = isset($_POST['lembrar']);

    if (!$email) {
        $erro = 'Email inválido';
    } elseif (empty($senha)) {
        $erro = 'Senha é obrigatória';
    } else {
        if (fazerLogin($email, $senha, $lembrar)) {
            header('Location: /GymForge-Academic/index.php');
            exit();
        } else {
            $erro = 'Email ou senha incorretos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GymForge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GymForge-Academic/assets/css/gymforge.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body.bg-login-neutro {
            background: linear-gradient(135deg, #1A1A1A 0%, #232526 100%);
            min-height: 100vh;
        }
        .login-container {
            position: relative;
            z-index: 1;
        }
        .password-toggle {
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .password-toggle:hover {
            color: #FF6B00 !important;
        }
        .form-check-input:checked {
            background-color: #FF6B00;
            border-color: #FF6B00;
        }
        .form-check-input:focus {
            border-color: #FF6B00;
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 0, 0.25);
        }
    </style>
</head>
<body class="bg-login-neutro">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center py-5 login-container">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4">
                <div class="text-center mb-4">
                    <img src="/GymForge-Academic/assets/img/logo-white.png" alt="GymForge" class="img-fluid mb-4" style="max-width: 200px;">
                </div>
                
                <div class="glass-effect">
                    <h2 class="text-center mb-4">ACESSE SUA CONTA</h2>
                    <?php if ($erro): ?>
                        <div class="alert alert-danger bg-transparent text-forge-accent border border-forge-accent">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($erro); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-forge-accent">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control bg-transparent border-0 border-bottom text-white" 
                                    id="email" name="email" placeholder="Seu email" required 
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-forge-accent">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control bg-transparent border-0 border-bottom text-white" 
                                    id="senha" name="senha" placeholder="Sua senha" required>
                                <span class="input-group-text bg-transparent border-0">
                                    <i class="fas fa-eye password-toggle text-forge-accent" id="togglePassword"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="lembrar" name="lembrar">
                                <label class="form-check-label text-white" for="lembrar">
                                    <i class="fas fa-remember me-1"></i> Lembrar minha senha
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg forge-glow">
                                <i class="fas fa-sign-in-alt me-2"></i> Entrar
                            </button>
                            <a href="/GymForge-Academic/cadastro.php" class="btn btn-accent btn-lg">
                                <i class="fas fa-user-plus me-2"></i> Criar Conta
                            </a>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="/GymForge-Academic/forms/usuario/recuperar_senha.php" class="text-forge-accent text-decoration-none">
                                <small><i class="fas fa-key me-1"></i> Esqueceu sua senha?</small>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="/GymForge-Academic/index.php" class="text-white text-decoration-none">
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
                form.classList.add('was-validated')
            }, false)
        })
    })()

    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('senha');
        const icon = this;
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Auto-focus on email field
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('email').focus();
    });
    </script>
</body>
</html> 
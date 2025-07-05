<?php
require_once '../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Login</h2>
                    
                    <?php if (isset($_SESSION['mensagem'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show">
                            <?php 
                            echo $_SESSION['mensagem']['texto'];
                            unset($_SESSION['mensagem']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>/actions/usuario/login.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <div class="password-field">
                                <input type="password" class="form-control" id="senha" name="senha" required>
                                <button type="button" class="password-toggle" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="lembrar">
                            <label class="form-check-label" for="lembrar">Lembrar meu email</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <p class="mb-0">
                            Não tem uma conta? 
                            <a href="<?php echo BASE_URL; ?>/forms/usuario/cadastro.php">Cadastre-se</a>
                        </p>
                        <p class="mt-2">
                            <a href="<?php echo BASE_URL; ?>/forms/usuario/recuperar_senha.php">
                                <i class="bi bi-key me-1"></i>Esqueceu sua senha?
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?> 
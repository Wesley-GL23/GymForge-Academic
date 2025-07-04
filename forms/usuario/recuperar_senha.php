<?php
require_once '../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Recuperar Senha</h2>
                    
                    <?php if (isset($_SESSION['mensagem'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show">
                            <?php 
                            echo $_SESSION['mensagem']['texto'];
                            unset($_SESSION['mensagem']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>/actions/usuario/recuperar_senha.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Enviar Link de Recuperação</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <p>
                            Lembrou sua senha? 
                            <a href="<?php echo BASE_URL; ?>/forms/usuario/login.php">Voltar para o login</a>
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
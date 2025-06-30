<?php
require_once '../../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-card">
                <div class="text-center mb-4">
                    <img src="<?php echo BASE_URL; ?>/assets/img/logo_small.png" alt="GYMFORGE™" class="mb-4" style="width: 80px; height: auto;">
                    <h3 class="text-light">Recuperar Senha</h3>
                    <p class="text-light opacity-75">Digite seu e-mail para receber as instruções</p>
                </div>
                
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['flash_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                <?php endif; ?>

                <form action="../../actions/usuario/recuperar_senha.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="seu.email@exemplo.com" required>
                        <div class="invalid-feedback text-light">
                            Por favor, insira um e-mail válido.
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            Enviar Instruções
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0 text-light">
                            Lembrou sua senha? 
                            <a href="login.php" class="text-light">Voltar ao login</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?> 
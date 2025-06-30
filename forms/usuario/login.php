<?php
require_once '../../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-card">
                <div class="text-center mb-4">
                    <img src="<?php echo BASE_URL; ?>/assets/img/logo_small.png" alt="GYMFORGE™" class="mb-4" style="width: 80px; height: auto;">
                    <h3 class="text-light">Bem-vindo de volta!</h3>
                    <p class="text-light opacity-75">Entre para continuar sua jornada</p>
                </div>
                
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['flash_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                <?php endif; ?>

                <form action="../../actions/usuario/login.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="seu.email@exemplo.com" required>
                        <div class="invalid-feedback text-light">
                            Por favor, insira um e-mail válido.
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="position-relative">
                            <input type="password" class="form-control" id="senha" name="senha" 
                                   placeholder="Digite sua senha" required>
                            <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y text-light opacity-75" 
                                    id="toggleSenha" style="padding-right: 12px;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback text-light">
                            Por favor, insira sua senha.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="lembrar" name="lembrar">
                            <label class="form-check-label text-light" for="lembrar">Lembrar-me</label>
                        </div>
                        <a href="recuperar_senha.php" class="text-light opacity-75">Esqueceu a senha?</a>
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Entrar
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0 text-light">
                            Ainda não tem uma conta? 
                            <a href="cadastro.php" class="text-light">Cadastre-se</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Preencher o email se existir no cookie
window.addEventListener('load', function() {
    const emailCookie = document.cookie
        .split('; ')
        .find(row => row.startsWith('gymforge_email='));
    
    if (emailCookie) {
        const email = emailCookie.split('=')[1];
        document.getElementById('email').value = decodeURIComponent(email);
        document.getElementById('lembrar').checked = true;
    }
});

// Toggle de visibilidade da senha
document.getElementById('toggleSenha').addEventListener('click', function() {
    const senhaInput = document.getElementById('senha');
    const icon = this.querySelector('i');
    
    if (senhaInput.type === 'password') {
        senhaInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        senhaInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Validação do formulário
(function () {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php
require_once '../../includes/footer.php';
?> 
<?php
require_once '../../includes/header.php';
require_once '../../config/conexao.php';

$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
$tokenValido = false;
$mensagemErro = '';

if ($token) {
    $stmt = $conn->prepare("
        SELECT usuario_id 
        FROM recuperacao_senha 
        WHERE token = ? 
        AND expira > NOW() 
        AND usado = 0
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $tokenValido = true;
    } else {
        $mensagemErro = 'Link inválido ou expirado. Por favor, solicite um novo link de recuperação.';
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-card">
                <div class="text-center mb-4">
                    <img src="<?php echo BASE_URL; ?>/assets/img/logo_small.png" alt="GYMFORGE™" class="mb-4" style="width: 80px; height: auto;">
                    <h3 class="text-light">Redefinir Senha</h3>
                    <?php if ($tokenValido): ?>
                        <p class="text-light opacity-75">Digite sua nova senha</p>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['flash_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                <?php endif; ?>

                <?php if ($mensagemErro): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $mensagemErro; ?>
                        <div class="mt-3">
                            <a href="recuperar_senha.php" class="btn btn-outline-light">Voltar para Recuperação de Senha</a>
                        </div>
                    </div>
                <?php elseif ($tokenValido): ?>
                    <form action="../../actions/usuario/redefinir_senha.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="mb-4">
                            <label for="senha" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" 
                                   minlength="6" required>
                            <div class="invalid-feedback text-light">
                                A senha deve ter no mínimo 6 caracteres.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" 
                                   minlength="6" required>
                            <div class="invalid-feedback text-light">
                                As senhas não coincidem.
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Salvar Nova Senha
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('confirmar_senha').addEventListener('input', function() {
    if (this.value !== document.getElementById('senha').value) {
        this.setCustomValidity('As senhas não coincidem');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php
require_once '../../includes/footer.php';
?> 
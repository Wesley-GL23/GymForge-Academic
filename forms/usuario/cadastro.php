<?php
require_once '../../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-card">
                <h3 class="text-center mb-4">Cadastro de Usuário</h3>
                <?php
                if (isset($_SESSION['msg'])) {
                    echo '<div class="alert alert-' . $_SESSION['msg_type'] . '">' . $_SESSION['msg'] . '</div>';
                    unset($_SESSION['msg']);
                    unset($_SESSION['msg_type']);
                }
                ?>
                <form action="../../actions/usuario/cadastrar.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" required 
                               minlength="3" maxlength="100" placeholder="Digite seu nome completo">
                        <div class="invalid-feedback text-light">
                            Por favor, insira seu nome completo (mínimo 3 caracteres).
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               placeholder="seu.email@exemplo.com">
                        <div class="invalid-feedback text-light">
                            Por favor, insira um e-mail válido.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" 
                                   required minlength="6" placeholder="Mínimo 6 caracteres">
                            <div class="invalid-feedback text-light">
                                A senha deve ter no mínimo 6 caracteres.
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                            <input type="password" class="form-control" id="confirmar_senha" 
                                   name="confirmar_senha" required placeholder="Digite a senha novamente">
                            <div class="invalid-feedback text-light">
                                As senhas não conferem.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            Criar Conta
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0">
                            Já tem uma conta? 
                            <a href="login.php" class="text-light">Faça login</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict'
    
    var forms = document.querySelectorAll('.needs-validation')
    var senha = document.getElementById('senha')
    var confirmarSenha = document.getElementById('confirmar_senha')
    
    function validarSenhasIguais() {
        if (senha.value !== confirmarSenha.value) {
            confirmarSenha.setCustomValidity('As senhas não conferem')
        } else {
            confirmarSenha.setCustomValidity('')
        }
    }
    
    senha.addEventListener('change', validarSenhasIguais)
    confirmarSenha.addEventListener('keyup', validarSenhasIguais)
    
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
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
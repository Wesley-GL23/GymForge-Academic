document.addEventListener('DOMContentLoaded', function() {
    // Toggle de visualização da senha
    const togglePassword = document.querySelector('.password-toggle');
    const password = document.querySelector('#senha');
    
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Atualizar ícone
            const icon = this.querySelector('i');
            if (type === 'password') {
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    }

    // Lembrar login
    const rememberCheck = document.querySelector('#lembrar');
    const emailField = document.querySelector('#email');
    
    if (rememberCheck && emailField) {
        // Carregar email salvo
        const savedEmail = localStorage.getItem('gymforge_email');
        if (savedEmail) {
            emailField.value = savedEmail;
            rememberCheck.checked = true;
        }

        // Salvar email quando checkbox mudar
        rememberCheck.addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('gymforge_email', emailField.value);
            } else {
                localStorage.removeItem('gymforge_email');
            }
        });

        // Atualizar email salvo quando digitar
        emailField.addEventListener('input', function() {
            if (rememberCheck.checked) {
                localStorage.setItem('gymforge_email', this.value);
            }
        });
    }
}); 
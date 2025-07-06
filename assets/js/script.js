// Inicialização do Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa todos os tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inicializa todos os popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Função para validação de formulários
function validarFormulario(form) {
    'use strict';

    // Fetch all forms we want to apply custom validation styles to
    var forms = document.querySelectorAll('.needs-validation');

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

// Função para preview de imagem
function previewImagem(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Função para confirmar exclusão
function confirmarExclusao(event, mensagem) {
    if (!confirm(mensagem || 'Tem certeza que deseja excluir este item?')) {
        event.preventDefault();
        return false;
    }
    return true;
}

// Função para adicionar exercício ao treino
function adicionarExercicio() {
    const template = document.querySelector('#exercicio-template');
    const container = document.querySelector('#exercicios-container');
    const clone = template.content.cloneNode(true);
    const index = container.children.length;

    // Atualiza os IDs e names dos campos
    clone.querySelectorAll('[name]').forEach(input => {
        input.name = input.name.replace('__INDEX__', index);
        input.id = input.id.replace('__INDEX__', index);
    });

    clone.querySelectorAll('[for]').forEach(label => {
        label.htmlFor = label.htmlFor.replace('__INDEX__', index);
    });

    container.appendChild(clone);
}

// Função para remover exercício do treino
function removerExercicio(btn) {
    const exercicio = btn.closest('.exercicio-item');
    exercicio.remove();
}

// Função para buscar exercícios
function buscarExercicios(termo) {
    const cards = document.querySelectorAll('.exercicio-card');
    cards.forEach(card => {
        const titulo = card.querySelector('.card-title').textContent.toLowerCase();
        const categoria = card.querySelector('.badge').textContent.toLowerCase();
        const descricao = card.querySelector('.card-text').textContent.toLowerCase();
        
        const termoBusca = termo.toLowerCase();
        
        if (titulo.includes(termoBusca) || categoria.includes(termoBusca) || descricao.includes(termoBusca)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Função para filtrar exercícios por categoria
function filtrarCategoria(categoria) {
    const cards = document.querySelectorAll('.exercicio-card');
    cards.forEach(card => {
        const cardCategoria = card.querySelector('.badge').textContent;
        if (categoria === 'todos' || cardCategoria === categoria) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Função para marcar notificação como lida
function marcarComoLida(id) {
    fetch(`actions/notificacao/marcar_lida.php?id=${id}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`#notificacao-${id}`).classList.add('text-muted');
            atualizarContadorNotificacoes();
        }
    });
}

// Função para atualizar contador de notificações
function atualizarContadorNotificacoes() {
    fetch('actions/notificacao/contar.php')
    .then(response => response.json())
    .then(data => {
        const contador = document.querySelector('.notification-count');
        if (data.count > 0) {
            contador.textContent = data.count;
            contador.style.display = '';
        } else {
            contador.style.display = 'none';
        }
    });
}

// Header Scroll Effect
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.forge-navbar');
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        // Adiciona classe quando rola mais que 50px
        if (currentScroll > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
});

// Toggle Password Visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const type = input.type === 'password' ? 'text' : 'password';
    input.type = type;
    
    const icon = input.nextElementSibling.querySelector('i');
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
}

// Dropdown Menu Animation
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        
        dropdown.addEventListener('show.bs.dropdown', function() {
            menu.classList.add('animate__animated', 'animate__fadeIn');
        });
        
        dropdown.addEventListener('hide.bs.dropdown', function() {
            menu.classList.add('animate__animated', 'animate__fadeOut');
            setTimeout(() => {
                menu.classList.remove('animate__animated', 'animate__fadeOut');
            }, 300);
        });
    });
});

// Alert Auto-dismiss
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.forge-alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Form Validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    });
});

// Loading State for Buttons
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.forge-button[type="submit"]');
    
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const form = button.closest('form');
            
            if (form && form.checkValidity()) {
                const originalContent = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<div class="forge-loading"></div>';
                
                // Restaura o botão após 3 segundos se o form não foi submetido
                setTimeout(() => {
                    if (button.disabled) {
                        button.disabled = false;
                        button.innerHTML = originalContent;
                    }
                }, 3000);
            }
        });
    });
}); 
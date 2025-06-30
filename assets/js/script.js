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
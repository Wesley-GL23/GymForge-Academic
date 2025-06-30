console.log('Script carregado!');

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

    // Navbar Scroll Effect
    const navbar = document.querySelector('.navbar');
    const navbarHeight = navbar.offsetHeight;
    let lastScroll = 0;
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= navbarHeight) {
            navbar.classList.remove('navbar-scrolled', 'navbar-hidden');
        } else {
            if (currentScroll > lastScroll && !navbar.classList.contains('navbar-hidden')) {
                // Scroll Down
                navbar.classList.remove('navbar-scrolled');
                navbar.classList.add('navbar-hidden');
            } else if (currentScroll < lastScroll && navbar.classList.contains('navbar-hidden')) {
                // Scroll Up
                navbar.classList.remove('navbar-hidden');
                navbar.classList.add('navbar-scrolled');
            }
        }
        lastScroll = currentScroll;
    });
    
    // Smooth Scroll for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offset = navbarHeight;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Animate Elements on Scroll
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    const animateOptions = {
        threshold: 0.2,
        rootMargin: '0px'
    };
    
    const animateObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, animateOptions);
    
    animateElements.forEach(element => {
        animateObserver.observe(element);
    });
    
    // Form Validation
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Newsletter Form
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const emailInput = newsletterForm.querySelector('input[type="email"]');
            const submitButton = newsletterForm.querySelector('button[type="submit"]');
            
            if (emailInput.value) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                
                try {
                    // Simular envio (substituir por chamada real à API)
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    
                    // Mostrar mensagem de sucesso
                    emailInput.value = '';
                    showToast('Sucesso!', 'Você foi inscrito na nossa newsletter.', 'success');
                } catch (error) {
                    showToast('Erro!', 'Não foi possível completar sua inscrição.', 'error');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="bi bi-arrow-right"></i>';
                }
            }
        });
    }
    
    // Toast Notification
    function showToast(title, message, type = 'info') {
        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            const container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
        }
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong><br>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.querySelector('.toast-container').appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
    
    // Adicionar classes de animação aos elementos
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        const elements = section.querySelectorAll('.card, .feature-icon, h2, .lead, .btn');
        elements.forEach(element => {
            element.classList.add('animate-on-scroll');
        });
    });

    // Configuração do Intersection Observer
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    // Criar o observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observar todos os elementos com a classe animate-on-scroll
    document.querySelectorAll('.animate-fadeInUp').forEach((element) => {
        observer.observe(element);
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

// Função para animar contagem
function animateCounter(element) {
    console.log('Iniciando animação para:', element);
    const target = parseInt(element.getAttribute('data-valor'));
    const duration = 2000; // 2 segundos
    const step = target / (duration / 16); // 60fps
    let current = 0;

    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current) + '+';
    }, 16);
}

// Função para verificar se elemento está visível na viewport
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    const isVisible = (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
    console.log('Elemento visível:', isVisible);
    return isVisible;
}

// Iniciar animação quando os elementos estiverem visíveis
function handleScroll() {
    console.log('Verificando elementos...');
    const contadores = document.querySelectorAll('.numero-contador');
    console.log('Contadores encontrados:', contadores.length);
    
    contadores.forEach(contador => {
        console.log('Verificando contador:', contador);
        // Removi a verificação de visibilidade para testar
        if (!contador.classList.contains('animated')) {
            console.log('Animando contador:', contador);
            animateCounter(contador);
            contador.classList.add('animated');
        }
    });
}

// Iniciar imediatamente e também no scroll
handleScroll(); // Chamar imediatamente
window.addEventListener('scroll', handleScroll);
document.addEventListener('DOMContentLoaded', handleScroll);

// Animação dos Números
function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const current = Math.floor(progress * (end - start) + start);
        element.textContent = current.toLocaleString();
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Intersection Observer para animação dos números
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counter = entry.target;
            const valor = parseInt(counter.getAttribute('data-valor'));
            animateValue(counter, 0, valor, 2000);
            observer.unobserve(counter);
        }
    });
}, observerOptions);

document.querySelectorAll('.numero-contador').forEach(counter => {
    observer.observe(counter);
});

// Animação de entrada dos elementos
const fadeElements = document.querySelectorAll('.fade-in');
const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            fadeObserver.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.1
});

fadeElements.forEach(element => {
    element.style.opacity = '0';
    element.style.transform = 'translateY(20px)';
    element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    fadeObserver.observe(element);
});

// Parallax Effect
window.addEventListener('scroll', () => {
    const parallaxElements = document.querySelectorAll('.parallax');
    parallaxElements.forEach(element => {
        const speed = element.getAttribute('data-speed') || 0.5;
        const yPos = -(window.scrollY * speed);
        element.style.transform = `translateY(${yPos}px)`;
    });
});

// Typed.js para efeito de digitação
if (typeof Typed !== 'undefined') {
    new Typed('#typed-text', {
        strings: [
            'Transforme seu corpo',
            'Supere seus limites',
            'Alcance seus objetivos'
        ],
        typeSpeed: 50,
        backSpeed: 30,
        backDelay: 2000,
        loop: true
    });
}

// Lazy Loading para imagens
document.addEventListener('DOMContentLoaded', () => {
    const lazyImages = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.getAttribute('data-src');
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));
});

// Tooltip Initialization
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

// Mobile Menu
const mobileMenuBtn = document.querySelector('.navbar-toggler');
const mobileMenu = document.querySelector('.navbar-collapse');

if (mobileMenuBtn && mobileMenu) {
    document.addEventListener('click', (e) => {
        if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target) && mobileMenu.classList.contains('show')) {
            mobileMenuBtn.click();
        }
    });
}

// Preloader
window.addEventListener('load', () => {
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        preloader.style.opacity = '0';
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 500);
    }
}); 
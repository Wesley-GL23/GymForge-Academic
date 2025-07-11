// GymForge Main JavaScript

// Função para inicializar todos os componentes
function initGymForge() {
    initParallax();
    initScrollEffects();
    initAnimations();
    initInteractions();
}

// Efeito Parallax nos backgrounds
function initParallax() {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        document.querySelectorAll('.hero-background').forEach(background => {
            const speed = 0.5;
            const yPos = -(scrolled * speed);
            background.style.backgroundPosition = `center ${yPos}px`;
        });
    });
}

// Efeitos de Scroll
function initScrollEffects() {
    const navbar = document.querySelector('.navbar');
    const backToTopBtn = document.getElementById('backToTop');

    window.addEventListener('scroll', () => {
        // Navbar Effect
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        // Back to Top Button
        if (window.pageYOffset > 300) {
            backToTopBtn.style.display = 'block';
            backToTopBtn.classList.add('fade-in');
        } else {
            backToTopBtn.classList.remove('fade-in');
            setTimeout(() => {
                if (window.pageYOffset <= 300) {
                    backToTopBtn.style.display = 'none';
                }
            }, 300);
        }
    });

    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Animações
function initAnimations() {
    // Inicializar AOS
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        disable: 'mobile'
    });

    // Typed.js melhorado para texto dinâmico com efeito metálico
    const typedElement = document.querySelector('.hero-dynamic-text');
    if (typedElement) {
        // Adicionar classe para animação de entrada
        typedElement.classList.add('typing-animation');
        
        const typed = new Typed('.hero-dynamic-text', {
            strings: typedElement.getAttribute('data-typed-items').split(','),
            typeSpeed: 80, // Velocidade mais lenta
            backSpeed: 40, // Velocidade de apagar mais lenta
            backDelay: 4000, // Mais tempo para ler
            loop: true,
            showCursor: true,
            cursorChar: '|',
            fadeOut: true, // Efeito de fade
            fadeOutClass: 'typed-fade-out',
            fadeOutDelay: 500,
            autoInsertCss: true,
            smartBackspace: true, // Só apaga o que não coincide
            shuffle: false, // Manter ordem
            onBegin: (self) => {
                // Efeito metálico no início
                typedElement.classList.add('typing-active');
                typedElement.style.textShadow = '0 0 15px rgba(255, 107, 0, 0.8), 0 0 30px rgba(255, 140, 0, 0.5)';
            },
            onComplete: (self) => {
                // Efeito de destaque metálico quando completa
                typedElement.classList.remove('typing-active');
                typedElement.classList.add('typing-complete');
                typedElement.style.textShadow = '0 0 25px rgba(255, 107, 0, 0.9), 0 0 50px rgba(255, 140, 0, 0.7), 0 0 75px rgba(255, 165, 0, 0.5)';
                
                setTimeout(() => {
                    typedElement.classList.remove('typing-complete');
                    typedElement.style.textShadow = '0 0 10px rgba(255, 107, 0, 0.5), 0 0 20px rgba(255, 140, 0, 0.3)';
                }, 2000);
            },
            onStringTyped: (arrayPos) => {
                // Efeito de forja metálico
                typedElement.style.animation = 'forgeGlow 0.5s ease-in-out, textPulse 0.3s ease-in-out';
                setTimeout(() => {
                    typedElement.style.animation = '';
                }, 500);
            },
            onReset: (self) => {
                // Reset dos efeitos
                typedElement.classList.remove('typing-active', 'typing-complete');
                typedElement.style.textShadow = '0 0 10px rgba(255, 107, 0, 0.5), 0 0 20px rgba(255, 140, 0, 0.3)';
            }
        });
    }
}

// Interações
function initInteractions() {
    // Hover Effects
    document.querySelectorAll('.hover-effect').forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Newsletter Form
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            // Aqui você pode adicionar a lógica para enviar o email para seu backend
            showToast('Obrigado por se inscrever! Em breve você receberá nossas novidades.', 'success');
            this.reset();
        });
    }

    // Tooltips e Popovers do Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
}

// Toast Notifications
function showToast(message, type = 'info') {
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
    toastContainer.style.zIndex = '1070';

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    const toastContent = document.createElement('div');
    toastContent.className = 'd-flex';

    const toastBody = document.createElement('div');
    toastBody.className = 'toast-body';
    toastBody.textContent = message;

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'btn-close btn-close-white me-2 m-auto';
    closeButton.setAttribute('data-bs-dismiss', 'toast');
    closeButton.setAttribute('aria-label', 'Close');

    toastContent.appendChild(toastBody);
    toastContent.appendChild(closeButton);
    toast.appendChild(toastContent);
    toastContainer.appendChild(toast);
    document.body.appendChild(toastContainer);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toastContainer);
    });
}

// Loading States
function showLoading(element) {
    element.classList.add('loading');
    element.setAttribute('disabled', true);
}

function hideLoading(element) {
    element.classList.remove('loading');
    element.removeAttribute('disabled');
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', initGymForge);

// Exportar funções para uso global
window.GymForge = {
    showToast,
    showLoading,
    hideLoading
}; 
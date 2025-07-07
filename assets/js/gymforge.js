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

    // Typed.js para texto dinâmico
    const typedElement = document.querySelector('.hero-dynamic-text');
    if (typedElement) {
        const typed = new Typed('.hero-dynamic-text', {
            strings: typedElement.getAttribute('data-typed-items').split(','),
            typeSpeed: 60,
            backSpeed: 30,
            backDelay: 3000,
            loop: true,
            showCursor: true,
            cursorChar: '|'
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
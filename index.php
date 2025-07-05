<?php
// Carregar configurações primeiro
require_once __DIR__ . '/config/config.php';

// Depois carregar o header que usa as configurações
require_once 'includes/header.php';
require_once 'config/forge_ranks.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content animate-fadeInUp">
                <h1>Transforme seu treino em uma<br>jornada épica</h1>
                <p>Junte-se a uma comunidade de atletas determinados e alcance resultados extraordinários com nosso sistema inteligente de treinos.</p>
                <div class="hero-buttons">
                    <a href="<?php echo BASE_URL; ?>/forms/usuario/cadastro.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus me-2"></i>Comece Agora
                    </a>
                    <a href="#como-funciona" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-play-circle me-2"></i>Como Funciona
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-title">
                <h2>Por que escolher o GymForge?</h2>
                <p>Descubra como nossa plataforma pode transformar sua experiência de treino</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 animate-fadeInUp animate-delay-1">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3>Progresso Inteligente</h3>
                        <p>Acompanhe sua evolução com métricas detalhadas e visualizações claras do seu progresso.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fadeInUp animate-delay-2">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <h3>Treinos Personalizados</h3>
                        <p>Receba programas de treino adaptados ao seu nível, objetivos e disponibilidade.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fadeInUp animate-delay-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <h3>Sistema de Ranks</h3>
                        <p>Evolua através dos ranks e desbloqueie novos desafios conforme progride.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ranks Section -->
    <section class="ranks">
        <div class="container">
            <div class="section-title text-center text-light mb-5">
                <h2>Evolua seu Nível</h2>
                <p>Cada rank representa um novo patamar na sua jornada fitness</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 animate-fadeInUp">
                    <div class="rank-card">
                        <div class="rank-icon">
                            <i class="bi bi-shield-fill"></i>
                        </div>
                        <h3>Bronze</h3>
                        <p>Primeiros passos na jornada. Aprenda os fundamentos e construa uma base sólida.</p>
                        <div class="rank-progress">
                            <div class="rank-progress-bar" style="width: 30%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate-fadeInUp animate-delay-1">
                    <div class="rank-card">
                        <div class="rank-icon">
                            <i class="bi bi-shield-fill-check"></i>
                        </div>
                        <h3>Prata</h3>
                        <p>Domine as técnicas básicas e comece a explorar treinos mais desafiadores.</p>
                        <div class="rank-progress">
                            <div class="rank-progress-bar" style="width: 60%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate-fadeInUp animate-delay-2">
                    <div class="rank-card">
                        <div class="rank-icon">
                            <i class="bi bi-shield-fill-exclamation"></i>
                        </div>
                        <h3>Ouro</h3>
                        <p>Alcance performance avançada e inspire outros em sua jornada.</p>
                        <div class="rank-progress">
                            <div class="rank-progress-bar" style="width: 90%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Resources Section -->
    <section class="resources">
        <div class="container">
            <div class="section-title">
                <h2>Recursos Exclusivos</h2>
                <p>Ferramentas e conteúdos para maximizar seus resultados</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 animate-fadeInUp">
                    <div class="resource-card">
                        <div class="resource-image" style="background-image: url('<?php echo BASE_URL; ?>/assets/img/workout-tracker.jpg')"></div>
                        <div class="resource-content">
                            <h3>Tracker de Treinos</h3>
                            <p>Registre e acompanhe cada sessão de treino com detalhes precisos.</p>
                            <div class="resource-meta">
                                <span><i class="bi bi-clock"></i> Atualizado em tempo real</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate-fadeInUp animate-delay-1">
                    <div class="resource-card">
                        <div class="resource-image" style="background-image: url('<?php echo BASE_URL; ?>/assets/img/nutrition-guide.jpg')"></div>
                        <div class="resource-content">
                            <h3>Guia Nutricional</h3>
                            <p>Dicas e orientações para uma alimentação balanceada e focada em resultados.</p>
                            <div class="resource-meta">
                                <span><i class="bi bi-book"></i> Conteúdo Premium</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate-fadeInUp animate-delay-2">
                    <div class="resource-card">
                        <div class="resource-image" style="background-image: url('<?php echo BASE_URL; ?>/assets/img/community.jpg')"></div>
                        <div class="resource-content">
                            <h3>Comunidade</h3>
                            <p>Conecte-se com outros atletas, compartilhe experiências e celebre conquistas.</p>
                            <div class="resource-meta">
                                <span><i class="bi bi-people"></i> +1000 membros ativos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Pronto para começar sua transformação?</h2>
            <p>Junte-se a milhares de atletas que já estão transformando seus treinos com o GymForge</p>
            <a href="<?php echo BASE_URL; ?>/forms/usuario/cadastro.php" class="btn btn-light btn-lg">
                <i class="bi bi-arrow-right-circle me-2"></i>Comece Sua Jornada
            </a>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>O que dizem nossos atletas</h2>
                <p>Histórias reais de transformação e superação</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 animate-fadeInUp">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "O GymForge mudou completamente minha forma de treinar. Os treinos personalizados e o sistema de progressão me mantêm sempre motivado."
                        </div>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">
                                <img src="<?php echo BASE_URL; ?>/assets/img/testimonial-1.jpg" alt="João Silva">
                            </div>
                            <div class="testimonial-info">
                                <h4>João Silva</h4>
                                <p>Rank Ouro</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate-fadeInUp animate-delay-1">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "A comunidade é incrível! Encontrei pessoas com os mesmos objetivos e isso fez toda diferença na minha motivação."
                        </div>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">
                                <img src="<?php echo BASE_URL; ?>/assets/img/testimonial-2.jpg" alt="Maria Santos">
                            </div>
                            <div class="testimonial-info">
                                <h4>Maria Santos</h4>
                                <p>Rank Prata</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate-fadeInUp animate-delay-2">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "Os recursos de tracking e as métricas detalhadas me ajudaram a entender melhor minha evolução e ajustar meus treinos."
                        </div>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">
                                <img src="<?php echo BASE_URL; ?>/assets/img/testimonial-3.jpg" alt="Pedro Oliveira">
                            </div>
                            <div class="testimonial-info">
                                <h4>Pedro Oliveira</h4>
                                <p>Rank Bronze</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Hero Section */
.hero-section {
    position: relative;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/assets/img/hero-background.jpg');
    background-size: cover;
    background-position: center;
    overflow: hidden;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-section h1 {
    font-size: 4rem;
    font-weight: bold;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.hero-subtitle {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.hero-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.particles-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

/* Features Section */
.features-section {
    padding: 5rem 0;
    background: var(--blue-night);
}

.feature-card {
    text-align: center;
    padding: 2rem;
    border-radius: 15px;
    height: 100%;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
}

.feature-icon {
    font-size: 3rem;
    color: var(--forge-orange);
    margin-bottom: 1.5rem;
}

/* Ranks Section */
.ranks-section {
    padding: 5rem 0;
    background: var(--blue-steel);
}

.rank-timeline {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin: 4rem 0;
}

.rank-timeline::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: rgba(255,255,255,0.1);
    z-index: 1;
}

.rank-item {
    position: relative;
    z-index: 2;
    text-align: center;
}

.rank-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    border: 3px solid var(--blue-night);
}

.rank-icon img {
    width: 60%;
    height: 60%;
    object-fit: contain;
}

/* CTA Section */
.cta-section {
    padding: 5rem 0;
    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('/assets/img/cta-background.jpg');
    background-size: cover;
    background-position: center;
    color: white;
}

.cta-section h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.cta-section p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

/* Responsividade */
@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 3rem;
    }

    .hero-subtitle {
        font-size: 1.2rem;
    }

    .rank-timeline {
        flex-direction: column;
        gap: 2rem;
    }

    .rank-timeline::before {
        width: 2px;
        height: 100%;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
    }
}
</style>

<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
particlesJS('particles-js', {
    particles: {
        number: {
            value: 80,
            density: {
                enable: true,
                value_area: 800
            }
        },
        color: {
            value: '#ffffff'
        },
        shape: {
            type: 'circle'
        },
        opacity: {
            value: 0.5,
            random: false
        },
        size: {
            value: 3,
            random: true
        },
        line_linked: {
            enable: true,
            distance: 150,
            color: '#ffffff',
            opacity: 0.4,
            width: 1
        },
        move: {
            enable: true,
            speed: 6,
            direction: 'none',
            random: false,
            straight: false,
            out_mode: 'out',
            bounce: false
        }
    },
    interactivity: {
        detect_on: 'canvas',
        events: {
            onhover: {
                enable: true,
                mode: 'repulse'
            },
            onclick: {
                enable: true,
                mode: 'push'
            },
            resize: true
        }
    },
    retina_detect: true
});
</script>

<?php
require_once 'includes/footer.php';
?>
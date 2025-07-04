<?php
require_once 'includes/header.php';
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>GymForge</h1>
        <p class="hero-subtitle">Transforme seu treino em uma jornada épica</p>
        <div class="hero-buttons">
            <a href="/forms/usuario/cadastro.php" class="btn btn-primary btn-lg">Comece Agora</a>
            <a href="/forms/usuario/login.php" class="btn btn-outline-light btn-lg">Já tenho conta</a>
        </div>
    </div>
    <div class="particles-container" id="particles-js"></div>
</div>

<section class="features-section">
    <div class="container">
        <h2 class="text-center mb-5">Recursos Únicos</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="feature-card glass-effect">
                    <i class="fas fa-fire feature-icon"></i>
                    <h3>Sistema de Têmpera</h3>
                    <p>Evolua seus músculos através de um sistema único de têmpera, desbloqueando novos níveis de poder.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card glass-effect">
                    <i class="fas fa-users feature-icon"></i>
                    <h3>Guildas</h3>
                    <p>Junte-se a outros guerreiros em guildas, participe de eventos e conquiste glória juntos.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card glass-effect">
                    <i class="fas fa-trophy feature-icon"></i>
                    <h3>Conquistas</h3>
                    <p>Desbloqueie conquistas especiais e mostre sua dedicação através de emblemas únicos.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ranks-section">
    <div class="container">
        <h2 class="text-center mb-5">Evolua seu Personagem</h2>
        <div class="rank-timeline">
            <?php foreach ($RANKS as $code => $rank): ?>
            <div class="rank-item">
                <div class="rank-icon" style="background: <?php echo $rank['cor']; ?>">
                    <img src="/assets/img/ranks/<?php echo $code; ?>.png" alt="<?php echo $rank['nome']; ?>">
                </div>
                <h4><?php echo $rank['nome']; ?></h4>
                <p>Nível <?php echo $rank['min_level']; ?>+</p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container text-center">
        <h2>Pronto para começar sua jornada?</h2>
        <p>Junte-se a milhares de guerreiros e comece sua transformação hoje!</p>
        <a href="/forms/usuario/cadastro.php" class="btn btn-primary btn-lg">Criar Conta Grátis</a>
    </div>
</section>

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
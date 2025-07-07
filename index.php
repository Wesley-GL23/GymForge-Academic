<?php
// Carregar configurações primeiro
require_once __DIR__ . '/config/config.php';

// Depois carregar o header que usa as configurações
require_once 'includes/header.php';
require_once 'config/forge_ranks.php';

$titulo = "GymForge - Seu treino. Sua força. Sua evolução.";
?>

<main class="forge-main">
    <!-- Hero Section -->
    <section class="hero-background bg-forge-1">
        <div class="container">
            <div class="glass-effect text-center" data-aos="fade-up">
                <h1 class="display-4 fw-bold mb-4">FORJE SUA MELHOR VERSÃO</h1>
                <p class="lead mb-4 hero-dynamic-text" data-typed-items="Transforme seu corpo,Supere seus limites,Alcance seus objetivos,Seja mais forte">Transforme seu corpo e mente com o GymForge. Nossa plataforma combina treinos personalizados, acompanhamento profissional e gamificação para tornar sua jornada fitness mais eficiente e divertida.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="/GymForge-Academic/cadastro.php" class="btn btn-primary btn-lg forge-glow" data-aos="fade-right" data-aos-delay="200">
                        <i class="fas fa-fire me-2"></i>Comece Agora
                    </a>
                    <a href="/GymForge-Academic/exercicios/" class="btn btn-accent btn-lg" data-aos="fade-left" data-aos-delay="200">
                        <i class="fas fa-dumbbell me-2"></i>Explorar
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="hero-background bg-forge-2">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-8 text-center mb-5">
                    <div class="glass-effect" data-aos="fade-down">
                        <h2 class="mb-4">POR QUE ESCOLHER O GYMFORGE?</h2>
                        <p class="lead">Uma plataforma completa para sua evolução</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="glass-effect h-100 text-center">
                        <i class="fas fa-dumbbell fa-3x text-forge-accent mb-3"></i>
                        <h3>TREINOS PERSONALIZADOS</h3>
                        <p>Crie e gerencie seus treinos com nossa biblioteca completa de exercícios e vídeos demonstrativos.</p>
                        <a href="/GymForge-Academic/views/treinos/" class="btn btn-outline-accent mt-3 hover-effect">
                            <i class="fas fa-arrow-right me-2"></i>Saiba Mais
                        </a>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="glass-effect h-100 text-center">
                        <i class="fas fa-chart-line fa-3x text-forge-accent mb-3"></i>
                        <h3>ACOMPANHAMENTO</h3>
                        <p>Monitore sua evolução com gráficos detalhados e métricas personalizadas.</p>
                        <a href="/GymForge-Academic/views/dashboard/" class="btn btn-outline-accent mt-3 hover-effect">
                            <i class="fas fa-arrow-right me-2"></i>Ver Dashboard
                        </a>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="glass-effect h-100 text-center">
                        <i class="fas fa-fire fa-3x text-forge-accent mb-3"></i>
                        <h3>SISTEMA DE FORJA</h3>
                        <p>Evolua seu personagem, complete desafios e desbloqueie conquistas únicas.</p>
                        <a href="/GymForge-Academic/views/forge/" class="btn btn-outline-accent mt-3 hover-effect">
                            <i class="fas fa-arrow-right me-2"></i>Explorar Forja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Forge System -->
    <section class="hero-background bg-forge-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="glass-effect">
                        <h2>FORJE-SE</h2>
                        <p class="lead mb-4">No GymForge, cada treino é uma oportunidade de se fortalecer. Nossa mecânica única de progressão transforma seus esforços em conquistas reais.</p>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3" data-aos="fade-left" data-aos-delay="100">
                                <i class="fas fa-check text-forge-accent me-2"></i>Sistema de níveis e experiência
                            </li>
                            <li class="mb-3" data-aos="fade-left" data-aos-delay="200">
                                <i class="fas fa-check text-forge-accent me-2"></i>Conquistas desbloqueáveis
                            </li>
                            <li class="mb-3" data-aos="fade-left" data-aos-delay="300">
                                <i class="fas fa-check text-forge-accent me-2"></i>Desafios semanais
                            </li>
                            <li class="mb-3" data-aos="fade-left" data-aos-delay="400">
                                <i class="fas fa-check text-forge-accent me-2"></i>Ranking de usuários
                            </li>
                        </ul>
                        <a href="/GymForge-Academic/cadastro.php" class="btn btn-primary btn-lg forge-glow" data-aos="fade-up">
                            <i class="fas fa-fire me-2"></i>Comece sua Jornada
                        </a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <img src="/GymForge-Academic/assets/img/gymforge-badge.png" alt="GymForge Badge" class="img-fluid rounded-circle forge-glow" style="max-width: 400px; margin: 0 auto; display: block;">
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="hero-background bg-forge-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mb-5">
                    <div class="glass-effect">
                        <h2 class="mb-4">FORJADOS NA COMUNIDADE</h2>
                        <p class="lead">O que dizem nossos usuários</p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center g-4">
                <div class="col-md-4 d-flex">
                    <div class="glass-effect h-100 text-center w-100">
                        <img src="/GymForge-Academic/assets/img/testimonials/rafael.jpg" alt="João Silva" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover; object-position: top center;">
                        <div class="mb-3 text-forge-accent">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"O GymForge revolucionou minha forma de treinar. A gamificação torna tudo mais divertido e motivador!"</p>
                        <div class="d-flex align-items-center mt-3 justify-content-center">
                            <div class="ms-3">
                                <h6 class="mb-0">João Silva</h6>
                                <small class="text-forge-accent">Nível 25 - Guerreiro de Ferro</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="glass-effect h-100 text-center w-100">
                        <img src="/GymForge-Academic/assets/img/testimonials/amanda.jpg" alt="Maria Santos" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover; object-position: top center;">
                        <div class="mb-3 text-forge-accent">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"Como personal trainer, estou impressionada com a IA de feedback em tempo real do GymForge. Meus alunos evoluem muito mais rápido!"</p>
                        <div class="d-flex align-items-center mt-3 justify-content-center">
                            <div class="ms-3">
                                <h6 class="mb-0">Maria Santos</h6>
                                <small class="text-forge-accent">Nível 18 - Forjadora de Aço</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="glass-effect h-100 text-center w-100">
                        <img src="/GymForge-Academic/assets/img/testimonials/bruno.jpg" alt="Pedro Oliveira" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover; object-position: top center;">
                        <div class="mb-3 text-forge-accent">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"A comunidade do GymForge é incrível! Os desafios em grupo e o sistema de guildas tornam tudo mais envolvente."</p>
                        <div class="d-flex align-items-center mt-3 justify-content-center">
                            <div class="ms-3">
                                <h6 class="mb-0">Pedro Oliveira</h6>
                                <small class="text-forge-accent">Nível 30 - Mestre da Forja</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="hero-background bg-forge-1">
        <div class="container">
            <div class="glass-effect text-center" data-aos="zoom-in">
                <h2 class="mb-4">PRONTO PARA COMEÇAR SUA JORNADA?</h2>
                <p class="lead mb-4">Junte-se a milhares de usuários que já estão forjando sua melhor versão.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="/GymForge-Academic/cadastro.php" class="btn btn-primary btn-lg forge-glow" data-aos="fade-right" data-aos-delay="200">
                        <i class="fas fa-fire me-2"></i>Criar Conta
                    </a>
                    <a href="/GymForge-Academic/login.php" class="btn btn-accent btn-lg" data-aos="fade-left" data-aos-delay="200">
                        <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
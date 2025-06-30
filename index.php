<?php
require_once 'config/config.php';
require_once 'config/conexao.php';
require_once 'includes/header.php';
?>

<main class="fade-in">
    <!-- Hero Section -->
    <section class="hero-section vh-100 d-flex align-items-center">
        <div class="hero-pattern"></div>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-8 text-center text-lg-start animate-fadeInUp">
                    <div class="hero-badge">
                        <i class="bi bi-robot me-2"></i>Powered by GYMFORGE
                    </div>
                    <h1 class="display-3 fw-bold mb-4" style="font-family: 'Montserrat', sans-serif;">
                        Forjando sua melhor<br>versão
                    </h1>
                    <p class="lead mb-4 opacity-75">
                        O app mais completo para sua jornada fitness. Treinos personalizados pela IA,
                        comunidade ativa e resultados reais.
                    </p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                        <a href="forms/usuario/cadastro.php" class="btn btn-primary btn-lg">
                            Comece Agora
                        </a>
                        <a href="#como-funciona" class="btn btn-outline-light btn-lg">
                            Saiba Mais
                        </a>
                    </div>
                    <div class="mt-4 d-flex gap-4 justify-content-center justify-content-lg-start">
                        <div class="text-center">
                            <h3 class="h2 fw-bold text-primary mb-0">10000+</h3>
                            <p class="small opacity-75">Usuários Ativos</p>
                        </div>
                        <div class="text-center">
                            <h3 class="h2 fw-bold text-primary mb-0">100+</h3>
                            <p class="small opacity-75">Exercícios</p>
                        </div>
                        <div class="text-center">
                            <h3 class="h2 fw-bold text-primary mb-0">4.8<small class="fs-6">/5</small></h3>
                            <p class="small opacity-75">Avaliação</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="position-relative">
                        <img src="assets/img/gymforge-logo.jpeg" alt="GYMFORGE App" class="img-fluid rounded-4 shadow-lg">
                        <div class="position-absolute top-0 end-0 translate-middle-y">
                            <img src="assets/img/favicon/logo_small.png" alt="GYMFORGE Icon" class="img-fluid" style="width: 80px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recursos Section -->
    <section id="recursos" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" style="font-family: 'Montserrat', sans-serif;">
                    Recursos Exclusivos
                </h2>
                <p class="lead text-light opacity-75" style="font-family: 'Inter', sans-serif;">
                    Tudo que você precisa para alcançar seus objetivos
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 bg-dark border-primary">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-calendar-check display-4 text-primary"></i>
                            </div>
                            <h3 class="h4 mb-3" style="font-family: 'Montserrat', sans-serif;">
                                Treinos Personalizados
                            </h3>
                            <p class="text-light opacity-75" style="font-family: 'Inter', sans-serif;">
                                Programas adaptados ao seu nível e objetivos, com acompanhamento profissional.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 bg-dark border-primary">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-graph-up display-4 text-primary"></i>
                            </div>
                            <h3 class="h4 mb-3" style="font-family: 'Montserrat', sans-serif;">
                                Acompanhamento
                            </h3>
                            <p class="text-light opacity-75" style="font-family: 'Inter', sans-serif;">
                                Monitore seu progresso com gráficos detalhados e métricas personalizadas.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 bg-dark border-primary">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-people display-4 text-primary"></i>
                            </div>
                            <h3 class="h4 mb-3" style="font-family: 'Montserrat', sans-serif;">
                                Comunidade
                            </h3>
                            <p class="text-light opacity-75" style="font-family: 'Inter', sans-serif;">
                                Conecte-se com outros membros, compartilhe conquistas e inspire-se.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 bg-dark border-primary">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-trophy display-4 text-primary"></i>
                            </div>
                            <h3 class="h4 mb-3" style="font-family: 'Montserrat', sans-serif;">
                                Gamificação
                            </h3>
                            <p class="text-light opacity-75" style="font-family: 'Inter', sans-serif;">
                                Ganhe medalhas, suba de nível e participe de desafios motivadores.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeria Section -->
    <section id="galeria" class="py-5">
        <div class="container text-center">
            <h2 class="display-4 mb-3">Galeria de Treinos</h2>
            <p class="lead mb-5">Conheça alguns dos exercícios disponíveis na plataforma</p>
            
            <div class="row g-4">
                <!-- Flexão -->
                <div class="col-md-6 col-lg-3">
                    <div class="exercise-card h-100">
                        <div class="exercise-gif mb-3">
                            <img src="<?php echo BASE_URL; ?>/temp_gifs/pushup.gif" alt="Flexão de Braço" class="img-fluid rounded">
                        </div>
                        <h4 class="mb-2">Flexão de Braço</h4>
                        <p class="text-muted">Fortalecimento de peito e tríceps</p>
                    </div>
                </div>
                
                <!-- Agachamento -->
                <div class="col-md-6 col-lg-3">
                    <div class="exercise-card h-100">
                        <div class="exercise-gif mb-3">
                            <img src="<?php echo BASE_URL; ?>/temp_gifs/squat.gif" alt="Agachamento" class="img-fluid rounded">
                        </div>
                        <h4 class="mb-2">Agachamento</h4>
                        <p class="text-muted">Fortalecimento de pernas e glúteos</p>
                    </div>
                </div>
                
                <!-- Abdominal -->
                <div class="col-md-6 col-lg-3">
                    <div class="exercise-card h-100">
                        <div class="exercise-gif mb-3">
                            <img src="<?php echo BASE_URL; ?>/temp_gifs/abdominal.gif" alt="Abdominal" class="img-fluid rounded">
                        </div>
                        <h4 class="mb-2">Abdominal</h4>
                        <p class="text-muted">Fortalecimento do core</p>
                    </div>
                </div>
                
                <!-- Jumping Jack -->
                <div class="col-md-6 col-lg-3">
                    <div class="exercise-card h-100">
                        <div class="exercise-gif mb-3">
                            <img src="<?php echo BASE_URL; ?>/temp_gifs/jumping_jack.gif" alt="Jumping Jack" class="img-fluid rounded">
                        </div>
                        <h4 class="mb-2">Jumping Jack</h4>
                        <p class="text-muted">Exercício aeróbico completo</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <a href="#planos" class="btn btn-primary btn-lg">
                    Experimente Grátis
                </a>
            </div>
        </div>
    </section>

    <!-- Como Funciona Section -->
    <section id="como-funciona" class="py-5">
        <div class="container text-center">
            <h2 class="display-4 mb-3">Como Funciona</h2>
            <p class="lead mb-5">Três passos simples para começar sua transformação com nossa IA</p>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box p-4 rounded-3 h-100">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-person-plus-fill text-primary display-4"></i>
                        </div>
                        <h3 class="h2 text-primary">1</h3>
                        <h4>Cadastre-se</h4>
                        <p>Crie sua conta gratuitamente e preencha seu perfil com seus objetivos e condição física atual.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-box p-4 rounded-3 h-100">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-robot text-primary display-4"></i>
                        </div>
                        <h3 class="h2 text-primary">2</h3>
                        <h4>IA Personalizada</h4>
                        <p>Nossa inteligência artificial avançada criará um programa personalizado baseado no seu perfil e objetivos.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-box p-4 rounded-3 h-100">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-graph-up-arrow text-primary display-4"></i>
                        </div>
                        <h3 class="h2 text-primary">3</h3>
                        <h4>Acompanhe seu Progresso</h4>
                        <p>Registre seus treinos e veja sua evolução em tempo real com análises inteligentes da nossa IA.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Depoimentos Section -->
    <section id="depoimentos" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" style="font-family: 'Montserrat', sans-serif;">
                    Depoimentos
                </h2>
                <p class="lead text-light opacity-75" style="font-family: 'Inter', sans-serif;">
                    O que nossa comunidade diz
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card bg-dark border-primary h-100">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                            </div>
                            <p class="text-light opacity-75 mb-3" style="font-family: 'Inter', sans-serif;">
                                "O GYMFORGE mudou minha vida! Em 6 meses consegui resultados que não 
                                consegui em anos de academia. Os treinos são desafiadores e a 
                                comunidade é incrível!"
                            </p>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <img src="<?php echo BASE_URL; ?>/assets/img/depoimentos/depoimento-1.jpg.png" alt="Ana Silva" class="rounded-circle" width="60" height="60">
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-family: 'Montserrat', sans-serif;">
                                        Ana Silva
                                    </h5>
                                    <small class="text-light opacity-75">Membro há 8 meses</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-dark border-primary h-100">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                            </div>
                            <p class="text-light opacity-75 mb-3" style="font-family: 'Inter', sans-serif;">
                                "Como instrutor, o GYMFORGE me ajuda a gerenciar meus alunos de forma 
                                eficiente. A plataforma é intuitiva e os resultados são excelentes!"
                            </p>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <img src="<?php echo BASE_URL; ?>/assets/img/depoimentos/depoimento-2.jpg.png" alt="Pedro Santos" class="rounded-circle" width="60" height="60">
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-family: 'Montserrat', sans-serif;">
                                        Pedro Santos
                                    </h5>
                                    <small class="text-light opacity-75">Instrutor</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-dark border-primary h-100">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                                <i class="bi bi-star-fill text-primary"></i>
                            </div>
                            <p class="text-light opacity-75 mb-3" style="font-family: 'Inter', sans-serif;">
                                "Amo como posso acompanhar meu progresso! Os gráficos e métricas me 
                                mantêm motivado, e os instrutores são muito atenciosos."
                            </p>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <img src="<?php echo BASE_URL; ?>/assets/img/depoimentos/depoimento-3.jpg.png" alt="Carlos Oliveira" class="rounded-circle" width="60" height="60">
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-family: 'Montserrat', sans-serif;">
                                        Carlos Oliveira
                                    </h5>
                                    <small class="text-light opacity-75">Membro há 1 ano</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Planos Section -->
    <section id="planos" class="py-5">
        <div class="container text-center">
            <h2 class="display-4 mb-3">Planos</h2>
            <p class="lead mb-5">Escolha o plano ideal para você</p>
            
            <div class="row justify-content-center g-4">
                <div class="col-md-5">
                    <div class="card h-100 pricing-card">
                        <div class="card-body p-4">
                            <h3 class="card-title">Básico</h3>
                            <div class="price-tag my-4">
                                <span class="currency">R$</span>
                                <span class="amount">0</span>
                                <span class="period">/mês</span>
                            </div>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Treinos básicos gerados por IA</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Acesso à biblioteca de exercícios</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Registro de treinos</li>
                                <li><i class="bi bi-x-circle text-muted me-2"></i>Análise avançada de progresso</li>
                                <li><i class="bi bi-x-circle text-muted me-2"></i>Treinos personalizados</li>
                            </ul>
                            <button class="btn btn-outline-primary btn-lg w-100 mt-4">Começar Grátis</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <div class="card h-100 pricing-card border-primary">
                        <div class="card-body p-4">
                            <div class="popular-badge">Popular</div>
                            <h3 class="card-title">Pro</h3>
                            <div class="price-tag my-4">
                                <span class="currency">R$</span>
                                <span class="amount">19,90</span>
                                <span class="period">/mês</span>
                            </div>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Treinos avançados com IA</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Biblioteca completa de exercícios</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Registro detalhado de treinos</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Análise avançada de progresso</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Treinos 100% personalizados</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Suporte prioritário</li>
                            </ul>
                            <button class="btn btn-primary btn-lg w-100 mt-4">Assinar Pro</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" style="font-family: 'Montserrat', sans-serif;">
                    Perguntas Frequentes
                </h2>
                <p class="lead text-light opacity-75" style="font-family: 'Inter', sans-serif;">
                    Tire suas dúvidas
                </p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item bg-dark border-light">
                            <h2 class="accordion-header">
                                <button class="accordion-button bg-dark text-light" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Como começar?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-light opacity-75">
                                    Basta criar uma conta gratuita, preencher seu perfil com seus objetivos 
                                    e preferências. Nossos instrutores criarão um programa personalizado 
                                    para você em até 24 horas.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item bg-dark border-light">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Preciso ter experiência?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-light opacity-75">
                                    Não! O GYMFORGE é para todos os níveis. Nossos treinos são adaptados 
                                    ao seu nível de experiência, seja iniciante, intermediário ou avançado.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item bg-dark border-light">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Posso cancelar quando quiser?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-light opacity-75">
                                    Sim! Não há contratos longos. Você pode cancelar sua assinatura Pro 
                                    a qualquer momento e continuar usando os recursos do plano Básico.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary">
        <div class="container py-5">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="display-4 fw-bold mb-4" style="font-family: 'Montserrat', sans-serif;">
                        Comece sua transformação hoje
                    </h2>
                    <p class="lead mb-4" style="font-family: 'Inter', sans-serif;">
                        Junte-se a milhares de pessoas que já estão forjando sua melhor versão
                    </p>
                    <a href="forms/usuario/cadastro.php" class="btn btn-light btn-lg">
                        Criar Conta Grátis
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
require_once 'includes/footer.php';
?> 
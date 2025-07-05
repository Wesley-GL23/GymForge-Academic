    </main>

    <!-- Footer Profissional -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <!-- Coluna 1: Sobre -->
                <div class="col-lg-4 col-md-6 footer-section">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-lightning-charge-fill text-accent fs-3 me-2"></i>
                        <h5 class="mb-0">GYMFORGE</h5>
                    </div>
                    <p class="text-muted mb-4">
                        Transforme sua jornada fitness com a plataforma mais completa para gerenciamento de academias e acompanhamento de treinos. Forjando sua melhor versão desde 2024.
                    </p>
                    <div class="social-links">
                        <a href="#" class="me-3" data-tooltip="Siga-nos no Facebook">
                            <i class="bi bi-facebook fs-5"></i>
                        </a>
                        <a href="#" class="me-3" data-tooltip="Siga-nos no Instagram">
                            <i class="bi bi-instagram fs-5"></i>
                        </a>
                        <a href="#" class="me-3" data-tooltip="Siga-nos no Twitter">
                            <i class="bi bi-twitter fs-5"></i>
                        </a>
                        <a href="#" class="me-3" data-tooltip="Siga-nos no YouTube">
                            <i class="bi bi-youtube fs-5"></i>
                        </a>
                        <a href="#" data-tooltip="Siga-nos no LinkedIn">
                            <i class="bi bi-linkedin fs-5"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Coluna 2: Links Rápidos -->
                <div class="col-lg-2 col-md-6 footer-section">
                    <h6 class="footer-title">Links Rápidos</h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>">Início</a></li>
                        <li><a href="<?php echo BASE_URL; ?>exercises.php">Exercícios</a></li>
                        <li><a href="<?php echo BASE_URL; ?>workouts.php">Treinos</a></li>
                        <li><a href="<?php echo BASE_URL; ?>about.php">Sobre Nós</a></li>
                        <li><a href="<?php echo BASE_URL; ?>contact.php">Contato</a></li>
                        <li><a href="<?php echo BASE_URL; ?>blog.php">Blog</a></li>
                    </ul>
                </div>
                
                <!-- Coluna 3: Recursos -->
                <div class="col-lg-2 col-md-6 footer-section">
                    <h6 class="footer-title">Recursos</h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>help.php">Central de Ajuda</a></li>
                        <li><a href="<?php echo BASE_URL; ?>tutorials.php">Tutoriais</a></li>
                        <li><a href="<?php echo BASE_URL; ?>api.php">API</a></li>
                        <li><a href="<?php echo BASE_URL; ?>integrations.php">Integrações</a></li>
                        <li><a href="<?php echo BASE_URL; ?>partners.php">Parceiros</a></li>
                        <li><a href="<?php echo BASE_URL; ?>developers.php">Desenvolvedores</a></li>
                    </ul>
                </div>
                
                <!-- Coluna 4: Suporte e Contato -->
                <div class="col-lg-4 col-md-6 footer-section">
                    <h6 class="footer-title">Suporte & Contato</h6>
                    <div class="contact-info mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-envelope text-accent me-2"></i>
                            <span>suporte@gymforge.com</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-telephone text-accent me-2"></i>
                            <span>+55 (11) 99999-9999</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt text-accent me-2"></i>
                            <span>São Paulo, SP - Brasil</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock text-accent me-2"></i>
                            <span>Seg-Sex: 8h às 18h</span>
                        </div>
                    </div>
                    
                    <!-- Newsletter -->
                    <div class="newsletter">
                        <h6 class="mb-3">Newsletter</h6>
                        <p class="text-muted small mb-3">Receba dicas de fitness e novidades do GymForge</p>
                        <form class="d-flex">
                            <input type="email" class="form-control me-2" placeholder="Seu email" required>
                            <button type="submit" class="btn btn-accent">
                                <i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Linha de Separação -->
            <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.1);">
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0 text-muted">
                            &copy; 2024 GymForge. Todos os direitos reservados.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <a href="<?php echo BASE_URL; ?>privacy.php" class="text-muted text-decoration-none">Privacidade</a>
                            </li>
                            <li class="list-inline-item">
                                <span class="text-muted">•</span>
                            </li>
                            <li class="list-inline-item">
                                <a href="<?php echo BASE_URL; ?>terms.php" class="text-muted text-decoration-none">Termos de Uso</a>
                            </li>
                            <li class="list-inline-item">
                                <span class="text-muted">•</span>
                            </li>
                            <li class="list-inline-item">
                                <a href="<?php echo BASE_URL; ?>cookies.php" class="text-muted text-decoration-none">Cookies</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Botão Voltar ao Topo -->
    <button id="backToTop" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; z-index: 1000; display: none; border-radius: 50%; width: 50px; height: 50px;">
        <i class="bi bi-arrow-up"></i>
    </button>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    
    <!-- Scripts Adicionais -->
    <script>
        // Botão Voltar ao Topo
        const backToTopBtn = document.getElementById('backToTop');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Ativar tooltips do Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Ativar popovers do Bootstrap
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    </script>
    
    <!-- Analytics (exemplo) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
</body>
</html> 
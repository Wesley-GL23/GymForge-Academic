<?php
$titulo = "Erro Interno";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 fw-bold text-danger">500</h1>
                <h2 class="mb-4">Ops! Algo deu errado</h2>
                <p class="lead mb-5">Nossos servidores encontraram um erro interno. Estamos trabalhando para resolver o problema o mais rápido possível.</p>
                
                <div class="error-actions">
                    <a href="/GymForge-Academic/" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i>Página Inicial
                    </a>
                    <a href="/GymForge-Academic/views/contato/" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contatar Suporte
                    </a>
                </div>
                
                <div class="mt-5">
                    <h3 class="h5 mb-4">Enquanto isso, você pode:</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-redo text-primary me-2"></i>Atualizar a página</li>
                        <li class="mb-2"><i class="fas fa-clock text-primary me-2"></i>Tentar novamente em alguns minutos</li>
                        <li class="mb-2"><i class="fas fa-envelope text-primary me-2"></i>Entrar em contato com nosso suporte</li>
                    </ul>
                </div>
                
                <?php if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin'): ?>
                <div class="mt-5 text-start">
                    <div class="alert alert-danger">
                        <h4 class="alert-heading"><i class="fas fa-bug me-2"></i>Detalhes Técnicos</h4>
                        <hr>
                        <p class="mb-0">
                            <?php 
                            if (isset($_SESSION['error_details'])) {
                                echo htmlspecialchars($_SESSION['error_details']);
                                unset($_SESSION['error_details']);
                            } else {
                                echo "Nenhum detalhe técnico disponível.";
                            }
                            ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?> 
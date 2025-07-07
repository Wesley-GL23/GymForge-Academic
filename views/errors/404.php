<?php
$titulo = "Página não encontrada";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 fw-bold text-primary">404</h1>
                <h2 class="mb-4">Ops! Página não encontrada</h2>
                <p class="lead mb-5">A página que você está procurando pode ter sido removida, renomeada ou está temporariamente indisponível.</p>
                
                <div class="error-actions">
                    <a href="/GymForge-Academic/" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i>Página Inicial
                    </a>
                    <a href="/GymForge-Academic/views/contato/" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contatar Suporte
                    </a>
                </div>
                
                <div class="mt-5">
                    <h3 class="h5 mb-4">Você pode tentar:</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Verificar o endereço digitado</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Usar a barra de pesquisa acima</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Navegar pelo menu principal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?> 
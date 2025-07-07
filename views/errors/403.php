<?php
$titulo = "Acesso Negado";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 fw-bold text-warning">403</h1>
                <h2 class="mb-4">Acesso Negado</h2>
                <p class="lead mb-5">Você não tem permissão para acessar esta página. Se você acredita que isso é um erro, entre em contato com o administrador.</p>
                
                <div class="error-actions">
                    <a href="/GymForge-Academic/" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i>Página Inicial
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/GymForge-Academic/forms/usuario/login.php" class="btn btn-accent btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                    </a>
                    <?php else: ?>
                    <a href="/GymForge-Academic/views/contato/" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contatar Suporte
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="mt-5">
                    <h3 class="h5 mb-4">Possíveis razões:</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-user-lock text-warning me-2"></i>Nível de acesso insuficiente</li>
                        <li class="mb-2"><i class="fas fa-clock text-warning me-2"></i>Sessão expirada</li>
                        <li class="mb-2"><i class="fas fa-shield-alt text-warning me-2"></i>Restrições de segurança</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?> 
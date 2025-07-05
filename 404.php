<?php
// Garantir que o BASE_URL esteja disponível mesmo em erros
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/config/config.php';
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container text-center py-5">
    <img src="<?php echo BASE_URL; ?>/assets/img/logo_small.png" alt="GYMFORGE" class="mb-4" style="height: 60px;">
    <h1 class="display-1 fw-bold">404</h1>
    <h2 class="mb-4">Página não encontrada</h2>
    <p class="lead mb-5">Desculpe, mas a página que você está procurando não existe ou foi movida.</p>
    <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary btn-lg">Voltar para a Página Inicial</a>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
<?php
require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="display-4 mb-4">Bem-vindo ao GymForge</h1>
            <p class="lead mb-4">
                Transforme seu treino com o GymForge - a plataforma completa para gerenciar seus exercícios e acompanhar seu progresso.
            </p>
            <?php if (!estaLogado()): ?>
                <div class="d-grid gap-2 d-md-flex">
                    <a href="<?php echo BASE_URL; ?>/forms/usuario/cadastro.php" class="btn btn-primary btn-lg me-md-2">
                        Começar Agora
                    </a>
                    <a href="<?php echo BASE_URL; ?>/forms/usuario/login.php" class="btn btn-outline-primary btn-lg">
                        Fazer Login
                    </a>
                </div>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/views/dashboard/" class="btn btn-primary btn-lg">
                    Ir para Dashboard
                </a>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <img src="<?php echo BASE_URL; ?>/assets/img/hero-image.jpg" alt="GymForge Hero" class="img-fluid rounded shadow">
        </div>
    </div>
</div>

<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Recursos Principais</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-dumbbell fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Biblioteca de Exercícios</h5>
                        <p class="card-text">
                            Acesse uma vasta biblioteca de exercícios com GIFs demonstrativos e instruções detalhadas.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Planejamento de Treinos</h5>
                        <p class="card-text">
                            Crie e gerencie seus treinos personalizados com facilidade e praticidade.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Acompanhamento de Progresso</h5>
                        <p class="card-text">
                            Monitore seu desenvolvimento e mantenha-se motivado com estatísticas detalhadas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <h2 class="text-center mb-5">Dicas de Treino</h2>
    <div class="row">
        <?php
        $conn = conectarBD();
        $sql = "SELECT * FROM dicas ORDER BY created_at DESC LIMIT 3";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($dica = $result->fetch_assoc()) {
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="card h-100">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($dica['titulo']) . '</h5>';
                echo '<span class="badge bg-secondary mb-2">' . htmlspecialchars($dica['categoria']) . '</span>';
                echo '<p class="card-text">' . htmlspecialchars($dica['conteudo']) . '</p>';
                echo '</div></div></div>';
            }
        }
        fecharConexao($conn);
        ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?> 
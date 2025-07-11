<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/exercise_functions.php';

// Verifica se está logado
requireAuth();

// Buscar exercícios
$exercicios = buscarExercicios();

// Título da página
$titulo = "Gerenciar Exercícios";
?>

<?php include_once __DIR__ . '/../../includes/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $titulo; ?></h1>
        <a href="criar.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Exercício
        </a>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo htmlspecialchars($_SESSION['mensagem']);
            unset($_SESSION['mensagem']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo htmlspecialchars($_SESSION['erro']);
            unset($_SESSION['erro']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($exercicios as $exercicio): ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <?php if (!empty($exercicio['video_url'])): ?>
                        <div class="card-img-top position-relative" style="height: 200px;">
                            <video class="w-100 h-100 object-fit-cover" muted>
                                <source src="/GymForge-Academic/<?php echo htmlspecialchars($exercicio['video_url']); ?>" type="video/mp4">
                                Seu navegador não suporta vídeos.
                            </video>
                            <button class="btn btn-play position-absolute top-50 start-50 translate-middle" 
                                    onclick="toggleVideo(this)">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-dumbbell fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>

                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($exercicio['nome']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($exercicio['descricao']); ?></p>
                        
                        <div class="mb-3">
                            <strong>Grupo Muscular:</strong>
                            <span class="badge bg-primary"><?php echo ucfirst($exercicio['categoria']); ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Nível:</strong>
                            <span class="badge bg-secondary"><?php echo ucfirst($exercicio['nivel_dificuldade']); ?></span>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="detalhes.php?id=<?php echo $exercicio['id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="editar.php?id=<?php echo $exercicio['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="confirmarExclusao(<?php echo $exercicio['id']; ?>)">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="modalConfirmacao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este exercício?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="excluir.php" method="post" class="d-inline">
                    <input type="hidden" name="id" id="exercicioId">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleVideo(btn) {
    const video = btn.parentElement.querySelector('video');
    if (video.paused) {
        video.play();
        btn.innerHTML = '<i class="fas fa-pause"></i>';
    } else {
        video.pause();
        btn.innerHTML = '<i class="fas fa-play"></i>';
    }
}

function confirmarExclusao(id) {
    document.getElementById('exercicioId').value = id;
    new bootstrap.Modal(document.getElementById('modalConfirmacao')).show();
}

// Parar todos os vídeos quando fechar um modal
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('hidden.bs.modal', () => {
        document.querySelectorAll('video').forEach(video => {
            video.pause();
            const btn = video.parentElement.querySelector('.btn-play');
            if (btn) btn.innerHTML = '<i class="fas fa-play"></i>';
        });
    });
});
</script>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?> 
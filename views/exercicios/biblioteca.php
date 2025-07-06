<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';
require_once '../../includes/exercise_functions.php';

// Carrega os exercícios do banco de dados
$exercicios = listar_exercicios();
$categorias = listarCategorias();
$gruposMusculares = listarGruposMusculares();
$niveisDificuldade = listarNiveisDificuldade();

// Verifica se o usuário é admin para mostrar os botões de CRUD
$isAdmin = isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin';
?>

<div class="exercises-library">
    <!-- Hero Section -->
    <div class="library-hero glass-effect">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 mb-4">Biblioteca de Exercícios</h1>
                    <p class="lead mb-4">Explore nossa coleção completa de exercícios com vídeos em HD e instruções detalhadas.</p>
                    
                    <!-- Search Bar -->
                    <div class="search-bar glass-effect-light mb-4">
                        <i class="fas fa-search"></i>
                        <input type="text" id="exerciseSearch" placeholder="Buscar exercícios..." class="form-control">
                    </div>

                    <?php if ($isAdmin): ?>
                    <div class="admin-controls mb-4">
                        <a href="/forms/exercicio/form.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novo Exercício
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <!-- Stats Cards -->
                    <div class="row g-4">
                        <?php foreach ($categorias as $key => $categoria): ?>
                        <div class="col-6">
                            <div class="stat-card glass-effect-light">
                                <i class="fas fa-dumbbell"></i>
                                <h3><?php echo $categoria; ?></h3>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section py-4">
        <div class="container">
            <div class="filters-wrapper glass-effect-light">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <select class="form-select" id="categoryFilter">
                            <option value="">Todas Categorias</option>
                            <?php foreach ($categorias as $valor => $nome): ?>
                            <option value="<?php echo $valor; ?>"><?php echo $nome; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="muscleFilter">
                            <option value="">Todos Músculos</option>
                            <?php foreach ($gruposMusculares as $valor => $nome): ?>
                            <option value="<?php echo $valor; ?>"><?php echo $nome; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="difficultyFilter">
                            <option value="">Todas Dificuldades</option>
                            <?php foreach ($niveisDificuldade as $valor => $nome): ?>
                            <option value="<?php echo $valor; ?>"><?php echo $nome; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" id="clearFilters">
                            Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exercises Grid -->
    <div class="exercises-grid py-5">
        <div class="container">
            <div class="row g-4" id="exercisesContainer">
                <?php foreach ($exercicios as $exercicio): ?>
                <div class="col-lg-4 col-md-6 exercise-card" 
                     data-category="<?php echo $exercicio['categoria']; ?>"
                     data-difficulty="<?php echo $exercicio['nivel_dificuldade']; ?>"
                     data-muscles="<?php echo $exercicio['grupo_muscular']; ?>">
                    <div class="card glass-effect h-100">
                        <!-- Video/GIF Preview -->
                        <div class="card-video-wrapper">
                            <?php if ($exercicio['video_url']): ?>
                            <video class="card-video" loop muted>
                                <source src="<?php echo $exercicio['video_url']; ?>" type="video/mp4">
                            </video>
                            <?php elseif ($exercicio['gif_url']): ?>
                            <img src="<?php echo $exercicio['gif_url']; ?>" alt="<?php echo $exercicio['nome']; ?>" class="card-img-top">
                            <?php else: ?>
                            <div class="placeholder-image">
                                <i class="fas fa-dumbbell fa-3x"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Card Content -->
                        <div class="card-body">
                            <div class="difficulty-badge <?php echo $exercicio['nivel_dificuldade']; ?>">
                                <?php echo $niveisDificuldade[$exercicio['nivel_dificuldade']]; ?>
                            </div>
                            <h3 class="card-title"><?php echo $exercicio['nome']; ?></h3>
                            <p class="card-text"><?php echo $exercicio['descricao']; ?></p>
                            
                            <!-- Exercise Details -->
                            <div class="exercise-details">
                                <div class="detail">
                                    <i class="fas fa-dumbbell"></i>
                                    <?php echo $categorias[$exercicio['categoria']]; ?>
                                </div>
                                <div class="detail">
                                    <i class="fas fa-running"></i>
                                    <?php echo $gruposMusculares[$exercicio['grupo_muscular']]; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exerciseModal<?php echo $exercicio['id']; ?>">
                                Ver Detalhes
                            </button>
                            <?php if ($isAdmin): ?>
                            <div class="admin-controls">
                                <a href="/forms/exercicio/form.php?id=<?php echo $exercicio['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="deleteExercise(<?php echo $exercicio['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Exercise Modal -->
                <div class="modal fade" id="exerciseModal<?php echo $exercicio['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content glass-effect">
                            <div class="modal-header border-0">
                                <h5 class="modal-title"><?php echo $exercicio['nome']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Video/GIF Player -->
                                <?php if ($exercicio['video_url']): ?>
                                <div class="video-player-wrapper mb-4">
                                    <video controls class="w-100">
                                        <source src="<?php echo $exercicio['video_url']; ?>" type="video/mp4">
                                    </video>
                                </div>
                                <?php elseif ($exercicio['gif_url']): ?>
                                <div class="gif-wrapper mb-4">
                                    <img src="<?php echo $exercicio['gif_url']; ?>" alt="<?php echo $exercicio['nome']; ?>" class="w-100">
                                </div>
                                <?php endif; ?>

                                <!-- Exercise Instructions -->
                                <div class="instructions-wrapper">
                                    <h6 class="mb-3">Instruções:</h6>
                                    <div class="instructions-text">
                                        <?php echo nl2br($exercicio['instrucoes']); ?>
                                    </div>
                                </div>

                                <!-- Exercise Details -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h6>Grupo Muscular:</h6>
                                        <p><?php echo $gruposMusculares[$exercicio['grupo_muscular']]; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Informações:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-dumbbell"></i> <?php echo $categorias[$exercicio['categoria']]; ?></li>
                                            <li><i class="fas fa-signal"></i> <?php echo $niveisDificuldade[$exercicio['nivel_dificuldade']]; ?></li>
                                        </ul>
                                    </div>
                                </div>

                                <?php if ($exercicio['dicas_seguranca']): ?>
                                <div class="safety-tips mt-4">
                                    <h6>Dicas de Segurança:</h6>
                                    <div class="alert alert-info">
                                        <?php echo nl2br($exercicio['dicas_seguranca']); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Fechar</button>
                                <?php if ($isAdmin): ?>
                                <a href="/forms/exercicio/form.php?id=<?php echo $exercicio['id']; ?>" class="btn btn-warning">
                                    Editar
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($isAdmin): ?>
<script>
function deleteExercise(id) {
    if (confirm('Tem certeza que deseja excluir este exercício?')) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('csrf_token', '<?php echo generateCsrfToken(); ?>');

        fetch('/actions/exercicio/crud.php?acao=deletar', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Erro ao excluir exercício');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir exercício');
        });
    }
}

// Validação do formulário
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Filtros
document.getElementById('exerciseSearch').addEventListener('input', filterExercises);
document.getElementById('categoryFilter').addEventListener('change', filterExercises);
document.getElementById('muscleFilter').addEventListener('change', filterExercises);
document.getElementById('difficultyFilter').addEventListener('change', filterExercises);
document.getElementById('clearFilters').addEventListener('click', clearFilters);

function filterExercises() {
    const search = document.getElementById('exerciseSearch').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const muscle = document.getElementById('muscleFilter').value;
    const difficulty = document.getElementById('difficultyFilter').value;

    document.querySelectorAll('.exercise-card').forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const cardCategory = card.dataset.category;
        const cardMuscles = card.dataset.muscles;
        const cardDifficulty = card.dataset.difficulty;

        const matchesSearch = title.includes(search);
        const matchesCategory = !category || cardCategory === category;
        const matchesMuscle = !muscle || cardMuscles.includes(muscle);
        const matchesDifficulty = !difficulty || cardDifficulty === difficulty;

        if (matchesSearch && matchesCategory && matchesMuscle && matchesDifficulty) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function clearFilters() {
    document.getElementById('exerciseSearch').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('muscleFilter').value = '';
    document.getElementById('difficultyFilter').value = '';
    document.querySelectorAll('.exercise-card').forEach(card => card.style.display = '');
}
</script>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>
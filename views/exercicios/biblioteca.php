<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/exercise_functions.php';

// Verifica se é administrador
$is_admin = isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin';

// Buscar exercícios do banco de dados
$filtros = [];
if (!empty($_GET['categoria'])) $filtros['categoria'] = $_GET['categoria'];
if (!empty($_GET['nivel'])) $filtros['nivel'] = $_GET['nivel'];
if (!empty($_GET['busca'])) $filtros['busca'] = $_GET['busca'];

$exercicios = buscarExercicios($filtros);

// Processar formulário de atualização
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'];
        $dados = [
            'nome' => $_POST['nome'],
            'categoria' => $_POST['categoria'],
            'descricao' => $_POST['descricao'],
            'nivel_dificuldade' => $_POST['nivel_dificuldade']
        ];
        
        if (atualizarExercicio($id, $dados)) {
            $_SESSION['mensagem'] = "Exercício atualizado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao atualizar exercício.";
        }
        
        // Recarrega a página para mostrar as alterações
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao atualizar exercício: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca de Exercícios - GymForge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.2s ease-in-out;
        }
        .btn-play {
            background: rgba(0,0,0,0.5);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            color: white;
        }
        .btn-play:hover {
            background: rgba(0,0,0,0.7);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['mensagem']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['erro']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col">
                <h1>Biblioteca de Exercícios</h1>
            </div>
            <?php if ($is_admin): ?>
            <div class="col-auto">
                <a href="criar.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Exercício
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-4">
                <select class="form-select" id="filtroCategoria">
                    <option value="">Todas as Categorias</option>
                    <option value="musculacao">Musculação</option>
                    <option value="cardio">Cardio</option>
                    <option value="funcional">Funcional</option>
                    <option value="alongamento">Alongamento</option>
                    <option value="yoga">Yoga</option>
                    <option value="pilates">Pilates</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filtroNivel">
                    <option value="">Todos os Níveis</option>
                    <option value="iniciante">Iniciante</option>
                    <option value="intermediario">Intermediário</option>
                    <option value="avancado">Avançado</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="busca" placeholder="Buscar exercícios...">
            </div>
        </div>

        <!-- Grid de Exercícios -->
        <div class="row g-4">
            <?php foreach ($exercicios as $exercicio): ?>
                <div class="col-lg-4 col-md-6 exercise-card" 
                     data-category="<?php echo htmlspecialchars($exercicio['categoria']); ?>"
                     data-difficulty="<?php echo htmlspecialchars($exercicio['nivel_dificuldade']); ?>">
                    <div class="card h-100 shadow-sm card-hover">
                        <!-- Vídeo/Imagem Preview -->
                        <div class="card-media-wrapper position-relative">
                            <?php if (!empty($exercicio['video_url'])): ?>
                                <div class="video-container">
                                    <video class="card-video w-100" loop muted preload="metadata">
                                        <source src="<?php echo htmlspecialchars($exercicio['video_url']); ?>" type="video/mp4">
                                        Seu navegador não suporta vídeos HTML5.
                                    </video>
                                    <button class="btn btn-play position-absolute top-50 start-50 translate-middle">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="placeholder-image bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-dumbbell fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <?php if ($is_admin): ?>
                                <!-- Formulário de Edição (Admin) -->
                                <form method="post" class="exercise-form">
                                    <input type="hidden" name="id" value="<?php echo $exercicio['id']; ?>">
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Nome</label>
                                        <input type="text" class="form-control" name="nome" 
                                               value="<?php echo htmlspecialchars($exercicio['nome']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Categoria</label>
                                        <select class="form-select" name="categoria" required>
                                            <option value="musculacao" <?php echo $exercicio['categoria'] === 'musculacao' ? 'selected' : ''; ?>>Musculação</option>
                                            <option value="cardio" <?php echo $exercicio['categoria'] === 'cardio' ? 'selected' : ''; ?>>Cardio</option>
                                            <option value="funcional" <?php echo $exercicio['categoria'] === 'funcional' ? 'selected' : ''; ?>>Funcional</option>
                                            <option value="alongamento" <?php echo $exercicio['categoria'] === 'alongamento' ? 'selected' : ''; ?>>Alongamento</option>
                                            <option value="yoga" <?php echo $exercicio['categoria'] === 'yoga' ? 'selected' : ''; ?>>Yoga</option>
                                            <option value="pilates" <?php echo $exercicio['categoria'] === 'pilates' ? 'selected' : ''; ?>>Pilates</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Descrição</label>
                                        <textarea class="form-control" name="descricao" rows="2"><?php echo htmlspecialchars($exercicio['descricao']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Nível</label>
                                        <select class="form-select" name="nivel_dificuldade" required>
                                            <option value="iniciante" <?php echo $exercicio['nivel_dificuldade'] === 'iniciante' ? 'selected' : ''; ?>>Iniciante</option>
                                            <option value="intermediario" <?php echo $exercicio['nivel_dificuldade'] === 'intermediario' ? 'selected' : ''; ?>>Intermediário</option>
                                            <option value="avancado" <?php echo $exercicio['nivel_dificuldade'] === 'avancado' ? 'selected' : ''; ?>>Avançado</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save"></i> Salvar
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="excluirExercicio(<?php echo $exercicio['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <!-- Visualização Normal (Usuário) -->
                                <h5 class="card-title"><?php echo htmlspecialchars($exercicio['nome']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($exercicio['descricao']); ?></p>
                                <div class="d-flex gap-2 mb-2">
                                    <span class="badge bg-primary"><?php echo ucfirst($exercicio['categoria']); ?></span>
                                    <span class="badge bg-secondary"><?php echo ucfirst($exercicio['nivel_dificuldade']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Reprodução de vídeo
        document.querySelectorAll('.card-video').forEach(video => {
            const btn = video.parentElement.querySelector('.btn-play');
            if (btn) {
                btn.addEventListener('click', () => {
                    if (video.paused) {
                        video.play();
                        btn.innerHTML = '<i class="fas fa-pause"></i>';
                    } else {
                        video.pause();
                        btn.innerHTML = '<i class="fas fa-play"></i>';
                    }
                });
            }
        });

        // Filtros
        const filtroCategoria = document.getElementById('filtroCategoria');
        const filtroNivel = document.getElementById('filtroNivel');
        const busca = document.getElementById('busca');

        function aplicarFiltros() {
            const categoria = filtroCategoria.value.toLowerCase();
            const nivel = filtroNivel.value.toLowerCase();
            const termo = busca.value.toLowerCase();

            document.querySelectorAll('.exercise-card').forEach(card => {
                const cardCategoria = card.dataset.category.toLowerCase();
                const cardNivel = card.dataset.difficulty.toLowerCase();
                const cardNome = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
                const cardDesc = card.querySelector('.card-text')?.textContent.toLowerCase() || '';

                const matchCategoria = !categoria || cardCategoria === categoria;
                const matchNivel = !nivel || cardNivel === nivel;
                const matchBusca = !termo || 
                                 cardNome.includes(termo) || 
                                 cardDesc.includes(termo);

                card.style.display = (matchCategoria && matchNivel && matchBusca) ? '' : 'none';
            });
        }

        filtroCategoria.addEventListener('change', aplicarFiltros);
        filtroNivel.addEventListener('change', aplicarFiltros);
        busca.addEventListener('input', aplicarFiltros);

        // Função para excluir exercício
        function excluirExercicio(id) {
            if (confirm('Tem certeza que deseja excluir este exercício?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'excluir.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
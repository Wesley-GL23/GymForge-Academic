<?php
require_once __DIR__ . '/../../includes/exercise_functions.php';
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';

// Verifica se o usuário está logado e é admin
requireAdmin();

// Verifica se é edição
$id = $_GET['id'] ?? 0;
$exercicio = null;
if ($id) {
    $exercicio = buscar_exercicio($id);
    if (!$exercicio) {
        header('Location: /views/exercicios/biblioteca.php');
        exit;
    }
}

// Lista de opções para os selects
$categorias = listarCategorias();
$gruposMusculares = listarGruposMusculares();
$niveisDificuldade = listarNiveisDificuldade();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card glass-effect">
                <div class="card-body">
                    <h2 class="card-title mb-4"><?php echo $id ? 'Editar Exercício' : 'Novo Exercício'; ?></h2>
                    
                    <form id="exercicioForm" action="/actions/exercicio/crud.php?acao=<?php echo $id ? 'atualizar' : 'criar'; ?>" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <?php if ($id): ?>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <?php endif; ?>

                        <!-- Nome do Exercício -->
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Exercício</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?php echo $exercicio['nome'] ?? ''; ?>" required>
                            <div class="invalid-feedback">
                                Por favor, informe o nome do exercício.
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?php echo $exercicio['descricao'] ?? ''; ?></textarea>
                            <div class="invalid-feedback">
                                Por favor, informe a descrição do exercício.
                            </div>
                        </div>

                        <!-- Categoria -->
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Selecione uma categoria</option>
                                <?php foreach ($categorias as $valor => $nome): ?>
                                    <option value="<?php echo $valor; ?>" <?php echo ($exercicio['categoria'] ?? '') === $valor ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecione uma categoria.
                            </div>
                        </div>

                        <!-- Grupo Muscular -->
                        <div class="mb-3">
                            <label for="grupo_muscular" class="form-label">Grupo Muscular</label>
                            <select class="form-select" id="grupo_muscular" name="grupo_muscular" required>
                                <option value="">Selecione um grupo muscular</option>
                                <?php foreach ($gruposMusculares as $valor => $nome): ?>
                                    <option value="<?php echo $valor; ?>" <?php echo ($exercicio['grupo_muscular'] ?? '') === $valor ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecione um grupo muscular.
                            </div>
                        </div>

                        <!-- Nível de Dificuldade -->
                        <div class="mb-3">
                            <label for="nivel_dificuldade" class="form-label">Nível de Dificuldade</label>
                            <select class="form-select" id="nivel_dificuldade" name="nivel_dificuldade" required>
                                <?php foreach ($niveisDificuldade as $valor => $nome): ?>
                                    <option value="<?php echo $valor; ?>" <?php echo ($exercicio['nivel_dificuldade'] ?? '') === $valor ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecione um nível de dificuldade.
                            </div>
                        </div>

                        <!-- URLs de Mídia -->
                        <div class="mb-3">
                            <label for="gif_url" class="form-label">URL do GIF</label>
                            <input type="url" class="form-control" id="gif_url" name="gif_url" 
                                   value="<?php echo $exercicio['gif_url'] ?? ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="video_url" class="form-label">URL do Vídeo</label>
                            <input type="url" class="form-control" id="video_url" name="video_url" 
                                   value="<?php echo $exercicio['video_url'] ?? ''; ?>">
                        </div>

                        <!-- Instruções -->
                        <div class="mb-3">
                            <label for="instrucoes" class="form-label">Instruções</label>
                            <textarea class="form-control" id="instrucoes" name="instrucoes" rows="4" required><?php echo $exercicio['instrucoes'] ?? ''; ?></textarea>
                            <div class="invalid-feedback">
                                Por favor, informe as instruções do exercício.
                            </div>
                        </div>

                        <!-- Dicas de Segurança -->
                        <div class="mb-3">
                            <label for="dicas_seguranca" class="form-label">Dicas de Segurança</label>
                            <textarea class="form-control" id="dicas_seguranca" name="dicas_seguranca" rows="3"><?php echo $exercicio['dicas_seguranca'] ?? ''; ?></textarea>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between">
                            <a href="/GymForge-Academic/views/exercicios/biblioteca.php" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <?php echo $id ? 'Atualizar' : 'Criar'; ?> Exercício
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

// Preview de mídia
document.getElementById('gif_url').addEventListener('change', function() {
    // Adicionar preview do GIF
});

document.getElementById('video_url').addEventListener('change', function() {
    // Adicionar preview do vídeo
});
</script>

<?php require_once '../../includes/footer.php'; ?>

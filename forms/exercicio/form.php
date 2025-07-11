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
        header('Location: /GymForge-Academic/views/exercicios/biblioteca.php');
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
                    
                    <form id="exercicioForm" action="/GymForge-Academic/actions/exercicio/crud.php" 
                          method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="acao" value="<?php echo $id ? 'atualizar' : 'criar'; ?>">
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
                                Por favor, forneça uma descrição para o exercício.
                            </div>
                        </div>

                        <!-- Categoria -->
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Selecione uma categoria</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria; ?>" 
                                            <?php echo (isset($exercicio['categoria']) && $exercicio['categoria'] === $categoria) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($categoria); ?>
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
                                <?php foreach ($gruposMusculares as $grupo): ?>
                                    <option value="<?php echo $grupo; ?>" 
                                            <?php echo (isset($exercicio['grupo_muscular']) && $exercicio['grupo_muscular'] === $grupo) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($grupo); ?>
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
                                <option value="">Selecione um nível</option>
                                <?php foreach ($niveisDificuldade as $nivel): ?>
                                    <option value="<?php echo $nivel; ?>" 
                                            <?php echo (isset($exercicio['nivel_dificuldade']) && $exercicio['nivel_dificuldade'] === $nivel) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($nivel); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecione um nível de dificuldade.
                            </div>
                        </div>

                        <!-- Equipamento -->
                        <div class="mb-3">
                            <label for="equipamento" class="form-label">Equipamento Necessário</label>
                            <input type="text" class="form-control" id="equipamento" name="equipamento" 
                                   value="<?php echo $exercicio['equipamento'] ?? ''; ?>">
                        </div>

                        <!-- Upload de Vídeo -->
                        <div class="mb-3">
                            <label for="video" class="form-label">Vídeo Demonstrativo</label>
                            <input type="file" class="form-control" id="video" name="video" 
                                   accept="video/mp4,video/webm">
                            <?php if (!empty($exercicio['video_url'])): ?>
                                <div class="mt-2">
                                    <video class="img-thumbnail" controls style="max-width: 200px;">
                                        <source src="<?php echo $exercicio['video_url']; ?>" type="video/mp4">
                                        Seu navegador não suporta vídeos HTML5.
                                    </video>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Formatos aceitos: MP4, WebM. Tamanho máximo: 50MB</div>
                        </div>

                        <!-- Upload de Imagem -->
                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem do Exercício</label>
                            <input type="file" class="form-control" id="imagem" name="imagem" 
                                   accept="image/jpeg,image/png,image/gif">
                            <?php if (!empty($exercicio['imagem_url'])): ?>
                                <div class="mt-2">
                                    <img src="<?php echo $exercicio['imagem_url']; ?>" 
                                         class="img-thumbnail" style="max-width: 200px;" 
                                         alt="Imagem atual do exercício">
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Formatos aceitos: JPEG, PNG, GIF. Tamanho máximo: 5MB</div>
                        </div>

                        <!-- Instruções -->
                        <div class="mb-3">
                            <label for="instrucoes" class="form-label">Instruções de Execução</label>
                            <textarea class="form-control" id="instrucoes" name="instrucoes" rows="4"><?php echo $exercicio['instrucoes'] ?? ''; ?></textarea>
                            <div class="form-text">Descreva passo a passo como executar o exercício corretamente.</div>
                        </div>

                        <!-- Dicas de Segurança -->
                        <div class="mb-3">
                            <label for="dicas_seguranca" class="form-label">Dicas de Segurança</label>
                            <textarea class="form-control" id="dicas_seguranca" name="dicas_seguranca" rows="3"><?php echo $exercicio['dicas_seguranca'] ?? ''; ?></textarea>
                            <div class="form-text">Forneça dicas importantes para evitar lesões durante a execução.</div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between">
                            <a href="/GymForge-Academic/views/exercicios/biblioteca.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?php echo $id ? 'Atualizar' : 'Salvar'; ?>
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

// Preview de imagem
document.getElementById('imagem').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('img');
            preview.src = e.target.result;
            preview.className = 'img-thumbnail mt-2';
            preview.style.maxWidth = '200px';
            
            const container = this.parentElement.querySelector('.mt-2');
            if (container) {
                container.innerHTML = '';
                container.appendChild(preview);
            } else {
                const newContainer = document.createElement('div');
                newContainer.className = 'mt-2';
                newContainer.appendChild(preview);
                this.parentElement.appendChild(newContainer);
            }
        }.bind(this);
        reader.readAsDataURL(e.target.files[0]);
    }
});

// Preview de vídeo
document.getElementById('video').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const video = document.createElement('video');
        video.className = 'img-thumbnail mt-2';
        video.controls = true;
        video.style.maxWidth = '200px';
        
        const source = document.createElement('source');
        source.src = URL.createObjectURL(e.target.files[0]);
        source.type = e.target.files[0].type;
        
        video.appendChild(source);
        
        const container = this.parentElement.querySelector('.mt-2');
        if (container) {
            container.innerHTML = '';
            container.appendChild(video);
        } else {
            const newContainer = document.createElement('div');
            newContainer.className = 'mt-2';
            newContainer.appendChild(video);
            this.parentElement.appendChild(newContainer);
        }
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>

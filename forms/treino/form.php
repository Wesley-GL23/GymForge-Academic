<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/training_functions.php';
require_once __DIR__ . '/../../includes/exercise_functions.php';

// Verifica se o usuário está logado
if (!esta_logado()) {
    header('Location: /forms/usuario/login.php');
    exit;
}

// Recupera dados do treino se for edição
$treino = null;
$form_data = $_SESSION['form_data'] ?? null;
unset($_SESSION['form_data']);

if (isset($_GET['id'])) {
    $treino = buscar_treino($_GET['id'], $_SESSION['usuario_id']);
    if (!$treino) {
        $_SESSION['erro'] = "Treino não encontrado";
        header('Location: /views/treinos/');
        exit;
    }
    $form_data = $treino;
}

// Lista todos os exercícios disponíveis
$exercicios = listar_exercicios();

// Gera token CSRF
$csrf_token = gerar_csrf_token();

$titulo = $treino ? "Editar Treino" : "Criar Novo Treino";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container py-4">
    <h1 class="mb-4"><?php echo $titulo; ?></h1>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['erro'];
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>

    <form action="/actions/treino/crud.php" method="POST" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="acao" value="<?php echo $treino ? 'atualizar' : 'criar'; ?>">
        <?php if ($treino): ?>
            <input type="hidden" name="id" value="<?php echo $treino['id']; ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome do Treino *</label>
                <input type="text" class="form-control" id="nome" name="nome" 
                       value="<?php echo $form_data['nome'] ?? ''; ?>" required>
                <div class="invalid-feedback">
                    Por favor, informe um nome para o treino.
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="tipo" class="form-label">Tipo de Treino</label>
                <select class="form-select" id="tipo" name="tipo">
                    <option value="normal" <?php echo ($form_data['tipo'] ?? '') === 'normal' ? 'selected' : ''; ?>>Normal</option>
                    <option value="desafio" <?php echo ($form_data['tipo'] ?? '') === 'desafio' ? 'selected' : ''; ?>>Desafio</option>
                    <option value="evento" <?php echo ($form_data['tipo'] ?? '') === 'evento' ? 'selected' : ''; ?>>Evento</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo $form_data['descricao'] ?? ''; ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nivel_dificuldade" class="form-label">Nível de Dificuldade</label>
                <select class="form-select" id="nivel_dificuldade" name="nivel_dificuldade">
                    <option value="iniciante" <?php echo ($form_data['nivel_dificuldade'] ?? '') === 'iniciante' ? 'selected' : ''; ?>>Iniciante</option>
                    <option value="intermediario" <?php echo ($form_data['nivel_dificuldade'] ?? '') === 'intermediario' ? 'selected' : ''; ?>>Intermediário</option>
                    <option value="avancado" <?php echo ($form_data['nivel_dificuldade'] ?? '') === 'avancado' ? 'selected' : ''; ?>>Avançado</option>
                </select>
            </div>

            <?php if ($treino): ?>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="ativo" <?php echo ($form_data['status'] ?? '') === 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                    <option value="concluido" <?php echo ($form_data['status'] ?? '') === 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                    <option value="arquivado" <?php echo ($form_data['status'] ?? '') === 'arquivado' ? 'selected' : ''; ?>>Arquivado</option>
                </select>
            </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="data_inicio" class="form-label">Data de Início *</label>
                <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                       value="<?php echo $form_data['data_inicio'] ?? ''; ?>" required>
                <div class="invalid-feedback">
                    Por favor, informe a data de início.
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="data_fim" class="form-label">Data de Término</label>
                <input type="date" class="form-control" id="data_fim" name="data_fim" 
                       value="<?php echo $form_data['data_fim'] ?? ''; ?>">
            </div>
        </div>

        <h3 class="mt-4 mb-3">Exercícios</h3>
        <div id="exercicios-container">
            <?php if ($treino && !empty($treino['exercicios'])): ?>
                <?php foreach ($treino['exercicios'] as $ordem => $ex): ?>
                    <div class="exercicio-item card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Exercício *</label>
                                    <select class="form-select exercicio-select" name="exercicios[<?php echo $ordem; ?>][id]" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($exercicios as $exercicio): ?>
                                            <option value="<?php echo $exercicio['id']; ?>" 
                                                    <?php echo $ex['exercicio_id'] == $exercicio['id'] ? 'selected' : ''; ?>>
                                                <?php echo $exercicio['nome']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Séries *</label>
                                    <input type="number" class="form-control" name="exercicios[<?php echo $ordem; ?>][series]" 
                                           value="<?php echo $ex['series']; ?>" required min="1">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Repetições *</label>
                                    <input type="number" class="form-control" name="exercicios[<?php echo $ordem; ?>][repeticoes]" 
                                           value="<?php echo $ex['repeticoes']; ?>" required min="1">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Peso (kg)</label>
                                    <input type="number" class="form-control" name="exercicios[<?php echo $ordem; ?>][peso]" 
                                           value="<?php echo $ex['peso']; ?>" step="0.5">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Descanso (seg)</label>
                                    <input type="number" class="form-control" name="exercicios[<?php echo $ordem; ?>][tempo_descanso]" 
                                           value="<?php echo $ex['tempo_descanso']; ?>" step="5">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" name="exercicios[<?php echo $ordem; ?>][observacoes]" rows="2"><?php echo $ex['observacoes']; ?></textarea>
                            </div>

                            <button type="button" class="btn btn-danger btn-sm remover-exercicio">Remover Exercício</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="button" class="btn btn-secondary mb-4" id="adicionar-exercicio">+ Adicionar Exercício</button>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="/views/treinos/" class="btn btn-outline-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar Treino</button>
        </div>
    </form>
</div>

<template id="exercicio-template">
    <div class="exercicio-item card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Exercício *</label>
                    <select class="form-select exercicio-select" name="exercicios[INDEX][id]" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($exercicios as $exercicio): ?>
                            <option value="<?php echo $exercicio['id']; ?>"><?php echo $exercicio['nome']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Séries *</label>
                    <input type="number" class="form-control" name="exercicios[INDEX][series]" required min="1">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Repetições *</label>
                    <input type="number" class="form-control" name="exercicios[INDEX][repeticoes]" required min="1">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Peso (kg)</label>
                    <input type="number" class="form-control" name="exercicios[INDEX][peso]" step="0.5">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Descanso (seg)</label>
                    <input type="number" class="form-control" name="exercicios[INDEX][tempo_descanso]" step="5">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" name="exercicios[INDEX][observacoes]" rows="2"></textarea>
            </div>

            <button type="button" class="btn btn-danger btn-sm remover-exercicio">Remover Exercício</button>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('exercicios-container');
    const template = document.getElementById('exercicio-template');
    const btnAdicionar = document.getElementById('adicionar-exercicio');
    let exercicioIndex = container.children.length;

    // Função para atualizar os índices dos exercícios
    function atualizarIndices() {
        const exercicios = container.getElementsByClassName('exercicio-item');
        Array.from(exercicios).forEach((exercicio, index) => {
            exercicio.querySelectorAll('[name*="[INDEX]"]').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
            });
        });
    }

    // Adicionar novo exercício
    btnAdicionar.addEventListener('click', () => {
        const clone = template.content.cloneNode(true);
        const novoExercicio = clone.querySelector('.exercicio-item');
        
        novoExercicio.querySelectorAll('[name*="[INDEX]"]').forEach(input => {
            input.name = input.name.replace('INDEX', exercicioIndex);
        });
        
        container.appendChild(novoExercicio);
        exercicioIndex++;
    });

    // Remover exercício
    container.addEventListener('click', (e) => {
        if (e.target.classList.contains('remover-exercicio')) {
            e.target.closest('.exercicio-item').remove();
            atualizarIndices();
            exercicioIndex--;
        }
    });

    // Validação do formulário
    const form = document.querySelector('form');
    form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Adiciona um exercício se não houver nenhum
    if (container.children.length === 0) {
        btnAdicionar.click();
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
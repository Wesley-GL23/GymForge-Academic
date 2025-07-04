<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/training_functions.php';

// Verifica se o usuário está logado
if (!esta_logado()) {
    header('Location: /forms/usuario/login.php');
    exit;
}

// Verifica se foi fornecido um ID
if (!isset($_GET['id'])) {
    $_SESSION['erro'] = "ID do treino não fornecido";
    header('Location: /views/treinos/');
    exit;
}

// Busca o treino
$treino = buscar_treino($_GET['id'], $_SESSION['usuario_id']);
if (!$treino) {
    $_SESSION['erro'] = "Treino não encontrado";
    header('Location: /views/treinos/');
    exit;
}

$titulo = "Visualizar Treino";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo htmlspecialchars($treino['nome']); ?></h1>
        
        <div class="btn-group">
            <a href="/forms/treino/form.php?id=<?php echo $treino['id']; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Treino
            </a>
            <button type="button" class="btn btn-danger" onclick="confirmarExclusao(<?php echo $treino['id']; ?>)">
                <i class="fas fa-trash"></i> Excluir
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['sucesso'];
            unset($_SESSION['sucesso']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['erro'];
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Detalhes do Treino</h5>
                    
                    <?php if (!empty($treino['descricao'])): ?>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($treino['descricao'])); ?></p>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tipo:</strong> <?php echo ucfirst($treino['tipo']); ?></p>
                            <p><strong>Nível:</strong> <?php echo ucfirst($treino['nivel_dificuldade']); ?></p>
                            <p><strong>Status:</strong> <?php echo ucfirst($treino['status']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data de Início:</strong> <?php echo date('d/m/Y', strtotime($treino['data_inicio'])); ?></p>
                            <?php if (!empty($treino['data_fim'])): ?>
                                <p><strong>Data de Término:</strong> <?php echo date('d/m/Y', strtotime($treino['data_fim'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Resumo</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-dumbbell"></i> <?php echo count($treino['exercicios']); ?> exercícios</li>
                        <li><i class="fas fa-clock"></i> Tempo estimado: 
                            <?php 
                            $tempo_total = 0;
                            foreach ($treino['exercicios'] as $ex) {
                                // Tempo por série (30s em média) + tempo de descanso
                                $tempo_total += ($ex['series'] * 30) + ($ex['series'] * ($ex['tempo_descanso'] ?? 60));
                            }
                            echo floor($tempo_total / 60) . ' minutos';
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mb-4">Exercícios do Treino</h2>
    
    <?php if (empty($treino['exercicios'])): ?>
        <div class="alert alert-info">
            Este treino ainda não possui exercícios cadastrados.
            <a href="/forms/treino/form.php?id=<?php echo $treino['id']; ?>" class="alert-link">
                Adicionar exercícios
            </a>
        </div>
    <?php else: ?>
        <div class="accordion" id="exerciciosAccordion">
            <?php foreach ($treino['exercicios'] as $i => $ex): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?php echo $i === 0 ? '' : 'collapsed'; ?>" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#exercicio<?php echo $i; ?>">
                            <?php echo ($i + 1) . '. ' . htmlspecialchars($ex['nome_exercicio']); ?>
                        </button>
                    </h2>
                    <div id="exercicio<?php echo $i; ?>" 
                         class="accordion-collapse collapse <?php echo $i === 0 ? 'show' : ''; ?>" 
                         data-bs-parent="#exerciciosAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Grupo Muscular:</strong> <?php echo ucfirst($ex['grupo_muscular']); ?></p>
                                    <p><strong>Séries:</strong> <?php echo $ex['series']; ?></p>
                                    <p><strong>Repetições:</strong> <?php echo $ex['repeticoes']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <?php if (!empty($ex['peso'])): ?>
                                        <p><strong>Peso:</strong> <?php echo $ex['peso']; ?> kg</p>
                                    <?php endif; ?>
                                    <?php if (!empty($ex['tempo_descanso'])): ?>
                                        <p><strong>Descanso:</strong> <?php echo $ex['tempo_descanso']; ?> segundos</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!empty($ex['observacoes'])): ?>
                                <div class="mt-3">
                                    <strong>Observações:</strong>
                                    <p><?php echo nl2br(htmlspecialchars($ex['observacoes'])); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if ($treino['status'] === 'ativo'): ?>
                                <button type="button" 
                                        class="btn btn-success mt-3"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalProgresso"
                                        data-exercicio-id="<?php echo $ex['exercicio_id']; ?>"
                                        data-exercicio-nome="<?php echo htmlspecialchars($ex['nome_exercicio']); ?>"
                                        data-series="<?php echo $ex['series']; ?>"
                                        data-peso="<?php echo $ex['peso']; ?>">
                                    <i class="fas fa-check"></i> Registrar Progresso
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de Progresso -->
<div class="modal fade" id="modalProgresso" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/actions/treino/crud.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo gerar_csrf_token(); ?>">
                <input type="hidden" name="acao" value="registrar_progresso">
                <input type="hidden" name="treino_id" value="<?php echo $treino['id']; ?>">
                <input type="hidden" name="exercicio_id" id="exercicio_id">

                <div class="modal-header">
                    <h5 class="modal-title">Registrar Progresso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <h6 id="exercicio_nome" class="mb-3"></h6>

                    <div class="mb-3">
                        <label for="series_completadas" class="form-label">Séries Completadas *</label>
                        <input type="number" class="form-control" id="series_completadas" name="series_completadas" 
                               required min="1">
                    </div>

                    <div class="mb-3">
                        <label for="peso_utilizado" class="form-label">Peso Utilizado (kg)</label>
                        <input type="number" class="form-control" id="peso_utilizado" name="peso_utilizado" 
                               step="0.5">
                    </div>

                    <div class="mb-3">
                        <label for="dificuldade_percebida" class="form-label">Dificuldade Percebida (1-10)</label>
                        <input type="range" class="form-range" id="dificuldade_percebida" name="dificuldade_percebida" 
                               min="1" max="10" value="5">
                        <div class="d-flex justify-content-between">
                            <span>Fácil</span>
                            <span id="dificuldade_valor">5</span>
                            <span>Difícil</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Progresso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Confirmar exclusão
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este treino?')) {
        window.location.href = `/actions/treino/crud.php?acao=deletar&id=${id}`;
    }
}

// Configurar modal de progresso
document.addEventListener('DOMContentLoaded', function() {
    const modalProgresso = document.getElementById('modalProgresso');
    if (modalProgresso) {
        modalProgresso.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const exercicioId = button.getAttribute('data-exercicio-id');
            const exercicioNome = button.getAttribute('data-exercicio-nome');
            const series = button.getAttribute('data-series');
            const peso = button.getAttribute('data-peso');

            this.querySelector('#exercicio_id').value = exercicioId;
            this.querySelector('#exercicio_nome').textContent = exercicioNome;
            this.querySelector('#series_completadas').value = series;
            this.querySelector('#peso_utilizado').value = peso || '';

            // Atualizar valor da dificuldade
            const dificuldadeRange = this.querySelector('#dificuldade_percebida');
            const dificuldadeValor = this.querySelector('#dificuldade_valor');
            
            dificuldadeRange.addEventListener('input', function() {
                dificuldadeValor.textContent = this.value;
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
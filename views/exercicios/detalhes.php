<?php
require_once __DIR__ . '/../../includes/exercise_functions.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID do exercício não fornecido']);
    exit;
}

$exercicio = buscar_exercicio($_GET['id']);
if (!$exercicio) {
    http_response_code(404);
    echo json_encode(['erro' => 'Exercício não encontrado']);
    exit;
}
?>

<div class="exercise-details">
    <!-- Cabeçalho -->
    <div class="exercise-header mb-4">
        <h3 class="mb-3"><?php echo htmlspecialchars($exercicio['nome']); ?></h3>
        <div class="exercise-tags mb-3">
            <span class="badge bg-primary"><?php echo ucfirst($exercicio['categoria']); ?></span>
            <span class="badge bg-secondary"><?php echo ucfirst($exercicio['grupo_muscular']); ?></span>
            <span class="badge <?php echo getNivelClass($exercicio['nivel_dificuldade']); ?>">
                <?php echo ucfirst($exercicio['nivel_dificuldade']); ?>
            </span>
        </div>
    </div>

    <!-- Mídia -->
    <div class="exercise-media mb-4">
        <?php if (!empty($exercicio['video_url'])): ?>
            <div class="video-container mb-3">
                <video controls class="w-100">
                    <source src="<?php echo htmlspecialchars($exercicio['video_url']); ?>" type="video/mp4">
                    Seu navegador não suporta vídeos HTML5.
                </video>
            </div>
        <?php elseif (!empty($exercicio['imagem_url'])): ?>
            <div class="image-container mb-3">
                <img src="<?php echo htmlspecialchars($exercicio['imagem_url']); ?>" 
                     class="img-fluid" 
                     alt="<?php echo htmlspecialchars($exercicio['nome']); ?>">
            </div>
        <?php endif; ?>
    </div>

    <!-- Descrição -->
    <div class="exercise-description mb-4">
        <h4 class="mb-3">Descrição</h4>
        <p><?php echo nl2br(htmlspecialchars($exercicio['descricao'])); ?></p>
    </div>

    <!-- Equipamento -->
    <?php if (!empty($exercicio['equipamento'])): ?>
    <div class="exercise-equipment mb-4">
        <h4 class="mb-3">Equipamento Necessário</h4>
        <p><i class="fas fa-dumbbell me-2"></i><?php echo htmlspecialchars($exercicio['equipamento']); ?></p>
    </div>
    <?php endif; ?>

    <!-- Instruções -->
    <?php if (!empty($exercicio['instrucoes'])): ?>
    <div class="exercise-instructions mb-4">
        <h4 class="mb-3">Instruções de Execução</h4>
        <div class="instructions-list">
            <?php 
            $instrucoes = explode("\n", $exercicio['instrucoes']);
            foreach ($instrucoes as $i => $instrucao): 
                if (trim($instrucao)): 
            ?>
                <div class="instruction-step mb-2">
                    <span class="step-number"><?php echo $i + 1; ?></span>
                    <span class="step-text"><?php echo htmlspecialchars(trim($instrucao)); ?></span>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Dicas de Segurança -->
    <?php if (!empty($exercicio['dicas_seguranca'])): ?>
    <div class="exercise-safety mb-4">
        <h4 class="mb-3">Dicas de Segurança</h4>
        <div class="safety-tips">
            <?php 
            $dicas = explode("\n", $exercicio['dicas_seguranca']);
            foreach ($dicas as $dica): 
                if (trim($dica)): 
            ?>
                <div class="safety-tip mb-2">
                    <i class="fas fa-shield-alt text-warning me-2"></i>
                    <span><?php echo htmlspecialchars(trim($dica)); ?></span>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.exercise-details {
    max-width: 800px;
    margin: 0 auto;
}

.video-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
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

.instruction-step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.step-number {
    background-color: var(--bs-primary);
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.step-text {
    flex: 1;
}

.safety-tip {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.safety-tip i {
    flex-shrink: 0;
    margin-top: 4px;
}

.exercise-tags .badge {
    margin-right: 0.5rem;
}
</style> 
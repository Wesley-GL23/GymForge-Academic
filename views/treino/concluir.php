<?php
require_once '../../includes/header.php';
require_once '../../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../forms/usuario/login.php');
    exit;
}

$treino_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$treino_id) {
    header('Location: meus_treinos.php');
    exit;
}

// Busca informações do treino
$stmt = $conn->prepare("
    SELECT t.*, u.nome as instrutor_nome 
    FROM treinos t 
    JOIN usuarios u ON t.instrutor_id = u.id 
    WHERE t.id = ? AND t.aluno_id = ?
");
$stmt->bind_param("ii", $treino_id, $_SESSION['usuario_id']);
$stmt->execute();
$treino = $stmt->get_result()->fetch_assoc();

if (!$treino) {
    header('Location: meus_treinos.php');
    exit;
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-card">
                <div class="text-center mb-4">
                    <h3 class="text-light">Concluir Treino</h3>
                    <p class="text-light opacity-75">
                        <?php echo htmlspecialchars($treino['nome']); ?> - 
                        Instrutor: <?php echo htmlspecialchars($treino['instrutor_nome']); ?>
                    </p>
                </div>

                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['flash_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                <?php endif; ?>

                <form action="../../actions/treino/concluir.php" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="treino_id" value="<?php echo $treino_id; ?>">
                    
                    <div class="mb-4">
                        <label for="data_conclusao" class="form-label">Data de Conclusão</label>
                        <input type="datetime-local" class="form-control" id="data_conclusao" name="data_conclusao" 
                               value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                        <div class="invalid-feedback text-light">
                            Por favor, selecione a data e hora de conclusão.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="duracao_minutos" class="form-label">Duração (minutos)</label>
                        <input type="number" class="form-control" id="duracao_minutos" name="duracao_minutos" 
                               min="1" max="300" required>
                        <div class="invalid-feedback text-light">
                            Por favor, insira a duração do treino (entre 1 e 300 minutos).
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nível de Dificuldade</label>
                        <div class="d-flex gap-3">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nivel_dificuldade" 
                                           id="dificuldade<?php echo $i; ?>" value="<?php echo $i; ?>" 
                                           <?php echo $i === 3 ? 'checked' : ''; ?> required>
                                    <label class="form-check-label text-light" for="dificuldade<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </label>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <div class="form-text text-light opacity-75">
                            1 = Muito Fácil, 5 = Muito Difícil
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3" 
                                  placeholder="Como você se sentiu? Alguma dificuldade específica?"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="meus_treinos.php" class="btn btn-outline-light">Voltar</a>
                        <button type="submit" class="btn btn-primary">
                            Marcar como Concluído
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?> 
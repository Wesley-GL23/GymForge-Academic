<?php
require_once '../../includes/header.php';
requireLogin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMessage('Exercício não encontrado', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
    exit();
}

$conn = conectarBD();
$id = (int)$_GET['id'];

// Buscar exercício
$sql = "SELECT * FROM exercicios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$exercicio = $stmt->get_result()->fetch_assoc();

if (!$exercicio) {
    setMessage('Exercício não encontrado', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
    exit();
}

fecharConexao($conn);
?>

<div class="container">
    <nav aria-label="breadcrumb" class="mt-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/views/exercicio/listar.php">Exercícios</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($exercicio['nome']); ?></li>
        </ol>
    </nav>

    <div class="card">
        <div class="row g-0">
            <?php if (!empty($exercicio['gif_url'])): ?>
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($exercicio['gif_url']); ?>" 
                     class="img-fluid rounded-start" 
                     alt="<?php echo htmlspecialchars($exercicio['nome']); ?>">
            </div>
            <?php endif; ?>
            <div class="col-md-<?php echo !empty($exercicio['gif_url']) ? '6' : '12'; ?>">
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($exercicio['nome']); ?></h1>
                    <span class="badge bg-secondary mb-3"><?php echo htmlspecialchars($exercicio['categoria']); ?></span>
                    
                    <h5 class="mt-4">Descrição</h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($exercicio['descricao'])); ?></p>
                    
                    <?php if (!empty($exercicio['instrucoes'])): ?>
                    <h5 class="mt-4">Instruções</h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($exercicio['instrucoes'])); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($exercicio['dicas'])): ?>
                    <h5 class="mt-4">Dicas</h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($exercicio['dicas'])); ?></p>
                    <?php endif; ?>

                    <?php if (eAdmin()): ?>
                    <div class="mt-4">
                        <a href="<?php echo BASE_URL; ?>/views/exercicio/editar.php?id=<?php echo $exercicio['id']; ?>" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger" 
                                onclick="confirmarExclusao(<?php echo $exercicio['id']; ?>)">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este exercício?')) {
        window.location.href = '<?php echo BASE_URL; ?>/actions/exercicio/excluir.php?id=' + id;
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?> 
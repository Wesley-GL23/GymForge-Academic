<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';
require_once '../../config/conexao.php';

// Verifica se é admin
requireAdmin();

// Verifica se ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMessage('Exercício não encontrado', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
    exit();
}

$conn = conectarBD();
$id = (int)$_GET['id'];

// Busca o exercício
$stmt = $conn->prepare("SELECT * FROM exercicios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$exercicio = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Se exercício não existe
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
            <li class="breadcrumb-item active" aria-current="page">Editar Exercício</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h1 class="card-title"><i class="fas fa-edit"></i> Editar Exercício</h1>
        </div>
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/actions/exercicio/editar.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $exercicio['id']; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome *</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?php echo htmlspecialchars($exercicio['nome']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoria *</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Selecione...</option>
                                <option value="Superiores" <?php echo $exercicio['categoria'] == 'Superiores' ? 'selected' : ''; ?>>Superiores</option>
                                <option value="Inferiores" <?php echo $exercicio['categoria'] == 'Inferiores' ? 'selected' : ''; ?>>Inferiores</option>
                                <option value="Abdômen" <?php echo $exercicio['categoria'] == 'Abdômen' ? 'selected' : ''; ?>>Abdômen</option>
                                <option value="Costas" <?php echo $exercicio['categoria'] == 'Costas' ? 'selected' : ''; ?>>Costas</option>
                                <option value="Funcionais" <?php echo $exercicio['categoria'] == 'Funcionais' ? 'selected' : ''; ?>>Funcionais</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição *</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?php echo htmlspecialchars($exercicio['descricao']); ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="instrucoes" class="form-label">Instruções</label>
                            <textarea class="form-control" id="instrucoes" name="instrucoes" rows="3"><?php echo htmlspecialchars($exercicio['instrucoes']); ?></textarea>
                            <div class="form-text">Passo a passo de como realizar o exercício corretamente.</div>
                        </div>

                        <div class="mb-3">
                            <label for="dicas" class="form-label">Dicas</label>
                            <textarea class="form-control" id="dicas" name="dicas" rows="3"><?php echo htmlspecialchars($exercicio['dicas']); ?></textarea>
                            <div class="form-text">Dicas para melhor execução e aproveitamento do exercício.</div>
                        </div>

                        <div class="mb-3">
                            <label for="gif" class="form-label">GIF Demonstrativo</label>
                            <?php if (!empty($exercicio['gif_url'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo htmlspecialchars($exercicio['gif_url']); ?>" 
                                     alt="GIF atual" class="img-thumbnail" style="max-height: 100px;">
                                <div class="form-text">GIF atual</div>
                            </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="gif" name="gif" accept=".gif">
                            <div class="form-text">Upload de novo GIF para substituir o atual (opcional).</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/exercicio/listar.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 
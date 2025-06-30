<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';
require_once '../../config/conexao.php';

// Verifica se é admin
requireAdmin();

// Verifica se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['msg'] = 'ID do treino não fornecido';
    $_SESSION['msg_type'] = 'danger';
    header('Location: listar.php');
    exit;
}

$treino_id = (int)$_GET['id'];

// Conexão com o banco
$conn = conectarBD();

// Busca dados do treino
$sql = "SELECT t.*, u.nome as nome_usuario 
        FROM treinos t 
        LEFT JOIN usuarios u ON t.usuario_id = u.id 
        WHERE t.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $treino_id);
$stmt->execute();
$treino = $stmt->get_result()->fetch_assoc();

if (!$treino) {
    $_SESSION['msg'] = 'Treino não encontrado';
    $_SESSION['msg_type'] = 'danger';
    header('Location: listar.php');
    exit;
}

// Busca exercícios do treino
$sql = "SELECT te.*, e.nome as nome_exercicio, e.categoria 
        FROM treino_exercicios te 
        LEFT JOIN exercicios e ON te.exercicio_id = e.id 
        WHERE te.treino_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $treino_id);
$stmt->execute();
$exercicios_treino = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Organiza exercícios do treino em um array para fácil acesso
$exercicios_selecionados = [];
foreach ($exercicios_treino as $exercicio) {
    $exercicios_selecionados[$exercicio['exercicio_id']] = $exercicio;
}

// Busca todos os exercícios disponíveis
$sql = "SELECT id, nome, categoria FROM exercicios ORDER BY categoria, nome";
$result = $conn->query($sql);
$exercicios_por_categoria = [];
while ($exercicio = $result->fetch_assoc()) {
    $exercicios_por_categoria[$exercicio['categoria']][] = $exercicio;
}

// Busca usuários
$usuarios = $conn->query("SELECT id, nome FROM usuarios WHERE nivel = 'cliente' ORDER BY nome")->fetch_all(MYSQLI_ASSOC);

fecharConexao($conn);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard/admin.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../usuario/listar.php">
                            <i class="fas fa-users"></i> Usuários
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../exercicio/listar.php">
                            <i class="fas fa-dumbbell"></i> Exercícios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="listar.php">
                            <i class="fas fa-clipboard-list"></i> Treinos
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Editar Treino</h1>
            </div>

            <?php
            if (isset($_SESSION['msg'])) {
                echo '<div class="alert alert-' . $_SESSION['msg_type'] . '">' . $_SESSION['msg'] . '</div>';
                unset($_SESSION['msg']);
                unset($_SESSION['msg_type']);
            }
            ?>

            <div class="card">
                <div class="card-body">
                    <form action="../../actions/treino/editar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="id" value="<?php echo $treino_id; ?>">

                        <!-- Informações Básicas -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="usuario_id" class="form-label">Usuário</label>
                                <select class="form-select" id="usuario_id" name="usuario_id" required>
                                    <option value="">Selecione o usuário...</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?php echo $usuario['id']; ?>" 
                                            <?php echo $usuario['id'] == $treino['usuario_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($usuario['nome']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor, selecione um usuário.
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="data" class="form-label">Data do Treino</label>
                                <input type="date" class="form-control" id="data" name="data" 
                                       value="<?php echo $treino['data']; ?>" required>
                                <div class="invalid-feedback">
                                    Por favor, selecione a data do treino.
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nome" class="form-label">Nome do Treino</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?php echo htmlspecialchars($treino['nome']); ?>" required>
                            <div class="invalid-feedback">
                                Por favor, insira o nome do treino.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($treino['descricao']); ?></textarea>
                        </div>

                        <!-- Seleção de Exercícios -->
                        <h4 class="mb-3">Exercícios do Treino</h4>
                        <div id="exercicios-container">
                            <?php foreach ($exercicios_por_categoria as $categoria => $exercicios): ?>
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($categoria); ?></h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($exercicios as $exercicio): ?>
                                    <div class="exercicio-item mb-3 border-bottom pb-3">
                                        <div class="form-check">
                                            <input class="form-check-input exercicio-checkbox" type="checkbox" 
                                                   id="exercicio_<?php echo $exercicio['id']; ?>"
                                                   data-exercicio-id="<?php echo $exercicio['id']; ?>"
                                                   <?php echo isset($exercicios_selecionados[$exercicio['id']]) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="exercicio_<?php echo $exercicio['id']; ?>">
                                                <?php echo htmlspecialchars($exercicio['nome']); ?>
                                            </label>
                                        </div>
                                        <div class="exercicio-detalhes mt-2" style="display: <?php echo isset($exercicios_selecionados[$exercicio['id']]) ? 'block' : 'none'; ?>;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="hidden" name="exercicios[]" value="<?php echo $exercicio['id']; ?>" 
                                                           <?php echo isset($exercicios_selecionados[$exercicio['id']]) ? '' : 'disabled'; ?>>
                                                    <label class="form-label">Séries</label>
                                                    <input type="number" class="form-control" name="series[]" 
                                                           min="1" value="<?php echo isset($exercicios_selecionados[$exercicio['id']]) ? $exercicios_selecionados[$exercicio['id']]['series'] : '3'; ?>" 
                                                           <?php echo isset($exercicios_selecionados[$exercicio['id']]) ? '' : 'disabled'; ?> required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Repetições</label>
                                                    <input type="number" class="form-control" name="repeticoes[]" 
                                                           min="1" value="<?php echo isset($exercicios_selecionados[$exercicio['id']]) ? $exercicios_selecionados[$exercicio['id']]['repeticoes'] : '12'; ?>" 
                                                           <?php echo isset($exercicios_selecionados[$exercicio['id']]) ? '' : 'disabled'; ?> required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Observações</label>
                                                    <input type="text" class="form-control" name="observacoes[]" 
                                                           value="<?php echo isset($exercicios_selecionados[$exercicio['id']]) ? htmlspecialchars($exercicios_selecionados[$exercicio['id']]['observacoes']) : ''; ?>" 
                                                           <?php echo isset($exercicios_selecionados[$exercicio['id']]) ? '' : 'disabled'; ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Validação do formulário
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()

// Gerenciamento dos exercícios
document.querySelectorAll('.exercicio-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const detalhes = this.closest('.exercicio-item').querySelector('.exercicio-detalhes');
        const inputs = detalhes.querySelectorAll('input');
        
        if (this.checked) {
            detalhes.style.display = 'block';
            inputs.forEach(input => input.disabled = false);
        } else {
            detalhes.style.display = 'none';
            inputs.forEach(input => input.disabled = true);
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?> 
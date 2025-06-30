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

// Busca informações do treino
$conn = conectarBD();

// Busca dados do treino e usuário
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

// Verifica se o usuário tem permissão para ver o treino
if ($_SESSION['tipo_usuario'] === 'cliente' && $treino['aluno_id'] != $_SESSION['usuario_id']) {
    header('Location: meus_treinos.php');
    exit;
}

// Busca exercícios do treino
$sql = "SELECT te.*, e.nome as nome_exercicio, e.categoria, e.gif_url 
        FROM treino_exercicios te 
        LEFT JOIN exercicios e ON te.exercicio_id = e.id 
        WHERE te.treino_id = ?
        ORDER BY e.categoria, e.nome";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $treino_id);
$stmt->execute();
$exercicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Agrupa exercícios por categoria
$exercicios_por_categoria = [];
foreach ($exercicios as $exercicio) {
    $exercicios_por_categoria[$exercicio['categoria']][] = $exercicio;
}

// Se for cliente, busca o histórico de conclusões deste treino
$historico_conclusoes = null;
if ($_SESSION['tipo_usuario'] === 'cliente') {
    $stmt = $conn->prepare("
        SELECT * FROM treinos_concluidos 
        WHERE treino_id = ? AND usuario_id = ?
        ORDER BY data_conclusao DESC
        LIMIT 5
    ");
    $stmt->bind_param("ii", $treino_id, $_SESSION['usuario_id']);
    $stmt->execute();
    $historico_conclusoes = $stmt->get_result();
}

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
                <h1 class="h2">Detalhes do Treino</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <?php if ($_SESSION['tipo_usuario'] === 'cliente'): ?>
                        <a href="concluir.php?id=<?php echo $treino_id; ?>" class="btn btn-primary">
                            Marcar como Concluído
                        </a>
                    <?php elseif ($_SESSION['tipo_usuario'] === 'instrutor' || $_SESSION['tipo_usuario'] === 'admin'): ?>
                        <div class="btn-group">
                            <a href="editar.php?id=<?php echo $treino_id; ?>" class="btn btn-outline-light">
                                Editar
                            </a>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                                Excluir
                            </button>
                        </div>
                    <?php endif; ?>
                    <a href="listar.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <?php
            if (isset($_SESSION['msg'])) {
                echo '<div class="alert alert-' . $_SESSION['msg_type'] . '">' . $_SESSION['msg'] . '</div>';
                unset($_SESSION['msg']);
                unset($_SESSION['msg_type']);
            }
            ?>

            <!-- Informações do Treino -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Informações Gerais</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nome do Treino:</strong> <?php echo htmlspecialchars($treino['nome']); ?></p>
                            <p><strong>Usuário:</strong> <?php echo htmlspecialchars($treino['nome_usuario']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($treino['data'])); ?></p>
                            <p><strong>Total de Exercícios:</strong> <?php echo count($exercicios); ?></p>
                        </div>
                    </div>
                    <?php if (!empty($treino['descricao'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Descrição:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($treino['descricao'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Lista de Exercícios -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Exercícios do Treino</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($exercicios_por_categoria)): ?>
                        <?php foreach ($exercicios_por_categoria as $categoria => $exercicios): ?>
                        <div class="categoria-exercicios mb-4">
                            <h5 class="bg-light p-2 rounded"><?php echo htmlspecialchars($categoria); ?></h5>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                <?php foreach ($exercicios as $exercicio): ?>
                                <div class="col">
                                    <div class="card h-100">
                                        <?php if (!empty($exercicio['gif_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($exercicio['gif_url']); ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($exercicio['nome_exercicio']); ?>">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($exercicio['nome_exercicio']); ?></h5>
                                            <ul class="list-unstyled">
                                                <li><strong>Séries:</strong> <?php echo $exercicio['series']; ?></li>
                                                <li><strong>Repetições:</strong> <?php echo $exercicio['repeticoes']; ?></li>
                                                <?php if (!empty($exercicio['observacoes'])): ?>
                                                <li class="mt-2">
                                                    <strong>Observações:</strong><br>
                                                    <?php echo nl2br(htmlspecialchars($exercicio['observacoes'])); ?>
                                                </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">Nenhum exercício cadastrado para este treino.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php if ($_SESSION['tipo_usuario'] === 'instrutor' || $_SESSION['tipo_usuario'] === 'admin'): ?>
    <!-- Modal de Exclusão -->
    <div class="modal fade" id="modalExcluir" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header border-light">
                    <h5 class="modal-title text-light">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-light">
                    Tem certeza que deseja excluir este treino?
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <a href="../../actions/treino/excluir.php?id=<?php echo $treino_id; ?>" class="btn btn-danger">
                        Excluir
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($_SESSION['tipo_usuario'] === 'cliente' && $historico_conclusoes): ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <div class="form-card">
                    <h4 class="text-light mb-4">Histórico de Conclusões</h4>
                    <?php if ($historico_conclusoes->num_rows === 0): ?>
                        <p class="text-light">Nenhuma conclusão registrada ainda.</p>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php while ($conclusao = $historico_conclusoes->fetch_assoc()): ?>
                                <div class="border-bottom border-light pb-3">
                                    <p class="text-light mb-1">
                                        <?php echo date('d/m/Y H:i', strtotime($conclusao['data_conclusao'])); ?>
                                    </p>
                                    <p class="text-light mb-1">
                                        <i class="bi bi-clock"></i> <?php echo $conclusao['duracao_minutos']; ?> minutos
                                    </p>
                                    <p class="text-light mb-1">
                                        <i class="bi bi-lightning"></i> Dificuldade: 
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?php echo $i <= $conclusao['nivel_dificuldade'] ? '-fill' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </p>
                                    <?php if ($conclusao['observacoes']): ?>
                                        <p class="text-light opacity-75 mb-0">
                                            <?php echo nl2br(htmlspecialchars($conclusao['observacoes'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?> 
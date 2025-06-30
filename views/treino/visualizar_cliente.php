<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';
require_once '../../config/conexao.php';

// Verifica se é cliente
requireCliente();

// Verifica se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['msg'] = 'ID do treino não fornecido';
    $_SESSION['msg_type'] = 'danger';
    header('Location: meus_treinos.php');
    exit;
}

$treino_id = (int)$_GET['id'];

// Busca informações do treino
$conn = conectarBD();

// Busca dados do treino e verifica se pertence ao usuário
$sql = "SELECT t.* FROM treinos t WHERE t.id = ? AND t.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $treino_id, $_SESSION['usuario_id']);
$stmt->execute();
$treino = $stmt->get_result()->fetch_assoc();

if (!$treino) {
    $_SESSION['msg'] = 'Treino não encontrado ou sem permissão para acessar';
    $_SESSION['msg_type'] = 'danger';
    header('Location: meus_treinos.php');
    exit;
}

// Busca exercícios do treino
$sql = "SELECT te.*, e.nome as nome_exercicio, e.categoria, e.gif_url, e.descricao as descricao_exercicio 
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

fecharConexao($conn);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard/cliente.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="meus_treinos.php">
                            <i class="fas fa-dumbbell"></i> Meus Treinos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../perfil/editar.php">
                            <i class="fas fa-user"></i> Meu Perfil
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
                    <a href="meus_treinos.php" class="btn btn-secondary">
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
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?php echo htmlspecialchars($treino['nome']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-calendar"></i> Data:</strong> 
                               <?php echo date('d/m/Y', strtotime($treino['data'])); ?></p>
                            <p><strong><i class="fas fa-dumbbell"></i> Total de Exercícios:</strong> 
                               <?php echo count($exercicios); ?></p>
                        </div>
                    </div>
                    <?php if (!empty($treino['descricao'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong><i class="fas fa-info-circle"></i> Descrição:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($treino['descricao'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Lista de Exercícios -->
            <div class="accordion" id="exerciciosAccordion">
                <?php 
                $categoria_index = 0;
                foreach ($exercicios_por_categoria as $categoria => $exercicios): 
                    $categoria_id = "categoria_" . $categoria_index;
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading_<?php echo $categoria_id; ?>">
                        <button class="accordion-button <?php echo $categoria_index > 0 ? 'collapsed' : ''; ?>" 
                                type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapse_<?php echo $categoria_id; ?>" 
                                aria-expanded="<?php echo $categoria_index === 0 ? 'true' : 'false'; ?>" 
                                aria-controls="collapse_<?php echo $categoria_id; ?>">
                            <i class="fas fa-dumbbell me-2"></i>
                            <?php echo htmlspecialchars($categoria); ?> 
                            <span class="badge bg-primary ms-2"><?php echo count($exercicios); ?> exercícios</span>
                        </button>
                    </h2>
                    <div id="collapse_<?php echo $categoria_id; ?>" 
                         class="accordion-collapse collapse <?php echo $categoria_index === 0 ? 'show' : ''; ?>" 
                         aria-labelledby="heading_<?php echo $categoria_id; ?>" 
                         data-bs-parent="#exerciciosAccordion">
                        <div class="accordion-body">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                <?php foreach ($exercicios as $exercicio): ?>
                                <div class="col">
                                    <div class="card h-100">
                                        <?php if (!empty($exercicio['gif_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($exercicio['gif_url']); ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($exercicio['nome_exercicio']); ?>"
                                             style="max-height: 200px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($exercicio['nome_exercicio']); ?></h5>
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item">
                                                    <strong><i class="fas fa-redo"></i> Séries:</strong> 
                                                    <?php echo $exercicio['series']; ?>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong><i class="fas fa-sync"></i> Repetições:</strong> 
                                                    <?php echo $exercicio['repeticoes']; ?>
                                                </li>
                                                <?php if (!empty($exercicio['observacoes'])): ?>
                                                <li class="list-group-item">
                                                    <strong><i class="fas fa-comment"></i> Observações:</strong><br>
                                                    <?php echo nl2br(htmlspecialchars($exercicio['observacoes'])); ?>
                                                </li>
                                                <?php endif; ?>
                                            </ul>
                                            <?php if (!empty($exercicio['descricao_exercicio'])): ?>
                                            <button class="btn btn-outline-primary btn-sm w-100" type="button" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalExercicio_<?php echo $exercicio['exercicio_id']; ?>">
                                                <i class="fas fa-info-circle"></i> Como Fazer
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal com Descrição do Exercício -->
                                <?php if (!empty($exercicio['descricao_exercicio'])): ?>
                                <div class="modal fade" id="modalExercicio_<?php echo $exercicio['exercicio_id']; ?>" 
                                     tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    Como Fazer: <?php echo htmlspecialchars($exercicio['nome_exercicio']); ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <?php if (!empty($exercicio['gif_url'])): ?>
                                                    <div class="col-md-6 mb-3">
                                                        <img src="<?php echo htmlspecialchars($exercicio['gif_url']); ?>" 
                                                             class="img-fluid rounded" 
                                                             alt="<?php echo htmlspecialchars($exercicio['nome_exercicio']); ?>">
                                                    </div>
                                                    <?php endif; ?>
                                                    <div class="col-md-<?php echo !empty($exercicio['gif_url']) ? '6' : '12'; ?>">
                                                        <?php echo nl2br(htmlspecialchars($exercicio['descricao_exercicio'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                $categoria_index++;
                endforeach; 
                ?>
            </div>
        </main>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 
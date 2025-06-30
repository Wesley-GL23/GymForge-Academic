<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';
require_once '../../config/conexao.php';

// Verifica se é admin
requireAdmin();

// Conexão com o banco
$conn = conectarBD();

// Buscar categorias para o filtro
$sql = "SELECT DISTINCT categoria FROM exercicios ORDER BY categoria";
$categorias = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

// Aplicar filtros
$where = "1=1";
$params = [];
$types = "";

if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
    $where .= " AND categoria = ?";
    $params[] = $_GET['categoria'];
    $types .= "s";
}

if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $where .= " AND (nome LIKE ? OR descricao LIKE ?)";
    $busca = "%" . $_GET['busca'] . "%";
    $params[] = $busca;
    $params[] = $busca;
    $types .= "ss";
}

// Buscar exercícios
$sql = "SELECT * FROM exercicios WHERE $where ORDER BY nome";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$exercicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
                        <a class="nav-link active" href="#">
                            <i class="fas fa-dumbbell"></i> Exercícios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../treino/listar.php">
                            <i class="fas fa-clipboard-list"></i> Treinos
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gerenciar Exercícios</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="cadastro.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Exercício
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

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label for="busca" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="busca" name="busca" 
                                   value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>"
                                   placeholder="Digite o nome ou descrição">
                        </div>
                        <div class="col-md-4">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Todas</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['categoria']); ?>"
                                        <?php echo (isset($_GET['categoria']) && $_GET['categoria'] == $cat['categoria']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['categoria']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Exercícios -->
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($exercicios)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Categoria</th>
                                        <th>GIF</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($exercicios as $exercicio): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($exercicio['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($exercicio['categoria']); ?></td>
                                        <td>
                                            <?php if (!empty($exercicio['gif_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($exercicio['gif_url']); ?>" 
                                                     alt="GIF do exercício" height="50">
                                            <?php else: ?>
                                                <span class="text-muted">Sem GIF</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="visualizar.php?id=<?php echo $exercicio['id']; ?>" 
                                               class="btn btn-sm btn-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar.php?id=<?php echo $exercicio['id']; ?>" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmarExclusao(<?php echo $exercicio['id']; ?>)"
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <?php if ($total_paginas > 1): ?>
                        <nav aria-label="Navegação de páginas" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $pagina-1; ?>&busca=<?php echo urlencode($busca); ?>&categoria=<?php echo urlencode($categoria); ?>">
                                        Anterior
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>&categoria=<?php echo urlencode($categoria); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo $pagina >= $total_paginas ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $pagina+1; ?>&busca=<?php echo urlencode($busca); ?>&categoria=<?php echo urlencode($categoria); ?>">
                                        Próxima
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <p class="text-center">Nenhum exercício encontrado.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalConfirmacao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este exercício?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluir" action="../../actions/exercicio/excluir.php" method="POST" class="d-inline">
                    <input type="hidden" id="exercicio_id" name="id" value="">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    document.getElementById('exercicio_id').value = id;
    new bootstrap.Modal(document.getElementById('modalConfirmacao')).show();
}
</script>

<?php require_once '../../includes/footer.php'; ?> 
<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';
require_once '../../config/conexao.php';

// Verifica se é admin
requireAdmin();

// Conexão com o banco
$conn = conectarBD();

// Parâmetros de filtro e paginação
$busca = isset($_GET['busca']) ? limparString($conn, $_GET['busca']) : '';
$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : 0;
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

// Construir a query base
$sql_count = "SELECT COUNT(DISTINCT t.id) as total 
              FROM treinos t 
              LEFT JOIN usuarios u ON t.usuario_id = u.id 
              WHERE 1=1";
              
$sql = "SELECT t.*, u.nome as nome_usuario, 
        (SELECT COUNT(*) FROM treino_exercicios WHERE treino_id = t.id) as total_exercicios 
        FROM treinos t 
        LEFT JOIN usuarios u ON t.usuario_id = u.id 
        WHERE 1=1";

$params = array();
$tipos = "";

// Adicionar filtros se existirem
if (!empty($busca)) {
    $sql .= " AND (t.nome LIKE ? OR t.descricao LIKE ? OR u.nome LIKE ?)";
    $sql_count .= " AND (t.nome LIKE ? OR t.descricao LIKE ? OR u.nome LIKE ?)";
    $busca_param = "%$busca%";
    $params[] = &$busca_param;
    $params[] = &$busca_param;
    $params[] = &$busca_param;
    $tipos .= "sss";
}

if ($usuario_id > 0) {
    $sql .= " AND t.usuario_id = ?";
    $sql_count .= " AND t.usuario_id = ?";
    $params[] = &$usuario_id;
    $tipos .= "i";
}

if (!empty($data_inicio)) {
    $sql .= " AND t.data >= ?";
    $sql_count .= " AND t.data >= ?";
    $params[] = &$data_inicio;
    $tipos .= "s";
}

if (!empty($data_fim)) {
    $sql .= " AND t.data <= ?";
    $sql_count .= " AND t.data <= ?";
    $params[] = &$data_fim;
    $tipos .= "s";
}

// Buscar total de registros
$stmt_count = $conn->prepare($sql_count);
if (!empty($params)) {
    $stmt_count->bind_param($tipos, ...$params);
}
$stmt_count->execute();
$total_registros = $stmt_count->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Adicionar ordenação e paginação
$sql .= " ORDER BY t.data DESC, t.nome LIMIT ? OFFSET ?";
$tipos .= "ii";
$params[] = &$por_pagina;
$params[] = &$offset;

// Buscar treinos
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($tipos, ...$params);
}
$stmt->execute();
$treinos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Buscar usuários para o filtro
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
                        <a class="nav-link active" href="#">
                            <i class="fas fa-clipboard-list"></i> Treinos
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gerenciar Treinos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="cadastro.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Treino
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
                        <div class="col-md-3">
                            <label for="busca" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="busca" name="busca" 
                                   value="<?php echo htmlspecialchars($busca); ?>" 
                                   placeholder="Nome, descrição...">
                        </div>
                        <div class="col-md-3">
                            <label for="usuario_id" class="form-label">Usuário</label>
                            <select class="form-select" id="usuario_id" name="usuario_id">
                                <option value="">Todos</option>
                                <?php foreach ($usuarios as $user): ?>
                                <option value="<?php echo $user['id']; ?>"
                                        <?php echo $usuario_id === $user['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['nome']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="data_inicio" class="form-label">Data Início</label>
                            <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                                   value="<?php echo $data_inicio; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="data_fim" class="form-label">Data Fim</label>
                            <input type="date" class="form-control" id="data_fim" name="data_fim" 
                                   value="<?php echo $data_fim; ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Treinos -->
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($treinos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome do Treino</th>
                                        <th>Usuário</th>
                                        <th>Data</th>
                                        <th>Exercícios</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($treinos as $treino): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($treino['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($treino['nome_usuario']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($treino['data'])); ?></td>
                                        <td><?php echo $treino['total_exercicios']; ?> exercícios</td>
                                        <td>
                                            <a href="visualizar.php?id=<?php echo $treino['id']; ?>" 
                                               class="btn btn-sm btn-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar.php?id=<?php echo $treino['id']; ?>" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmarExclusao(<?php echo $treino['id']; ?>)"
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
                                    <a class="page-link" href="?pagina=<?php echo $pagina-1; ?>&busca=<?php echo urlencode($busca); ?>&usuario_id=<?php echo $usuario_id; ?>&data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>">
                                        Anterior
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>&usuario_id=<?php echo $usuario_id; ?>&data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo $pagina >= $total_paginas ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $pagina+1; ?>&busca=<?php echo urlencode($busca); ?>&usuario_id=<?php echo $usuario_id; ?>&data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>">
                                        Próxima
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <p class="text-center">Nenhum treino encontrado.</p>
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
                Tem certeza que deseja excluir este treino?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluir" action="../../actions/treino/excluir.php" method="POST" class="d-inline">
                    <input type="hidden" id="treino_id" name="id" value="">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    document.getElementById('treino_id').value = id;
    new bootstrap.Modal(document.getElementById('modalConfirmacao')).show();
}
</script>

<?php require_once '../../includes/footer.php'; ?> 
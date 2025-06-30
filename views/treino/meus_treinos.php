<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';
require_once '../../config/conexao.php';

// Verifica se é cliente
requireCliente();

// Parâmetros de filtro e paginação
$busca = isset($_GET['busca']) ? limparString($conn, $_GET['busca']) : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

// Conexão com o banco
$conn = conectarBD();

// Construir a query base
$sql_count = "SELECT COUNT(*) as total FROM treinos WHERE usuario_id = ?";
$sql = "SELECT t.*, 
        (SELECT COUNT(*) FROM treino_exercicios WHERE treino_id = t.id) as total_exercicios,
        u.nome as instrutor_nome
        FROM treinos t 
        JOIN usuarios u ON t.instrutor_id = u.id 
        WHERE t.usuario_id = ?";

$params = array();
$tipos = "i";
$params[] = &$_SESSION['usuario_id'];

// Adicionar filtros se existirem
if (!empty($busca)) {
    $sql .= " AND (t.nome LIKE ? OR t.descricao LIKE ?)";
    $sql_count .= " AND (nome LIKE ? OR descricao LIKE ?)";
    $busca_param = "%$busca%";
    $params[] = &$busca_param;
    $params[] = &$busca_param;
    $tipos .= "ss";
}

if (!empty($data_inicio)) {
    $sql .= " AND t.data >= ?";
    $sql_count .= " AND data >= ?";
    $params[] = &$data_inicio;
    $tipos .= "s";
}

if (!empty($data_fim)) {
    $sql .= " AND t.data <= ?";
    $sql_count .= " AND data <= ?";
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
                        <a class="nav-link active" href="#">
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
                <h1 class="h2">Meus Treinos</h1>
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
                        <div class="col-md-4">
                            <label for="busca" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="busca" name="busca" 
                                   value="<?php echo htmlspecialchars($busca); ?>" 
                                   placeholder="Nome, descrição...">
                        </div>
                        <div class="col-md-3">
                            <label for="data_inicio" class="form-label">Data Início</label>
                            <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                                   value="<?php echo $data_inicio; ?>">
                        </div>
                        <div class="col-md-3">
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
            <?php if (!empty($treinos)): ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($treinos as $treino): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($treino['nome']); ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($treino['data'])); ?>
                                    </small>
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-dumbbell"></i> <?php echo $treino['total_exercicios']; ?> exercícios
                                </p>
                                <?php if (!empty($treino['descricao'])): ?>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($treino['descricao'], 0, 100))); ?>...</p>
                                <?php endif; ?>
                                <a href="visualizar_cliente.php?id=<?php echo $treino['id']; ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="fas fa-eye"></i> Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginação -->
                <?php if ($total_paginas > 1): ?>
                <nav aria-label="Navegação de páginas" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $pagina-1; ?>&busca=<?php echo urlencode($busca); ?>&data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>">
                                Anterior
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>&data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo $pagina >= $total_paginas ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $pagina+1; ?>&busca=<?php echo urlencode($busca); ?>&data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>">
                                Próxima
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Nenhum treino encontrado.
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 
<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';
require_once '../../config/conexao.php';

// Verifica se é cliente
requireCliente();

// Conexão com o banco
$conn = conectarBD();

// Busca informações do usuário
$sql = "SELECT nome FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Busca treinos recentes
$sql = "SELECT t.*, 
        (SELECT COUNT(*) FROM treino_exercicios WHERE treino_id = t.id) as total_exercicios 
        FROM treinos t 
        WHERE t.usuario_id = ? 
        ORDER BY t.data DESC 
        LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$treinos_recentes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Busca total de treinos
$sql = "SELECT COUNT(*) as total FROM treinos WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$total_treinos = $stmt->get_result()->fetch_assoc()['total'];

// Busca dica do dia
$sql = "SELECT * FROM dicas ORDER BY RAND() LIMIT 1";
$dica = $conn->query($sql)->fetch_assoc();

// Buscar último treino
$sql = "SELECT * FROM treinos WHERE usuario_id = ? ORDER BY data_criacao DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$ultimoTreino = $stmt->get_result()->fetch_assoc();

// Busca as últimas medidas do usuário
$stmt = $conn->prepare("
    SELECT * FROM medidas_usuario 
    WHERE usuario_id = ? 
    ORDER BY data_registro DESC 
    LIMIT 2
");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$medidas = $stmt->get_result();
$ultima_medida = $medidas->fetch_assoc();
$penultima_medida = $medidas->fetch_assoc();

// Busca os treinos concluídos na última semana
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_treinos,
        SUM(duracao_minutos) as total_minutos,
        AVG(nivel_dificuldade) as media_dificuldade
    FROM treinos_concluidos 
    WHERE usuario_id = ? 
    AND data_conclusao >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$treinos_semana = $stmt->get_result()->fetch_assoc();

// Busca os próximos treinos
$stmt = $conn->prepare("
    SELECT t.*, u.nome as instrutor_nome 
    FROM treinos t 
    JOIN usuarios u ON t.instrutor_id = u.id 
    WHERE t.aluno_id = ? 
    AND t.id NOT IN (
        SELECT treino_id 
        FROM treinos_concluidos 
        WHERE usuario_id = ? 
        AND data_conclusao >= CURDATE()
    )
    LIMIT 3
");
$stmt->bind_param("ii", $_SESSION['usuario_id'], $_SESSION['usuario_id']);
$stmt->execute();
$proximos_treinos = $stmt->get_result();

fecharConexao($conn);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../treino/meus_treinos.php">
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
                <h1 class="h2">Bem-vindo(a), <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>
            </div>

            <?php
            if (isset($_SESSION['msg'])) {
                echo '<div class="alert alert-' . $_SESSION['msg_type'] . '">' . $_SESSION['msg'] . '</div>';
                unset($_SESSION['msg']);
                unset($_SESSION['msg_type']);
            }
            ?>

            <!-- Cards de Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-clipboard-list"></i> Meus Treinos</h5>
                            <h2 class="display-4"><?php echo $total_treinos; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-calendar"></i> Último Treino</h5>
                            <p class="lead">
                                <?php 
                                if ($ultimoTreino) {
                                    echo htmlspecialchars($ultimoTreino['nome']) . '<br>';
                                    echo '<small>Criado em: ' . date('d/m/Y', strtotime($ultimoTreino['data_criacao'])) . '</small>';
                                } else {
                                    echo 'Nenhum treino registrado';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?php echo BASE_URL; ?>/views/treino/meus_treinos.php" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-clipboard-list"></i> Ver Meus Treinos
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo BASE_URL; ?>/views/exercicio/listar.php" class="btn btn-success btn-lg w-100 mb-3">
                                <i class="fas fa-dumbbell"></i> Catálogo de Exercícios
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Treinos Recentes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-history"></i> Treinos Recentes</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($treinos_recentes)): ?>
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <?php foreach ($treinos_recentes as $treino): ?>
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
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <a href="../treino/visualizar_cliente.php?id=<?php echo $treino['id']; ?>" 
                                           class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-eye"></i> Ver Detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center mb-0">Nenhum treino cadastrado ainda.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dica do Dia -->
            <?php if ($dica): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-lightbulb"></i> Dica do Dia</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($dica['titulo']); ?></h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($dica['conteudo'])); ?></p>
                    <p class="card-text">
                        <small class="text-muted">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($dica['categoria']); ?>
                        </small>
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Resumo da Semana -->
            <div class="col-md-4 mb-4">
                <div class="form-card h-100">
                    <h4 class="text-light mb-4">Resumo da Semana</h4>
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <h5 class="text-light mb-2">Treinos Concluídos</h5>
                            <p class="display-4 text-primary mb-0">
                                <?php echo $treinos_semana['total_treinos'] ?: 0; ?>
                            </p>
                        </div>
                        
                        <?php if ($treinos_semana['total_treinos'] > 0): ?>
                            <div>
                                <p class="text-light mb-1">
                                    <i class="bi bi-clock"></i> 
                                    Total: <?php echo $treinos_semana['total_minutos']; ?> minutos
                                </p>
                                <p class="text-light mb-0">
                                    <i class="bi bi-lightning"></i>
                                    Dificuldade média: <?php echo number_format($treinos_semana['media_dificuldade'], 1); ?>/5
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Medidas -->
            <div class="col-md-4 mb-4">
                <div class="form-card h-100">
                    <h4 class="text-light mb-4">Minhas Medidas</h4>
                    <?php if ($ultima_medida): ?>
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <p class="text-light mb-1">
                                    Última atualização: <?php echo date('d/m/Y', strtotime($ultima_medida['data_registro'])); ?>
                                </p>
                                <div class="d-flex gap-3">
                                    <div>
                                        <small class="text-light opacity-75">Peso</small>
                                        <p class="h4 text-primary mb-0">
                                            <?php echo number_format($ultima_medida['peso'], 1); ?> kg
                                            <?php if ($penultima_medida): 
                                                $diff = $ultima_medida['peso'] - $penultima_medida['peso'];
                                                $icon = $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'dash');
                                                $color = $diff > 0 ? 'danger' : ($diff < 0 ? 'success' : 'light');
                                            ?>
                                                <small class="text-<?php echo $color; ?>">
                                                    <i class="bi bi-arrow-<?php echo $icon; ?>"></i>
                                                    <?php echo abs(number_format($diff, 1)); ?>
                                                </small>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div>
                                        <small class="text-light opacity-75">IMC</small>
                                        <p class="h4 text-primary mb-0">
                                            <?php echo number_format($ultima_medida['imc'], 1); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="../perfil/medidas.php" class="btn btn-outline-light btn-sm">
                                Ver Todas as Medidas
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="text-light">Nenhuma medida registrada ainda.</p>
                        <a href="../perfil/medidas.php" class="btn btn-primary">
                            Registrar Medidas
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Próximos Treinos -->
            <div class="col-md-4 mb-4">
                <div class="form-card h-100">
                    <h4 class="text-light mb-4">Próximos Treinos</h4>
                    <?php if ($proximos_treinos->num_rows > 0): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php while ($treino = $proximos_treinos->fetch_assoc()): ?>
                                <div class="border-bottom border-light pb-3">
                                    <h5 class="text-light mb-1">
                                        <?php echo htmlspecialchars($treino['nome']); ?>
                                    </h5>
                                    <p class="text-light opacity-75 mb-2">
                                        Instrutor: <?php echo htmlspecialchars($treino['instrutor_nome']); ?>
                                    </p>
                                    <a href="../treino/visualizar.php?id=<?php echo $treino['id']; ?>" 
                                       class="btn btn-outline-light btn-sm">
                                        Ver Treino
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-light">Nenhum treino pendente.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 
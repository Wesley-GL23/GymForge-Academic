<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';
require_once '../../config/conexao.php';

// Verifica se é admin
requireAdmin();

// Buscar estatísticas
$conn = conectarBD();

// Total de usuários
$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = $conn->query($sql);
$totalUsuarios = $result->fetch_assoc()['total'];

// Total de exercícios
$sql = "SELECT COUNT(*) as total FROM exercicios";
$result = $conn->query($sql);
$totalExercicios = $result->fetch_assoc()['total'];

// Total de treinos
$sql = "SELECT COUNT(*) as total FROM treinos";
$result = $conn->query($sql);
$totalTreinos = $result->fetch_assoc()['total'];

fecharConexao($conn);
?>

<div class="container">
    <h1 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard Administrativo</h1>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users"></i> Total de Usuários</h5>
                    <h2 class="display-4"><?php echo $totalUsuarios; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-dumbbell"></i> Total de Exercícios</h5>
                    <h2 class="display-4"><?php echo $totalExercicios; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-clipboard-list"></i> Total de Treinos</h5>
                    <h2 class="display-4"><?php echo $totalTreinos; ?></h2>
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
                <div class="col-md-4">
                    <a href="<?php echo BASE_URL; ?>/forms/usuario/cadastro.php" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="fas fa-user-plus"></i> Novo Usuário
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="<?php echo BASE_URL; ?>/views/exercicio/cadastro.php" class="btn btn-success btn-lg w-100 mb-3">
                        <i class="fas fa-plus"></i> Novo Exercício
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="<?php echo BASE_URL; ?>/views/treino/cadastro.php" class="btn btn-info btn-lg w-100 mb-3">
                        <i class="fas fa-plus"></i> Novo Treino
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 
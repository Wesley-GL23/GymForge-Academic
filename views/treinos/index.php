<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/training_functions.php';

// Verifica se o usuário está logado
if (!estaLogado()) {
    header('Location: /forms/usuario/login.php');
    exit;
}

// Busca os treinos do usuário
$treinos = listar_treinos($_SESSION['usuario_id']);

$titulo = "Meus Treinos";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $titulo; ?></h1>
        <a href="/GymForge-Academic/forms/treino/form.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Treino
        </a>
    </div>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['sucesso'];
            unset($_SESSION['sucesso']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['erro'];
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($treinos)): ?>
        <div class="alert alert-info">
            Você ainda não tem nenhum treino cadastrado. 
            <a href="/GymForge-Academic/forms/treino/form.php" class="alert-link">Criar meu primeiro treino</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($treinos as $treino): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($treino['nome']); ?></h5>
                            
                            <p class="card-text">
                                <?php if (!empty($treino['descricao'])): ?>
                                    <?php echo htmlspecialchars(substr($treino['descricao'], 0, 100)); ?>...
                                <?php else: ?>
                                    <em>Sem descrição</em>
                                <?php endif; ?>
                            </p>

                            <div class="mb-3">
                                <span class="badge bg-<?php 
                                    echo $treino['status'] === 'ativo' ? 'success' : 
                                        ($treino['status'] === 'concluido' ? 'primary' : 'secondary');
                                ?>">
                                    <?php echo ucfirst($treino['status']); ?>
                                </span>

                                <span class="badge bg-info">
                                    <?php echo ucfirst($treino['nivel_dificuldade']); ?>
                                </span>

                                <?php if ($treino['tipo'] !== 'normal'): ?>
                                    <span class="badge bg-warning text-dark">
                                        <?php echo ucfirst($treino['tipo']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="small text-muted mb-3">
                                <div>Início: <?php echo date('d/m/Y', strtotime($treino['data_inicio'])); ?></div>
                                <?php if (!empty($treino['data_fim'])): ?>
                                    <div>Término: <?php echo date('d/m/Y', strtotime($treino['data_fim'])); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex justify-content-between">
                                <a href="/GymForge-Academic/views/treinos/visualizar.php?id=<?php echo $treino['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Visualizar
                                </a>

                                <div class="btn-group">
                                    <a href="/GymForge-Academic/forms/treino/form.php?id=<?php echo $treino['id']; ?>" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>

                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmarExclusao(<?php echo $treino['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este treino?')) {
        window.location.href = `/actions/treino/crud.php?acao=deletar&id=${id}`;
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

// Verificar se o usuário está logado
verificarLogin();

// Buscar exercícios do banco de dados
try {
    $stmt = $conn->query("SELECT * FROM exercicios ORDER BY nome");
    $exercicios = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao buscar exercícios: ' . $e->getMessage();
}

// Mensagem de feedback (sucesso/erro após operações CRUD)
$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Exercícios - GymForge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Exercícios</h2>
            <?php if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin'): ?>
            <a href="criar.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Novo Exercício
            </a>
            <?php endif; ?>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensagem); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($exercicios as $exercicio): ?>
            <div class="col">
                <div class="card h-100">
                    <?php if ($exercicio['imagem_url']): ?>
                        <img src="<?php echo htmlspecialchars($exercicio['imagem_url']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($exercicio['nome']); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($exercicio['nome']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($exercicio['descricao']); ?></p>
                        <ul class="list-unstyled">
                            <li><strong>Grupo Muscular:</strong> <?php echo htmlspecialchars($exercicio['grupo_muscular']); ?></li>
                            <li><strong>Nível:</strong> <?php echo htmlspecialchars($exercicio['nivel_dificuldade']); ?></li>
                            <li><strong>Equipamento:</strong> <?php echo htmlspecialchars($exercicio['equipamento']); ?></li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <div class="btn-group w-100">
                            <a href="visualizar.php?id=<?php echo $exercicio['id']; ?>" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <?php if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin'): ?>
                            <a href="editar.php?id=<?php echo $exercicio['id']; ?>" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <button type="button" 
                                    class="btn btn-outline-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal<?php echo $exercicio['id']; ?>">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmação de Exclusão -->
            <?php if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin'): ?>
            <div class="modal fade" id="deleteModal<?php echo $exercicio['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmar Exclusão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            Tem certeza que deseja excluir o exercício "<?php echo htmlspecialchars($exercicio['nome']); ?>"?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form action="excluir.php" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $exercicio['id']; ?>">
                                <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
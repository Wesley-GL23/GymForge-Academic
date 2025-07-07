<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

// Verificar se é administrador
verificarAdmin();

// Buscar usuários do banco de dados
try {
    $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY nome");
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao buscar usuários: ' . $e->getMessage();
}

// Mensagem de feedback
$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - GymForge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Usuários</h2>
            <a href="criar.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Novo Usuário
            </a>
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

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Nível</th>
                                <th>Data de Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $usuario['nivel'] === 'administrador' ? 'danger' : 
                                            ($usuario['nivel'] === 'cliente' ? 'primary' : 'secondary'); 
                                    ?>">
                                        <?php echo htmlspecialchars($usuario['nivel']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($usuario['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="editar.php?id=<?php echo $usuario['id']; ?>" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <?php if ($usuario['id'] !== $_SESSION['usuario_id']): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal<?php echo $usuario['id']; ?>">
                                            <i class="bi bi-trash"></i> Excluir
                                        </button>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Modal de Confirmação de Exclusão -->
                                    <div class="modal fade" id="deleteModal<?php echo $usuario['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmar Exclusão</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja excluir o usuário "<?php echo htmlspecialchars($usuario['nome']); ?>"?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="excluir.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                                        <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
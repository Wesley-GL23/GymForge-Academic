<?php
require_once '../../includes/header.php';
requireAdmin(); // Apenas admins podem ver a lista de usuários

$conn = conectarBD();
$sql = "SELECT id, nome, email, nivel, created_at FROM usuarios ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users"></i> Usuários</h2>
        <a href="<?php echo BASE_URL; ?>/forms/usuario/cadastro.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Novo Usuário
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Nível</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($usuario = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $usuario['nivel'] === 'admin' ? 'danger' : 'primary'; ?>">
                                            <?php echo ucfirst($usuario['nivel']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($usuario['created_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/views/usuario/editar.php?id=<?php echo $usuario['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" title="Excluir" 
                                                onclick="confirmarExclusao(<?php echo $usuario['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhum usuário cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        window.location.href = '<?php echo BASE_URL; ?>/actions/usuario/excluir.php?id=' + id;
    }
}
</script>

<?php
fecharConexao($conn);
require_once '../../includes/footer.php';
?> 
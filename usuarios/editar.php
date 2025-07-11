<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

// Permitir que o usuário edite apenas o próprio perfil
session_start();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!isset($_SESSION['user_id']) || !$id || $_SESSION['user_id'] != $id) {
    header('Location: ../acesso_negado.php');
    exit();
}

$erro = '';
$sucesso = '';

// Buscar imagem de perfil com qualquer extensão suportada
$profile_img_path = null;
$exts = ['jpg', 'jpeg', 'png', 'gif'];
foreach ($exts as $ext) {
    $try_path = "../assets/img/profiles/$id.$ext";
    if (file_exists($try_path)) {
        $profile_img_path = $try_path;
        break;
    }
}
$has_profile_img = $profile_img_path !== null;
$default_avatar = '../assets/img/avatar-default.png';

try {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        header('Location: index.php');
        exit();
    }
} catch (PDOException $e) {
    $erro = 'Erro ao buscar usuário: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar e sanitizar dados
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $nivel = filter_input(INPUT_POST, 'nivel', FILTER_SANITIZE_STRING);

    // Validações
    if (empty($nome)) {
        $erro = 'Nome é obrigatório';
    } elseif (!$email) {
        $erro = 'Email inválido';
    } elseif (!empty($senha) && $senha !== $confirmar_senha) {
        $erro = 'As senhas não conferem';
    } elseif (!in_array($nivel, ['visitante', 'cliente', 'administrador'])) {
        $erro = 'Nível de acesso inválido';
    } else {
        try {
            // Verificar se email já existe (exceto para o próprio usuário)
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                $erro = 'Este email já está cadastrado para outro usuário';
            } else {
                // Atualizar usuário
                if (!empty($senha)) {
                    // Se a senha foi fornecida, atualiza com a nova senha
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("
                        UPDATE usuarios 
                        SET nome = ?, email = ?, senha = ?, nivel = ? 
                        WHERE id = ?
                    ");
                    $params = [$nome, $email, $senha_hash, $nivel, $id];
                } else {
                    // Se a senha não foi fornecida, mantém a senha atual
                    $stmt = $conn->prepare("
                        UPDATE usuarios 
                        SET nome = ?, email = ?, nivel = ? 
                        WHERE id = ?
                    ");
                    $params = [$nome, $email, $nivel, $id];
                }
                
                if ($stmt->execute($params)) {
                    $sucesso = 'Usuário atualizado com sucesso!';
                    // Atualizar dados do usuário para exibir na tela
                    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
                    $stmt->execute([$id]);
                    $usuario = $stmt->fetch();
                } else {
                    $erro = 'Erro ao atualizar usuário';
                }
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao atualizar usuário: ' . $e->getMessage();
        }
    }

    // Upload da foto de perfil
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['foto']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            // Remover imagens antigas do usuário
            foreach ($allowed as $old_ext) {
                $old_path = "../assets/img/profiles/$id.$old_ext";
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            $dest_path = "../assets/img/profiles/$id.$ext";
            move_uploaded_file($tmp_name, $dest_path);
            $has_profile_img = true;
            $profile_img_path = $dest_path . '?v=' . time();
        }
    }
}

$titulo = 'Editar Usuário';
include '../includes/header.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title mb-4">Editar Usuário</h2>
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                    <?php endif; ?>
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($sucesso); ?></div>
                    <?php endif; ?>
                    <form method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                        <div class="mb-3 text-center">
                            <img src="<?php echo $has_profile_img ? $profile_img_path : $default_avatar; ?>" alt="Foto de Perfil" class="rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #FF6B00;">
                            <div>
                                <label for="foto" class="form-label">Foto de Perfil</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            </div>
                        </div>

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome *</label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                                <div class="invalid-feedback">
                                    Por favor, insira o nome.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                <div class="invalid-feedback">
                                    Por favor, insira um email válido.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                                <input type="password" class="form-control" id="senha" name="senha" minlength="6">
                                <div class="invalid-feedback">
                                    A senha deve ter pelo menos 6 caracteres.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha">
                                <div class="invalid-feedback">
                                    As senhas devem ser iguais.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nivel" class="form-label">Nível de Acesso *</label>
                                <select class="form-select" id="nivel" name="nivel" required>
                                    <option value="">Selecione...</option>
                                    <option value="visitante" <?php echo $usuario['nivel'] === 'visitante' ? 'selected' : ''; ?>>Visitante</option>
                                    <option value="cliente" <?php echo $usuario['nivel'] === 'cliente' ? 'selected' : ''; ?>>Cliente</option>
                                    <option value="administrador" <?php echo $usuario['nivel'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor, selecione o nível de acesso.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?> 
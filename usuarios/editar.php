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

// Buscar dados do usuário
$profile_img_path = "../assets/img/profiles/$id.jpg";
$has_profile_img = file_exists($profile_img_path);
$default_avatar = '../assets/img/avatar-default.png';

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
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
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                $erro = 'Este email já está cadastrado para outro usuário';
            } else {
                // Atualizar usuário
                if (!empty($senha)) {
                    // Se a senha foi fornecida, atualiza com a nova senha
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("
                        UPDATE usuarios 
                        SET nome = ?, email = ?, senha = ?, nivel = ? 
                        WHERE id = ?
                    ");
                    $params = [$nome, $email, $senha_hash, $nivel, $id];
                } else {
                    // Se a senha não foi fornecida, mantém a senha atual
                    $stmt = $pdo->prepare("
                        UPDATE usuarios 
                        SET nome = ?, email = ?, nivel = ? 
                        WHERE id = ?
                    ");
                    $params = [$nome, $email, $nivel, $id];
                }
                
                if ($stmt->execute($params)) {
                    $_SESSION['mensagem'] = 'Usuário atualizado com sucesso!';
                    header('Location: index.php');
                    exit();
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
            move_uploaded_file($tmp_name, "../assets/img/profiles/$id.jpg");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - GymForge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Editar Usuário</h2>
                        
                        <?php if ($erro): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                // Validação adicional de senha
                var senha = document.getElementById('senha')
                var confirmar_senha = document.getElementById('confirmar_senha')
                if (senha.value && senha.value !== confirmar_senha.value) {
                    confirmar_senha.setCustomValidity('As senhas não conferem')
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    confirmar_senha.setCustomValidity('')
                }
                
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</body>
</html> 
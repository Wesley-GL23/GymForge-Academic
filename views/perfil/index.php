<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/header.php';

$user = usuarioAtual();
if (!$user) {
    header('Location: /GymForge-Academic/login.php');
    exit;
}

// Buscar a foto de perfil do usuário com qualquer extensão
$profile_img_path = '';
$allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
foreach ($allowed_exts as $ext) {
    $path = __DIR__ . "/../../assets/img/profiles/{$user['id']}.$ext";
    if (file_exists($path)) {
        $profile_img_path = "/GymForge-Academic/assets/img/profiles/{$user['id']}.$ext";
        break;
    }
}
$default_avatar = '/GymForge-Academic/assets/img/avatar-default.png';

// Upload da foto de perfil
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['foto']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_exts)) {
            // Apagar outras fotos antigas do usuário
            foreach ($allowed_exts as $old_ext) {
                $old_path = __DIR__ . "/../../assets/img/profiles/{$user['id']}.$old_ext";
                if (file_exists($old_path)) unlink($old_path);
            }
            $dest = __DIR__ . "/../../assets/img/profiles/{$user['id']}.$ext";
            if (move_uploaded_file($tmp_name, $dest)) {
                $profile_img_path = "/GymForge-Academic/assets/img/profiles/{$user['id']}.$ext";
                $msg = '<div class="alert alert-success mt-2">Foto de perfil atualizada com sucesso!</div>';
            } else {
                $msg = '<div class="alert alert-danger mt-2">Erro ao salvar a imagem.</div>';
            }
        } else {
            $msg = '<div class="alert alert-danger mt-2">Formato de imagem não permitido.</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger mt-2">Erro ao enviar a imagem.</div>';
    }
}
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="glass-effect p-4">
                <div class="text-center mb-4">
                    <img src="<?php echo $profile_img_path ? $profile_img_path . '?t=' . time() : $default_avatar; ?>" alt="Foto de Perfil" class="rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #FF6B00;">
                    <form method="POST" enctype="multipart/form-data" class="mt-2">
                        <input type="file" name="foto" accept="image/*" class="form-control d-inline-block w-auto" style="display:inline-block; max-width:200px;">
                        <button type="submit" class="btn btn-outline-accent ms-2">Trocar foto</button>
                    </form>
                    <?php echo $msg; ?>
                </div>
                <h2 class="mb-4 text-center text-forge-accent">Meu Perfil</h2>
                <dl class="row mb-4">
                    <dt class="col-sm-3">Nome:</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($user['nome']); ?></dd>
                    <dt class="col-sm-3">Email:</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($user['email']); ?></dd>
                    <dt class="col-sm-3">Nível:</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($user['nivel']); ?></dd>
                </dl>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="/GymForge-Academic/usuarios/editar.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-user-edit me-2"></i>Editar Perfil
                    </a>
                    <a href="/GymForge-Academic/forms/usuario/redefinir_senha.php" class="btn btn-outline-accent">
                        <i class="fas fa-key me-2"></i>Redefinir Senha
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?> 
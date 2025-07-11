<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/exercise_functions.php';

// Verifica se é administrador
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    $_SESSION['erro'] = "Acesso negado.";
    header('Location: biblioteca.php');
    exit;
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = $_POST['nome'];
        $categoria = $_POST['categoria'];
        $descricao = $_POST['descricao'];
        $nivel_dificuldade = $_POST['nivel_dificuldade'];
        
        // Upload de vídeo
        $video_url = '';
        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $video_tmp = $_FILES['video']['tmp_name'];
            $video_name = $_FILES['video']['name'];
            $video_ext = strtolower(pathinfo($video_name, PATHINFO_EXTENSION));
            
            // Verificar extensão
            if (!in_array($video_ext, ['mp4', 'webm'])) {
                throw new Exception("Formato de vídeo não suportado. Use MP4 ou WebM.");
            }
            
            // Gerar nome único
            $video_path = 'assets/exercises/videos/' . $categoria . '/' . uniqid() . '.' . $video_ext;
            $full_path = __DIR__ . '/../../' . $video_path;
            
            // Criar diretório se não existir
            if (!is_dir(dirname($full_path))) {
                mkdir(dirname($full_path), 0777, true);
            }
            
            // Mover arquivo
            if (move_uploaded_file($video_tmp, $full_path)) {
                $video_url = $video_path;
            } else {
                throw new Exception("Erro ao fazer upload do vídeo.");
            }
        }
        
        // Inserir no banco
        $stmt = $conn->prepare("
            INSERT INTO exercicios (nome, categoria, descricao, nivel_dificuldade, video_url)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$nome, $categoria, $descricao, $nivel_dificuldade, $video_url])) {
            $_SESSION['mensagem'] = "Exercício criado com sucesso!";
            header('Location: biblioteca.php');
            exit;
        } else {
            throw new Exception("Erro ao criar exercício.");
        }
    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao criar exercício: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Exercício - GymForge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Novo Exercício</h2>
                        
                        <?php if (isset($_SESSION['erro'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo htmlspecialchars($_SESSION['erro']);
                                unset($_SESSION['erro']);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nome do Exercício</label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Categoria</label>
                                <select class="form-select" name="categoria" required>
                                    <option value="musculacao">Musculação</option>
                                    <option value="cardio">Cardio</option>
                                    <option value="funcional">Funcional</option>
                                    <option value="alongamento">Alongamento</option>
                                    <option value="yoga">Yoga</option>
                                    <option value="pilates">Pilates</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="descricao" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nível de Dificuldade</label>
                                <select class="form-select" name="nivel_dificuldade" required>
                                    <option value="iniciante">Iniciante</option>
                                    <option value="intermediario">Intermediário</option>
                                    <option value="avancado">Avançado</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Vídeo Demonstrativo</label>
                                <input type="file" class="form-control" name="video" accept="video/mp4,video/webm">
                                <div class="form-text">Formatos aceitos: MP4, WebM</div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="biblioteca.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Criar Exercício</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
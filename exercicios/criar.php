<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

// Verificar se é administrador
verificarAdmin();

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar e sanitizar dados
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
    $grupo_muscular = filter_input(INPUT_POST, 'grupo_muscular', FILTER_SANITIZE_STRING);
    $nivel_dificuldade = filter_input(INPUT_POST, 'nivel_dificuldade', FILTER_SANITIZE_STRING);
    $equipamento = filter_input(INPUT_POST, 'equipamento', FILTER_SANITIZE_STRING);
    $video_url = filter_input(INPUT_POST, 'video_url', FILTER_SANITIZE_URL);
    $imagem_url = filter_input(INPUT_POST, 'imagem_url', FILTER_SANITIZE_URL);

    // Validar campos obrigatórios
    if (empty($nome)) {
        $erro = 'Nome é obrigatório';
    } elseif (empty($grupo_muscular)) {
        $erro = 'Grupo muscular é obrigatório';
    } elseif (empty($nivel_dificuldade)) {
        $erro = 'Nível de dificuldade é obrigatório';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO exercicios (
                    nome, descricao, grupo_muscular, nivel_dificuldade, 
                    equipamento, video_url, imagem_url, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([
                $nome, $descricao, $grupo_muscular, $nivel_dificuldade,
                $equipamento, $video_url, $imagem_url, $_SESSION['usuario_id']
            ])) {
                $_SESSION['mensagem'] = 'Exercício criado com sucesso!';
                header('Location: index.php');
                exit();
            } else {
                $erro = 'Erro ao criar exercício';
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao criar exercício: ' . $e->getMessage();
        }
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
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Novo Exercício</h2>
                        
                        <?php if ($erro): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome *</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                                <div class="invalid-feedback">
                                    Por favor, insira o nome do exercício.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="grupo_muscular" class="form-label">Grupo Muscular *</label>
                                <select class="form-select" id="grupo_muscular" name="grupo_muscular" required>
                                    <option value="">Selecione...</option>
                                    <option value="peito">Peito</option>
                                    <option value="costas">Costas</option>
                                    <option value="pernas">Pernas</option>
                                    <option value="bracos">Braços</option>
                                    <option value="ombros">Ombros</option>
                                    <option value="abdomen">Abdômen</option>
                                    <option value="gluteos">Glúteos</option>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor, selecione o grupo muscular.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nivel_dificuldade" class="form-label">Nível de Dificuldade *</label>
                                <select class="form-select" id="nivel_dificuldade" name="nivel_dificuldade" required>
                                    <option value="">Selecione...</option>
                                    <option value="iniciante">Iniciante</option>
                                    <option value="intermediario">Intermediário</option>
                                    <option value="avancado">Avançado</option>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor, selecione o nível de dificuldade.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="equipamento" class="form-label">Equipamento</label>
                                <input type="text" class="form-control" id="equipamento" name="equipamento">
                            </div>

                            <div class="mb-3">
                                <label for="video_url" class="form-label">URL do Vídeo</label>
                                <input type="url" class="form-control" id="video_url" name="video_url">
                                <div class="invalid-feedback">
                                    Por favor, insira uma URL válida.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="imagem_url" class="form-label">URL da Imagem</label>
                                <input type="url" class="form-control" id="imagem_url" name="imagem_url">
                                <div class="invalid-feedback">
                                    Por favor, insira uma URL válida.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Criar Exercício</button>
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
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</body>
</html> 
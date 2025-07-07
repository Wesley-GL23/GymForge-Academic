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
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $nivel = filter_input(INPUT_POST, 'nivel', FILTER_SANITIZE_STRING);

    // Validações
    if (empty($nome)) {
        $erro = 'Nome é obrigatório';
    } elseif (!$email) {
        $erro = 'Email inválido';
    } elseif (empty($senha)) {
        $erro = 'Senha é obrigatória';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não conferem';
    } elseif (!in_array($nivel, ['visitante', 'cliente', 'administrador'])) {
        $erro = 'Nível de acesso inválido';
    } else {
        try {
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $erro = 'Este email já está cadastrado';
            } else {
                // Criar novo usuário
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (nome, email, senha, nivel) 
                    VALUES (?, ?, ?, ?)
                ");
                
                if ($stmt->execute([$nome, $email, $senha_hash, $nivel])) {
                    $_SESSION['mensagem'] = 'Usuário criado com sucesso!';
                    header('Location: index.php');
                    exit();
                } else {
                    $erro = 'Erro ao criar usuário';
                }
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao criar usuário: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Usuário - GymForge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Novo Usuário</h2>
                        
                        <?php if ($erro): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome *</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                                <div class="invalid-feedback">
                                    Por favor, insira o nome.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">
                                    Por favor, insira um email válido.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha *</label>
                                <input type="password" class="form-control" id="senha" name="senha" 
                                       required minlength="6">
                                <div class="invalid-feedback">
                                    A senha deve ter pelo menos 6 caracteres.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="confirmar_senha" class="form-label">Confirmar Senha *</label>
                                <input type="password" class="form-control" id="confirmar_senha" 
                                       name="confirmar_senha" required>
                                <div class="invalid-feedback">
                                    As senhas devem ser iguais.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nivel" class="form-label">Nível de Acesso *</label>
                                <select class="form-select" id="nivel" name="nivel" required>
                                    <option value="">Selecione...</option>
                                    <option value="visitante">Visitante</option>
                                    <option value="cliente">Cliente</option>
                                    <option value="administrador">Administrador</option>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor, selecione o nível de acesso.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Criar Usuário</button>
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
                if (senha.value !== confirmar_senha.value) {
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
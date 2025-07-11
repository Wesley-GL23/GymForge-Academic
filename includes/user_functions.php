<?php
function cadastrarUsuario($nome, $email, $senha) {
    global $conn;
    
    try {
        // Verifica se email já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return false;
        }

        // Hash da senha
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        // Insere novo usuário
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nome, email, senha, nivel, data_cadastro) 
            VALUES (?, ?, ?, \"cliente\", NOW())
        ");
        $stmt->execute([$nome, $email, $hash]);

        // Log da ação
        $user_id = $conn->lastInsertId();
        $auth = new Auth($conn);
        $auth->logAction($user_id, "registro", "Novo usuário registrado");

        return true;
    } catch (Exception $e) {
        error_log("Erro no cadastro: " . $e->getMessage());
        return false;
    }
}

function fazerLogout() {
    $auth = new Auth($GLOBALS["conn"]);
    $auth->logout();
    header("Location: " . BASE_URL . "/forms/usuario/login.php");
    exit;
}

<?php
session_start();
require_once '../../config/config.php';
require_once '../../config/conexao.php';

// Arquivo de log
$log_file = fopen("login_debug.log", "a");

function writeLog($message) {
    global $log_file;
    fwrite($log_file, date('Y-m-d H:i:s') . " - " . $message . "\n");
}

// Verificar se já existe um cookie e preencher o formulário
if (isset($_COOKIE['gymforge_email']) && !isset($_POST['email'])) {
    $_POST['email'] = $_COOKIE['gymforge_email'];
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    writeLog("Iniciando processo de login");
    $conn = conectarBD();
    
    // Limpa e obtém os dados do formulário
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $lembrar = isset($_POST['lembrar']);
    
    writeLog("Email recebido: " . $email);
    writeLog("Senha recebida (length): " . strlen($senha));
    
    // Validação básica
    if (empty($email) || empty($senha)) {
        setFlashMessage('danger', '<i class="fas fa-exclamation-circle"></i> Por favor, preencha todos os campos.');
        header('Location: ' . BASE_URL . '/forms/usuario/login.php');
        exit;
    }
    
    // Busca o usuário no banco de dados
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        writeLog("Erro: falha ao preparar consulta");
        setFlashMessage("danger", "Erro ao preparar consulta. Por favor, tente novamente mais tarde.");
        header("Location: " . BASE_URL . "/forms/usuario/login.php");
        exit();
    }
    
    $stmt->bind_param("s", $email);
    
    if (!$stmt->execute()) {
        writeLog("Erro: falha ao executar consulta");
        setFlashMessage("danger", "Erro ao executar consulta. Por favor, tente novamente mais tarde.");
        header("Location: " . BASE_URL . "/forms/usuario/login.php");
        exit();
    }
    
    $result = $stmt->get_result();
    
    writeLog("Consulta SQL executada");
    writeLog("Número de resultados: " . $result->num_rows);
    
    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        writeLog("Hash da senha no banco: " . $usuario['senha']);
        
        // Verifica a senha
        if (password_verify($senha, $usuario['senha'])) {
            writeLog("Senha verificada com sucesso");
            // Login bem-sucedido
            // Se a opção "Lembrar-me" estiver marcada, criar cookie
            if ($lembrar) {
                setcookie('gymforge_email', $email, time() + (30 * 24 * 60 * 60), '/', '', true, true);
            } else {
                if (isset($_COOKIE['gymforge_email'])) {
                    setcookie('gymforge_email', '', time() - 3600, '/');
                }
            }
            
            // Iniciar sessão
            session_regenerate_id(true);
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nome'];
            $_SESSION['user_email'] = $usuario['email'];
            $_SESSION['user_nivel'] = $usuario['nivel'];
            
            writeLog("Nível do usuário: " . $usuario['nivel']);
            
            setFlashMessage('success', '<i class="fas fa-check-circle"></i> Bem-vindo(a) de volta, ' . $usuario['nome'] . '!');
            
            // Redireciona com base no nível do usuário
            if ($usuario['nivel'] === 'admin') {
                header('Location: ' . BASE_URL . '/views/dashboard/admin.php');
            } else {
                header('Location: ' . BASE_URL . '/views/dashboard/cliente.php');
            }
            exit;
        } else {
            writeLog("Erro: senha incorreta");
            setFlashMessage('danger', '<i class="fas fa-exclamation-circle"></i> E-mail ou senha incorretos. Tente novamente.');
        }
    } else {
        writeLog("Erro: email não encontrado");
        setFlashMessage("danger", "E-mail não encontrado.");
    }
    
    $stmt->close();
    fecharConexao($conn);
    fclose($log_file);
    
    // Se chegou até aqui, houve erro
    header("Location: " . BASE_URL . "/forms/usuario/login.php");
    exit();
}

// Se alguém tentar acessar diretamente o arquivo
header("Location: " . BASE_URL . "/forms/usuario/login.php");
exit(); 
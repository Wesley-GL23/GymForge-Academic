<?php
require_once '../../config/config.php';
require_once '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = 'Por favor, insira um e-mail válido.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../forms/usuario/recuperar_senha.php');
        exit;
    }
    
    // Verifica se o email existe
    $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['flash_message'] = 'E-mail não encontrado em nossa base de dados.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../forms/usuario/recuperar_senha.php');
        exit;
    }
    
    $usuario = $result->fetch_assoc();
    
    // Gera token único
    $token = bin2hex(random_bytes(32));
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Salva o token no banco
    $stmt = $conn->prepare("INSERT INTO recuperacao_senha (usuario_id, token, expira) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario['id'], $token, $expira);
    
    if (!$stmt->execute()) {
        $_SESSION['flash_message'] = 'Erro ao processar sua solicitação. Tente novamente.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../forms/usuario/recuperar_senha.php');
        exit;
    }
    
    // Envia o email (em produção, usar serviço de email)
    $resetLink = BASE_URL . '/forms/usuario/redefinir_senha.php?token=' . $token;
    $to = $email;
    $subject = 'GYMFORGE - Recuperação de Senha';
    $message = "
    Olá {$usuario['nome']},

    Recebemos uma solicitação para redefinir sua senha no GYMFORGE.
    
    Para criar uma nova senha, clique no link abaixo:
    {$resetLink}
    
    Este link é válido por 1 hora.
    
    Se você não solicitou esta alteração, ignore este e-mail.
    
    Atenciosamente,
    Equipe GYMFORGE
    ";
    
    $headers = 'From: noreply@gymforge.com' . "\r\n" .
        'Reply-To: noreply@gymforge.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    
    // Em desenvolvimento, apenas simula o envio
    // mail($to, $subject, $message, $headers);
    
    $_SESSION['flash_message'] = 'Instruções de recuperação de senha foram enviadas para seu e-mail. 
        <br><small class="text-muted">Em ambiente de desenvolvimento, use este link: 
        <a href="'.$resetLink.'" class="alert-link">Redefinir Senha</a></small>';
    $_SESSION['flash_type'] = 'success';
    header('Location: ../../forms/usuario/recuperar_senha.php');
    exit;
}

header('Location: ../../forms/usuario/recuperar_senha.php');
exit; 
<?php
require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../classes/ForgeSystem.php';

// Verificar método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token inválido']);
    exit;
}

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

try {
    // Pegar dados do request
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['guild_id']) || !is_numeric($data['guild_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID da guilda inválido']);
        exit;
    }

    $guild_id = (int)$data['guild_id'];
    $message = isset($data['message']) ? trim($data['message']) : '';

    $forge = new ForgeSystem($conn, $_SESSION['user_id']);
    $status = $forge->getCharacterStatus();

    if (!is_array($status) || !isset($status['character']['id'])) {
        throw new Exception('Status do personagem inválido');
    }

    // Verificar nível mínimo
    if ($status['character']['level'] < 10) {
        echo json_encode(['success' => false, 'message' => 'Nível mínimo requerido: 10']);
        exit;
    }

    // Verificar se já é membro de uma guilda
    $stmt = $conn->prepare("
        SELECT gm.id, g.name as guild_name 
        FROM guild_members gm
        JOIN forge_guilds g ON gm.guild_id = g.id
        WHERE gm.character_id = ?
    ");
    $stmt->execute([$status['character']['id']]);
    $current_guild = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($current_guild) {
        echo json_encode([
            'success' => false, 
            'message' => "Você já é membro da guilda '{$current_guild['guild_name']}'"
        ]);
        exit;
    }

    // Verificar se a guilda existe e tem vagas
    $stmt = $conn->prepare("
        SELECT g.*, 
            (
                SELECT COUNT(*) 
                FROM guild_members 
                WHERE guild_id = g.id
            ) as member_count,
            (
                SELECT COUNT(*)
                FROM guild_join_requests
                WHERE guild_id = g.id 
                AND character_id = ?
                AND status = 'pending'
            ) as pending_request
        FROM forge_guilds g
        WHERE g.id = ?
    ");
    $stmt->execute([$status['character']['id'], $guild_id]);
    $guild = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guild) {
        echo json_encode(['success' => false, 'message' => 'Guilda não encontrada']);
        exit;
    }

    if (isset($guild['pending_request']) && $guild['pending_request'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Você já tem uma solicitação pendente para esta guilda']);
        exit;
    }

    if (isset($guild['member_count']) && $guild['member_count'] >= 50) {
        echo json_encode(['success' => false, 'message' => 'Guilda está cheia']);
        exit;
    }

    $conn->beginTransaction();

    try {
        // Criar solicitação
        $stmt = $conn->prepare("
            INSERT INTO guild_join_requests 
            (guild_id, character_id, message) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$guild_id, $status['character']['id'], $message]);

        // Registrar no log do sistema
        $stmt = $conn->prepare("
            INSERT INTO system_logs 
            (user_id, action_type, description, ip_address, user_agent) 
            VALUES (?, 'guild_request', ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            "Solicitou entrada na guilda '{$guild['name']}'",
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);

        // Notificar líder e oficiais
        $stmt = $conn->prepare("
            INSERT INTO system_notifications 
            (user_id, title, message, type)
            SELECT 
                u.id,
                'Nova Solicitação de Guilda',
                CONCAT(?, ' solicitou entrada na guilda'),
                'guild'
            FROM guild_members gm
            JOIN forge_characters fc ON fc.id = gm.character_id
            JOIN usuarios u ON u.id = fc.user_id
            WHERE gm.guild_id = ? AND gm.role IN ('leader', 'officer')
        ");
        $stmt->execute([
            $status['character']['name'],
            $guild_id
        ]);

        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Solicitação enviada com sucesso'
        ]);

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log('Erro ao solicitar entrada: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao enviar solicitação. Tente novamente mais tarde.'
    ]);
} 
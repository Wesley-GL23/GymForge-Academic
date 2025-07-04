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
    if (!isset($data['character_id']) || !is_numeric($data['character_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID do membro inválido']);
        exit;
    }

    $target_character_id = (int)$data['character_id'];
    
    // Verificar se o alvo não é o próprio usuário
    if ($target_character_id === (int)$_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Você não pode expulsar a si mesmo']);
        exit;
    }

    $forge = new ForgeSystem($conn, $_SESSION['user_id']);
    $status = $forge->getCharacterStatus();

    if (!is_array($status) || !isset($status['character']['id'])) {
        throw new Exception('Status do personagem inválido');
    }

    // Verificar se o usuário é líder de alguma guilda
    $stmt = $conn->prepare("
        SELECT g.id as guild_id, g.name as guild_name
        FROM forge_guilds g
        WHERE g.leader_id = ?
    ");
    $stmt->execute([$status['character']['id']]);
    $guild = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guild) {
        echo json_encode(['success' => false, 'message' => 'Você não é líder de nenhuma guilda']);
        exit;
    }

    // Verificar se o alvo é membro da guilda
    $stmt = $conn->prepare("
        SELECT gm.role, fc.id as character_id, fc.user_id,
               u.nome as character_name
        FROM guild_members gm
        JOIN forge_characters fc ON fc.id = gm.character_id
        JOIN usuarios u ON fc.user_id = u.id
        WHERE gm.guild_id = ? AND gm.character_id = ?
    ");
    $stmt->execute([$guild['guild_id'], $target_character_id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        echo json_encode(['success' => false, 'message' => 'Membro não encontrado na guilda']);
        exit;
    }

    if (isset($member['role']) && $member['role'] === 'leader') {
        echo json_encode(['success' => false, 'message' => 'Não é possível expulsar o líder']);
        exit;
    }

    $conn->beginTransaction();

    try {
        // Remover membro
        $stmt = $conn->prepare("
            DELETE FROM guild_members
            WHERE guild_id = ? AND character_id = ?
        ");
        $stmt->execute([$guild['guild_id'], $target_character_id]);

        // Registrar no log do sistema
        $stmt = $conn->prepare("
            INSERT INTO system_logs 
            (user_id, action_type, description, ip_address, user_agent) 
            VALUES (?, 'guild_kick', ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            "Expulsou {$member['character_name']} da guilda '{$guild['guild_name']}'",
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);

        // Criar notificação para o membro expulso
        $stmt = $conn->prepare("
            INSERT INTO system_notifications 
            (user_id, title, message, type) 
            VALUES (?, 'Expulsão da Guilda', ?, 'guild')
        ");
        $stmt->execute([
            $member['user_id'],
            "Você foi expulso da guilda '{$guild['guild_name']}'"
        ]);

        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => "{$member['character_name']} foi expulso da guilda"
        ]);

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log('Erro ao expulsar membro: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao expulsar membro. Tente novamente mais tarde.'
    ]);
} 
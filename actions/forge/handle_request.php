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
    if (!isset($data['request_id']) || !isset($data['action'])) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    $request_id = (int)$data['request_id'];
    $action = $data['action'];

    if (!in_array($action, ['accept', 'reject'])) {
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        exit;
    }

    $forge = new ForgeSystem($conn, $_SESSION['user_id']);
    $status = $forge->getCharacterStatus();

    if (!is_array($status) || !isset($status['character']['id'])) {
        throw new Exception('Status do personagem inválido');
    }

    // Verificar se é líder ou oficial de alguma guilda
    $stmt = $conn->prepare("
        SELECT 
            g.id as guild_id,
            g.name as guild_name,
            gm.role
        FROM guild_members gm
        JOIN forge_guilds g ON g.id = gm.guild_id
        WHERE gm.character_id = ? AND gm.role IN ('leader', 'officer')
    ");
    $stmt->execute([$status['character']['id']]);
    $guild_role = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guild_role) {
        echo json_encode(['success' => false, 'message' => 'Você não tem permissão para esta ação']);
        exit;
    }

    // Buscar solicitação
    $stmt = $conn->prepare("
        SELECT 
            gjr.*,
            fc.level,
            fc.user_id,
            u.nome as character_name,
            (
                SELECT COUNT(*) 
                FROM guild_members 
                WHERE guild_id = gjr.guild_id
            ) as member_count
        FROM guild_join_requests gjr
        JOIN forge_characters fc ON fc.id = gjr.character_id
        JOIN usuarios u ON u.id = fc.user_id
        WHERE gjr.id = ? AND gjr.guild_id = ? AND gjr.status = 'pending'
    ");
    $stmt->execute([$request_id, $guild_role['guild_id']]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        echo json_encode(['success' => false, 'message' => 'Solicitação não encontrada']);
        exit;
    }

    // Verificar nível mínimo
    if ($request['level'] < 10) {
        echo json_encode(['success' => false, 'message' => 'Jogador não atende ao nível mínimo']);
        exit;
    }

    // Verificar limite de membros se for aceitar
    if ($action === 'accept' && isset($request['member_count']) && $request['member_count'] >= 50) {
        echo json_encode(['success' => false, 'message' => 'Guilda está cheia']);
        exit;
    }

    $conn->beginTransaction();

    try {
        // Atualizar status da solicitação
        $stmt = $conn->prepare("
            UPDATE guild_join_requests 
            SET status = ?, 
                response_date = NOW(),
                responded_by = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $action === 'accept' ? 'accepted' : 'rejected',
            $status['character']['id'],
            $request_id
        ]);

        if ($action === 'accept') {
            // Adicionar como membro
            $stmt = $conn->prepare("
                INSERT INTO guild_members 
                (guild_id, character_id, role, contribution_points) 
                VALUES (?, ?, 'member', 10)
            ");
            $stmt->execute([$guild_role['guild_id'], $request['character_id']]);
        }

        // Registrar no log do sistema
        $stmt = $conn->prepare("
            INSERT INTO system_logs 
            (user_id, action_type, description, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            'guild_request_' . $action,
            ($action === 'accept' ? 'Aceitou' : 'Rejeitou') . " solicitação de {$request['character_name']} para a guilda '{$guild_role['guild_name']}'",
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);

        // Criar notificação para o solicitante
        $stmt = $conn->prepare("
            INSERT INTO system_notifications 
            (user_id, title, message, type) 
            VALUES (?, ?, ?, 'guild')
        ");
        $stmt->execute([
            $request['user_id'],
            $action === 'accept' ? 'Solicitação Aceita' : 'Solicitação Rejeitada',
            $action === 'accept' 
                ? "Sua solicitação para entrar na guilda '{$guild_role['guild_name']}' foi aceita!"
                : "Sua solicitação para entrar na guilda '{$guild_role['guild_name']}' foi rejeitada."
        ]);

        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Solicitação ' . ($action === 'accept' ? 'aceita' : 'rejeitada') . ' com sucesso'
        ]);

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log('Erro ao processar solicitação: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao processar solicitação. Tente novamente mais tarde.'
    ]);
} 
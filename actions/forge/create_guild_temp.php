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
    if (!isset($data['name']) || !isset($data['description'])) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    $name = trim($data['name']);
    $description = trim($data['description']);

    // Validar nome
    if (strlen($name) < 3 || strlen($name) > 32) {
        echo json_encode(['success' => false, 'message' => 'Nome deve ter entre 3 e 32 caracteres']);
        exit;
    }

    // Validar descrição
    if (strlen($description) < 10 || strlen($description) > 500) {
        echo json_encode(['success' => false, 'message' => 'Descrição deve ter entre 10 e 500 caracteres']);
        exit;
    }

    $forge = new ForgeSystem($conn, $_SESSION['user_id']);
    $status = $forge->getCharacterStatus();

    if (!is_array($status) || !isset($status['character']['id'])) {
        throw new Exception('Status do personagem inválido');
    }

    // Verificar nível mínimo
    if ($status['character']['level'] < 20) {
        echo json_encode(['success' => false, 'message' => 'Nível mínimo requerido: 20']);
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

    // Verificar se nome já existe
    $stmt = $conn->prepare("SELECT id FROM forge_guilds WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['success' => false, 'message' => 'Nome já está em uso']);
        exit;
    }

    // Verificar custo
    $creation_cost = 1000;
    if ($status['character']['gold'] < $creation_cost) {
        echo json_encode([
            'success' => false, 
            'message' => "Ouro insuficiente. Necessário: {$creation_cost}"
        ]);
        exit;
    }

    $conn->beginTransaction();

    try {
        // Criar guilda
        $stmt = $conn->prepare("
            INSERT INTO forge_guilds 
            (name, description, leader_id, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$name, $description, $status['character']['id']]);
        $guild_id = $conn->lastInsertId();

        // Adicionar líder como membro
        $stmt = $conn->prepare("
            INSERT INTO guild_members 
            (guild_id, character_id, role, contribution_points) 
            VALUES (?, ?, 'leader', 100)
        ");
        $stmt->execute([$guild_id, $status['character']['id']]);

        // Deduzir custo
        $stmt = $conn->prepare("
            UPDATE forge_characters 
            SET gold = gold - ? 
            WHERE id = ?
        ");
        $stmt->execute([$creation_cost, $status['character']['id']]);

        // Registrar no log do sistema
        $stmt = $conn->prepare("
            INSERT INTO system_logs 
            (user_id, action_type, description, ip_address, user_agent) 
            VALUES (?, 'guild_creation', ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            "Criou a guilda '{$name}'",
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);

        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Guilda criada com sucesso',
            'guild_id' => $guild_id
        ]);

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log('Erro ao criar guilda: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao criar guilda. Tente novamente mais tarde.'
    ]);
} 
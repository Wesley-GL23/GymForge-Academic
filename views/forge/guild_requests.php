<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../classes/ForgeSystem.php';

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Você precisa estar logado para acessar esta página'
    ];
    header('Location: /forms/usuario/login.php');
    exit;
}

try {
    $forge = new ForgeSystem($conn, $_SESSION['user_id']);
    $status = $forge->getCharacterStatus();

    // Verificar se é líder ou oficial de alguma guilda
    $stmt = $conn->prepare("
        SELECT 
            g.*,
            gm.role,
            gm.contribution_points,
            (
                SELECT COUNT(*)
                FROM guild_join_requests
                WHERE guild_id = g.id AND status = 'pending'
            ) as pending_requests
        FROM guild_members gm
        JOIN forge_guilds g ON g.id = gm.guild_id
        WHERE gm.character_id = ? AND gm.role IN ('leader', 'officer')
    ");
    $stmt->execute([$status['character']['id']]);
    $guild_role = $stmt->fetch();

    if (!$guild_role) {
        $_SESSION['mensagem'] = [
            'tipo' => 'warning',
            'texto' => 'Você não tem permissão para acessar esta página'
        ];
        header('Location: /views/forge/guilds.php');
        exit;
    }

    // Buscar solicitações pendentes com informações detalhadas
    $stmt = $conn->prepare("
        SELECT 
            gjr.*,
            fc.level,
            fc.current_rank,
            fc.total_workouts,
            fc.total_exercises,
            fc.creation_date,
            u.nome,
            u.email,
            (
                SELECT COUNT(*)
                FROM muscle_tempering mt
                WHERE mt.character_id = fc.id AND mt.current_level >= 50
            ) as strong_muscles,
            (
                SELECT COUNT(*)
                FROM guild_members gm2
                WHERE gm2.character_id = fc.id
            ) as current_guilds,
            (
                SELECT COUNT(*)
                FROM guild_join_requests gjr2
                WHERE gjr2.character_id = fc.id 
                AND gjr2.status = 'rejected'
                AND gjr2.request_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ) as recent_rejections
        FROM guild_join_requests gjr
        JOIN forge_characters fc ON fc.id = gjr.character_id
        JOIN usuarios u ON u.id = fc.user_id
        WHERE gjr.guild_id = ? AND gjr.status = 'pending'
        ORDER BY gjr.request_date DESC
    ");
    $stmt->execute([$guild_role['id']]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Gerar token CSRF para as ações
    $csrf_token = generateCsrfToken();

} catch (Exception $e) {
    error_log('Erro na página de solicitações: ' . $e->getMessage());
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Ocorreu um erro ao carregar as solicitações. Tente novamente mais tarde.'
    ];
    header('Location: /views/forge/guilds.php');
    exit;
}
?>

<div class="forge-requests-container">
    <!-- Cabeçalho -->
    <div class="requests-header glass-effect">
        <div class="guild-info">
            <img src="<?php echo htmlspecialchars($guild_role['emblem_url']); ?>" 
                 alt="Emblema da Guilda" 
                 class="guild-emblem"
                 onerror="this.src='/assets/img/default_guild_emblem.png'">
            <div>
                <h1><?php echo htmlspecialchars($guild_role['name']); ?></h1>
                <div class="guild-stats">
                    <span class="role-badge"><?php echo ucfirst($guild_role['role']); ?></span>
                    <span class="points-badge">
                        <i class="fas fa-star"></i> 
                        <?php echo number_format($guild_role['contribution_points']); ?> pontos
                    </span>
                </div>
            </div>
        </div>
        <div class="requests-count">
            <div class="count-badge">
                <?php echo count($requests); ?> solicitações pendentes
            </div>
            <button class="refresh-btn" onclick="refreshRequests()">
                <i class="fas fa-sync-alt"></i> Atualizar
            </button>
        </div>
    </div>

    <!-- Lista de Solicitações -->
    <div class="requests-grid" id="requestsGrid">
        <?php foreach ($requests as $request): ?>
        <div class="request-card glass-effect" data-request-id="<?php echo $request['id']; ?>">
            <div class="request-header">
                <div class="requester-info">
                    <h3><?php echo htmlspecialchars($request['nome']); ?></h3>
                    <div class="badges">
                        <span class="rank-badge" style="background-color: <?php echo $forge_config['ranks'][$request['current_rank']]['badge_color']; ?>">
                            <?php echo $forge_config['ranks'][$request['current_rank']]['name']; ?>
                        </span>
                        <?php if ($request['current_guilds'] > 0): ?>
                        <span class="warning-badge">
                            <i class="fas fa-exclamation-triangle"></i> Já pertence a uma guilda
                        </span>
                        <?php endif; ?>
                        <?php if ($request['recent_rejections'] > 0): ?>
                        <span class="warning-badge">
                            <i class="fas fa-ban"></i> <?php echo $request['recent_rejections']; ?> rejeições recentes
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="request-date">
                    <div>Solicitado em <?php echo date('d/m/Y H:i', strtotime($request['request_date'])); ?></div>
                    <div class="account-age">
                        Conta criada em <?php echo date('d/m/Y', strtotime($request['creation_date'])); ?>
                    </div>
                </div>
            </div>

            <div class="character-stats">
                <div class="stat">
                    <i class="fas fa-star"></i>
                    <span class="value">Nível <?php echo $request['level']; ?></span>
                    <span class="label">Nível do Personagem</span>
                </div>
                <div class="stat">
                    <i class="fas fa-dumbbell"></i>
                    <span class="value"><?php echo number_format($request['total_workouts']); ?></span>
                    <span class="label">Treinos Totais</span>
                </div>
                <div class="stat">
                    <i class="fas fa-fire"></i>
                    <span class="value"><?php echo number_format($request['total_exercises']); ?></span>
                    <span class="label">Exercícios</span>
                </div>
                <div class="stat">
                    <i class="fas fa-hammer"></i>
                    <span class="value"><?php echo $request['strong_muscles']; ?></span>
                    <span class="label">Músculos Fortes</span>
                </div>
            </div>

            <?php if (!empty($request['message'])): ?>
            <div class="request-message">
                <i class="fas fa-quote-left"></i>
                <p><?php echo htmlspecialchars($request['message']); ?></p>
            </div>
            <?php endif; ?>

            <div class="request-actions">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <button class="accept-btn" onclick="handleRequest(<?php echo $request['id']; ?>, 'accept', '<?php echo $csrf_token; ?>')">
                    <i class="fas fa-check"></i> Aceitar
                </button>
                <button class="reject-btn" onclick="handleRequest(<?php echo $request['id']; ?>, 'reject', '<?php echo $csrf_token; ?>')">
                    <i class="fas fa-times"></i> Rejeitar
                </button>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($requests)): ?>
        <div class="no-requests glass-effect">
            <i class="fas fa-scroll"></i>
            <h3>Nenhuma Solicitação Pendente</h3>
            <p>Quando alguém solicitar entrada na guilda, aparecerá aqui.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Estilos CSS -->
<style>
.forge-requests-container {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.requests-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    border-radius: 15px;
}

.guild-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.guild-emblem {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
    background: rgba(0, 0, 0, 0.2);
}

.guild-stats {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

.role-badge, .points-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    font-size: 0.875rem;
}

.points-badge i {
    color: var(--forge-primary);
}

.requests-count {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.count-badge {
    font-size: 1.1rem;
    opacity: 0.8;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.refresh-btn {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: inherit;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.refresh-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

.requests-grid {
    display: grid;
    gap: 1.5rem;
}

.request-card {
    padding: 1.5rem;
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.request-card:hover {
    transform: translateY(-2px);
}

.request-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.requester-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.rank-badge, .warning-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.875rem;
}

.warning-badge {
    background: rgba(255, 87, 51, 0.2);
    color: #ff5733;
}

.request-date {
    font-size: 0.875rem;
    opacity: 0.7;
    text-align: right;
}

.account-age {
    margin-top: 0.25rem;
    font-size: 0.8rem;
}

.character-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.stat {
    text-align: center;
}

.stat i {
    font-size: 1.5rem;
    color: var(--forge-primary);
    margin-bottom: 0.5rem;
}

.stat .value {
    display: block;
    font-size: 1.25rem;
    font-weight: bold;
}

.stat .label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.request-message {
    margin: 1.5rem 0;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    position: relative;
}

.request-message i {
    position: absolute;
    top: -0.5rem;
    left: -0.5rem;
    color: var(--forge-primary);
    font-size: 1.5rem;
}

.request-message p {
    margin: 0;
    padding-left: 1.5rem;
    font-style: italic;
}

.request-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.request-actions button {
    flex: 1;
    padding: 0.75rem;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.accept-btn {
    background: var(--forge-primary);
    color: white;
}

.accept-btn:hover {
    background: var(--forge-primary-dark);
}

.reject-btn {
    background: rgba(255, 87, 51, 0.2);
    color: #ff5733;
}

.reject-btn:hover {
    background: rgba(255, 87, 51, 0.3);
}

.no-requests {
    text-align: center;
    padding: 3rem;
    border-radius: 15px;
}

.no-requests i {
    font-size: 3rem;
    color: var(--forge-primary);
    margin-bottom: 1rem;
}

.no-requests h3 {
    margin-bottom: 0.5rem;
}

.no-requests p {
    opacity: 0.8;
}

@media (max-width: 768px) {
    .forge-requests-container {
        padding: 1rem;
    }

    .requests-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .guild-info {
        flex-direction: column;
        text-align: center;
    }

    .request-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .request-date {
        text-align: center;
    }

    .character-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<!-- Scripts JavaScript -->
<script>
async function handleRequest(requestId, action, csrfToken) {
    try {
        const response = await fetch('/actions/forge/handle_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                request_id: requestId,
                action: action,
                csrf_token: csrfToken
            })
        });

        const data = await response.json();

        if (data.success) {
            // Remover o card da solicitação com animação
            const card = document.querySelector(`[data-request-id="${requestId}"]`);
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            setTimeout(() => {
                card.remove();
                
                // Atualizar contador
                const count = document.querySelectorAll('.request-card').length;
                document.querySelector('.count-badge').textContent = `${count} solicitações pendentes`;
                
                // Mostrar mensagem "sem solicitações" se necessário
                if (count === 0) {
                    const noRequests = document.createElement('div');
                    noRequests.className = 'no-requests glass-effect';
                    noRequests.innerHTML = `
                        <i class="fas fa-scroll"></i>
                        <h3>Nenhuma Solicitação Pendente</h3>
                        <p>Quando alguém solicitar entrada na guilda, aparecerá aqui.</p>
                    `;
                    document.getElementById('requestsGrid').appendChild(noRequests);
                }
            }, 300);

            // Mostrar notificação
            showNotification(data.message, 'success');
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        showNotification(error.message, 'error');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

async function refreshRequests() {
    const refreshBtn = document.querySelector('.refresh-btn');
    refreshBtn.disabled = true;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Atualizando...';
    
    try {
        const response = await fetch(window.location.href);
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        document.getElementById('requestsGrid').innerHTML = doc.getElementById('requestsGrid').innerHTML;
        showNotification('Lista atualizada com sucesso!', 'success');
    } catch (error) {
        showNotification('Erro ao atualizar lista. Tente novamente.', 'error');
    } finally {
        refreshBtn.disabled = false;
        refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Atualizar';
    }
}
</script>

<!-- Estilos para Notificações -->
<style>
.notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 1rem 2rem;
    border-radius: 10px;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    display: flex;
    align-items: center;
    gap: 1rem;
    z-index: 1000;
    animation: slideIn 0.3s ease;
    transition: opacity 0.3s ease;
}

.notification.success {
    border-left: 4px solid #4CAF50;
}

.notification.error {
    border-left: 4px solid #f44336;
}

.notification i {
    font-size: 1.25rem;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
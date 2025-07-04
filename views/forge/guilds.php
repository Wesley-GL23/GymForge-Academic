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

    if (!is_array($status) || !isset($status['character']['id'])) {
        throw new Exception('Status do personagem inválido');
    }

    // Buscar todas as guildas com estatísticas detalhadas
    $stmt = $conn->prepare("
        SELECT 
            g.*,
            COUNT(DISTINCT gm.id) as member_count,
            COALESCE(SUM(fc.total_xp), 0) as total_guild_xp,
            COALESCE(SUM(fc.total_workouts), 0) as total_workouts,
            COALESCE(SUM(fc.total_exercises), 0) as total_exercises,
            COALESCE(AVG(fc.level), 0) as avg_member_level,
            u.nome as leader_name,
            fc_leader.level as leader_level,
            fc_leader.current_rank as leader_rank,
            (
                SELECT COUNT(*)
                FROM guild_join_requests
                WHERE guild_id = g.id AND status = 'pending'
            ) as pending_requests
        FROM forge_guilds g
        LEFT JOIN guild_members gm ON gm.guild_id = g.id
        LEFT JOIN forge_characters fc ON fc.id = gm.character_id
        LEFT JOIN forge_characters fc_leader ON fc_leader.id = g.leader_id
        LEFT JOIN usuarios u ON u.id = fc_leader.user_id
        GROUP BY g.id
        ORDER BY total_guild_xp DESC
    ");
    $stmt->execute();
    $guilds = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Verificar se o usuário é líder de alguma guilda
    $stmt = $conn->prepare("
        SELECT 
            g.*,
            COUNT(DISTINCT gm.id) as member_count,
            COALESCE(SUM(fc.total_xp), 0) as total_guild_xp,
            (
                SELECT COUNT(*)
                FROM guild_join_requests
                WHERE guild_id = g.id AND status = 'pending'
            ) as pending_requests
        FROM forge_guilds g
        LEFT JOIN guild_members gm ON gm.guild_id = g.id
        LEFT JOIN forge_characters fc ON fc.id = gm.character_id
        WHERE g.leader_id = ?
        GROUP BY g.id
    ");
    $stmt->execute([$status['character']['id']]);
    $my_guild_as_leader = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    // Verificar se o usuário é membro de alguma guilda
    $stmt = $conn->prepare("
        SELECT 
            g.*,
            gm.role,
            gm.contribution_points,
            gm.joined_at,
            COUNT(DISTINCT gm2.id) as member_count,
            COALESCE(SUM(fc.total_xp), 0) as total_guild_xp,
            COALESCE(SUM(fc.total_workouts), 0) as total_workouts,
            COALESCE(SUM(fc.total_exercises), 0) as total_exercises,
            u_leader.nome as leader_name,
            fc_leader.level as leader_level,
            fc_leader.current_rank as leader_rank
        FROM guild_members gm
        JOIN forge_guilds g ON g.id = gm.guild_id
        LEFT JOIN guild_members gm2 ON gm2.guild_id = g.id
        LEFT JOIN forge_characters fc ON fc.id = gm2.character_id
        LEFT JOIN forge_characters fc_leader ON fc_leader.id = g.leader_id
        LEFT JOIN usuarios u_leader ON u_leader.id = fc_leader.user_id
        WHERE gm.character_id = ?
        GROUP BY g.id
    ");
    $stmt->execute([$status['character']['id']]);
    $my_guild_membership = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    // Verificar solicitações pendentes do usuário
    $stmt = $conn->prepare("
        SELECT guild_id 
        FROM guild_join_requests 
        WHERE character_id = ? AND status = 'pending'
    ");
    $stmt->execute([$status['character']['id']]);
    $pending_requests = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

    // Gerar token CSRF para as ações
    $csrf_token = generateCsrfToken();

} catch (Exception $e) {
    error_log('Erro na página de guildas: ' . $e->getMessage());
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Ocorreu um erro ao carregar as guildas. Tente novamente mais tarde.'
    ];
    header('Location: /index.php');
    exit;
}

// Definir valores padrão para evitar erros de undefined
$guild_config = [
    'min_level' => 10,
    'max_members' => 50,
    'roles' => [
        'leader' => ['icon' => 'crown'],
        'officer' => ['icon' => 'shield-alt'],
        'member' => ['icon' => 'user']
    ]
];
?>

<div class="forge-guilds-container">
    <!-- Cabeçalho da Página -->
    <div class="guilds-header glass-effect">
        <div class="header-content">
            <h1>Guildas da Forja</h1>
            <p class="guild-stats-summary">
                <?php echo count($guilds); ?> guildas ativas • 
                <?php echo array_sum(array_column($guilds, 'member_count')); ?> membros totais
            </p>
        </div>
        <?php if (!$my_guild_membership && isset($status['character']['level']) && $status['character']['level'] >= $guild_config['min_level']): ?>
        <button class="create-guild-btn" onclick="showCreateGuildModal()">
            <i class="fas fa-plus"></i> Criar Nova Guilda
        </button>
        <?php elseif (!isset($status['character']['level']) || $status['character']['level'] < $guild_config['min_level']): ?>
        <div class="level-requirement-notice">
            <i class="fas fa-lock"></i>
            Alcance o nível <?php echo $guild_config['min_level']; ?> para criar ou entrar em uma guilda
        </div>
        <?php endif; ?>
    </div>

    <!-- Minha Guilda (se for membro) -->
    <?php if ($my_guild_membership): ?>
    <div class="my-guild-section glass-effect">
        <div class="guild-banner" style="--guild-primary: <?php echo htmlspecialchars($my_guild_membership['primary_color'] ?? '#4A90E2'); ?>; --guild-secondary: <?php echo htmlspecialchars($my_guild_membership['secondary_color'] ?? '#2C3E50'); ?>">
            <div class="guild-header">
                <img src="<?php echo htmlspecialchars($my_guild_membership['emblem_url'] ?? '/assets/img/default_guild_emblem.png'); ?>" 
                     alt="Emblema da Guilda" 
                     class="guild-emblem"
                     onerror="this.src='/assets/img/default_guild_emblem.png'">
                <div class="guild-info">
                    <div class="guild-title">
                        <h2><?php echo htmlspecialchars($my_guild_membership['name']); ?></h2>
                        <div class="guild-badges">
                            <?php if (isset($my_guild_membership['role'])): ?>
                            <span class="guild-role <?php echo htmlspecialchars($my_guild_membership['role']); ?>">
                                <i class="fas fa-<?php echo $guild_config['roles'][$my_guild_membership['role']]['icon']; ?>"></i>
                                <?php echo ucfirst($my_guild_membership['role']); ?>
                            </span>
                            <?php endif; ?>
                            <?php if (isset($my_guild_membership['joined_at'])): ?>
                            <span class="member-since">
                                <i class="fas fa-calendar-alt"></i>
                                Membro desde <?php echo date('d/m/Y', strtotime($my_guild_membership['joined_at'])); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (isset($my_guild_membership['leader_name'])): ?>
                    <div class="guild-leader">
                        <span>Líder: <?php echo htmlspecialchars($my_guild_membership['leader_name']); ?></span>
                        <?php if (isset($my_guild_membership['leader_level'])): ?>
                        <span class="leader-level">Nível <?php echo $my_guild_membership['leader_level']; ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($my_guild_membership['role']) && $my_guild_membership['role'] === 'leader'): ?>
                <div class="guild-actions">
                    <button class="manage-guild-btn" onclick="showManageGuildModal(<?php echo $my_guild_membership['id']; ?>)">
                        <i class="fas fa-cog"></i> Gerenciar Guilda
                    </button>
                    <?php if (isset($my_guild_membership['pending_requests']) && $my_guild_membership['pending_requests'] > 0): ?>
                    <a href="/views/forge/guild_requests.php" class="view-requests-btn">
                        <i class="fas fa-user-plus"></i>
                        <?php echo $my_guild_membership['pending_requests']; ?> solicitações
                    </a>
                    <?php endif; ?>
                </div>
                <?php elseif (isset($my_guild_membership['role']) && $my_guild_membership['role'] === 'officer' && isset($my_guild_membership['pending_requests']) && $my_guild_membership['pending_requests'] > 0): ?>
                <a href="/views/forge/guild_requests.php" class="view-requests-btn">
                    <i class="fas fa-user-plus"></i>
                    <?php echo $my_guild_membership['pending_requests']; ?> solicitações
                </a>
                <?php endif; ?>
            </div>

            <div class="guild-stats">
                <div class="stat">
                    <i class="fas fa-users"></i>
                    <span class="value"><?php echo ($my_guild_membership['member_count'] ?? 0) . '/' . $guild_config['max_members']; ?></span>
                    <span class="label">Membros</span>
                </div>
                <div class="stat">
                    <i class="fas fa-star"></i>
                    <span class="value"><?php echo number_format($my_guild_membership['total_guild_xp'] ?? 0); ?></span>
                    <span class="label">XP Total</span>
                </div>
                <div class="stat">
                    <i class="fas fa-trophy"></i>
                    <span class="value"><?php echo number_format($my_guild_membership['total_workouts'] ?? 0); ?></span>
                    <span class="label">Treinos</span>
                </div>
                <div class="stat">
                    <i class="fas fa-fire"></i>
                    <span class="value"><?php echo number_format($my_guild_membership['total_exercises'] ?? 0); ?></span>
                    <span class="label">Exercícios</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Lista de Todas as Guildas -->
    <div class="guilds-section">
        <div class="guilds-filters glass-effect">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="guildSearch" placeholder="Buscar guilda..." onkeyup="filterGuilds()">
            </div>
            <div class="filter-options">
                <select id="guildSort" onchange="sortGuilds()">
                    <option value="xp">Ordenar por XP</option>
                    <option value="level">Ordenar por Nível</option>
                    <option value="members">Ordenar por Membros</option>
                    <option value="name">Ordenar por Nome</option>
                </select>
                <label class="filter-checkbox">
                    <input type="checkbox" id="showVacancies" onchange="filterGuilds()">
                    Mostrar apenas com vagas
                </label>
            </div>
        </div>

        <div class="guilds-grid" id="guildsGrid">
            <?php foreach ($guilds as $guild): ?>
            <div class="guild-card glass-effect" 
                 data-guild-id="<?php echo $guild['id']; ?>"
                 data-guild-name="<?php echo htmlspecialchars($guild['name']); ?>"
                 data-guild-members="<?php echo $guild['member_count']; ?>"
                 data-guild-xp="<?php echo $guild['total_guild_xp']; ?>"
                 data-guild-level="<?php echo $guild['avg_member_level']; ?>">
                
                <div class="guild-banner" style="--guild-primary: <?php echo htmlspecialchars($guild['primary_color'] ?? '#4A90E2'); ?>; --guild-secondary: <?php echo htmlspecialchars($guild['secondary_color'] ?? '#2C3E50'); ?>">
                    <img src="<?php echo htmlspecialchars($guild['emblem_url'] ?? '/assets/img/default_guild_emblem.png'); ?>" 
                         alt="Emblema da Guilda" 
                         class="guild-emblem"
                         onerror="this.src='/assets/img/default_guild_emblem.png'">
                    <h3><?php echo htmlspecialchars($guild['name']); ?></h3>
                </div>

                <div class="guild-info">
                    <div class="guild-leader">
                        <span>Líder: <?php echo htmlspecialchars($guild['leader_name'] ?? 'Desconhecido'); ?></span>
                        <?php if (isset($guild['leader_level'])): ?>
                        <span class="leader-level">Nível <?php echo $guild['leader_level']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="guild-stats">
                        <div class="stat">
                            <i class="fas fa-users"></i>
                            <span class="value"><?php echo $guild['member_count'] . '/' . $guild_config['max_members']; ?></span>
                            <span class="label">Membros</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-star"></i>
                            <span class="value"><?php echo number_format($guild['total_guild_xp']); ?></span>
                            <span class="label">XP Total</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-chart-line"></i>
                            <span class="value"><?php echo number_format($guild['avg_member_level'], 1); ?></span>
                            <span class="label">Nível Médio</span>
                        </div>
                    </div>

                    <?php if (!$my_guild_membership && isset($status['character']['level']) && $status['character']['level'] >= $guild_config['min_level']): ?>
                        <?php if ($guild['member_count'] < $guild_config['max_members']): ?>
                            <?php if (in_array($guild['id'], $pending_requests)): ?>
                            <button class="request-pending-btn" disabled>
                                <i class="fas fa-clock"></i> Solicitação Pendente
                            </button>
                            <?php else: ?>
                            <button class="request-join-btn" onclick="requestJoinGuild(<?php echo $guild['id']; ?>, '<?php echo $csrf_token; ?>')">
                                <i class="fas fa-sign-in-alt"></i> Solicitar Entrada
                            </button>
                            <?php endif; ?>
                        <?php else: ?>
                        <button class="guild-full-btn" disabled>
                            <i class="fas fa-users-slash"></i> Guilda Cheia
                        </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal de Criação de Guilda -->
<div id="createGuildModal" class="modal">
    <div class="modal-content glass-effect">
        <h2>Criar Nova Guilda</h2>
        <form id="createGuildForm" onsubmit="return createGuild(event)">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="guildName">Nome da Guilda</label>
                <input type="text" id="guildName" name="name" required 
                       minlength="3" maxlength="100" pattern="[A-Za-zÀ-ÿ0-9\s\-_]{3,100}"
                       title="O nome deve ter entre 3 e 100 caracteres e pode conter letras, números, espaços, hífens e underscores">
            </div>

            <div class="form-group">
                <label for="guildDescription">Descrição</label>
                <textarea id="guildDescription" name="description" required
                          minlength="10" maxlength="500"
                          placeholder="Descreva o propósito e objetivos da sua guilda..."></textarea>
            </div>

            <div class="form-group">
                <label for="guildEmblem">Emblema da Guilda</label>
                <input type="file" id="guildEmblem" name="emblem" accept="image/*"
                       onchange="previewEmblem(this)">
                <div id="emblemPreview" class="emblem-preview"></div>
            </div>

            <div class="form-group colors">
                <div>
                    <label for="primaryColor">Cor Primária</label>
                    <input type="color" id="primaryColor" name="primary_color" 
                           value="#4A90E2">
                </div>
                <div>
                    <label for="secondaryColor">Cor Secundária</label>
                    <input type="color" id="secondaryColor" name="secondary_color"
                           value="#2C3E50">
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeModal('createGuildModal')" class="cancel-btn">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="create-btn">
                    <i class="fas fa-plus"></i> Criar Guilda
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Funções JavaScript aqui...
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?> 

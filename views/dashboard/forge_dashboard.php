<?php
require_once '../../includes/header.php';
require_once '../../config/forge_system.php';
require_once '../../classes/ForgeSystem.php';

// Inicializar sistema
$forge = new ForgeSystem($conn, $_SESSION['user_id']);
$character = $forge->getCharacterStatus();
?>

<div class="dashboard-container">
    <!-- Barra Lateral -->
    <div class="sidebar glass-effect">
        <div class="character-info">
            <div class="character-avatar">
                <div class="rank-badge" style="background: <?php echo $RANKS[$character['current_rank']]['cor']; ?>">
                    <?php echo $character['level']; ?>
                </div>
                <img src="/assets/img/ranks/<?php echo $character['current_rank']; ?>.png" alt="Avatar">
            </div>
            <h3><?php echo $_SESSION['user_name']; ?></h3>
            <p class="rank-name"><?php echo $RANKS[$character['current_rank']]['nome']; ?></p>
            
            <!-- Barra de XP -->
            <div class="xp-bar">
                <div class="progress">
                    <div class="progress-bar" style="width: <?php echo $character['xp_percentage']; ?>%"></div>
                </div>
                <span class="xp-text"><?php echo number_format($character['current_xp']); ?> / <?php echo number_format($character['xp_for_next']); ?> XP</span>
            </div>
        </div>

        <!-- Menu de Navegação -->
        <nav class="dashboard-nav">
            <a href="#overview" class="active">
                <i class="fas fa-home"></i> Visão Geral
            </a>
            <a href="#character">
                <i class="fas fa-user"></i> Personagem
            </a>
            <a href="#guild">
                <i class="fas fa-users"></i> Guilda
            </a>
            <a href="#training">
                <i class="fas fa-dumbbell"></i> Treinos
            </a>
            <a href="#achievements">
                <i class="fas fa-trophy"></i> Conquistas
            </a>
            <a href="#events">
                <i class="fas fa-calendar"></i> Eventos
            </a>
        </nav>
    </div>

    <!-- Conteúdo Principal -->
    <div class="main-content">
        <!-- Cabeçalho -->
        <header class="dashboard-header glass-effect">
            <div class="header-stats">
                <div class="stat-item">
                    <i class="fas fa-dumbbell"></i>
                    <span class="stat-value"><?php echo number_format($character['total_workouts']); ?></span>
                    <span class="stat-label">Treinos</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-fire"></i>
                    <span class="stat-value"><?php echo number_format($character['total_exercises']); ?></span>
                    <span class="stat-label">Exercícios</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-bolt"></i>
                    <span class="stat-value"><?php echo $character['streak_days']; ?></span>
                    <span class="stat-label">Dias Seguidos</span>
                </div>
            </div>

            <!-- Notificações -->
            <div class="notifications-dropdown">
                <button class="btn btn-dark">
                    <i class="fas fa-bell"></i>
                    <?php if ($character['unread_notifications'] > 0): ?>
                    <span class="notification-badge"><?php echo $character['unread_notifications']; ?></span>
                    <?php endif; ?>
                </button>
            </div>
        </header>

        <!-- Grade de Cards -->
        <div class="dashboard-grid">
            <!-- Progresso Diário -->
            <div class="dashboard-card glass-effect">
                <h3>Progresso Diário</h3>
                <div class="daily-goals">
                    <div class="goal-item">
                        <div class="goal-progress" style="--progress: <?php echo ($character['daily_exercises'] / 5) * 100; ?>%">
                            <span class="goal-value"><?php echo $character['daily_exercises']; ?>/5</span>
                        </div>
                        <span class="goal-label">Exercícios</span>
                    </div>
                    <div class="goal-item">
                        <div class="goal-progress" style="--progress: <?php echo ($character['daily_xp'] / 1000) * 100; ?>%">
                            <span class="goal-value"><?php echo $character['daily_xp']; ?>/1000</span>
                        </div>
                        <span class="goal-label">XP</span>
                    </div>
                </div>
            </div>

            <!-- Têmpera dos Músculos -->
            <div class="dashboard-card glass-effect">
                <h3>Têmpera dos Músculos</h3>
                <div class="muscle-grid">
                    <?php foreach ($character['muscles'] as $muscle): ?>
                    <div class="muscle-item" style="--tempering-color: <?php echo $TEMPERING_STAGES[$muscle['visual_stage']]['cor']; ?>">
                        <div class="muscle-icon">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="muscle-info">
                            <span class="muscle-name"><?php echo $MUSCLE_GROUPS[$muscle['muscle_group']]; ?></span>
                            <div class="tempering-bar">
                                <div class="progress" style="width: <?php echo $muscle['current_level']; ?>%"></div>
                            </div>
                            <span class="tempering-stage"><?php echo $TEMPERING_STAGES[$muscle['visual_stage']]['nome']; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Atividades da Guilda -->
            <?php if ($character['guild']): ?>
            <div class="dashboard-card glass-effect">
                <h3>Atividades da Guilda</h3>
                <div class="guild-activities">
                    <?php foreach ($character['guild']['recent_activities'] as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-<?php echo $activity['icon']; ?>"></i>
                        </div>
                        <div class="activity-info">
                            <span class="activity-title"><?php echo $activity['title']; ?></span>
                            <span class="activity-time"><?php echo $activity['time']; ?></span>
                        </div>
                        <div class="activity-points">+<?php echo $activity['points']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Próximos Eventos -->
            <div class="dashboard-card glass-effect">
                <h3>Próximos Eventos</h3>
                <div class="events-list">
                    <?php foreach ($character['upcoming_events'] as $event): ?>
                    <div class="event-item">
                        <div class="event-time">
                            <span class="event-date"><?php echo date('d/m', strtotime($event['start_date'])); ?></span>
                            <span class="event-hour"><?php echo date('H:i', strtotime($event['start_date'])); ?></span>
                        </div>
                        <div class="event-info">
                            <span class="event-title"><?php echo $event['title']; ?></span>
                            <span class="event-type"><?php echo $event['type']; ?></span>
                        </div>
                        <button class="btn btn-primary btn-sm">Participar</button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos do Dashboard */
.dashboard-container {
    display: flex;
    min-height: 100vh;
    background: var(--blue-night);
    color: var(--white);
}

/* Barra Lateral */
.sidebar {
    width: 280px;
    padding: 2rem;
    background: rgba(255, 255, 255, 0.05);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.character-info {
    text-align: center;
    margin-bottom: 2rem;
}

.character-avatar {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 1rem;
}

.character-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.rank-badge {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    border: 2px solid var(--blue-night);
}

.xp-bar {
    margin-top: 1rem;
}

.xp-bar .progress {
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    margin-bottom: 0.5rem;
}

.xp-bar .progress-bar {
    background: var(--blue-royal);
    border-radius: 3px;
    transition: width 0.3s ease;
}

/* Menu de Navegação */
.dashboard-nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.dashboard-nav a {
    padding: 1rem;
    color: var(--white);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.dashboard-nav a:hover,
.dashboard-nav a.active {
    background: rgba(255, 255, 255, 0.1);
}

.dashboard-nav i {
    margin-right: 1rem;
}

/* Conteúdo Principal */
.main-content {
    flex: 1;
    padding: 2rem;
}

/* Cabeçalho */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    margin-bottom: 2rem;
    border-radius: 15px;
}

.header-stats {
    display: flex;
    gap: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--blue-royal);
}

/* Grade de Cards */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.dashboard-card {
    padding: 1.5rem;
    border-radius: 15px;
}

/* Progresso Diário */
.daily-goals {
    display: flex;
    gap: 2rem;
    margin-top: 1rem;
}

.goal-item {
    flex: 1;
    text-align: center;
}

.goal-progress {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    border-radius: 50%;
    background: conic-gradient(
        var(--blue-royal) calc(var(--progress) * 1%),
        rgba(255, 255, 255, 0.1) 0
    );
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Têmpera dos Músculos */
.muscle-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.muscle-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.muscle-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--tempering-color);
    display: flex;
    align-items: center;
    justify-content: center;
}

.tempering-bar {
    height: 4px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    margin: 0.5rem 0;
}

.tempering-bar .progress {
    height: 100%;
    background: var(--tempering-color);
    border-radius: 2px;
    transition: width 0.3s ease;
}

/* Atividades da Guilda */
.guild-activities {
    margin-top: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.activity-info {
    flex: 1;
}

.activity-points {
    color: var(--blue-royal);
    font-weight: bold;
}

/* Eventos */
.events-list {
    margin-top: 1rem;
}

.event-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.event-time {
    text-align: center;
    min-width: 60px;
}

.event-date {
    display: block;
    font-weight: bold;
}

.event-info {
    flex: 1;
}

.event-title {
    display: block;
    font-weight: bold;
}

.event-type {
    font-size: 0.9rem;
    opacity: 0.8;
}
</style>

<?php require_once '../../includes/footer.php'; ?>

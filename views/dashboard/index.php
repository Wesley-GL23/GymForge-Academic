<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/training_functions.php';
require_once __DIR__ . '/../../includes/exercise_functions.php';

// Verifica se o usuário está logado
if (!estaLogado()) {
    header('Location: /forms/usuario/login.php');
    exit;
}

// Busca informações do usuário
$usuario_id = $_SESSION['usuario_id'];
$nivel = $_SESSION['nivel'];

// Busca treinos ativos
$treinos_ativos = listar_treinos($usuario_id, 'ativo', 3);

// Busca últimos exercícios adicionados
listar_exercicios();

$titulo = "Dashboard";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <nav class="dashboard-sidebar">
        <div class="sidebar-header">
            <a href="/GymForge-Academic/" class="sidebar-home-link">
                <img src="/assets/img/logo_small.png" alt="GymForge" class="sidebar-logo" style="width:40px;height:40px;object-fit:contain;">
            </a>
            <h3>GymForge</h3>
        </div>

        <ul class="sidebar-menu">
            <li><a href="/GymForge-Academic/" class="sidebar-link"><i class="fas fa-home me-2"></i>Página Inicial</a></li>
            <li class="active">
                <a href="/GymForge-Academic/views/dashboard/"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li>
                <a href="/GymForge-Academic/views/treinos/"><i class="fas fa-dumbbell"></i> Meus Treinos</a>
            </li>
            <li>
                <a href="/GymForge-Academic/views/exercicios/biblioteca.php"><i class="fas fa-book"></i> Biblioteca de Exercícios</a>
            </li>
            <li>
                <a href="/GymForge-Academic/views/forge/character.php"><i class="fas fa-user-ninja"></i> Meu Personagem</a>
            </li>
            <li>
                <a href="/GymForge-Academic/views/forge/guilds.php"><i class="fas fa-users"></i> Guildas</a>
            </li>
            <?php if ($nivel === 'admin'): ?>
            <li class="nav-header">Administração</li>
            <li>
                <a href="/GymForge-Academic/views/admin/exercicios.php"><i class="fas fa-cog"></i> Gerenciar Exercícios</a>
            </li>
            <li>
                <a href="/GymForge-Academic/views/admin/usuarios.php"><i class="fas fa-users-cog"></i> Gerenciar Usuários</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="dashboard-main">
        <div class="dashboard-header">
            <div class="welcome-message">
                <h1>Bem-vindo ao GymForge</h1>
                <p>Continue sua jornada de transformação</p>
            </div>
            <div class="header-actions">
                <a href="/GymForge-Academic/forms/treino/form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Treino
                </a>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Treinos Ativos -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Treinos Ativos</h3>
                    <a href="/GymForge-Academic/views/treinos/" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    <?php if (empty($treinos_ativos)): ?>
                        <p class="text-muted">Nenhum treino ativo no momento.</p>
                        <a href="/GymForge-Academic/forms/treino/form.php" class="btn btn-primary btn-sm">
                            Criar Meu Primeiro Treino
                        </a>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($treinos_ativos as $treino): ?>
                                <a href="/GymForge-Academic/views/treinos/visualizar.php?id=<?php echo $treino['id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($treino['nome']); ?></h5>
                                        <small><?php echo date('d/m/Y', strtotime($treino['data_inicio'])); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars(substr($treino['descricao'], 0, 100)); ?>...</p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Últimos Exercícios -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Últimos Exercícios</h3>
                    <a href="/GymForge-Academic/views/exercicios/biblioteca.php" class="btn btn-sm btn-outline-primary">Ver Biblioteca</a>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <?php foreach ($ultimos_exercicios as $exercicio): ?>
                            <div class="col">
                                <div class="card h-100">
                                    <?php if (!empty($exercicio['gif_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($exercicio['gif_url']); ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($exercicio['nome']); ?>">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($exercicio['nome']); ?></h5>
                                        <p class="card-text">
                                            <span class="badge bg-primary"><?php echo ucfirst($exercicio['categoria']); ?></span>
                                            <span class="badge bg-secondary"><?php echo ucfirst($exercicio['grupo_muscular']); ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Sistema Forge -->
            <div class="dashboard-card forge-card">
                <div class="card-header">
                    <h3>Sistema Forge</h3>
                    <a href="/GymForge-Academic/views/forge/character.php" class="btn btn-sm btn-outline-primary">Meu Personagem</a>
                </div>
                <div class="card-body">
                    <div class="forge-stats">
                        <div class="stat-item">
                            <i class="fas fa-fire"></i>
                            <span>Têmpera</span>
                            <strong>Nível 1</strong>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span>Guilda</span>
                            <strong>-</strong>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-trophy"></i>
                            <span>Conquistas</span>
                            <strong>0/50</strong>
                        </div>
                    </div>
                    <div class="forge-actions mt-4">
                        <a href="/GymForge-Academic/views/forge/guilds.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search"></i> Procurar Guilda
                        </a>
                        <a href="/GymForge-Academic/views/forge/events.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar"></i> Eventos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
/* Dashboard Layout */
.dashboard-container {
    display: flex;
    min-height: 100vh;
    background: var(--blue-night);
}

/* Sidebar */
.dashboard-sidebar {
    width: 250px;
    background: var(--blue-steel);
    padding: 1rem;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
}

.sidebar-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 1rem;
}

.sidebar-logo {
    width: 40px;
    height: 40px;
}

.sidebar-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-nav li {
    margin-bottom: 0.5rem;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: background-color 0.3s;
}

.sidebar-nav a:hover,
.sidebar-nav li.active a {
    background: rgba(255,255,255,0.1);
}

.nav-header {
    color: var(--forge-orange);
    font-size: 0.8rem;
    text-transform: uppercase;
    padding: 1rem;
    margin-top: 1rem;
}

/* Main Content */
.dashboard-main {
    flex: 1;
    margin-left: 250px;
    padding: 2rem;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.welcome-message h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.welcome-message p {
    color: var(--text-muted);
}

/* Dashboard Grid */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.dashboard-card {
    background: var(--blue-steel);
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.card-header h3 {
    margin: 0;
    font-size: 1.25rem;
}

.card-body {
    padding: 1rem;
}

/* Forge Card */
.forge-card {
    grid-column: span 2;
}

.forge-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(0,0,0,0.2);
    border-radius: 10px;
}

.stat-item i {
    font-size: 2rem;
    color: var(--forge-orange);
    margin-bottom: 0.5rem;
}

.stat-item span {
    display: block;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.stat-item strong {
    display: block;
    font-size: 1.5rem;
}

.forge-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-sidebar {
        width: 60px;
        padding: 0.5rem;
    }

    .sidebar-header h3,
    .nav-header,
    .sidebar-nav a span {
        display: none;
    }

    .dashboard-main {
        margin-left: 60px;
    }

    .forge-card {
        grid-column: span 1;
    }
}
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
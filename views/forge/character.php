<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../classes/ForgeSystem.php';

$forge = new ForgeSystem($conn, $_SESSION['user_id']);
$status = $forge->getCharacterStatus();

// Preparar dados para o gráfico de radar dos atributos
$attributes = [
    $status['character']['strength'],
    $status['character']['endurance'],
    $status['character']['technique'],
    $status['character']['wisdom']
];

$attribute_labels = ['Força', 'Resistência', 'Técnica', 'Sabedoria'];

// Preparar dados dos músculos para o mapa de calor
$muscle_levels = [];
foreach ($status['muscles'] as $muscle) {
    $muscle_levels[$muscle['muscle_group']] = $muscle['current_level'];
}
?>

<div class="forge-character-container">
    <!-- Cabeçalho do Personagem -->
    <div class="character-header glass-effect">
        <div class="rank-badge" style="background-color: <?php echo $forge_config['ranks'][$status['character']['current_rank']]['badge_color']; ?>">
            <h2><?php echo $forge_config['ranks'][$status['character']['current_rank']]['name']; ?></h2>
            <div class="level">Nível <?php echo $status['character']['level']; ?></div>
        </div>
        
        <?php if ($status['guild']): ?>
        <div class="guild-info">
            <img src="<?php echo htmlspecialchars($status['guild']['emblem_url']); ?>" alt="Emblema da Guilda" class="guild-emblem">
            <div class="guild-details">
                <h3><?php echo htmlspecialchars($status['guild']['name']); ?></h3>
                <span class="guild-role"><?php echo ucfirst($status['guild']['role']); ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Grid Principal -->
    <div class="character-grid">
        <!-- Gráfico de Radar dos Atributos -->
        <div class="attributes-chart glass-effect">
            <h3>Atributos do Ferreiro</h3>
            <canvas id="attributesChart"></canvas>
            <?php if ($status['character']['attribute_points'] > 0): ?>
            <div class="available-points">
                Pontos Disponíveis: <?php echo $status['character']['attribute_points']; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Mapa de Calor do Corpo -->
        <div class="body-heatmap glass-effect">
            <h3>Têmpera Muscular</h3>
            <div class="body-container">
                <img src="/assets/img/body-outline.png" alt="Silhueta do Corpo" class="body-outline">
                <div class="muscle-highlights">
                    <?php foreach ($muscle_levels as $muscle => $level): ?>
                    <div 
                        class="muscle-highlight" 
                        data-muscle="<?php echo $muscle; ?>"
                        style="
                            --muscle-glow: <?php echo getVisualStageColor($level); ?>;
                            --muscle-intensity: <?php echo $level/100; ?>;
                        "
                    ></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Lista de Conquistas -->
        <div class="achievements-list glass-effect">
            <h3>Conquistas Lendárias</h3>
            <div class="achievements-grid">
                <?php foreach ($status['achievements'] as $achievement): ?>
                <div class="achievement-card <?php echo $achievement['completed'] ? 'completed' : ''; ?>">
                    <i class="fas fa-<?php echo $forge_config['special_achievements'][$achievement['achievement_code']]['icon']; ?>"></i>
                    <div class="achievement-info">
                        <h4><?php echo $forge_config['special_achievements'][$achievement['achievement_code']]['name']; ?></h4>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo $achievement['progress']; ?>%"></div>
                        </div>
                        <span class="progress-text"><?php echo $achievement['progress']; ?>%</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="stats-panel glass-effect">
            <h3>Estatísticas da Forja</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <i class="fas fa-dumbbell"></i>
                    <span class="stat-value"><?php echo number_format($status['character']['total_workouts']); ?></span>
                    <span class="stat-label">Treinos Totais</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-fire"></i>
                    <span class="stat-value"><?php echo number_format($status['character']['total_exercises']); ?></span>
                    <span class="stat-label">Exercícios</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-star"></i>
                    <span class="stat-value"><?php echo number_format($status['character']['total_xp']); ?></span>
                    <span class="stat-label">XP Total</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos CSS -->
<style>
.forge-character-container {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.character-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.rank-badge {
    padding: 1rem 2rem;
    border-radius: 10px;
    color: white;
    text-align: center;
}

.rank-badge h2 {
    margin: 0;
    font-size: 1.5rem;
}

.level {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-top: 0.5rem;
}

.guild-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.guild-emblem {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.character-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
}

.attributes-chart {
    grid-column: 1;
    grid-row: 1;
}

.body-heatmap {
    grid-column: 2;
    grid-row: 1 / span 2;
}

.achievements-list {
    grid-column: 1;
    grid-row: 2;
}

.stats-panel {
    grid-column: 1 / span 2;
    grid-row: 3;
}

.body-container {
    position: relative;
    width: 100%;
    height: 600px;
}

.body-outline {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.muscle-highlight {
    position: absolute;
    background: radial-gradient(
        circle at center,
        var(--muscle-glow) calc(var(--muscle-intensity) * 100%),
        transparent
    );
    mix-blend-mode: screen;
    pointer-events: none;
}

.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.achievement-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.achievement-card.completed {
    background: rgba(0, 255, 0, 0.1);
}

.achievement-card i {
    font-size: 2rem;
    color: var(--muscle-glow, white);
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    margin: 0.5rem 0;
}

.progress {
    height: 100%;
    background: linear-gradient(90deg, #FF4D00, #FFD700);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    text-align: center;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.stat-item i {
    font-size: 2rem;
    color: #FF4D00;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
}

.stat-label {
    opacity: 0.8;
}

/* Animações */
@keyframes glow {
    0% { filter: brightness(1); }
    50% { filter: brightness(1.2); }
    100% { filter: brightness(1); }
}

.muscle-highlight {
    animation: glow 2s ease-in-out infinite;
}

.achievement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Radar dos Atributos
const ctx = document.getElementById('attributesChart').getContext('2d');
new Chart(ctx, {
    type: 'radar',
    data: {
        labels: <?php echo json_encode($attribute_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($attributes); ?>,
            backgroundColor: 'rgba(255, 77, 0, 0.2)',
            borderColor: '#FF4D00',
            pointBackgroundColor: '#FF4D00',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#FF4D00'
        }]
    },
    options: {
        scales: {
            r: {
                angleLines: {
                    color: 'rgba(255, 255, 255, 0.2)'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.2)'
                },
                pointLabels: {
                    color: 'white'
                },
                ticks: {
                    color: 'white',
                    backdropColor: 'transparent'
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Função para posicionar os highlights dos músculos
const musclePositions = {
    chest: { top: '25%', left: '50%', width: '30%', height: '15%' },
    back: { top: '25%', left: '50%', width: '30%', height: '20%' },
    shoulders: { top: '20%', left: '50%', width: '40%', height: '10%' },
    biceps: { top: '30%', left: '25%', width: '10%', height: '15%' },
    triceps: { top: '30%', left: '75%', width: '10%', height: '15%' },
    forearms: { top: '40%', left: '20%', width: '8%', height: '12%' },
    abs: { top: '40%', left: '50%', width: '15%', height: '20%' },
    obliques: { top: '45%', left: '50%', width: '20%', height: '15%' },
    quads: { top: '60%', left: '50%', width: '25%', height: '20%' },
    hamstrings: { top: '65%', left: '50%', width: '25%', height: '15%' },
    calves: { top: '80%', left: '50%', width: '15%', height: '15%' },
    glutes: { top: '55%', left: '50%', width: '20%', height: '10%' }
};

document.querySelectorAll('.muscle-highlight').forEach(highlight => {
    const muscle = highlight.dataset.muscle;
    const position = musclePositions[muscle];
    
    Object.entries(position).forEach(([prop, value]) => {
        highlight.style[prop] = value;
    });
});
</script>

<?php
function getVisualStageColor($level) {
    if ($level >= 100) return '#FF4D00';
    if ($level >= 75) return '#00BFFF';
    if ($level >= 50) return '#4682B4';
    if ($level >= 25) return '#FF8C00';
    return '#CD7F32';
}

require_once __DIR__ . '/../../includes/footer.php';
?> 
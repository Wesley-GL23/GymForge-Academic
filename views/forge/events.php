<?php
require_once '../../includes/header.php';
require_once '../../config/forge_events.php';
require_once '../../classes/ForgeSystem.php';
require_once '../../classes/GuildActivitySystem.php';

// Inicializar sistemas
$forge = new ForgeSystem($conn, $_SESSION['user_id']);
$guildActivity = new GuildActivitySystem($conn, $forge->getCharacterId());

// Buscar eventos ativos
$activeEvents = $guildActivity->getActiveEvents();
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Eventos Ativos -->
        <div class="col-md-8">
            <div class="card bg-dark text-white glass-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Eventos Ativos</h5>
                    <?php if ($guildActivity->hasGuildPermission(['leader', 'officer'])): ?>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createEventModal">
                        <i class="fas fa-plus"></i> Novo Evento
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($activeEvents)): ?>
                        <p class="text-center">Nenhum evento ativo no momento.</p>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($activeEvents as $event): ?>
                                <div class="col-md-6">
                                    <div class="event-card glass-effect p-4 rounded">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h5 class="event-title"><?= htmlspecialchars($event['title']) ?></h5>
                                            <span class="badge bg-primary"><?= $event['event_type'] ?></span>
                                        </div>
                                        <p class="event-description mb-3"><?= htmlspecialchars($event['description']) ?></p>
                                        <div class="event-details mb-3">
                                            <small class="text-muted">
                                                Criado por: <?= htmlspecialchars($event['creator_name']) ?><br>
                                                Participantes: <?= $event['participants_count'] ?>
                                            </small>
                                        </div>
                                        <div class="progress mb-3" style="height: 5px;">
                                            <?php
                                            $start = strtotime($event['start_date']);
                                            $end = strtotime($event['end_date']);
                                            $now = time();
                                            $progress = ($now - $start) / ($end - $start) * 100;
                                            ?>
                                            <div class="progress-bar bg-primary" style="width: <?= $progress ?>%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Termina em: <?= date('d/m/Y H:i', strtotime($event['end_date'])) ?>
                                            </small>
                                            <button class="btn btn-outline-primary btn-sm" onclick="participateInEvent(<?= $event['id'] ?>)">
                                                Participar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Próximos Eventos -->
            <div class="card bg-dark text-white glass-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Próximos Eventos</h5>
                </div>
                <div class="card-body">
                    <div class="upcoming-events">
                        <!-- Lista de próximos eventos aqui -->
                    </div>
                </div>
            </div>

            <!-- Minhas Participações -->
            <div class="card bg-dark text-white glass-card">
                <div class="card-header">
                    <h5 class="mb-0">Minhas Participações</h5>
                </div>
                <div class="card-body">
                    <div class="my-participations">
                        <!-- Lista de participações aqui -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Criação de Evento -->
<div class="modal fade" id="createEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Criar Novo Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createEventForm">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-control" name="type" required>
                            <option value="daily">Desafio Diário</option>
                            <option value="weekly">Desafio Semanal</option>
                            <option value="special">Evento Especial</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Início</label>
                            <input type="datetime-local" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Fim</label>
                            <input type="datetime-local" class="form-control" name="end_date" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="createEvent()">Criar Evento</button>
            </div>
        </div>
    </div>
</div>

<script>
function participateInEvent(eventId) {
    fetch('/actions/forge/participate_event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ event_id: eventId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function createEvent() {
    const form = document.getElementById('createEventForm');
    const formData = new FormData(form);
    
    fetch('/actions/forge/create_event.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>

<?php require_once '../../includes/footer.php'; ?> 
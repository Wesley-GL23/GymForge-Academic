<?php
/**
 * Configuração do Sistema de Eventos da Forja
 * Define tipos de eventos, recompensas e requisitos
 */

// Tipos de Eventos
const FORGE_EVENT_TYPES = [
    'daily' => [
        'name' => 'Desafio Diário',
        'duration' => 24 * 3600, // 24 horas
        'cooldown' => 24 * 3600, // 24 horas
        'max_participants' => null,
        'base_xp' => 100,
        'base_points' => 50,
    ],
    'weekly' => [
        'name' => 'Desafio Semanal',
        'duration' => 7 * 24 * 3600, // 7 dias
        'cooldown' => 7 * 24 * 3600, // 7 dias
        'max_participants' => null,
        'base_xp' => 500,
        'base_points' => 250,
    ],
    'special' => [
        'name' => 'Evento Especial',
        'duration' => 3 * 24 * 3600, // 3 dias
        'cooldown' => 0, // Sem cooldown
        'max_participants' => 100,
        'base_xp' => 1000,
        'base_points' => 500,
    ]
];

// Tipos de Desafios
const FORGE_CHALLENGES = [
    'workout_streak' => [
        'name' => 'Sequência de Treinos',
        'description' => 'Complete uma sequência de treinos consecutivos',
        'type' => 'progress',
        'target' => 5,
        'multiplier' => 1.2
    ],
    'muscle_mastery' => [
        'name' => 'Maestria Muscular',
        'description' => 'Alcance um nível específico de têmpera em um grupo muscular',
        'type' => 'achievement',
        'target' => 1,
        'multiplier' => 1.5
    ],
    'guild_contribution' => [
        'name' => 'Contribuição para Guilda',
        'description' => 'Contribua com pontos para sua guilda',
        'type' => 'contribution',
        'target' => 1000,
        'multiplier' => 1.3
    ],
    'exercise_variety' => [
        'name' => 'Variedade de Exercícios',
        'description' => 'Complete exercícios diferentes',
        'type' => 'unique_count',
        'target' => 10,
        'multiplier' => 1.4
    ]
];

// Recompensas dos Eventos
const FORGE_REWARDS = [
    'xp_boost' => [
        'name' => 'Boost de XP',
        'description' => 'Aumenta o XP ganho em 50% por 24 horas',
        'duration' => 24 * 3600,
        'effect' => ['type' => 'multiplier', 'value' => 1.5]
    ],
    'tempering_boost' => [
        'name' => 'Boost de Têmpera',
        'description' => 'Aumenta a velocidade de têmpera em 100% por 12 horas',
        'duration' => 12 * 3600,
        'effect' => ['type' => 'multiplier', 'value' => 2.0]
    ],
    'guild_points' => [
        'name' => 'Pontos de Guilda',
        'description' => 'Pontos extras para sua guilda',
        'effect' => ['type' => 'points', 'value' => 100]
    ],
    'unique_title' => [
        'name' => 'Título Único',
        'description' => 'Título especial para seu perfil',
        'effect' => ['type' => 'cosmetic', 'value' => 'title']
    ],
    'aura_effect' => [
        'name' => 'Efeito de Aura',
        'description' => 'Efeito visual especial para seu personagem',
        'effect' => ['type' => 'cosmetic', 'value' => 'aura']
    ]
];

// Efeitos Visuais dos Eventos
const FORGE_VISUAL_EFFECTS = [
    'flame_aura' => [
        'name' => 'Aura Flamejante',
        'css_class' => 'flame-aura',
        'particle_effect' => 'flame',
        'color' => '#ff4400'
    ],
    'frost_aura' => [
        'name' => 'Aura Gélida',
        'css_class' => 'frost-aura',
        'particle_effect' => 'frost',
        'color' => '#00ccff'
    ],
    'thunder_aura' => [
        'name' => 'Aura Trovejante',
        'css_class' => 'thunder-aura',
        'particle_effect' => 'thunder',
        'color' => '#ffcc00'
    ],
    'void_aura' => [
        'name' => 'Aura do Vazio',
        'css_class' => 'void-aura',
        'particle_effect' => 'void',
        'color' => '#9900ff'
    ]
];

// Classe para gerenciar eventos
class ForgeEventManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Cria um novo evento
    public function createEvent($type, $title, $description, $start_time, $end_time, $requirements = [], $rewards = []) {
        if (!isset(FORGE_EVENT_TYPES[$type])) {
            throw new Exception('Tipo de evento inválido');
        }
        
        $eventConfig = FORGE_EVENT_TYPES[$type];
        
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO forge_events (
                    title, description, event_type, start_time, end_time,
                    requirements, rewards, max_participants
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $title,
                $description,
                $type,
                $start_time,
                $end_time,
                json_encode($requirements),
                json_encode($rewards),
                $eventConfig['max_participants']
            ]);
            
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            error_log('Erro ao criar evento: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Verifica se um usuário pode participar de um evento
    public function canParticipate($event_id, $character_id) {
        try {
            // Verifica se o evento existe e está ativo
            $stmt = $this->conn->prepare("
                SELECT e.*, COUNT(ep.id) as current_participants
                FROM forge_events e
                LEFT JOIN event_participation ep ON e.id = ep.event_id
                WHERE e.id = ? AND e.status = 'active'
                GROUP BY e.id
            ");
            $stmt->execute([$event_id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) {
                return ['can_participate' => false, 'reason' => 'Evento não encontrado ou inativo'];
            }
            
            // Verifica limite de participantes
            if ($event['max_participants'] && $event['current_participants'] >= $event['max_participants']) {
                return ['can_participate' => false, 'reason' => 'Evento lotado'];
            }
            
            // Verifica se já está participando
            $stmt = $this->conn->prepare("
                SELECT id FROM event_participation
                WHERE event_id = ? AND character_id = ?
            ");
            $stmt->execute([$event_id, $character_id]);
            if ($stmt->fetch()) {
                return ['can_participate' => false, 'reason' => 'Já está participando'];
            }
            
            // Verifica requisitos do personagem
            $stmt = $this->conn->prepare("
                SELECT c.*, g.id as guild_id
                FROM forge_characters c
                LEFT JOIN guild_members gm ON c.id = gm.character_id
                LEFT JOIN forge_guilds g ON gm.guild_id = g.id
                WHERE c.id = ?
            ");
            $stmt->execute([$character_id]);
            $character = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $requirements = json_decode($event['requirements'], true);
            
            if (isset($requirements['min_level']) && $character['level'] < $requirements['min_level']) {
                return ['can_participate' => false, 'reason' => 'Nível insuficiente'];
            }
            
            if (isset($requirements['guild_required']) && !$character['guild_id']) {
                return ['can_participate' => false, 'reason' => 'Necessário pertencer a uma guilda'];
            }
            
            return ['can_participate' => true];
        } catch (Exception $e) {
            error_log('Erro ao verificar participação: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Registra participação em um evento
    public function joinEvent($event_id, $character_id) {
        try {
            $canParticipate = $this->canParticipate($event_id, $character_id);
            if (!$canParticipate['can_participate']) {
                throw new Exception($canParticipate['reason']);
            }
            
            // Pega a guilda do personagem, se houver
            $stmt = $this->conn->prepare("
                SELECT guild_id FROM guild_members
                WHERE character_id = ?
            ");
            $stmt->execute([$character_id]);
            $guild = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $this->conn->prepare("
                INSERT INTO event_participation (
                    event_id, character_id, guild_id
                ) VALUES (?, ?, ?)
            ");
            
            return $stmt->execute([
                $event_id,
                $character_id,
                $guild ? $guild['guild_id'] : null
            ]);
        } catch (Exception $e) {
            error_log('Erro ao participar do evento: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Atualiza progresso em um evento
    public function updateProgress($event_id, $character_id, $progress) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE event_participation
                SET progress = ?, completed = (progress >= ?)
                WHERE event_id = ? AND character_id = ?
            ");
            
            // Pega o alvo do evento
            $eventStmt = $this->conn->prepare("
                SELECT requirements FROM forge_events WHERE id = ?
            ");
            $eventStmt->execute([$event_id]);
            $event = $eventStmt->fetch(PDO::FETCH_ASSOC);
            $requirements = json_decode($event['requirements'], true);
            $target = $requirements['target'] ?? 100;
            
            return $stmt->execute([
                $progress,
                $target,
                $event_id,
                $character_id
            ]);
        } catch (Exception $e) {
            error_log('Erro ao atualizar progresso: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Distribui recompensas do evento
    public function distributeRewards($event_id) {
        try {
            $this->conn->beginTransaction();
            
            // Pega participantes que completaram
            $stmt = $this->conn->prepare("
                SELECT ep.*, e.rewards, e.event_type
                FROM event_participation ep
                JOIN forge_events e ON ep.event_id = e.id
                WHERE ep.event_id = ? AND ep.completed = 1 AND ep.rewards_claimed = 0
            ");
            $stmt->execute([$event_id]);
            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($participants as $participant) {
                $rewards = json_decode($participant['rewards'], true);
                $eventType = FORGE_EVENT_TYPES[$participant['event_type']];
                
                // Aplica recompensas
                foreach ($rewards as $reward) {
                    $this->applyReward($participant['character_id'], $reward, $eventType);
                }
                
                // Marca recompensas como recebidas
                $stmt = $this->conn->prepare("
                    UPDATE event_participation
                    SET rewards_claimed = 1
                    WHERE event_id = ? AND character_id = ?
                ");
                $stmt->execute([$event_id, $participant['character_id']]);
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Erro ao distribuir recompensas: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Aplica uma recompensa específica
    private function applyReward($character_id, $reward, $eventType) {
        if (!isset(FORGE_REWARDS[$reward])) {
            throw new Exception('Recompensa inválida');
        }
        
        $rewardConfig = FORGE_REWARDS[$reward];
        
        switch ($rewardConfig['effect']['type']) {
            case 'multiplier':
                // Aplica multiplicador temporário
                $stmt = $this->conn->prepare("
                    INSERT INTO character_buffs (
                        character_id, buff_type, multiplier, expires_at
                    ) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))
                ");
                $stmt->execute([
                    $character_id,
                    $reward,
                    $rewardConfig['effect']['value'],
                    $rewardConfig['duration']
                ]);
                break;
                
            case 'points':
                // Adiciona pontos
                $stmt = $this->conn->prepare("
                    UPDATE forge_characters
                    SET total_xp = total_xp + ?
                    WHERE id = ?
                ");
                $points = $rewardConfig['effect']['value'] * $eventType['base_points'];
                $stmt->execute([$points, $character_id]);
                break;
                
            case 'cosmetic':
                // Adiciona item cosmético
                $stmt = $this->conn->prepare("
                    INSERT INTO character_cosmetics (
                        character_id, cosmetic_type, cosmetic_id
                    ) VALUES (?, ?, ?)
                ");
                $stmt->execute([
                    $character_id,
                    $rewardConfig['effect']['value'],
                    $reward
                ]);
                break;
        }
    }

    // Atualiza o status dos eventos ativos
    public function updateActiveEvents() {
        try {
            // Pega todos os eventos ativos
            $stmt = $this->conn->prepare("
                SELECT id, event_type, end_time
                FROM forge_events
                WHERE status = 'active'
            ");
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($events as $event) {
                // Verifica se o evento já terminou
                if (strtotime($event['end_time']) <= time()) {
                    // Distribui recompensas
                    $this->distributeRewards($event['id']);

                    // Atualiza status para concluído
                    $stmt = $this->conn->prepare("
                        UPDATE forge_events
                        SET status = 'completed',
                            completed_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$event['id']]);
                }
            }

            return true;
        } catch (Exception $e) {
            error_log('Erro ao atualizar eventos ativos: ' . $e->getMessage());
            throw $e;
        }
    }
}

// Instância global do gerenciador de eventos
global $eventManager;
$eventManager = new ForgeEventManager($conn);
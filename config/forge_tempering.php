<?php
/**
 * Configuração do Sistema de Têmpera dos Músculos
 * Define estágios, efeitos visuais e mecânicas de progressão
 */

// Grupos Musculares
const MUSCLE_GROUPS = [
    'chest' => [
        'name' => 'Peitoral',
        'base_heat_gain' => 10,
        'base_cooldown_rate' => 1,
        'exercises' => ['supino', 'flexao', 'crucifixo'],
    ],
    'back' => [
        'name' => 'Costas',
        'base_heat_gain' => 10,
        'base_cooldown_rate' => 1,
        'exercises' => ['barra_fixa', 'remada', 'pull_down'],
    ],
    'legs' => [
        'name' => 'Pernas',
        'base_heat_gain' => 15,
        'base_cooldown_rate' => 1.2,
        'exercises' => ['agachamento', 'leg_press', 'extensora'],
    ],
    'shoulders' => [
        'name' => 'Ombros',
        'base_heat_gain' => 8,
        'base_cooldown_rate' => 0.8,
        'exercises' => ['desenvolvimento', 'elevacao_lateral', 'remada_alta'],
    ],
    'arms' => [
        'name' => 'Braços',
        'base_heat_gain' => 7,
        'base_cooldown_rate' => 0.7,
        'exercises' => ['rosca_biceps', 'triceps_corda', 'martelo'],
    ],
    'core' => [
        'name' => 'Core',
        'base_heat_gain' => 12,
        'base_cooldown_rate' => 1.1,
        'exercises' => ['abdominal', 'prancha', 'russian_twist'],
    ]
];

// Estágios de Têmpera
const TEMPERING_STAGES = [
    'bronze_cold' => [
        'name' => 'Bronze Frio',
        'level' => 0,
        'heat_threshold' => 0,
        'color' => '#CD7F32',
        'particle_effect' => null,
        'bonus_multiplier' => 1.0,
    ],
    'bronze_hot' => [
        'name' => 'Bronze Quente',
        'level' => 1,
        'heat_threshold' => 100,
        'color' => '#FF9C39',
        'particle_effect' => 'ember',
        'bonus_multiplier' => 1.1,
    ],
    'iron_cold' => [
        'name' => 'Ferro Frio',
        'level' => 2,
        'heat_threshold' => 250,
        'color' => '#43464B',
        'particle_effect' => null,
        'bonus_multiplier' => 1.2,
    ],
    'iron_hot' => [
        'name' => 'Ferro Quente',
        'level' => 3,
        'heat_threshold' => 500,
        'color' => '#FF4C4C',
        'particle_effect' => 'spark',
        'bonus_multiplier' => 1.3,
    ],
    'steel_cold' => [
        'name' => 'Aço Frio',
        'level' => 4,
        'heat_threshold' => 1000,
        'color' => '#71797E',
        'particle_effect' => 'frost',
        'bonus_multiplier' => 1.4,
    ],
    'steel_hot' => [
        'name' => 'Aço Quente',
        'level' => 5,
        'heat_threshold' => 2000,
        'color' => '#FF9500',
        'particle_effect' => 'flame',
        'bonus_multiplier' => 1.5,
    ],
    'mithril_cold' => [
        'name' => 'Mithril Frio',
        'level' => 6,
        'heat_threshold' => 4000,
        'color' => '#97B0C4',
        'particle_effect' => 'starlight',
        'bonus_multiplier' => 1.6,
    ],
    'mithril_hot' => [
        'name' => 'Mithril Quente',
        'level' => 7,
        'heat_threshold' => 8000,
        'color' => '#5CE1E6',
        'particle_effect' => 'plasma',
        'bonus_multiplier' => 1.7,
    ],
    'adamantium_cold' => [
        'name' => 'Adamantium Frio',
        'level' => 8,
        'heat_threshold' => 16000,
        'color' => '#4B0082',
        'particle_effect' => 'void',
        'bonus_multiplier' => 1.8,
    ],
    'adamantium_hot' => [
        'name' => 'Adamantium Quente',
        'level' => 9,
        'heat_threshold' => 32000,
        'color' => '#9D00FF',
        'particle_effect' => 'thunder',
        'bonus_multiplier' => 2.0,
    ]
];

// Efeitos de Partículas
const PARTICLE_EFFECTS = [
    'ember' => [
        'name' => 'Brasas',
        'css_class' => 'particle-ember',
        'color' => '#FF6B00',
        'intensity' => 0.3,
    ],
    'spark' => [
        'name' => 'Faíscas',
        'css_class' => 'particle-spark',
        'color' => '#FF4C4C',
        'intensity' => 0.5,
    ],
    'flame' => [
        'name' => 'Chamas',
        'css_class' => 'particle-flame',
        'color' => '#FF9500',
        'intensity' => 0.7,
    ],
    'frost' => [
        'name' => 'Gelo',
        'css_class' => 'particle-frost',
        'color' => '#00FFFF',
        'intensity' => 0.4,
    ],
    'starlight' => [
        'name' => 'Luz Estelar',
        'css_class' => 'particle-starlight',
        'color' => '#97B0C4',
        'intensity' => 0.6,
    ],
    'plasma' => [
        'name' => 'Plasma',
        'css_class' => 'particle-plasma',
        'color' => '#5CE1E6',
        'intensity' => 0.8,
    ],
    'void' => [
        'name' => 'Vazio',
        'css_class' => 'particle-void',
        'color' => '#4B0082',
        'intensity' => 0.9,
    ],
    'thunder' => [
        'name' => 'Trovão',
        'css_class' => 'particle-thunder',
        'color' => '#9D00FF',
        'intensity' => 1.0,
    ]
];

// Classe para gerenciar têmpera dos músculos
class TemperingManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Calcula ganho de calor base para um exercício
    public function calculateHeatGain($character_id, $muscle_group, $exercise_intensity) {
        try {
            if (!isset(MUSCLE_GROUPS[$muscle_group])) {
                throw new Exception('Grupo muscular inválido');
            }
            
            $baseGain = MUSCLE_GROUPS[$muscle_group]['base_heat_gain'];
            
            // Pega atributos do personagem
            $stmt = $this->conn->prepare("
                SELECT fa.strength, fa.endurance
                FROM forge_attributes fa
                WHERE fa.character_id = ?
            ");
            $stmt->execute([$character_id]);
            $attributes = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calcula multiplicadores
            $strengthMult = 1 + ($attributes['strength'] * 0.05);
            $enduranceMult = 1 + ($attributes['endurance'] * 0.03);
            
            // Pega buffs ativos
            $stmt = $this->conn->prepare("
                SELECT buff_type, multiplier
                FROM character_buffs
                WHERE character_id = ? AND expires_at > NOW()
                AND buff_type = 'tempering_boost'
            ");
            $stmt->execute([$character_id]);
            $buffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $buffMult = 1.0;
            foreach ($buffs as $buff) {
                $buffMult *= $buff['multiplier'];
            }
            
            // Calcula ganho final
            $heatGain = $baseGain * $strengthMult * $enduranceMult * $buffMult * $exercise_intensity;
            
            return round($heatGain);
        } catch (Exception $e) {
            error_log('Erro ao calcular ganho de calor: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Atualiza têmpera após exercício
    public function updateTempering($character_id, $muscle_group, $heat_gain) {
        try {
            $this->conn->beginTransaction();
            
            // Pega estado atual do músculo
            $stmt = $this->conn->prepare("
                SELECT current_level, heat_points, total_exercises
                FROM muscle_tempering
                WHERE character_id = ? AND muscle_group = ?
            ");
            $stmt->execute([$character_id, $muscle_group]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$current) {
                // Cria novo registro se não existir
                $stmt = $this->conn->prepare("
                    INSERT INTO muscle_tempering (
                        character_id, muscle_group, current_level,
                        heat_points, total_exercises, visual_stage
                    ) VALUES (?, ?, 0, ?, 1, 'bronze_cold')
                ");
                $stmt->execute([$character_id, $muscle_group, $heat_gain]);
                $this->conn->commit();
                return ['level' => 0, 'stage' => 'bronze_cold', 'heat_points' => $heat_gain];
            }
            
            // Atualiza pontos de calor e total de exercícios
            $newHeat = $current['heat_points'] + $heat_gain;
            $newExercises = $current['total_exercises'] + 1;
            
            // Determina novo estágio
            $newStage = $this->determineStage($newHeat);
            $newLevel = TEMPERING_STAGES[$newStage]['level'];
            
            // Atualiza o registro
            $stmt = $this->conn->prepare("
                UPDATE muscle_tempering
                SET heat_points = ?,
                    current_level = ?,
                    visual_stage = ?,
                    total_exercises = ?,
                    last_exercise_date = NOW()
                WHERE character_id = ? AND muscle_group = ?
            ");
            $stmt->execute([
                $newHeat,
                $newLevel,
                $newStage,
                $newExercises,
                $character_id,
                $muscle_group
            ]);
            
            // Se subiu de nível, adiciona conquista
            if ($newLevel > $current['current_level']) {
                $this->addTemperingAchievement($character_id, $muscle_group, $newLevel);
            }
            
            $this->conn->commit();
            
            return [
                'level' => $newLevel,
                'stage' => $newStage,
                'heat_points' => $newHeat
            ];
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Erro ao atualizar têmpera: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Determina estágio baseado nos pontos de calor
    private function determineStage($heat_points) {
        $currentStage = 'bronze_cold';
        
        foreach (TEMPERING_STAGES as $stage => $config) {
            if ($heat_points >= $config['heat_threshold']) {
                $currentStage = $stage;
            } else {
                break;
            }
        }
        
        return $currentStage;
    }
    
    // Adiciona conquista de têmpera
    private function addTemperingAchievement($character_id, $muscle_group, $level) {
        $achievementCode = "tempering_{$muscle_group}_level_{$level}";
        
        $stmt = $this->conn->prepare("
            INSERT IGNORE INTO forge_achievements (
                character_id, achievement_code, progress,
                completed, unlocked_at
            ) VALUES (?, ?, 1, 1, NOW())
        ");
        
        $stmt->execute([$character_id, $achievementCode]);
    }
    
    // Aplica resfriamento periódico
    public function applyCooldown($character_id) {
        try {
            // Pega todos os músculos do personagem
            $stmt = $this->conn->prepare("
                SELECT muscle_group, heat_points, cooldown_rate,
                       last_exercise_date, visual_stage
                FROM muscle_tempering
                WHERE character_id = ?
            ");
            $stmt->execute([$character_id]);
            $muscles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = [
                'updated' => false,
                'old_heat' => 0,
                'new_heat' => 0,
                'rewards' => []
            ];
            
            foreach ($muscles as $muscle) {
                // Calcula tempo desde último exercício
                $lastExercise = strtotime($muscle['last_exercise_date']);
                $hoursSinceExercise = (time() - $lastExercise) / 3600;
                
                // Calcula pontos a resfriar
                $cooldownPoints = $hoursSinceExercise * $muscle['cooldown_rate'] * MUSCLE_GROUPS[$muscle['muscle_group']]['base_cooldown_rate'];
                
                // Aplica resfriamento
                $newHeat = max(0, $muscle['heat_points'] - $cooldownPoints);
                $newStage = $this->determineStage($newHeat);
                
                // Atualiza se mudou
                if ($newStage != $muscle['visual_stage']) {
                    $result['updated'] = true;
                    $result['old_heat'] = $muscle['heat_points'];
                    $result['new_heat'] = $newHeat;
                    
                    $stmt = $this->conn->prepare("
                        UPDATE muscle_tempering
                        SET heat_points = ?,
                            visual_stage = ?,
                            current_level = ?
                        WHERE character_id = ? AND muscle_group = ?
                    ");
                    $stmt->execute([
                        $newHeat,
                        $newStage,
                        TEMPERING_STAGES[$newStage]['level'],
                        $character_id,
                        $muscle['muscle_group']
                    ]);
                    
                    // Adiciona recompensa se resfriou completamente
                    if ($newHeat == 0) {
                        $result['rewards'][] = [
                            'type' => 'tempering_reset',
                            'amount' => 100,
                            'reason' => 'Resfriamento completo'
                        ];
                    }
                }
            }
            
            return $result;
        } catch (Exception $e) {
            error_log('Erro ao aplicar resfriamento: ' . $e->getMessage());
            throw $e;
        }
    }
}

// Instância global do gerenciador de têmpera
global $temperingManager;
$temperingManager = new TemperingManager($conn);
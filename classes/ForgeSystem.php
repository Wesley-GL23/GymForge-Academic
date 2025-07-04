<?php

class ForgeSystem {
    private $db;
    private $character_id;
    private $forge_config;

    public function __construct($db, $user_id) {
        $this->db = $db;
        $this->forge_config = json_decode(file_get_contents(__DIR__ . '/../database/forge_system.json'), true);
        $this->loadOrCreateCharacter($user_id);
    }

    private function loadOrCreateCharacter($user_id) {
        $stmt = $this->db->prepare("SELECT id FROM forge_characters WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        if ($character = $stmt->fetch()) {
            $this->character_id = $character['id'];
        } else {
            // Criar novo personagem
            $stmt = $this->db->prepare("INSERT INTO forge_characters (user_id) VALUES (?)");
            $stmt->execute([$user_id]);
            $this->character_id = $this->db->lastInsertId();
            
            // Inicializar atributos
            $stmt = $this->db->prepare("INSERT INTO forge_attributes (character_id) VALUES (?)");
            $stmt->execute([$this->character_id]);
            
            // Inicializar grupos musculares
            $this->initializeMuscleGroups();
        }
    }

    private function initializeMuscleGroups() {
        $muscle_groups = [
            'chest', 'back', 'shoulders', 'biceps', 'triceps',
            'forearms', 'abs', 'obliques', 'quads', 'hamstrings',
            'calves', 'glutes'
        ];

        $stmt = $this->db->prepare("
            INSERT INTO muscle_tempering 
            (character_id, muscle_group) 
            VALUES (?, ?)
        ");

        foreach ($muscle_groups as $muscle) {
            $stmt->execute([$this->character_id, $muscle]);
        }
    }

    public function gainExperience($amount) {
        // Buscar dados atuais do personagem
        $stmt = $this->db->prepare("
            SELECT level, total_xp, current_rank 
            FROM forge_characters 
            WHERE id = ?
        ");
        $stmt->execute([$this->character_id]);
        $character = $stmt->fetch();

        // Calcular novo XP e possível level up
        $new_xp = $character['total_xp'] + $amount;
        $new_level = $this->calculateLevel($new_xp);
        
        // Verificar mudança de rank
        $new_rank = $this->checkRankProgress($new_level);

        // Atualizar personagem
        $stmt = $this->db->prepare("
            UPDATE forge_characters 
            SET total_xp = ?, level = ?, current_rank = ?
            WHERE id = ?
        ");
        $stmt->execute([$new_xp, $new_level, $new_rank, $this->character_id]);

        return [
            'xp_gained' => $amount,
            'new_level' => $new_level,
            'old_level' => $character['level'],
            'new_rank' => $new_rank,
            'old_rank' => $character['current_rank']
        ];
    }

    private function calculateLevel($total_xp) {
        // Fórmula de level: cada level requer 20% mais XP que o anterior
        $level = 1;
        $xp_for_next = 100; // XP base para level 2
        $accumulated_xp = 0;

        while ($accumulated_xp <= $total_xp) {
            $accumulated_xp += $xp_for_next;
            if ($accumulated_xp > $total_xp) break;
            
            $level++;
            $xp_for_next = floor($xp_for_next * 1.2);
        }

        return $level;
    }

    private function checkRankProgress($level) {
        foreach ($this->forge_config['ranks'] as $rank_code => $rank) {
            if ($level >= $rank['min_level'] && $level <= $rank['max_level']) {
                // Verificar requisitos adicionais
                if ($this->meetsRankRequirements($rank_code)) {
                    return $rank_code;
                }
            }
        }
        return 'novice_forger'; // Fallback para rank inicial
    }

    private function meetsRankRequirements($rank_code) {
        $rank = $this->forge_config['ranks'][$rank_code];
        $requirements = $rank['requirements'];

        // Buscar dados do personagem
        $stmt = $this->db->prepare("
            SELECT total_workouts, total_exercises
            FROM forge_characters
            WHERE id = ?
        ");
        $stmt->execute([$this->character_id]);
        $character = $stmt->fetch();

        // Verificar requisitos básicos
        if ($character['total_workouts'] < $requirements['total_workouts'] ||
            $character['total_exercises'] < $requirements['total_exercises']) {
            return false;
        }

        // Verificar requisitos de nível dos músculos
        if (isset($requirements['muscle_groups_above_30'])) {
            $level_requirement = 30;
            $groups_needed = $requirements['muscle_groups_above_30'];
        } elseif (isset($requirements['muscle_groups_above_50'])) {
            $level_requirement = 50;
            $groups_needed = $requirements['muscle_groups_above_50'];
        } elseif (isset($requirements['muscle_groups_above_70'])) {
            $level_requirement = 70;
            $groups_needed = $requirements['muscle_groups_above_70'];
        } elseif (isset($requirements['muscle_groups_above_85'])) {
            $level_requirement = 85;
            $groups_needed = $requirements['muscle_groups_above_85'];
        } elseif (isset($requirements['muscle_groups_above_95'])) {
            $level_requirement = 95;
            $groups_needed = $requirements['muscle_groups_above_95'];
        } else {
            return true; // Sem requisitos de músculo
        }

        // Contar músculos acima do nível requerido
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM muscle_tempering
            WHERE character_id = ? AND current_level >= ?
        ");
        $stmt->execute([$this->character_id, $level_requirement]);
        $result = $stmt->fetch();

        return $result['count'] >= $groups_needed;
    }

    public function temperMuscle($muscle_group, $intensity) {
        // Verificar se o músculo existe
        $stmt = $this->db->prepare("
            SELECT current_level, total_exercises
            FROM muscle_tempering
            WHERE character_id = ? AND muscle_group = ?
        ");
        $stmt->execute([$this->character_id, $muscle_group]);
        
        if (!$muscle = $stmt->fetch()) {
            throw new Exception("Grupo muscular não encontrado");
        }

        // Calcular ganho de nível baseado na intensidade (1-10)
        $level_gain = min(1, $intensity / 10);
        $new_level = min(100, $muscle['current_level'] + $level_gain);
        
        // Determinar estágio visual
        $visual_stage = $this->calculateVisualStage($new_level);

        // Atualizar músculo
        $stmt = $this->db->prepare("
            UPDATE muscle_tempering
            SET current_level = ?,
                total_exercises = total_exercises + 1,
                last_exercise_date = NOW(),
                visual_stage = ?
            WHERE character_id = ? AND muscle_group = ?
        ");
        $stmt->execute([
            $new_level,
            $visual_stage,
            $this->character_id,
            $muscle_group
        ]);

        // Verificar conquistas
        $this->checkAchievements($muscle_group);

        return [
            'new_level' => $new_level,
            'level_gain' => $level_gain,
            'visual_stage' => $visual_stage
        ];
    }

    private function calculateVisualStage($level) {
        $stages = $this->forge_config['muscle_tempering']['chest']['visual_stages'];
        
        for ($i = count($stages) - 1; $i >= 0; $i--) {
            if ($level >= $stages[$i]['level']) {
                return $stages[$i]['description'];
            }
        }
        
        return $stages[0]['description'];
    }

    private function checkAchievements($muscle_group) {
        foreach ($this->forge_config['special_achievements'] as $code => $achievement) {
            // Verificar se já completou
            $stmt = $this->db->prepare("
                SELECT id, completed, progress
                FROM forge_achievements
                WHERE character_id = ? AND achievement_code = ?
            ");
            $stmt->execute([$this->character_id, $code]);
            $current = $stmt->fetch();

            if ($current && $current['completed']) continue;

            // Verificar progresso específico do achievement
            $progress = $this->calculateAchievementProgress($code);
            
            if (!$current) {
                // Criar novo registro
                $stmt = $this->db->prepare("
                    INSERT INTO forge_achievements
                    (character_id, achievement_code, progress, completed)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $this->character_id,
                    $code,
                    $progress,
                    $progress >= 100
                ]);
            } else {
                // Atualizar progresso
                $stmt = $this->db->prepare("
                    UPDATE forge_achievements
                    SET progress = ?, completed = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $progress,
                    $progress >= 100,
                    $current['id']
                ]);
            }
        }
    }

    private function calculateAchievementProgress($achievement_code) {
        $achievement = $this->forge_config['special_achievements'][$achievement_code];
        
        switch ($achievement_code) {
            case 'thunder_arms':
                // Verificar nível de bíceps e tríceps
                $stmt = $this->db->prepare("
                    SELECT 
                        SUM(CASE WHEN current_level >= 100 THEN 1 ELSE 0 END) as completed,
                        COUNT(*) as total
                    FROM muscle_tempering
                    WHERE character_id = ? 
                    AND muscle_group IN ('biceps', 'triceps')
                ");
                $stmt->execute([$this->character_id]);
                $result = $stmt->fetch();
                return ($result['completed'] / $result['total']) * 100;

            case 'iron_core':
                // Verificar total de exercícios de core
                $stmt = $this->db->prepare("
                    SELECT total_exercises
                    FROM muscle_tempering
                    WHERE character_id = ? 
                    AND muscle_group IN ('abs', 'obliques')
                ");
                $stmt->execute([$this->character_id]);
                $total = 0;
                while ($row = $stmt->fetch()) {
                    $total += $row['total_exercises'];
                }
                return min(100, ($total / 1000) * 100);

            case 'legendary_legs':
                // Verificar nível de todos os músculos das pernas
                $stmt = $this->db->prepare("
                    SELECT 
                        SUM(CASE WHEN current_level >= 100 THEN 1 ELSE 0 END) as completed,
                        COUNT(*) as total
                    FROM muscle_tempering
                    WHERE character_id = ? 
                    AND muscle_group IN ('quads', 'hamstrings', 'calves', 'glutes')
                ");
                $stmt->execute([$this->character_id]);
                $result = $stmt->fetch();
                return ($result['completed'] / $result['total']) * 100;

            default:
                return 0;
        }
    }

    public function getCharacterStatus() {
        // Buscar dados do personagem
        $stmt = $this->db->prepare("
            SELECT c.*, a.*
            FROM forge_characters c
            JOIN forge_attributes a ON a.character_id = c.id
            WHERE c.id = ?
        ");
        $stmt->execute([$this->character_id]);
        $character = $stmt->fetch();

        // Buscar músculos
        $stmt = $this->db->prepare("
            SELECT muscle_group, current_level, visual_stage
            FROM muscle_tempering
            WHERE character_id = ?
        ");
        $stmt->execute([$this->character_id]);
        $muscles = $stmt->fetchAll();

        // Buscar conquistas
        $stmt = $this->db->prepare("
            SELECT achievement_code, progress, completed
            FROM forge_achievements
            WHERE character_id = ?
        ");
        $stmt->execute([$this->character_id]);
        $achievements = $stmt->fetchAll();

        // Buscar guilda
        $stmt = $this->db->prepare("
            SELECT g.*, gm.role
            FROM guild_members gm
            JOIN forge_guilds g ON g.id = gm.guild_id
            WHERE gm.character_id = ?
        ");
        $stmt->execute([$this->character_id]);
        $guild = $stmt->fetch();

        return [
            'character' => $character,
            'muscles' => $muscles,
            'achievements' => $achievements,
            'guild' => $guild
        ];
    }
} 
<?php
require_once __DIR__ . '/../config/conexao.php';

class GuildActivitySystem {
    private $conn;
    private $guild_id;
    private $character_id;

    public function __construct($conn, $character_id) {
        $this->conn = $conn;
        $this->character_id = $character_id;
        $this->loadGuildInfo();
    }

    private function loadGuildInfo() {
        $stmt = $this->conn->prepare("
            SELECT guild_id 
            FROM guild_members 
            WHERE character_id = ?
        ");
        $stmt->execute([$this->character_id]);
        
        if ($member = $stmt->fetch()) {
            $this->guild_id = $member['guild_id'];
        }
    }

    /**
     * Registra uma nova atividade na guilda
     */
    public function logActivity($activity_type, $points, $description) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO guild_activities 
                (guild_id, character_id, activity_type, points, description) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $this->guild_id,
                $this->character_id,
                $activity_type,
                $points,
                $description
            ]);

            if ($result) {
                // Atualizar pontos do membro
                $stmt = $this->conn->prepare("
                    UPDATE guild_members 
                    SET contribution_points = contribution_points + ? 
                    WHERE guild_id = ? AND character_id = ?
                ");
                $stmt->execute([$points, $this->guild_id, $this->character_id]);

                // Verificar conquistas
                $this->checkAchievements();
            }

            return $result;
        } catch (Exception $e) {
            error_log('Erro ao registrar atividade: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cria um novo desafio para a guilda
     */
    public function createChallenge($title, $description, $type, $goal_value, $duration_days, $reward_type, $reward_value) {
        try {
            $start_date = date('Y-m-d H:i:s');
            $end_date = date('Y-m-d H:i:s', strtotime("+{$duration_days} days"));

            $stmt = $this->conn->prepare("
                INSERT INTO guild_challenges 
                (guild_id, title, description, challenge_type, goal_value, 
                start_date, end_date, reward_type, reward_value) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $this->guild_id,
                $title,
                $description,
                $type,
                $goal_value,
                $start_date,
                $end_date,
                $reward_type,
                $reward_value
            ]);
        } catch (Exception $e) {
            error_log('Erro ao criar desafio: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Participa de um desafio
     */
    public function joinChallenge($challenge_id) {
        try {
            // Verificar se o desafio está ativo
            $stmt = $this->conn->prepare("
                SELECT * FROM guild_challenges 
                WHERE id = ? AND guild_id = ? AND status = 'active'
                AND NOW() BETWEEN start_date AND end_date
            ");
            $stmt->execute([$challenge_id, $this->guild_id]);
            
            if (!$stmt->fetch()) {
                throw new Exception('Desafio não disponível');
            }

            $stmt = $this->conn->prepare("
                INSERT INTO challenge_participation 
                (challenge_id, character_id) 
                VALUES (?, ?)
            ");
            
            return $stmt->execute([$challenge_id, $this->character_id]);
        } catch (Exception $e) {
            error_log('Erro ao participar do desafio: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Atualiza progresso em um desafio
     */
    public function updateChallengeProgress($challenge_id, $progress_value) {
        try {
            $this->conn->beginTransaction();

            // Atualizar progresso individual
            $stmt = $this->conn->prepare("
                UPDATE challenge_participation 
                SET contribution_value = contribution_value + ? 
                WHERE challenge_id = ? AND character_id = ?
            ");
            $stmt->execute([$progress_value, $challenge_id, $this->character_id]);

            // Atualizar progresso total do desafio
            $stmt = $this->conn->prepare("
                UPDATE guild_challenges 
                SET current_value = current_value + ? 
                WHERE id = ? AND guild_id = ?
            ");
            $stmt->execute([$progress_value, $challenge_id, $this->guild_id]);

            // Verificar se o desafio foi completado
            $stmt = $this->conn->prepare("
                SELECT * FROM guild_challenges 
                WHERE id = ? AND current_value >= goal_value 
                AND status = 'active'
            ");
            $stmt->execute([$challenge_id]);
            
            if ($challenge = $stmt->fetch()) {
                $this->completeChallenge($challenge);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Erro ao atualizar progresso: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Completa um desafio e distribui recompensas
     */
    private function completeChallenge($challenge) {
        try {
            // Atualizar status do desafio
            $stmt = $this->conn->prepare("
                UPDATE guild_challenges 
                SET status = 'completed' 
                WHERE id = ?
            ");
            $stmt->execute([$challenge['id']]);

            // Distribuir recompensas aos participantes
            $stmt = $this->conn->prepare("
                SELECT * FROM challenge_participation 
                WHERE challenge_id = ?
            ");
            $stmt->execute([$challenge['id']]);
            
            while ($participant = $stmt->fetch()) {
                $this->distributeReward(
                    $participant['character_id'],
                    $challenge['reward_type'],
                    $challenge['reward_value']
                );
            }

            // Registrar atividade
            $this->logActivity(
                'challenge',
                50,
                "Desafio '{$challenge['title']}' completado!"
            );

            return true;
        } catch (Exception $e) {
            error_log('Erro ao completar desafio: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Distribui recompensa para um personagem
     */
    private function distributeReward($character_id, $reward_type, $reward_value) {
        try {
            switch ($reward_type) {
                case 'xp':
                    $stmt = $this->conn->prepare("
                        UPDATE forge_characters 
                        SET total_xp = total_xp + ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$reward_value, $character_id]);
                    break;

                case 'points':
                    $stmt = $this->conn->prepare("
                        UPDATE guild_members 
                        SET contribution_points = contribution_points + ? 
                        WHERE guild_id = ? AND character_id = ?
                    ");
                    $stmt->execute([$reward_value, $this->guild_id, $character_id]);
                    break;

                case 'achievement':
                    // Implementar lógica de conquistas especiais
                    break;
            }
        } catch (Exception $e) {
            error_log('Erro ao distribuir recompensa: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verifica e atualiza conquistas da guilda
     */
    private function checkAchievements() {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM guild_achievements 
                WHERE guild_id = ? AND unlocked_at IS NULL
            ");
            $stmt->execute([$this->guild_id]);
            
            while ($achievement = $stmt->fetch()) {
                $this->updateAchievementProgress($achievement);
            }
        } catch (Exception $e) {
            error_log('Erro ao verificar conquistas: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Atualiza o progresso de uma conquista
     */
    private function updateAchievementProgress($achievement) {
        try {
            // Calcular progresso baseado no tipo da conquista
            $progress = $this->calculateAchievementProgress($achievement);

            // Atualizar progresso
            $stmt = $this->conn->prepare("
                UPDATE guild_achievements 
                SET progress = ? 
                WHERE id = ?
            ");
            $stmt->execute([$progress, $achievement['id']]);

            // Verificar se a conquista foi completada
            if ($progress >= $achievement['required_points']) {
                $this->unlockAchievement($achievement);
            }
        } catch (Exception $e) {
            error_log('Erro ao atualizar progresso da conquista: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calcula o progresso atual de uma conquista
     */
    private function calculateAchievementProgress($achievement) {
        // Implementar cálculos específicos baseados no código da conquista
        return 0; // Placeholder
    }

    /**
     * Desbloqueia uma conquista
     */
    private function unlockAchievement($achievement) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE guild_achievements 
                SET unlocked_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$achievement['id']]);

            // Registrar atividade
            $this->logActivity(
                'achievement',
                100,
                "Conquista '{$achievement['name']}' desbloqueada!"
            );

            // Aplicar recompensa
            $this->applyAchievementReward($achievement);
        } catch (Exception $e) {
            error_log('Erro ao desbloquear conquista: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Aplica a recompensa de uma conquista
     */
    private function applyAchievementReward($achievement) {
        try {
            switch ($achievement['reward_type']) {
                case 'xp':
                    // Dar XP para todos os membros
                    $stmt = $this->conn->prepare("
                        UPDATE forge_characters fc
                        JOIN guild_members gm ON fc.id = gm.character_id
                        SET fc.total_xp = fc.total_xp + ?
                        WHERE gm.guild_id = ?
                    ");
                    $stmt->execute([$achievement['reward_value'], $this->guild_id]);
                    break;

                case 'perk':
                    // Implementar sistema de perks
                    break;

                case 'title':
                    // Implementar sistema de títulos
                    break;

                case 'emblem':
                    // Implementar sistema de emblemas
                    break;
            }
        } catch (Exception $e) {
            error_log('Erro ao aplicar recompensa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createGuildEvent($title, $description, $start_date, $end_date, $type = 'challenge') {
        if (!$this->guild_id) {
            throw new Exception("Você precisa pertencer a uma guilda para criar eventos");
        }

        // Verificar permissão
        if (!$this->hasGuildPermission(['leader', 'officer'])) {
            throw new Exception("Apenas líderes e oficiais podem criar eventos");
        }

        $stmt = $this->conn->prepare("
            INSERT INTO forge_events 
            (guild_id, title, description, start_date, end_date, event_type, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $this->guild_id,
            $title,
            $description,
            $start_date,
            $end_date,
            $type,
            $this->character_id
        ]);
    }

    public function participateInEvent($event_id) {
        // Verificar se o evento existe e está ativo
        $stmt = $this->conn->prepare("
            SELECT * FROM forge_events 
            WHERE id = ? AND NOW() BETWEEN start_date AND end_date
        ");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();

        if (!$event) {
            throw new Exception("Evento não encontrado ou não está ativo");
        }

        // Verificar se já está participando
        $stmt = $this->conn->prepare("
            SELECT id FROM event_participation 
            WHERE event_id = ? AND character_id = ?
        ");
        $stmt->execute([$event_id, $this->character_id]);
        
        if ($stmt->fetch()) {
            throw new Exception("Você já está participando deste evento");
        }

        // Registrar participação
        $stmt = $this->conn->prepare("
            INSERT INTO event_participation 
            (event_id, character_id, join_date) 
            VALUES (?, ?, NOW())
        ");
        
        return $stmt->execute([$event_id, $this->character_id]);
    }

    public function getActiveEvents() {
        if (!$this->guild_id) return [];

        $stmt = $this->conn->prepare("
            SELECT e.*, 
                   c.nome as creator_name,
                   COUNT(p.id) as participants_count
            FROM forge_events e
            LEFT JOIN forge_characters c ON e.created_by = c.id
            LEFT JOIN event_participation p ON e.id = p.event_id
            WHERE e.guild_id = ? AND NOW() BETWEEN e.start_date AND e.end_date
            GROUP BY e.id
            ORDER BY e.start_date ASC
        ");
        
        $stmt->execute([$this->guild_id]);
        return $stmt->fetchAll();
    }

    public function getEventParticipants($event_id) {
        $stmt = $this->conn->prepare("
            SELECT c.nome, c.level, c.current_rank,
                   p.join_date, p.completion_date, p.status
            FROM event_participation p
            JOIN forge_characters c ON p.character_id = c.id
            WHERE p.event_id = ?
            ORDER BY p.join_date ASC
        ");
        
        $stmt->execute([$event_id]);
        return $stmt->fetchAll();
    }

    public function completeEventTask($event_id, $task_data) {
        // Verificar participação
        $stmt = $this->conn->prepare("
            SELECT * FROM event_participation 
            WHERE event_id = ? AND character_id = ?
        ");
        $stmt->execute([$event_id, $this->character_id]);
        $participation = $stmt->fetch();

        if (!$participation) {
            throw new Exception("Você não está participando deste evento");
        }

        // Registrar conclusão da tarefa
        $stmt = $this->conn->prepare("
            UPDATE event_participation 
            SET completion_date = NOW(),
                status = 'completed',
                task_data = ?
            WHERE event_id = ? AND character_id = ?
        ");
        
        return $stmt->execute([
            json_encode($task_data),
            $event_id,
            $this->character_id
        ]);
    }

    private function hasGuildPermission($allowed_roles) {
        $stmt = $this->conn->prepare("
            SELECT role 
            FROM guild_members 
            WHERE character_id = ? AND guild_id = ?
        ");
        $stmt->execute([$this->character_id, $this->guild_id]);
        $member = $stmt->fetch();

        return $member && in_array($member['role'], $allowed_roles);
    }
} 
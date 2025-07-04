<?php
require_once __DIR__ . '/../config/conexao.php';

class GuildActivity {
    private $conn;
    private $guild_id;
    private $character_id;

    public function __construct($conn, $guild_id, $character_id) {
        $this->conn = $conn;
        $this->guild_id = $guild_id;
        $this->character_id = $character_id;
    }

    /**
     * Registra uma nova atividade na guilda
     */
    public function logActivity($activity_type, $points, $description) {
        try {
            $this->conn->beginTransaction();

            // Inserir a atividade
            $stmt = $this->conn->prepare("
                INSERT INTO guild_activities 
                (guild_id, character_id, activity_type, points, description) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $this->guild_id,
                $this->character_id,
                $activity_type,
                $points,
                $description
            ]);

            // Atualizar pontos do membro
            $stmt = $this->conn->prepare("
                UPDATE guild_members 
                SET contribution_points = contribution_points + ? 
                WHERE guild_id = ? AND character_id = ?
            ");
            $stmt->execute([$points, $this->guild_id, $this->character_id]);

            // Atualizar XP total da guilda
            $stmt = $this->conn->prepare("
                UPDATE forge_guilds 
                SET total_xp = total_xp + ? 
                WHERE id = ?
            ");
            $stmt->execute([$points, $this->guild_id]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Erro ao registrar atividade: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retorna o histórico de atividades da guilda
     */
    public function getGuildActivities($limit = 20, $offset = 0) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    ga.*,
                    fc.name as character_name,
                    fc.level as character_level,
                    fc.current_rank as character_rank
                FROM guild_activities ga
                JOIN forge_characters fc ON ga.character_id = fc.id
                WHERE ga.guild_id = ?
                ORDER BY ga.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->execute([$this->guild_id, $limit, $offset]);
            return $stmt->fetchAll();

        } catch (Exception $e) {
            error_log('Erro ao buscar atividades: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retorna as estatísticas de atividade do membro
     */
    public function getMemberStats() {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(*) as total_activities,
                    SUM(points) as total_points,
                    activity_type,
                    DATE(created_at) as activity_date
                FROM guild_activities
                WHERE guild_id = ? AND character_id = ?
                GROUP BY activity_type, DATE(created_at)
                ORDER BY activity_date DESC
                LIMIT 30
            ");
            
            $stmt->execute([$this->guild_id, $this->character_id]);
            return $stmt->fetchAll();

        } catch (Exception $e) {
            error_log('Erro ao buscar estatísticas: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retorna o ranking de atividades da guilda
     */
    public function getActivityRanking($period = 'week') {
        try {
            $dateFilter = match($period) {
                'day' => 'DATE(ga.created_at) = CURDATE()',
                'week' => 'YEARWEEK(ga.created_at) = YEARWEEK(CURDATE())',
                'month' => 'MONTH(ga.created_at) = MONTH(CURDATE()) AND YEAR(ga.created_at) = YEAR(CURDATE())',
                'all' => '1=1',
                default => 'YEARWEEK(ga.created_at) = YEARWEEK(CURDATE())'
            };

            $stmt = $this->conn->prepare("
                SELECT 
                    fc.id as character_id,
                    fc.name as character_name,
                    fc.level as character_level,
                    fc.current_rank as character_rank,
                    gm.role as guild_role,
                    COUNT(ga.id) as activity_count,
                    SUM(ga.points) as total_points,
                    GROUP_CONCAT(DISTINCT ga.activity_type) as activity_types
                FROM forge_characters fc
                JOIN guild_members gm ON fc.id = gm.character_id
                LEFT JOIN guild_activities ga ON fc.id = ga.character_id
                WHERE gm.guild_id = ? AND {$dateFilter}
                GROUP BY fc.id
                ORDER BY total_points DESC
            ");
            
            $stmt->execute([$this->guild_id]);
            return $stmt->fetchAll();

        } catch (Exception $e) {
            error_log('Erro ao buscar ranking: ' . $e->getMessage());
            throw $e;
        }
    }
} 
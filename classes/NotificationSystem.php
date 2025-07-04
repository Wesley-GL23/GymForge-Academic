<?php

class NotificationSystem {
    private $db;
    private $character_id;

    public function __construct($db, $character_id) {
        $this->db = $db;
        $this->character_id = $character_id;
    }

    public function createNotification($type, $title, $message, $icon = "fa-bell", $color = "#4A90E2") {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO forge_notifications 
                (character_id, type, title, message, icon, color)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $this->character_id,
                $type,
                $title,
                $message,
                $icon,
                $color
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao criar notificação: " . $e->getMessage());
            return false;
        }
    }

    public function getUnreadNotifications() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM forge_notifications
                WHERE character_id = ? AND read_at IS NULL
                ORDER BY created_at DESC
            ");
            
            $stmt->execute([$this->character_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar notificações: " . $e->getMessage());
            return [];
        }
    }

    public function markAsRead($notification_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE forge_notifications
                SET read_at = CURRENT_TIMESTAMP
                WHERE id = ? AND character_id = ?
            ");
            
            return $stmt->execute([$notification_id, $this->character_id]);
        } catch (PDOException $e) {
            error_log("Erro ao marcar notificação como lida: " . $e->getMessage());
            return false;
        }
    }

    public function markAllAsRead() {
        try {
            $stmt = $this->db->prepare("
                UPDATE forge_notifications
                SET read_at = CURRENT_TIMESTAMP
                WHERE character_id = ? AND read_at IS NULL
            ");
            
            return $stmt->execute([$this->character_id]);
        } catch (PDOException $e) {
            error_log("Erro ao marcar todas notificações como lidas: " . $e->getMessage());
            return false;
        }
    }

    public function deleteOldNotifications($days = 30) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM forge_notifications
                WHERE character_id = ? 
                AND read_at IS NOT NULL
                AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            
            return $stmt->execute([$this->character_id, $days]);
        } catch (PDOException $e) {
            error_log("Erro ao deletar notificações antigas: " . $e->getMessage());
            return false;
        }
    }

    public function getNotificationCount() {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM forge_notifications
                WHERE character_id = ? AND read_at IS NULL
            ");
            
            $stmt->execute([$this->character_id]);
            $result = $stmt->fetch();
            return $result["count"];
        } catch (PDOException $e) {
            error_log("Erro ao contar notificações: " . $e->getMessage());
            return 0;
        }
    }

    public function getRecentNotifications($limit = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM forge_notifications
                WHERE character_id = ?
                ORDER BY created_at DESC
                LIMIT ?
            ");
            
            $stmt->execute([$this->character_id, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar notificações recentes: " . $e->getMessage());
            return [];
        }
    }
}

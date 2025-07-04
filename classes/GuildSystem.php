<?php

class GuildSystem {
    private $db;
    private $character_id;
    private $guild_id;

    public function __construct($db, $character_id) {
        $this->db = $db;
        $this->character_id = $character_id;
        $this->loadGuildInfo();
    }

    private function loadGuildInfo() {
        $stmt = $this->db->prepare("
            SELECT guild_id 
            FROM guild_members 
            WHERE character_id = ?
        ");
        $stmt->execute([$this->character_id]);
        
        if ($member = $stmt->fetch()) {
            $this->guild_id = $member['guild_id'];
        }
    }

    public function createGuild($name, $description = '') {
        // Verificar se o personagem já está em uma guilda
        if ($this->guild_id) {
            throw new Exception("Você já pertence a uma guilda");
        }

        // Verificar se o nome já existe
        $stmt = $this->db->prepare("
            SELECT id FROM forge_guilds 
            WHERE name = ?
        ");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            throw new Exception("Já existe uma guilda com este nome");
        }

        // Criar a guilda
        $stmt = $this->db->prepare("
            INSERT INTO forge_guilds 
            (name, leader_id, description) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$name, $this->character_id, $description]);
        $guild_id = $this->db->lastInsertId();

        // Adicionar líder como membro
        $stmt = $this->db->prepare("
            INSERT INTO guild_members 
            (guild_id, character_id, role) 
            VALUES (?, ?, 'leader')
        ");
        $stmt->execute([$guild_id, $this->character_id]);

        $this->guild_id = $guild_id;
        return $guild_id;
    }

    public function requestJoin($guild_id, $message = '') {
        // Verificar se já está em uma guilda
        if ($this->guild_id) {
            throw new Exception("Você já pertence a uma guilda");
        }

        // Verificar se já tem pedido pendente
        $stmt = $this->db->prepare("
            SELECT id FROM guild_join_requests 
            WHERE character_id = ? AND guild_id = ? AND status = 'pending'
        ");
        $stmt->execute([$this->character_id, $guild_id]);
        if ($stmt->fetch()) {
            throw new Exception("Você já tem um pedido pendente para esta guilda");
        }

        // Criar pedido
        $stmt = $this->db->prepare("
            INSERT INTO guild_join_requests 
            (guild_id, character_id, message) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$guild_id, $this->character_id, $message]);
    }

    public function handleJoinRequest($request_id, $accept = true) {
        // Verificar se é líder/oficial da guilda
        $stmt = $this->db->prepare("
            SELECT r.guild_id, r.character_id, g.member_count, g.max_members
            FROM guild_join_requests r
            JOIN forge_guilds g ON r.guild_id = g.id
            WHERE r.id = ? AND r.status = 'pending'
        ");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch();

        if (!$request) {
            throw new Exception("Pedido não encontrado ou já processado");
        }

        // Verificar permissão
        if (!$this->hasGuildPermission($request['guild_id'], ['leader', 'officer'])) {
            throw new Exception("Você não tem permissão para gerenciar pedidos");
        }

        if ($accept) {
            // Verificar limite de membros
            if ($request['member_count'] >= $request['max_members']) {
                throw new Exception("A guilda está cheia");
            }

            // Adicionar membro
            $stmt = $this->db->prepare("
                INSERT INTO guild_members 
                (guild_id, character_id) 
                VALUES (?, ?)
            ");
            $stmt->execute([$request['guild_id'], $request['character_id']]);

            // Atualizar contagem de membros
            $stmt = $this->db->prepare("
                UPDATE forge_guilds 
                SET member_count = member_count + 1 
                WHERE id = ?
            ");
            $stmt->execute([$request['guild_id']]);
        }

        // Atualizar status do pedido
        $stmt = $this->db->prepare("
            UPDATE guild_join_requests 
            SET status = ?, 
                response_date = NOW(), 
                responded_by = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $accept ? 'accepted' : 'rejected',
            $this->character_id,
            $request_id
        ]);
    }

    public function promoteGuildMember($member_id) {
        if (!$this->hasGuildPermission($this->guild_id, ['leader'])) {
            throw new Exception("Apenas o líder pode promover membros");
        }

        $stmt = $this->db->prepare("
            UPDATE guild_members 
            SET role = 'officer' 
            WHERE character_id = ? AND guild_id = ? AND role = 'member'
        ");
        $stmt->execute([$member_id, $this->guild_id]);
    }

    public function demoteGuildMember($member_id) {
        if (!$this->hasGuildPermission($this->guild_id, ['leader'])) {
            throw new Exception("Apenas o líder pode rebaixar oficiais");
        }

        $stmt = $this->db->prepare("
            UPDATE guild_members 
            SET role = 'member' 
            WHERE character_id = ? AND guild_id = ? AND role = 'officer'
        ");
        $stmt->execute([$member_id, $this->guild_id]);
    }

    public function kickGuildMember($member_id) {
        // Verificar permissão
        if (!$this->hasGuildPermission($this->guild_id, ['leader', 'officer'])) {
            throw new Exception("Você não tem permissão para expulsar membros");
        }

        // Verificar se não está tentando expulsar um oficial sendo oficial
        $stmt = $this->db->prepare("
            SELECT role 
            FROM guild_members 
            WHERE character_id = ? AND guild_id = ?
        ");
        $stmt->execute([$member_id, $this->guild_id]);
        $member = $stmt->fetch();

        if ($member['role'] === 'officer' && !$this->hasGuildPermission($this->guild_id, ['leader'])) {
            throw new Exception("Apenas o líder pode expulsar oficiais");
        }

        // Remover membro
        $stmt = $this->db->prepare("
            DELETE FROM guild_members 
            WHERE character_id = ? AND guild_id = ?
        ");
        $stmt->execute([$member_id, $this->guild_id]);

        // Atualizar contagem de membros
        $stmt = $this->db->prepare("
            UPDATE forge_guilds 
            SET member_count = member_count - 1 
            WHERE id = ?
        ");
        $stmt->execute([$this->guild_id]);
    }

    private function hasGuildPermission($guild_id, $allowed_roles) {
        $stmt = $this->db->prepare("
            SELECT role 
            FROM guild_members 
            WHERE character_id = ? AND guild_id = ?
        ");
        $stmt->execute([$this->character_id, $guild_id]);
        $member = $stmt->fetch();

        return $member && in_array($member['role'], $allowed_roles);
    }

    public function getGuildInfo($guild_id = null) {
        $guild_id = $guild_id ?? $this->guild_id;
        if (!$guild_id) return null;

        $stmt = $this->db->prepare("
            SELECT g.*, 
                   c.nome as leader_name,
                   COUNT(DISTINCT m.id) as active_members,
                   COUNT(DISTINCT r.id) as pending_requests
            FROM forge_guilds g
            LEFT JOIN forge_characters c ON g.leader_id = c.id
            LEFT JOIN guild_members m ON g.id = m.guild_id
            LEFT JOIN guild_join_requests r ON g.id = r.guild_id AND r.status = 'pending'
            WHERE g.id = ?
            GROUP BY g.id
        ");
        $stmt->execute([$guild_id]);
        return $stmt->fetch();
    }
} 
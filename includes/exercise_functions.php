<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth_functions.php';

/**
 * Lista todos os exercícios disponíveis
 */
function listar_exercicios($filtros = []) {
    $conn = get_connection();
    
    try {
        $sql = "SELECT * FROM exercicios WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($filtros['grupo_muscular'])) {
            $sql .= " AND grupo_muscular = ?";
            $params[] = $filtros['grupo_muscular'];
            $types .= "s";
        }
        
        if (!empty($filtros['nivel'])) {
            $sql .= " AND nivel = ?";
            $params[] = $filtros['nivel'];
            $types .= "s";
        }
        
        $sql .= " ORDER BY nome ASC";
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao listar exercícios: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca um exercício específico
 */
function buscar_exercicio($id) {
    $conn = get_connection();
    
    try {
        $stmt = $conn->prepare("SELECT * FROM exercicios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        error_log("Erro ao buscar exercício: " . $e->getMessage());
        return null;
    }
}

/**
 * Cria um novo exercício
 */
function criar_exercicio($dados) {
    requireNivel('admin');
    $conn = get_connection();
    
    try {
        $sql = "INSERT INTO exercicios (nome, descricao, grupo_muscular, nivel, video_url) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", 
            $dados['nome'],
            $dados['descricao'],
            $dados['grupo_muscular'],
            $dados['nivel'],
            $dados['video_url']
        );
        
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
        return false;
    } catch (Exception $e) {
        error_log("Erro ao criar exercício: " . $e->getMessage());
        return false;
    }
}

/**
 * Atualiza um exercício existente
 */
function atualizar_exercicio($id, $dados) {
    requireNivel('admin');
    $conn = get_connection();
    
    try {
        $sql = "UPDATE exercicios SET 
                nome = ?,
                descricao = ?,
                grupo_muscular = ?,
                nivel = ?,
                video_url = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi",
            $dados['nome'],
            $dados['descricao'],
            $dados['grupo_muscular'],
            $dados['nivel'],
            $dados['video_url'],
            $id
        );
        
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Erro ao atualizar exercício: " . $e->getMessage());
        return false;
    }
}

/**
 * Remove um exercício
 */
function deletar_exercicio($id) {
    requireNivel('admin');
    $conn = get_connection();
    
    try {
        $stmt = $conn->prepare("DELETE FROM exercicios WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Erro ao deletar exercício: " . $e->getMessage());
        return false;
    }
}

// Funções auxiliares
function listarCategorias() {
    return [
        'musculacao' => 'Musculação',
        'cardio' => 'Cardio',
        'funcional' => 'Funcional',
        'alongamento' => 'Alongamento'
    ];
}

function listarGruposMusculares() {
    return [
        'peito' => 'Peito',
        'costas' => 'Costas',
        'pernas' => 'Pernas',
        'ombros' => 'Ombros',
        'biceps' => 'Bíceps',
        'triceps' => 'Tríceps',
        'abdomen' => 'Abdômen',
        'gluteos' => 'Glúteos'
    ];
}

function listarNiveisDificuldade() {
    return [
        'iniciante' => 'Iniciante',
        'intermediario' => 'Intermediário',
        'avancado' => 'Avançado'
    ];
}

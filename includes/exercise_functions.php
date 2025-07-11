<?php
require_once __DIR__ . '/../config/config.php';

function buscarExercicios($filtros = []) {
    global $conn;
    
    $sql = "SELECT * FROM exercicios WHERE 1=1";
    $params = [];
    
    if (!empty($filtros['categoria'])) {
        $sql .= " AND categoria = ?";
        $params[] = $filtros['categoria'];
    }
    
    if (!empty($filtros['nivel'])) {
        $sql .= " AND nivel_dificuldade = ?";
        $params[] = $filtros['nivel'];
    }
    
    if (!empty($filtros['busca'])) {
        $sql .= " AND (nome LIKE ? OR descricao LIKE ?)";
        $termo = "%{$filtros['busca']}%";
        $params[] = $termo;
        $params[] = $termo;
    }
    
    $sql .= " ORDER BY nome";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao buscar exercícios: " . $e->getMessage());
        return [];
    }
}

function buscarExercicioPorId($id) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT * FROM exercicios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Erro ao buscar exercício: " . $e->getMessage());
        return null;
    }
}

function atualizarExercicio($id, $dados) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            UPDATE exercicios 
            SET nome = ?, categoria = ?, descricao = ?, nivel_dificuldade = ?, video_url = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $dados['nome'],
            $dados['categoria'],
            $dados['descricao'],
            $dados['nivel_dificuldade'],
            $dados['video_url'] ?? null,
            $id
        ]);
    } catch (Exception $e) {
        error_log("Erro ao atualizar exercício: " . $e->getMessage());
        return false;
    }
}

function excluirExercicio($id) {
    global $conn;
    try {
        // Primeiro busca o exercício para pegar o vídeo
        $exercicio = buscarExercicioPorId($id);
        if ($exercicio && !empty($exercicio['video_url'])) {
            $video_path = __DIR__ . '/../' . ltrim($exercicio['video_url'], '/');
            if (file_exists($video_path)) {
                unlink($video_path);
            }
        }
        
        // Depois exclui do banco
        $stmt = $conn->prepare("DELETE FROM exercicios WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        error_log("Erro ao excluir exercício: " . $e->getMessage());
        return false;
    }
}

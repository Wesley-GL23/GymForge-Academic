<?php
require_once 'auth_functions.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Cria um novo treino
 */
function criar_treino($usuario_id, $nome, $descricao, $tipo, $nivel_dificuldade, $data_inicio, $data_fim = null) {
    $conn = get_connection();
    
    try {
        $sql = "INSERT INTO treinos (usuario_id, nome, descricao, tipo, nivel_dificuldade, data_inicio, data_fim) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $usuario_id, $nome, $descricao, $tipo, $nivel_dificuldade, $data_inicio, $data_fim);
        
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
        return false;
    } catch (Exception $e) {
        error_log("Erro ao criar treino: " . $e->getMessage());
        return false;
    }
}

/**
 * Adiciona exercícios a um treino
 */
function adicionar_exercicios_treino($treino_id, $exercicios) {
    $conn = get_connection();
    
    try {
        $sql = "INSERT INTO treino_exercicios (treino_id, exercicio_id, ordem, series, repeticoes, peso, tempo_descanso, observacoes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        foreach ($exercicios as $ex) {
            $stmt->bind_param("iiiiidis", 
                $treino_id, 
                $ex['exercicio_id'], 
                $ex['ordem'], 
                $ex['series'], 
                $ex['repeticoes'], 
                $ex['peso'], 
                $ex['tempo_descanso'], 
                $ex['observacoes']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Erro ao adicionar exercício ao treino");
            }
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Erro ao adicionar exercícios ao treino: " . $e->getMessage());
        return false;
    }
}

/**
 * Busca um treino específico
 */
function buscar_treino($treino_id, $usuario_id = null) {
    $conn = get_connection();
    
    try {
        $sql = "SELECT t.*, u.nome as nome_usuario 
                FROM treinos t 
                JOIN usuarios u ON t.usuario_id = u.id 
                WHERE t.id = ?";
                
        if ($usuario_id !== null) {
            $sql .= " AND t.usuario_id = ?";
        }
        
        $stmt = $conn->prepare($sql);
        
        if ($usuario_id !== null) {
            $stmt->bind_param("ii", $treino_id, $usuario_id);
        } else {
            $stmt->bind_param("i", $treino_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $treino = $result->fetch_assoc();
            $treino['exercicios'] = buscar_exercicios_treino($treino_id);
            return $treino;
        }
        return null;
    } catch (Exception $e) {
        error_log("Erro ao buscar treino: " . $e->getMessage());
        return null;
    }
}

/**
 * Busca os exercícios de um treino
 */
function buscar_exercicios_treino($treino_id) {
    $conn = get_connection();
    
    try {
        $sql = "SELECT te.*, e.nome as nome_exercicio, e.grupo_muscular 
                FROM treino_exercicios te 
                JOIN exercicios e ON te.exercicio_id = e.id 
                WHERE te.treino_id = ? 
                ORDER BY te.ordem";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $treino_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao buscar exercícios do treino: " . $e->getMessage());
        return [];
    }
}

/**
 * Lista treinos de um usuário
 */
function listar_treinos($usuario_id = null, $status = null, $limite = null) {
    $conn = get_connection();
    
    try {
        $sql = "SELECT t.*, u.nome as nome_usuario 
                FROM treinos t 
                JOIN usuarios u ON t.usuario_id = u.id";
        
        $where = [];
        $params = [];
        $types = "";
        
        if ($usuario_id !== null) {
            $where[] = "t.usuario_id = ?";
            $params[] = $usuario_id;
            $types .= "i";
        }
        
        if ($status !== null) {
            $where[] = "t.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY t.data_inicio DESC";
        
        if ($limite !== null) {
            $sql .= " LIMIT ?";
            $params[] = $limite;
            $types .= "i";
        }
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao listar treinos: " . $e->getMessage());
        return [];
    }
}

/**
 * Atualiza um treino
 */
function atualizar_treino($treino_id, $usuario_id, $dados) {
    $conn = get_connection();
    
    try {
        // Primeiro verifica se o treino pertence ao usuário
        $treino = buscar_treino($treino_id, $usuario_id);
        if (!$treino) {
            return false;
        }
        
        $sql = "UPDATE treinos SET 
                nome = ?, 
                descricao = ?, 
                tipo = ?, 
                nivel_dificuldade = ?, 
                data_inicio = ?, 
                data_fim = ?, 
                status = ? 
                WHERE id = ? AND usuario_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssii", 
            $dados['nome'], 
            $dados['descricao'], 
            $dados['tipo'], 
            $dados['nivel_dificuldade'], 
            $dados['data_inicio'], 
            $dados['data_fim'], 
            $dados['status'], 
            $treino_id, 
            $usuario_id
        );
        
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Erro ao atualizar treino: " . $e->getMessage());
        return false;
    }
}

/**
 * Atualiza exercícios de um treino
 */
function atualizar_exercicios_treino($treino_id, $usuario_id, $exercicios) {
    $conn = get_connection();
    
    try {
        // Verifica se o treino pertence ao usuário
        $treino = buscar_treino($treino_id, $usuario_id);
        if (!$treino) {
            return false;
        }
        
        // Inicia transação
        $conn->begin_transaction();
        
        // Remove exercícios antigos
        $sql = "DELETE FROM treino_exercicios WHERE treino_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $treino_id);
        $stmt->execute();
        
        // Adiciona novos exercícios
        if (!adicionar_exercicios_treino($treino_id, $exercicios)) {
            throw new Exception("Erro ao adicionar novos exercícios");
        }
        
        // Confirma transação
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Erro ao atualizar exercícios do treino: " . $e->getMessage());
        return false;
    }
}

/**
 * Deleta um treino
 */
function deletar_treino($treino_id, $usuario_id) {
    $conn = get_connection();
    
    try {
        // Verifica se o treino pertence ao usuário
        $treino = buscar_treino($treino_id, $usuario_id);
        if (!$treino) {
            return false;
        }
        
        $sql = "DELETE FROM treinos WHERE id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $treino_id, $usuario_id);
        
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Erro ao deletar treino: " . $e->getMessage());
        return false;
    }
}

/**
 * Registra progresso em um treino
 */
function registrar_progresso_treino($treino_id, $exercicio_id, $usuario_id, $dados) {
    $conn = get_connection();
    
    try {
        $sql = "INSERT INTO progresso_treinos 
                (treino_id, exercicio_id, usuario_id, data_execucao, series_completadas, 
                peso_utilizado, dificuldade_percebida, observacoes) 
                VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiidis", 
            $treino_id, 
            $exercicio_id, 
            $usuario_id, 
            $dados['series_completadas'], 
            $dados['peso_utilizado'], 
            $dados['dificuldade_percebida'], 
            $dados['observacoes']
        );
        
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Erro ao registrar progresso: " . $e->getMessage());
        return false;
    }
}
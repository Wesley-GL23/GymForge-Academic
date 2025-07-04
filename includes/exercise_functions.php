<?php
require_once __DIR__ . '/../config/conexao.php';

function listarExercicios() {
    global $conn;
    try {
        $stmt = $conn->query("SELECT * FROM exercicios ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao listar exercícios: " . $e->getMessage());
        return [];
    }
}
function buscarExercicio($id) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT * FROM exercicios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao buscar exercício: " . $e->getMessage());
        return null;
    }
}

function criarExercicio($dados) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            INSERT INTO exercicios (
                nome, descricao, categoria, grupo_muscular, 
                nivel_dificuldade, gif_url, video_url, 
                instrucoes, dicas_seguranca
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $dados['nome'],
            $dados['descricao'],
            $dados['categoria'],
            $dados['grupo_muscular'],
            $dados['nivel_dificuldade'],
            $dados['gif_url'] ?? null,
            $dados['video_url'] ?? null,
            $dados['instrucoes'],
            $dados['dicas_seguranca']
        ]);
    } catch (Exception $e) {
        error_log("Erro ao criar exercício: " . $e->getMessage());
        return false;
    }
}
function atualizarExercicio($id, $dados) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            UPDATE exercicios SET 
                nome = ?,
                descricao = ?,
                categoria = ?,
                grupo_muscular = ?,
                nivel_dificuldade = ?,
                gif_url = ?,
                video_url = ?,
                instrucoes = ?,
                dicas_seguranca = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $dados['nome'],
            $dados['descricao'],
            $dados['categoria'],
            $dados['grupo_muscular'],
            $dados['nivel_dificuldade'],
            $dados['gif_url'] ?? null,
            $dados['video_url'] ?? null,
            $dados['instrucoes'],
            $dados['dicas_seguranca'],
            $id
        ]);
    } catch (Exception $e) {
        error_log("Erro ao atualizar exercício: " . $e->getMessage());
        return false;
    }
}

function deletarExercicio($id) {
    global $conn;
    try {
        $stmt = $conn->prepare("DELETE FROM exercicios WHERE id = ?");
        return $stmt->execute([$id]);
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

<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/exercise_functions.php';

// Verifica se o usuário está logado e é admin
requireAdmin();

// Verifica o token CSRF
if (!isset($_POST['csrf_token']) || !verificarCsrfToken($_POST['csrf_token'])) {
    $_SESSION['erro'] = "Token de segurança inválido";
    header('Location: /GymForge-Academic/views/exercicios/biblioteca.php');
    exit;
}

$acao = $_POST['acao'] ?? '';
$conn = get_connection();

try {
    switch ($acao) {
        case 'criar':
            // Validar dados
            $erros = validarDadosExercicio($_POST);
            if (!empty($erros)) {
                $_SESSION['erro'] = implode("<br>", $erros);
                $_SESSION['form_data'] = $_POST;
                header('Location: /GymForge-Academic/forms/exercicio/form.php');
                exit;
            }

            // Processar uploads
            $video_url = null;
            $imagem_url = null;

            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $video_url = processarUploadExercicio($_FILES, 'video');
            }

            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imagem_url = processarUploadExercicio($_FILES, 'imagem');
            }

            // Preparar dados para inserção
            $stmt = $conn->prepare("
                INSERT INTO exercicios (
                    nome, descricao, categoria, grupo_muscular, 
                    nivel_dificuldade, equipamento, video_url, 
                    imagem_url, instrucoes, dicas_seguranca,
                    created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("ssssssssssi",
                $_POST['nome'],
                $_POST['descricao'],
                $_POST['categoria'],
                $_POST['grupo_muscular'],
                $_POST['nivel_dificuldade'],
                $_POST['equipamento'],
                $video_url,
                $imagem_url,
                $_POST['instrucoes'],
                $_POST['dicas_seguranca'],
                $_SESSION['user_id']
            );

            if ($stmt->execute()) {
                $_SESSION['mensagem'] = "Exercício criado com sucesso!";
                header('Location: /GymForge-Academic/views/exercicios/biblioteca.php');
                exit;
            } else {
                throw new Exception("Erro ao criar exercício");
            }
            break;

        case 'atualizar':
            if (!isset($_POST['id'])) {
                throw new Exception("ID do exercício não fornecido");
            }

            // Validar dados
            $erros = validarDadosExercicio($_POST);
            if (!empty($erros)) {
                $_SESSION['erro'] = implode("<br>", $erros);
                $_SESSION['form_data'] = $_POST;
                header('Location: /GymForge-Academic/forms/exercicio/form.php?id=' . $_POST['id']);
                exit;
            }

            // Buscar exercício atual para manter URLs de mídia se não houver novos uploads
            $exercicio_atual = buscar_exercicio($_POST['id']);
            
            // Processar novos uploads
            $video_url = $exercicio_atual['video_url'];
            $imagem_url = $exercicio_atual['imagem_url'];

            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $video_url = processarUploadExercicio($_FILES, 'video');
                // Remover vídeo antigo se existir
                if ($exercicio_atual['video_url']) {
                    unlink(__DIR__ . '/../../' . ltrim($exercicio_atual['video_url'], '/'));
                }
            }

            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imagem_url = processarUploadExercicio($_FILES, 'imagem');
                // Remover imagem antiga se existir
                if ($exercicio_atual['imagem_url']) {
                    unlink(__DIR__ . '/../../' . ltrim($exercicio_atual['imagem_url'], '/'));
                }
            }

            // Atualizar exercício
            $stmt = $conn->prepare("
                UPDATE exercicios SET
                    nome = ?,
                    descricao = ?,
                    categoria = ?,
                    grupo_muscular = ?,
                    nivel_dificuldade = ?,
                    equipamento = ?,
                    video_url = ?,
                    imagem_url = ?,
                    instrucoes = ?,
                    dicas_seguranca = ?
                WHERE id = ?
            ");

            $stmt->bind_param("ssssssssssi",
                $_POST['nome'],
                $_POST['descricao'],
                $_POST['categoria'],
                $_POST['grupo_muscular'],
                $_POST['nivel_dificuldade'],
                $_POST['equipamento'],
                $video_url,
                $imagem_url,
                $_POST['instrucoes'],
                $_POST['dicas_seguranca'],
                $_POST['id']
            );

            if ($stmt->execute()) {
                $_SESSION['mensagem'] = "Exercício atualizado com sucesso!";
                header('Location: /GymForge-Academic/views/exercicios/biblioteca.php');
                exit;
            } else {
                throw new Exception("Erro ao atualizar exercício");
            }
            break;

        case 'excluir':
            if (!isset($_POST['id'])) {
                throw new Exception("ID do exercício não fornecido");
            }

            // Buscar exercício para remover arquivos de mídia
            $exercicio = buscar_exercicio($_POST['id']);
            
            // Remover arquivos de mídia
            if ($exercicio['video_url']) {
                unlink(__DIR__ . '/../../' . ltrim($exercicio['video_url'], '/'));
            }
            if ($exercicio['imagem_url']) {
                unlink(__DIR__ . '/../../' . ltrim($exercicio['imagem_url'], '/'));
            }

            // Excluir exercício
            $stmt = $conn->prepare("DELETE FROM exercicios WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);

            if ($stmt->execute()) {
                $_SESSION['mensagem'] = "Exercício excluído com sucesso!";
                header('Location: /GymForge-Academic/views/exercicios/biblioteca.php');
                exit;
            } else {
                throw new Exception("Erro ao excluir exercício");
            }
            break;

        default:
            throw new Exception("Ação inválida");
    }
} catch (Exception $e) {
    error_log("Erro no CRUD de exercícios: " . $e->getMessage());
    $_SESSION['erro'] = "Erro ao processar a solicitação: " . $e->getMessage();
    header('Location: /GymForge-Academic/views/exercicios/biblioteca.php');
    exit;
}

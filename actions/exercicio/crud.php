<?php
require_once '../../config/conexao.php';
require_once '../../includes/auth_functions.php';
require_once '../../includes/exercise_functions.php';

// Verifica se o usuário está logado e é admin
requireAdmin();

// Verifica o token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        die('Token CSRF inválido');
    }
}

$acao = $_GET['acao'] ?? '';
$response = ['success' => false, 'message' => ''];

switch ($acao) {
    case 'criar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'nome' => $_POST['nome'] ?? '',
                'descricao' => $_POST['descricao'] ?? '',
                'categoria' => $_POST['categoria'] ?? '',
                'grupo_muscular' => $_POST['grupo_muscular'] ?? '',
                'nivel_dificuldade' => $_POST['nivel_dificuldade'] ?? 'iniciante',
                'gif_url' => $_POST['gif_url'] ?? null,
                'video_url' => $_POST['video_url'] ?? null,
                'instrucoes' => $_POST['instrucoes'] ?? '',
                'dicas_seguranca' => $_POST['dicas_seguranca'] ?? ''
            ];

            if (criarExercicio($dados)) {
                $response = ['success' => true, 'message' => 'Exercício criado com sucesso!'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao criar exercício.'];
            }
        }
        break;

    case 'atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $dados = [
                'nome' => $_POST['nome'] ?? '',
                'descricao' => $_POST['descricao'] ?? '',
                'categoria' => $_POST['categoria'] ?? '',
                'grupo_muscular' => $_POST['grupo_muscular'] ?? '',
                'nivel_dificuldade' => $_POST['nivel_dificuldade'] ?? 'iniciante',
                'gif_url' => $_POST['gif_url'] ?? null,
                'video_url' => $_POST['video_url'] ?? null,
                'instrucoes' => $_POST['instrucoes'] ?? '',
                'dicas_seguranca' => $_POST['dicas_seguranca'] ?? ''
            ];

            if (atualizarExercicio($id, $dados)) {
                $response = ['success' => true, 'message' => 'Exercício atualizado com sucesso!'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao atualizar exercício.'];
            }
        }
        break;

    case 'deletar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            if (deletarExercicio($id)) {
                $response = ['success' => true, 'message' => 'Exercício deletado com sucesso!'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao deletar exercício.'];
            }
        }
        break;

    case 'buscar':
        $id = $_GET['id'] ?? 0;
        $exercicio = buscarExercicio($id);
        if ($exercicio) {
            $response = ['success' => true, 'data' => $exercicio];
        } else {
            $response = ['success' => false, 'message' => 'Exercício não encontrado.'];
        }
        break;

    case 'listar':
        $exercicios = listarExercicios();
        $response = ['success' => true, 'data' => $exercicios];
        break;

    default:
        $response = ['success' => false, 'message' => 'Ação inválida.'];
}

// Retorna a resposta em JSON
header('Content-Type: application/json');
echo json_encode($response);

<?php
require_once '../../config/conexao.php';
require_once '../../includes/auth_functions.php';
require_once '../../includes/exercise_functions.php';

// Verifica se o usuário está logado e é admin
requireNivel('admin');

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
                'grupo_muscular' => $_POST['grupo_muscular'] ?? '',
                'nivel' => $_POST['nivel_dificuldade'] ?? 'iniciante',
                'video_url' => $_POST['video_url'] ?? null
            ];

            if (criar_exercicio($dados)) {
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
                'grupo_muscular' => $_POST['grupo_muscular'] ?? '',
                'nivel' => $_POST['nivel_dificuldade'] ?? 'iniciante',
                'video_url' => $_POST['video_url'] ?? null
            ];

            if (atualizar_exercicio($id, $dados)) {
                $response = ['success' => true, 'message' => 'Exercício atualizado com sucesso!'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao atualizar exercício.'];
            }
        }
        break;

    case 'deletar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            if (deletar_exercicio($id)) {
                $response = ['success' => true, 'message' => 'Exercício deletado com sucesso!'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao deletar exercício.'];
            }
        }
        break;

    case 'buscar':
        $id = $_GET['id'] ?? 0;
        $exercicio = buscar_exercicio($id);
        if ($exercicio) {
            $response = ['success' => true, 'data' => $exercicio];
        } else {
            $response = ['success' => false, 'message' => 'Exercício não encontrado.'];
        }
        break;

    case 'listar':
        $exercicios = listar_exercicios();
        $response = ['success' => true, 'data' => $exercicios];
        break;

    default:
        $response = ['success' => false, 'message' => 'Ação inválida.'];
}

// Retorna a resposta em JSON
header('Content-Type: application/json');
echo json_encode($response);

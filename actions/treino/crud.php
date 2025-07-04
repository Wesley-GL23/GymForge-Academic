<?php
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../includes/training_functions.php';

// Verifica se o usuário está logado
if (!esta_logado()) {
    header('Location: /forms/usuario/login.php');
    exit;
}

// Verifica o token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verificar_csrf_token($_POST['csrf_token'])) {
        $_SESSION['erro'] = "Erro de validação do formulário.";
        header('Location: /forms/treino/form.php');
        exit;
    }
}

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$usuario_id = $_SESSION['usuario_id'];

switch ($acao) {
    case 'criar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro'] = "Método não permitido";
            header('Location: /forms/treino/form.php');
            exit;
        }

        // Validação dos campos
        $erros = [];
        if (empty($_POST['nome'])) $erros[] = "Nome é obrigatório";
        if (empty($_POST['data_inicio'])) $erros[] = "Data de início é obrigatória";
        if (empty($_POST['exercicios'])) $erros[] = "Selecione pelo menos um exercício";

        if (!empty($erros)) {
            $_SESSION['erro'] = implode("<br>", $erros);
            $_SESSION['form_data'] = $_POST;
            header('Location: /forms/treino/form.php');
            exit;
        }

        // Cria o treino
        $treino_id = criar_treino(
            $usuario_id,
            $_POST['nome'],
            $_POST['descricao'],
            $_POST['tipo'],
            $_POST['nivel_dificuldade'],
            $_POST['data_inicio'],
            $_POST['data_fim']
        );

        if ($treino_id) {
            // Prepara os exercícios
            $exercicios = [];
            foreach ($_POST['exercicios'] as $ordem => $ex) {
                $exercicios[] = [
                    'exercicio_id' => $ex['id'],
                    'ordem' => $ordem + 1,
                    'series' => $ex['series'],
                    'repeticoes' => $ex['repeticoes'],
                    'peso' => $ex['peso'] ?? null,
                    'tempo_descanso' => $ex['tempo_descanso'] ?? null,
                    'observacoes' => $ex['observacoes'] ?? null
                ];
            }

            if (adicionar_exercicios_treino($treino_id, $exercicios)) {
                $_SESSION['sucesso'] = "Treino criado com sucesso!";
                header('Location: /views/treinos/visualizar.php?id=' . $treino_id);
                exit;
            }
        }

        $_SESSION['erro'] = "Erro ao criar treino";
        $_SESSION['form_data'] = $_POST;
        header('Location: /forms/treino/form.php');
        break;

    case 'atualizar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro'] = "Método não permitido";
            header('Location: /forms/treino/form.php');
            exit;
        }

        $treino_id = $_POST['id'] ?? null;
        if (!$treino_id) {
            $_SESSION['erro'] = "ID do treino não fornecido";
            header('Location: /views/treinos/');
            exit;
        }

        // Validação dos campos
        $erros = [];
        if (empty($_POST['nome'])) $erros[] = "Nome é obrigatório";
        if (empty($_POST['data_inicio'])) $erros[] = "Data de início é obrigatória";
        if (empty($_POST['exercicios'])) $erros[] = "Selecione pelo menos um exercício";

        if (!empty($erros)) {
            $_SESSION['erro'] = implode("<br>", $erros);
            $_SESSION['form_data'] = $_POST;
            header('Location: /forms/treino/form.php?id=' . $treino_id);
            exit;
        }

        // Atualiza o treino
        $dados = [
            'nome' => $_POST['nome'],
            'descricao' => $_POST['descricao'],
            'tipo' => $_POST['tipo'],
            'nivel_dificuldade' => $_POST['nivel_dificuldade'],
            'data_inicio' => $_POST['data_inicio'],
            'data_fim' => $_POST['data_fim'],
            'status' => $_POST['status']
        ];

        if (atualizar_treino($treino_id, $usuario_id, $dados)) {
            // Prepara os exercícios
            $exercicios = [];
            foreach ($_POST['exercicios'] as $ordem => $ex) {
                $exercicios[] = [
                    'exercicio_id' => $ex['id'],
                    'ordem' => $ordem + 1,
                    'series' => $ex['series'],
                    'repeticoes' => $ex['repeticoes'],
                    'peso' => $ex['peso'] ?? null,
                    'tempo_descanso' => $ex['tempo_descanso'] ?? null,
                    'observacoes' => $ex['observacoes'] ?? null
                ];
            }

            if (atualizar_exercicios_treino($treino_id, $usuario_id, $exercicios)) {
                $_SESSION['sucesso'] = "Treino atualizado com sucesso!";
                header('Location: /views/treinos/visualizar.php?id=' . $treino_id);
                exit;
            }
        }

        $_SESSION['erro'] = "Erro ao atualizar treino";
        $_SESSION['form_data'] = $_POST;
        header('Location: /forms/treino/form.php?id=' . $treino_id);
        break;

    case 'deletar':
        $treino_id = $_GET['id'] ?? null;
        if (!$treino_id) {
            $_SESSION['erro'] = "ID do treino não fornecido";
            header('Location: /views/treinos/');
            exit;
        }

        if (deletar_treino($treino_id, $usuario_id)) {
            $_SESSION['sucesso'] = "Treino deletado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao deletar treino";
        }
        header('Location: /views/treinos/');
        break;

    case 'registrar_progresso':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro'] = "Método não permitido";
            header('Location: /views/treinos/');
            exit;
        }

        $treino_id = $_POST['treino_id'] ?? null;
        $exercicio_id = $_POST['exercicio_id'] ?? null;

        if (!$treino_id || !$exercicio_id) {
            $_SESSION['erro'] = "Dados incompletos";
            header('Location: /views/treinos/');
            exit;
        }

        $dados = [
            'series_completadas' => $_POST['series_completadas'],
            'peso_utilizado' => $_POST['peso_utilizado'],
            'dificuldade_percebida' => $_POST['dificuldade_percebida'],
            'observacoes' => $_POST['observacoes'] ?? null
        ];

        if (registrar_progresso_treino($treino_id, $exercicio_id, $usuario_id, $dados)) {
            $_SESSION['sucesso'] = "Progresso registrado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao registrar progresso";
        }
        header('Location: /views/treinos/visualizar.php?id=' . $treino_id);
        break;

    default:
        $_SESSION['erro'] = "Ação inválida";
        header('Location: /views/treinos/');
        break;
}
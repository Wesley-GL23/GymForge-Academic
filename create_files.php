<?php
// Função para criar arquivo com conteúdo
function createFile($path, $content) {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($path, $content);
    echo "Arquivo criado: $path\n";
}

// Conteúdo dos arquivos
$exerciseFunctions = <<<'EOD'
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
EOD;

$exercicioCrud = <<<'EOD'
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
EOD;

// Criar os arquivos
createFile('includes/exercise_functions.php', $exerciseFunctions);
createFile('actions/exercicio/crud.php', $exercicioCrud);

echo "Arquivos criados com sucesso!\n";
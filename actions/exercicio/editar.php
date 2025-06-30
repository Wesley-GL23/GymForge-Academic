<?php
require_once '../../includes/header.php';
requireAdmin();

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setMessage('Método inválido', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
    exit();
}

// Validação dos campos obrigatórios
if (empty($_POST['id']) || empty($_POST['nome']) || empty($_POST['categoria']) || empty($_POST['descricao'])) {
    setMessage('Preencha todos os campos obrigatórios', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/editar.php?id=' . $_POST['id']);
    exit();
}

$conn = conectarBD();

// Buscar exercício atual para comparar alterações
$id = (int)$_POST['id'];
$sql = "SELECT * FROM exercicios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$exercicio = $stmt->get_result()->fetch_assoc();

if (!$exercicio) {
    setMessage('Exercício não encontrado', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
    exit();
}

// Preparar os dados
$nome = limparString($conn, $_POST['nome']);
$categoria = limparString($conn, $_POST['categoria']);
$descricao = limparString($conn, $_POST['descricao']);
$instrucoes = !empty($_POST['instrucoes']) ? limparString($conn, $_POST['instrucoes']) : null;
$dicas = !empty($_POST['dicas']) ? limparString($conn, $_POST['dicas']) : null;
$gif_url = $exercicio['gif_url']; // Mantém o GIF atual por padrão

// Processar upload do novo GIF se existir
if (isset($_FILES['gif']) && $_FILES['gif']['error'] === UPLOAD_ERR_OK) {
    $gif = $_FILES['gif'];
    
    // Validar tipo do arquivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $gif['tmp_name']);
    finfo_close($finfo);
    
    if ($mime_type !== 'image/gif') {
        setMessage('O arquivo deve ser um GIF', 'danger');
        header('Location: ' . BASE_URL . '/views/exercicio/editar.php?id=' . $id);
        exit();
    }
    
    // Validar tamanho (2MB)
    if ($gif['size'] > 2 * 1024 * 1024) {
        setMessage('O arquivo deve ter no máximo 2MB', 'danger');
        header('Location: ' . BASE_URL . '/views/exercicio/editar.php?id=' . $id);
        exit();
    }
    
    // Remover GIF antigo se existir
    if ($gif_url) {
        $old_gif_path = str_replace(BASE_URL, '../../', $gif_url);
        if (file_exists($old_gif_path)) {
            unlink($old_gif_path);
        }
    }
    
    // Gerar nome único para o arquivo
    $nome_arquivo = uniqid() . '.gif';
    $diretorio = '../../assets/gifs/exercicios/' . strtolower($categoria);
    
    // Criar diretório se não existir
    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }
    
    // Mover o arquivo
    if (move_uploaded_file($gif['tmp_name'], $diretorio . '/' . $nome_arquivo)) {
        $gif_url = BASE_URL . '/assets/gifs/exercicios/' . strtolower($categoria) . '/' . $nome_arquivo;
    } else {
        setMessage('Erro ao fazer upload do GIF', 'danger');
        header('Location: ' . BASE_URL . '/views/exercicio/editar.php?id=' . $id);
        exit();
    }
}

// Atualizar no banco
$sql = "UPDATE exercicios SET nome = ?, categoria = ?, descricao = ?, instrucoes = ?, dicas = ?, gif_url = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssi", $nome, $categoria, $descricao, $instrucoes, $dicas, $gif_url, $id);

if ($stmt->execute()) {
    setMessage('Exercício atualizado com sucesso', 'success');
    header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
} else {
    setMessage('Erro ao atualizar exercício: ' . $conn->error, 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/editar.php?id=' . $id);
}

fecharConexao($conn);
exit(); 
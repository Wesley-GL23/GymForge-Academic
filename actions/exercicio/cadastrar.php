<?php
require_once '../../includes/header.php';
requireAdmin();

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setMessage('Método inválido', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/cadastro.php');
    exit();
}

// Validação dos campos obrigatórios
if (empty($_POST['nome']) || empty($_POST['categoria']) || empty($_POST['descricao'])) {
    setMessage('Preencha todos os campos obrigatórios', 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/cadastro.php');
    exit();
}

$conn = conectarBD();

// Preparar os dados
$nome = limparString($conn, $_POST['nome']);
$categoria = limparString($conn, $_POST['categoria']);
$descricao = limparString($conn, $_POST['descricao']);
$instrucoes = !empty($_POST['instrucoes']) ? limparString($conn, $_POST['instrucoes']) : null;
$dicas = !empty($_POST['dicas']) ? limparString($conn, $_POST['dicas']) : null;
$gif_url = null;

// Processar upload do GIF se existir
if (isset($_FILES['gif']) && $_FILES['gif']['error'] === UPLOAD_ERR_OK) {
    $gif = $_FILES['gif'];
    
    // Validar tipo do arquivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $gif['tmp_name']);
    finfo_close($finfo);
    
    if ($mime_type !== 'image/gif') {
        setMessage('O arquivo deve ser um GIF', 'danger');
        header('Location: ' . BASE_URL . '/views/exercicio/cadastro.php');
        exit();
    }
    
    // Validar tamanho (2MB)
    if ($gif['size'] > 2 * 1024 * 1024) {
        setMessage('O arquivo deve ter no máximo 2MB', 'danger');
        header('Location: ' . BASE_URL . '/views/exercicio/cadastro.php');
        exit();
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
        header('Location: ' . BASE_URL . '/views/exercicio/cadastro.php');
        exit();
    }
}

// Inserir no banco
$sql = "INSERT INTO exercicios (nome, categoria, descricao, instrucoes, dicas, gif_url) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $nome, $categoria, $descricao, $instrucoes, $dicas, $gif_url);

if ($stmt->execute()) {
    setMessage('Exercício cadastrado com sucesso', 'success');
    header('Location: ' . BASE_URL . '/views/exercicio/listar.php');
} else {
    setMessage('Erro ao cadastrar exercício: ' . $conn->error, 'danger');
    header('Location: ' . BASE_URL . '/views/exercicio/cadastro.php');
}

fecharConexao($conn);
exit(); 
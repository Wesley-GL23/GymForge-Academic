<?php
require_once __DIR__ . '/../includes/auth_functions.php';

// Verifica se o usuário está logado e é admin
requireAdmin();

$videos_dir = __DIR__ . '/../assets/videos';
$target_dir = __DIR__ . '/../assets/exercises/videos';
$log_file = __DIR__ . '/video_organization.log';

// Função para registrar operações no log
function log_operation($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Função para criar diretórios das categorias
function create_category_dirs() {
    global $target_dir;
    $categories = ['musculacao', 'cardio', 'funcional', 'alongamento', 'yoga', 'pilates'];
    foreach ($categories as $category) {
        $dir = "$target_dir/$category";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}

// Função para mover um vídeo para sua categoria
function move_video($video_file, $category) {
    global $videos_dir, $target_dir;
    
    $source = "$videos_dir/$video_file";
    $target = "$target_dir/$category/$video_file";
    
    if (!file_exists($source)) {
        log_operation("ERRO: Arquivo não encontrado: $video_file");
        return false;
    }
    
    if (!is_dir("$target_dir/$category")) {
        mkdir("$target_dir/$category", 0777, true);
    }
    
    if (copy($source, $target)) {
        log_operation("Sucesso: $video_file movido para $category/");
        return true;
    } else {
        log_operation("ERRO: Falha ao mover $video_file para $category/");
        return false;
    }
}

// Inicializa o arquivo de log
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    file_put_contents($log_file, "\n=== Nova operação iniciada em " . date('Y-m-d H:i:s') . " ===\n");
}

// Cria os diretórios das categorias
create_category_dirs();

// Processa a requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Organiza um único vídeo
    $video = $_POST['video'] ?? '';
    $category = $_POST['category'] ?? '';
    
    if ($video && $category) {
        if (move_video($video, $category)) {
            $_SESSION['mensagem'] = "Vídeo organizado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao organizar o vídeo.";
        }
    }
    
    header('Location: preview_videos.php');
    exit;
} elseif (isset($_GET['action']) && $_GET['action'] === 'organize_all') {
    // Organiza todos os vídeos baseado no exercises.json
    $exercises_json = __DIR__ . '/../database/exercises.json';
    
    if (file_exists($exercises_json)) {
        $json_content = file_get_contents($exercises_json);
        $exercises_data = json_decode($json_content, true);
        
        foreach ($exercises_data['exercises'] as $exercise) {
            $video = $exercise['video'];
            $category = $exercise['category'];
            
            // Converte as categorias do JSON para as pastas
            if ($category === 'strength') $category = 'musculacao';
            elseif ($category === 'core') $category = 'funcional';
            
            move_video($video, $category);
        }
        
        $_SESSION['mensagem'] = "Todos os vídeos foram organizados!";
    } else {
        $_SESSION['erro'] = "Arquivo de mapeamento não encontrado.";
    }
    
    header('Location: preview_videos.php');
    exit;
}

// Se chegou aqui, redireciona para a página de visualização
header('Location: preview_videos.php'); 
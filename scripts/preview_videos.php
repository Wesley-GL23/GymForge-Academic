<?php
require_once __DIR__ . '/../includes/auth_functions.php';

// Verifica se o usuário está logado e é admin
requireAdmin();

$videos_dir = __DIR__ . '/../assets/videos';
$exercises_json = __DIR__ . '/../database/exercises.json';

// Carrega o mapeamento de exercícios
$exercises_mapping = [];
if (file_exists($exercises_json)) {
    $json_content = file_get_contents($exercises_json);
    $exercises_data = json_decode($json_content, true);
    foreach ($exercises_data['exercises'] as $exercise) {
        $exercises_mapping[$exercise['video']] = [
            'name' => $exercise['name'],
            'category' => $exercise['category'],
            'description' => $exercise['description']
        ];
    }
}

// Lista todos os vídeos
$videos = array_filter(scandir($videos_dir), function($file) {
    return !in_array($file, ['.', '..']) && pathinfo($file, PATHINFO_EXTENSION) === 'mp4';
});

// Agrupa os vídeos em pares (tiny e small)
$video_pairs = [];
foreach ($videos as $video) {
    $base_name = str_replace(['_tiny', '_small'], '', $video);
    if (!isset($video_pairs[$base_name])) {
        $video_pairs[$base_name] = ['tiny' => null, 'small' => null];
    }
    
    if (strpos($video, '_tiny') !== false) {
        $video_pairs[$base_name]['tiny'] = $video;
    } else if (strpos($video, '_small') !== false) {
        $video_pairs[$base_name]['small'] = $video;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualização de Vídeos - GymForge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4">Visualização de Vídeos</h1>
        <p class="lead">Confirme as categorias dos vídeos antes de organizá-los</p>
        
        <div class="row g-4">
            <?php foreach ($video_pairs as $base_name => $versions): ?>
                <?php
                $video_file = $versions['tiny'] ?? $versions['small'];
                if (!$video_file) continue;
                
                $info = $exercises_mapping[$video_file] ?? [
                    'name' => 'Não identificado',
                    'category' => 'não categorizado',
                    'description' => 'Sem descrição'
                ];
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="video-container">
                            <video controls class="card-img-top">
                                <source src="/GymForge-Academic/assets/videos/<?php echo htmlspecialchars($video_file); ?>" type="video/mp4">
                                Seu navegador não suporta vídeos HTML5.
                            </video>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($info['name']); ?></h5>
                            <p class="card-text">
                                <strong>Arquivo:</strong> <?php echo htmlspecialchars($video_file); ?><br>
                                <strong>Categoria:</strong> <?php echo htmlspecialchars($info['category']); ?><br>
                                <strong>Descrição:</strong> <?php echo htmlspecialchars($info['description']); ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <form action="organize_videos.php" method="post" class="d-flex gap-2">
                                <input type="hidden" name="video" value="<?php echo htmlspecialchars($video_file); ?>">
                                <select name="category" class="form-select">
                                    <option value="">Selecione uma categoria...</option>
                                    <option value="musculacao" <?php echo $info['category'] === 'strength' ? 'selected' : ''; ?>>Musculação</option>
                                    <option value="cardio" <?php echo $info['category'] === 'cardio' ? 'selected' : ''; ?>>Cardio</option>
                                    <option value="funcional" <?php echo $info['category'] === 'core' ? 'selected' : ''; ?>>Funcional</option>
                                    <option value="alongamento">Alongamento</option>
                                    <option value="yoga">Yoga</option>
                                    <option value="pilates">Pilates</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-5">
            <a href="organize_videos.php?action=organize_all" class="btn btn-success btn-lg">
                Organizar Todos os Vídeos
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
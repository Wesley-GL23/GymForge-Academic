<?php
// Configuração do CDN
define('USE_CDN', true);
define('CDN_URL', 'https://cdn.gymforge.com.br');  // Exemplo - você configurará seu domínio
define('FALLBACK_TO_LOCAL', true);  // Se CDN falhar, usa arquivos locais

function get_video_url($path) {
    if (USE_CDN) {
        $cdn_url = CDN_URL . '/' . ltrim($path, '/');
        
        // Verifica se o CDN está respondendo
        $headers = @get_headers($cdn_url);
        if ($headers && strpos($headers[0], '200') !== false) {
            return $cdn_url;
        }
        
        // Se CDN falhou e fallback está ativado, usa local
        if (FALLBACK_TO_LOCAL) {
            return BASE_URL . '/' . ltrim($path, '/');
        }
    }
    
    return BASE_URL . '/' . ltrim($path, '/');
}

// Função para obter créditos do vídeo
function get_video_credits($video_id) {
    $credits_file = __DIR__ . '/../scripts/video_credits.json';
    if (file_exists($credits_file)) {
        $credits = json_decode(file_get_contents($credits_file), true);
        return isset($credits[$video_id]) ? $credits[$video_id] : null;
    }
    return null;
} 
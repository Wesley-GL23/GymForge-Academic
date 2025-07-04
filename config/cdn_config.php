<?php
/**
 * Configuração do CDN para diferentes ambientes
 * Este arquivo gerencia as configurações de CDN para servir assets estáticos
 */

// Detecta o ambiente atual
function getEnvironment() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        return 'local';
    } elseif (strpos($host, 'staging') !== false) {
        return 'staging';
    }
    return 'production';
}

// Configurações base para cada ambiente
$cdn_config = [
    'local' => [
        'enabled' => false,
        'base_url' => '',
        'image_url' => '/assets/img',
        'video_url' => '/assets/videos',
        'css_url' => '/assets/css',
        'js_url' => '/assets/js',
        'cache_version' => time(),
    ],
    'staging' => [
        'enabled' => true,
        'base_url' => 'https://staging-cdn.gymforge.com',
        'image_url' => '/assets/img',
        'video_url' => '/assets/videos',
        'css_url' => '/assets/css',
        'js_url' => '/assets/js',
        'cache_version' => '1.0.0',
    ],
    'production' => [
        'enabled' => true,
        'base_url' => 'https://cdn.gymforge.com',
        'image_url' => '/assets/img',
        'video_url' => '/assets/videos',
        'css_url' => '/assets/css',
        'js_url' => '/assets/js',
        'cache_version' => '1.0.0',
    ]
];

// Ambiente atual
$current_env = getEnvironment();
$config = $cdn_config[$current_env];

// Funções auxiliares para URLs de assets
function asset_url($path) {
    global $config;
    $version = $config['cache_version'];
    return $config['enabled'] 
        ? $config['base_url'] . $path . '?v=' . $version
        : $path . '?v=' . $version;
}

function image_url($path) {
    global $config;
    return asset_url($config['image_url'] . '/' . ltrim($path, '/'));
}

function video_url($path) {
    global $config;
    return asset_url($config['video_url'] . '/' . ltrim($path, '/'));
}

function css_url($path) {
    global $config;
    return asset_url($config['css_url'] . '/' . ltrim($path, '/'));
}

function js_url($path) {
    global $config;
    return asset_url($config['js_url'] . '/' . ltrim($path, '/'));
}

// Função para otimizar imagens
function get_optimized_image($path, $width = null, $height = null, $quality = 85) {
    global $config;
    if (!$config['enabled']) {
        return image_url($path);
    }

    $params = [];
    if ($width) $params[] = "w=$width";
    if ($height) $params[] = "h=$height";
    if ($quality) $params[] = "q=$quality";

    $query = !empty($params) ? '?' . implode('&', $params) : '';
    return $config['base_url'] . '/image/optimize' . $path . $query;
}

// Função para servir vídeos adaptáveis
function get_adaptive_video($path) {
    global $config;
    if (!$config['enabled']) {
        return video_url($path);
    }

    return $config['base_url'] . '/video/stream' . $path;
}
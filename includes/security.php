<?php
/**
 * Funções de Segurança do GymForge
 * 
 * Este arquivo contém todas as funções relacionadas à segurança do sistema.
 * Inclui proteções contra ataques comuns, rate limiting e outras medidas de segurança.
 */

// Headers de Segurança
function definir_headers_seguranca() {
    // Proteção contra XSS
    header("X-XSS-Protection: 1; mode=block");
    
    // Previne clickjacking
    header("X-Frame-Options: SAMEORIGIN");
    
    // Previne MIME-sniffing
    header("X-Content-Type-Options: nosniff");
    
    // Política de Segurança de Conteúdo
    header("Content-Security-Policy: default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'");
    
    // Referrer Policy
    header("Referrer-Policy: strict-origin-when-cross-origin");
    
    // HSTS (HTTP Strict Transport Security)
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    
    // Permissões Feature-Policy
    header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
}

// Rate Limiting
class RateLimiter {
    private $redis;
    private $max_tentativas;
    private $janela_tempo;
    
    public function __construct($max_tentativas = 5, $janela_tempo = 300) {
        $this->max_tentativas = $max_tentativas;
        $this->janela_tempo = $janela_tempo;
        
        // Comentado uso da classe Redis pois não está disponível
        // $this->redis = new Redis();
    }
    
    public function verificar($chave) {
        if (!$this->redis) {
            return true; // Fallback se Redis não estiver disponível
        }
        
        $tentativas = $this->redis->get($chave);
        
        if (!$tentativas) {
            $this->redis->setex($chave, $this->janela_tempo, 1);
            return true;
        }
        
        if ($tentativas >= $this->max_tentativas) {
            return false;
        }
        
        $this->redis->incr($chave);
        return true;
    }
    
    public function resetar($chave) {
        if ($this->redis) {
            $this->redis->del($chave);
        }
    }
    
    public function tempo_restante($chave) {
        if (!$this->redis) {
            return 0;
        }
        return $this->redis->ttl($chave);
    }
}

// Proteção contra CSRF
class CSRFProtector {
    public static function gerar_token() {
        if (!isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_tokens'][$token] = time();
        
        // Limpa tokens antigos
        foreach ($_SESSION['csrf_tokens'] as $t => $time) {
            if ($time < (time() - 3600)) {
                unset($_SESSION['csrf_tokens'][$t]);
            }
        }
        
        return $token;
    }
    
    public static function validar_token($token) {
        if (!isset($_SESSION['csrf_tokens'][$token])) {
            return false;
        }
        
        unset($_SESSION['csrf_tokens'][$token]);
        return true;
    }
    
    public static function campo_token() {
        $token = self::gerar_token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}

// Proteção contra XSS
function limpar_xss($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = limpar_xss($value);
        }
        return $data;
    }
    
    // Remove caracteres NULL
    $data = str_replace(chr(0), '', $data);
    
    // Remove tags script, iframe, object, embed, e atributos on*
    $data = preg_replace(
        [
            '/<script[^>]*?>.*?<\/script>/si',
            '/<iframe[^>]*?>.*?<\/iframe>/si',
            '/<object[^>]*?>.*?<\/object>/si',
            '/<embed[^>]*?>.*?<\/embed>/si',
            '/on\w+\s*=\s*(?:([\'"]).*?\1|(?:\s|.)*?(?:\s|$))/i'
        ],
        '',
        $data
    );
    
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Proteção contra SQL Injection
function escapar_sql($valor) {
    global $conexao;
    if (is_array($valor)) {
        return array_map('escapar_sql', $valor);
    }
    if (is_numeric($valor)) {
        return $valor;
    }
    return mysqli_real_escape_string($conexao, $valor);
}

// Geração de Hash Seguro
function gerar_hash_senha($senha) {
    return password_hash($senha, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 2
    ]);
}

// Verificação de Hash
function verificar_senha($senha, $hash) {
    return password_verify($senha, $hash);
}

// Geração de Token Seguro
function gerar_token_seguro($length = 32) {
    return bin2hex(random_bytes($length));
}

// Proteção contra Força Bruta
class ProtecaoForcaBruta {
    private $rate_limiter;
    
    public function __construct() {
        $this->rate_limiter = new RateLimiter(5, 900); // 5 tentativas em 15 minutos
    }
    
    public function registrar_tentativa($identificador) {
        $chave = "login_attempts:{$identificador}";
        return $this->rate_limiter->verificar($chave);
    }
    
    public function resetar_tentativas($identificador) {
        $chave = "login_attempts:{$identificador}";
        $this->rate_limiter->resetar($chave);
    }
    
    public function tempo_bloqueio($identificador) {
        $chave = "login_attempts:{$identificador}";
        return $this->rate_limiter->tempo_restante($chave);
    }
}

// Validação de Upload Seguro
function validar_upload_seguro($arquivo, $tipos_permitidos, $tamanho_maximo = 5242880) {
    // Verifica se houve erro no upload
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return [
            'sucesso' => false,
            'mensagem' => 'Erro no upload do arquivo.'
        ];
    }
    
    // Verifica o tamanho
    if ($arquivo['size'] > $tamanho_maximo) {
        return [
            'sucesso' => false,
            'mensagem' => 'Arquivo muito grande.'
        ];
    }
    
    // Verifica o tipo MIME
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipo_arquivo = $finfo->file($arquivo['tmp_name']);
    
    if (!in_array($tipo_arquivo, $tipos_permitidos)) {
        return [
            'sucesso' => false,
            'mensagem' => 'Tipo de arquivo não permitido.'
        ];
    }
    
    // Gera um nome seguro para o arquivo
    $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
    $novo_nome = uniqid() . '.' . $extensao;
    
    return [
        'sucesso' => true,
        'nome_seguro' => $novo_nome,
        'tipo' => $tipo_arquivo
    ];
}

// Sanitização de Nome de Arquivo
function sanitizar_nome_arquivo($nome) {
    // Remove caracteres especiais e acentos
    $nome = preg_replace('/[áàãâä]/ui', 'a', $nome);
    $nome = preg_replace('/[éèêë]/ui', 'e', $nome);
    $nome = preg_replace('/[íìîï]/ui', 'i', $nome);
    $nome = preg_replace('/[óòõôö]/ui', 'o', $nome);
    $nome = preg_replace('/[úùûü]/ui', 'u', $nome);
    $nome = preg_replace('/[ýÿ]/ui', 'y', $nome);
    $nome = preg_replace('/[ñ]/ui', 'n', $nome);
    $nome = preg_replace('/[ç]/ui', 'c', $nome);
    
    // Remove caracteres especiais
    $nome = preg_replace('/[^a-zA-Z0-9\-\._]/', '', $nome);
    
    // Limita o tamanho
    return substr($nome, 0, 255);
}

// Proteção contra Path Traversal
function caminho_seguro($base, $caminho) {
    $caminho_real = realpath($base . DIRECTORY_SEPARATOR . $caminho);
    $base_real = realpath($base);
    
    if ($caminho_real === false || strpos($caminho_real, $base_real) !== 0) {
        return false;
    }
    
    return $caminho_real;
}

// Log de Segurança
function registrar_log_seguranca($tipo, $mensagem, $dados = []) {
    $log = [
        'data' => date('Y-m-d H:i:s'),
        'tipo' => $tipo,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'usuario_id' => $_SESSION['user_id'] ?? 'não autenticado',
        'mensagem' => $mensagem,
        'dados' => json_encode($dados)
    ];
    
    error_log(implode('|', $log) . "\n", 3, __DIR__ . '/../logs/security.log');
}

// Proteção contra Sessão Inválida
function validar_sessao() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_activity'])) {
        return false;
    }
    
    $tempo_maximo = 30 * 60; // 30 minutos
    if (time() - $_SESSION['last_activity'] > $tempo_maximo) {
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

// Regeneração de ID de Sessão
function regenerar_sessao() {
    $old_session_data = $_SESSION;
    session_destroy();
    session_start();
    $_SESSION = $old_session_data;
    session_regenerate_id(true);
}

// Inicialização de Segurança
function inicializar_seguranca() {
    // Configura sessão segura
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Define headers de segurança
    definir_headers_seguranca();
    
    // Inicia sessão se ainda não iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenera ID da sessão periodicamente
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) {
        regenerar_sessao();
        $_SESSION['last_regeneration'] = time();
    }
} 
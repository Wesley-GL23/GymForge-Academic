<?php
/**
 * Funções de Autenticação e Segurança
 * Sistema robusto para gerenciar autenticação, sessões e segurança
 */

require_once __DIR__ . '/../config/conexao.php';

// Configurações de Segurança
session_start();
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Classe principal de autenticação
class Auth {
    private $conn;
    private $max_attempts;
    private $block_duration;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->loadSettings();
    }
    
    private function loadSettings() {
        $stmt = $this->conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key IN ('max_login_attempts', 'login_block_duration')");
        $stmt->execute();
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $this->max_attempts = (int)($settings['max_login_attempts'] ?? 5);
        $this->block_duration = (int)($settings['login_block_duration'] ?? 900);
    }

    // Login com proteção contra força bruta
    public function login($email, $senha) {
        try {
            // Verifica bloqueio
            if ($this->isBlocked($email)) {
                return ['success' => false, 'message' => 'Conta temporariamente bloqueada. Tente novamente mais tarde.'];
            }

            $stmt = $this->conn->prepare("SELECT id, nome, email, senha, nivel, tentativas_login, bloqueado_ate FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return ['success' => false, 'message' => 'Credenciais inválidas.'];
            }

            if (password_verify($senha, $usuario['senha'])) {
                // Reset tentativas após login bem-sucedido
                $this->resetLoginAttempts($email);
                
                // Gera novo ID de sessão
                session_regenerate_id(true);
                
                // Define dados da sessão
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nome'];
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['user_level'] = $usuario['nivel'];
                $_SESSION['last_activity'] = time();
                $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                // Registra login
                $this->logAction($usuario['id'], 'login', 'Login bem-sucedido');

                return ['success' => true, 'user' => $usuario];
            }

            // Incrementa tentativas de login
            $this->incrementLoginAttempts($email);

            return ['success' => false, 'message' => 'Credenciais inválidas.'];
        } catch (Exception $e) {
            $this->logAction(null, 'error', 'Erro no login: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do servidor.'];
        }
    }

    // Verifica se o usuário está autenticado
    public function isAuthenticated() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Verifica tempo de inatividade (30 minutos)
        if (time() - $_SESSION['last_activity'] > 1800) {
            $this->logout();
            return false;
        }

        // Verifica se IP e User Agent não mudaram
        if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] || 
            $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            $this->logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    // Verifica nível de acesso
    public function hasAccess($required_level) {
        if (!$this->isAuthenticated()) {
            return false;
        }

        $levels = ['visitante' => 1, 'cliente' => 2, 'admin' => 3];
        $user_level = $levels[$_SESSION['user_level']] ?? 0;
        $required_level = $levels[$required_level] ?? 0;

        return $user_level >= $required_level;
    }

    // Logout seguro
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logAction($_SESSION['user_id'], 'logout', 'Logout realizado');
        }

        // Limpa e destrói a sessão
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }

    // Recuperação de senha
    public function requestPasswordReset($email) {
        try {
            $stmt = $this->conn->prepare("SELECT id, nome FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return ['success' => false, 'message' => 'Email não encontrado.'];
            }

            // Gera token único
            $token = bin2hex(random_bytes(32));
            $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Salva token
            $stmt = $this->conn->prepare("UPDATE usuarios SET token_recuperacao = ?, token_expiracao = ? WHERE id = ?");
            $stmt->execute([$token, $expiration, $usuario['id']]);

            // Envia email (implementar função de envio)
            $resetLink = "https://{$_SERVER['HTTP_HOST']}/redefinir-senha.php?token=" . $token;
            
            return ['success' => true, 'message' => 'Instruções enviadas para seu email.', 'link' => $resetLink];
        } catch (Exception $e) {
            $this->logAction(null, 'error', 'Erro na recuperação de senha: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao processar solicitação.'];
        }
    }

    // Redefinição de senha
    public function resetPassword($token, $new_password) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id FROM usuarios 
                WHERE token_recuperacao = ? 
                AND token_expiracao > NOW()
                AND bloqueado_ate IS NULL OR bloqueado_ate < NOW()
            ");
            $stmt->execute([$token]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return ['success' => false, 'message' => 'Token inválido ou expirado.'];
            }

            // Atualiza senha e limpa token
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("
                UPDATE usuarios 
                SET senha = ?, token_recuperacao = NULL, token_expiracao = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$hash, $usuario['id']]);

            $this->logAction($usuario['id'], 'password_reset', 'Senha redefinida com sucesso');
            return ['success' => true, 'message' => 'Senha atualizada com sucesso.'];
        } catch (Exception $e) {
            $this->logAction(null, 'error', 'Erro na redefinição de senha: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao redefinir senha.'];
        }
    }

    // Proteção contra força bruta
    private function isBlocked($email) {
        $stmt = $this->conn->prepare("SELECT bloqueado_ate, tentativas_login FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        if ($result['bloqueado_ate'] && strtotime($result['bloqueado_ate']) > time()) {
            return true;
        }

        return false;
    }

    private function incrementLoginAttempts($email) {
        $stmt = $this->conn->prepare("
            UPDATE usuarios 
            SET tentativas_login = COALESCE(tentativas_login, 0) + 1,
                bloqueado_ate = CASE 
                    WHEN tentativas_login + 1 >= ? THEN DATE_ADD(NOW(), INTERVAL ? SECOND)
                    ELSE bloqueado_ate
                END
            WHERE email = ?
        ");
        $stmt->execute([$this->max_attempts, $this->block_duration, $email]);
    }

    private function resetLoginAttempts($email) {
        $stmt = $this->conn->prepare("
            UPDATE usuarios 
            SET tentativas_login = 0,
                bloqueado_ate = NULL
            WHERE email = ?
        ");
        $stmt->execute([$email]);
    }

    public function logAction($user_id, $action_type, $description) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO system_logs 
                (user_id, action_type, description, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $user_id,
                $action_type,
                $description,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);
        } catch (Exception $e) {
            error_log("Erro ao registrar log: " . $e->getMessage());
        }
    }
}

// Funções Globais de Autenticação
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'level' => $_SESSION['user_level']
    ];
}

function requireAuth() {
    $auth = new Auth($GLOBALS['conn']);
    if (!$auth->isAuthenticated()) {
        header('Location: /login.php');
        exit;
    }
}

function requireLevel($level) {
    $auth = new Auth($GLOBALS['conn']);
    if (!$auth->hasAccess($level)) {
        header('Location: /403.php');
        exit;
    }
}

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCsrfToken() . '">';
}

// Funções de Usuário
function cadastrarUsuario($nome, $email, $senha) {
    global $conn;
    
    try {
        // Verifica se email já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }

        // Hash da senha
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        // Insere novo usuário
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nome, email, senha, nivel, data_cadastro) 
            VALUES (?, ?, ?, 'cliente', NOW())
        ");
        $stmt->execute([$nome, $email, $hash]);

        // Log da ação
        $user_id = $conn->lastInsertId();
        $auth = new Auth($conn);
        $auth->logAction($user_id, 'registro', 'Novo usuário registrado');

        return true;
    } catch (Exception $e) {
        error_log("Erro no cadastro: " . $e->getMessage());
        return false;
    }
}

function fazerLogout() {
    $auth = new Auth($GLOBALS['conn']);
    $auth->logout();
    header('Location: ' . BASE_URL . '/forms/usuario/login.php');
    exit;
}

function conectarBD() {
    global $conn;
    return $conn;
}
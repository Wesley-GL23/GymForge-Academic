<?php
session_start();

function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    }
}

function verificarAdmin() {
    if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'administrador') {
        header('Location: acesso_negado.php');
        exit();
    }
}

function verificarCliente() {
    if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'cliente') {
        header('Location: acesso_negado.php');
        exit();
    }
}

class Auth {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function login($email, $senha) {
        try {
            // Verificar tentativas de login
            $stmt = $this->conn->prepare("
                SELECT bloqueado_ate, tentativas_login 
                FROM usuarios 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user_status = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user_status && $user_status['bloqueado_ate'] !== null) {
                $bloqueado_ate = strtotime($user_status['bloqueado_ate']);
                if ($bloqueado_ate > time()) {
                    $tempo_restante = ceil(($bloqueado_ate - time()) / 60);
                    return [
                        'success' => false,
                        'message' => "Conta bloqueada. Tente novamente em {$tempo_restante} minutos."
                    ];
                }
            }
            
            // Buscar usuário
            $stmt = $this->conn->prepare("
                SELECT id, nome, email, senha, nivel, tentativas_login 
                FROM usuarios 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Email ou senha incorretos'
                ];
            }
            
            // Verificar senha
            if (password_verify($senha, $usuario['senha'])) {
                // Reset tentativas de login
                $stmt = $this->conn->prepare("
                    UPDATE usuarios 
                    SET tentativas_login = 0, 
                        bloqueado_ate = NULL 
                    WHERE id = ?
                ");
                $stmt->execute([$usuario['id']]);
                
                // Criar sessão
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nome'];
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['user_level'] = $usuario['nivel'];
                
                return [
                    'success' => true,
                    'message' => 'Login realizado com sucesso'
                ];
            }
            
            // Incrementar tentativas de login
            $tentativas = $usuario['tentativas_login'] + 1;
            $bloqueado_ate = null;
            
            if ($tentativas >= 5) {
                $bloqueado_ate = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            }
            
            $stmt = $this->conn->prepare("
                UPDATE usuarios 
                SET tentativas_login = ?,
                    bloqueado_ate = ?
                WHERE id = ?
            ");
            $stmt->execute([$tentativas, $bloqueado_ate, $usuario['id']]);
            
            if ($bloqueado_ate) {
                return [
                    'success' => false,
                    'message' => 'Conta bloqueada por 30 minutos devido a muitas tentativas.'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Email ou senha incorretos'
            ];
            
        } catch (Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao fazer login. Tente novamente.'
            ];
        }
    }
    
    public function logout() {
        session_destroy();
        session_start();
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => 'Logout realizado com sucesso!'
        ];
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'nome' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'nivel' => $_SESSION['user_level']
        ];
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            $_SESSION['mensagem'] = [
                'tipo' => 'warning',
                'texto' => 'Você precisa fazer login para acessar esta página.'
            ];
            header('Location: ' . BASE_URL . '/forms/usuario/login.php');
            exit;
        }
    }
    
    public function requireAdmin() {
        $this->requireAuth();
        if ($_SESSION['user_level'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'danger',
                'texto' => 'Acesso negado. Você não tem permissão para acessar esta página.'
            ];
            header('Location: ' . BASE_URL . '/403.php');
            exit;
        }
    }
} 
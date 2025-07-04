<?php
// Configurações do banco de dados
$db_config = [
    'host' => 'localhost',
    'dbname' => 'gymforge_php',
    'charset' => 'utf8mb4',
    'username' => 'root',
    'password' => ''
];

try {
    // Primeiro tenta conectar sem especificar o banco de dados
    $conn = new PDO(
        "mysql:host={$db_config['host']};charset={$db_config['charset']}",
        $db_config['username'],
        $db_config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );

    // Verifica se o banco de dados existe
    $stmt = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$db_config['dbname']}'");
    if (!$stmt->fetch()) {
        // Se não existe, cria o banco de dados
        $sql = file_get_contents(__DIR__ . '/../database/gymforge.sql');
        $conn->exec($sql);
        echo "Banco de dados criado com sucesso!\n";
    }

    // Conecta ao banco de dados específico
    $conn->exec("USE {$db_config['dbname']}");

    // Função helper para executar queries com prepared statements
    function executeQuery($sql, $params = [], $fetch_mode = PDO::FETCH_ASSOC) {
        global $conn;
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erro na query: " . $e->getMessage());
            throw new Exception("Erro ao executar operação no banco de dados.");
        }
    }

    // Funções helpers para fetch
    function fetchOne($sql, $params = []) {
        $stmt = executeQuery($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    function fetchAll($sql, $params = []) {
        $stmt = executeQuery($sql, $params);
        $result = $stmt->fetchAll();
        return !empty($result) ? $result : [];
    }

    function fetchColumn($sql, $params = []) {
        $stmt = executeQuery($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return !empty($result) ? $result : [];
    }

    // Função helper para inserção
    function insertData($table, $data) {
        global $conn;
        try {
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $table,
                implode(', ', $fields),
                implode(', ', $placeholders)
            );

            $stmt = $conn->prepare($sql);
            $stmt->execute(array_values($data));
            return $conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro na inserção: " . $e->getMessage());
            throw new Exception("Erro ao inserir dados.");
        }
    }

    // Função helper para atualização
    function updateData($table, $data, $where, $whereParams = []) {
        global $conn;
        try {
            $set = array_map(function($field) {
                return "$field = ?";
            }, array_keys($data));

            $sql = sprintf(
                "UPDATE %s SET %s WHERE %s",
                $table,
                implode(', ', $set),
                $where
            );

            $params = array_merge(array_values($data), $whereParams);
            $stmt = $conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erro na atualização: " . $e->getMessage());
            throw new Exception("Erro ao atualizar dados.");
        }
    }

} catch(PDOException $e) {
    error_log("Erro de conexão: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}

// Função para escapar strings e prevenir SQL Injection
function limparString($conn, $string) {
    return $conn->real_escape_string(trim($string));
}

// Função para fechar a conexão
function fecharConexao($conn) {
    if ($conn) {
        $conn->close();
    }
} 
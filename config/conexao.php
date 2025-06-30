<?php
// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gymforge_php');

// Estabelece a conexão com o banco de dados
function conectarBD() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Erro na conexão: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Erro ao conectar com o banco de dados: " . $e->getMessage());
    }
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
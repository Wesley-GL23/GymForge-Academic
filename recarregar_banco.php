<?php
/**
 * Script para recriar o banco de dados
 * ATENÇÃO: Este script irá apagar todos os dados existentes!
 */

try {
    // Conecta ao MySQL sem selecionar um banco de dados
    $conn = new PDO(
        "mysql:host=localhost;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );

    // Apaga o banco de dados se existir
    $conn->exec("DROP DATABASE IF EXISTS gymforge_php");
    echo "Banco de dados antigo removido.\n";

    // Cria o banco de dados
    $conn->exec("CREATE DATABASE IF NOT EXISTS gymforge_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->exec("USE gymforge_php");
    echo "Banco de dados criado.\n";

    // Configura o modo SQL
    $conn->exec("SET SESSION sql_mode = ''");
    $conn->exec("SET SESSION explicit_defaults_for_timestamp = 0");

    // Lê o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/database/gymforge.sql');
    
    // Divide o SQL em comandos individuais
    $commands = array_filter(
        array_map(
            'trim',
            explode(';', $sql)
        ),
        function($cmd) { return !empty($cmd); }
    );

    // Executa cada comando separadamente
    foreach ($commands as $command) {
        try {
            $conn->exec($command);
        } catch (PDOException $e) {
            echo "Erro ao executar comando:\n{$command}\n\nErro: " . $e->getMessage() . "\n\n";
        }
    }
    echo "Estrutura do banco de dados criada.\n";

    // Carrega dados iniciais se necessário
    if (file_exists(__DIR__ . '/database/initial_data.sql')) {
        $sql = file_get_contents(__DIR__ . '/database/initial_data.sql');
        $commands = array_filter(
            array_map(
                'trim',
                explode(';', $sql)
            ),
            function($cmd) { return !empty($cmd); }
        );
        foreach ($commands as $command) {
            try {
                $conn->exec($command);
            } catch (PDOException $e) {
                echo "Erro ao carregar dados iniciais:\n{$command}\n\nErro: " . $e->getMessage() . "\n\n";
            }
        }
        echo "Dados iniciais carregados.\n";
    }

    echo "Processo concluído com sucesso!\n";

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
} 
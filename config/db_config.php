<?php

if (!defined('DB_CONFIG_INCLUDED')) {
    define('DB_CONFIG_INCLUDED', true);
    
    // Configurações do Banco de Dados
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'gymforge_php');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');

    return [
        'connection' => [
            'host' => DB_HOST,
            'dbname' => DB_NAME,
            'charset' => DB_CHARSET,
            'username' => DB_USER,
            'password' => DB_PASS
        ],
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ],
        'fetch_modes' => [
            'assoc' => PDO::FETCH_ASSOC,
            'obj' => PDO::FETCH_OBJ,
            'both' => PDO::FETCH_BOTH,
            'column' => PDO::FETCH_COLUMN
        ]
    ];
}
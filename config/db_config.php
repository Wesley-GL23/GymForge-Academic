<?php
return [
    'connection' => [
        'host' => 'localhost',
        'dbname' => 'gymforge_php',
        'charset' => 'utf8mb4',
        'username' => 'root',
        'password' => ''
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
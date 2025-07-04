<?php
require_once '../../config/config.php';
require_once '../../includes/auth_functions.php';

fazerLogout();

$_SESSION['mensagem'] = [
    'tipo' => 'success',
    'texto' => 'Logout realizado com sucesso!'
];

header('Location: ' . BASE_URL . '/index.php');
exit; 
<?php
require_once '../../includes/header.php';
requireLogin();

// Redireciona com base no nível do usuário
if (eAdmin()) {
    header('Location: ' . BASE_URL . '/views/dashboard/admin.php');
} else {
    header('Location: ' . BASE_URL . '/views/dashboard/cliente.php');
}
exit(); 
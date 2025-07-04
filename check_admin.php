<?php
require_once __DIR__ . '/includes/auth_functions.php';

// Verifica se o usuário está logado e é admin
requireLogin();
requireNivel('admin'); 
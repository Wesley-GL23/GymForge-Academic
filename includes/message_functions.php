<?php
// Função para definir mensagens de feedback
function setMessage($texto, $tipo = 'info') {
    $_SESSION['mensagem'] = [
        'texto' => $texto,
        'tipo' => $tipo
    ];
}

// Função para recuperar mensagens de feedback
function getMessage() {
    if (isset($_SESSION['mensagem'])) {
        $mensagem = $_SESSION['mensagem'];
        unset($_SESSION['mensagem']);
        return $mensagem;
    }
    return null;
} 
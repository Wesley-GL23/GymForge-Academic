<?php
require_once '../../config/config.php';
require_once '../../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../forms/usuario/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Campos obrigatórios
    $peso = filter_input(INPUT_POST, 'peso', FILTER_VALIDATE_FLOAT);
    $altura = filter_input(INPUT_POST, 'altura', FILTER_VALIDATE_FLOAT);
    
    if (!$peso || !$altura) {
        $_SESSION['flash_message'] = 'Peso e altura são campos obrigatórios.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ../../views/perfil/medidas.php');
        exit;
    }
    
    // Campos opcionais
    $circunferencia_braco = filter_input(INPUT_POST, 'circunferencia_braco', FILTER_VALIDATE_FLOAT) ?: null;
    $circunferencia_antebraco = filter_input(INPUT_POST, 'circunferencia_antebraco', FILTER_VALIDATE_FLOAT) ?: null;
    $circunferencia_peito = filter_input(INPUT_POST, 'circunferencia_peito', FILTER_VALIDATE_FLOAT) ?: null;
    $circunferencia_cintura = filter_input(INPUT_POST, 'circunferencia_cintura', FILTER_VALIDATE_FLOAT) ?: null;
    $circunferencia_quadril = filter_input(INPUT_POST, 'circunferencia_quadril', FILTER_VALIDATE_FLOAT) ?: null;
    $circunferencia_coxa = filter_input(INPUT_POST, 'circunferencia_coxa', FILTER_VALIDATE_FLOAT) ?: null;
    $circunferencia_panturrilha = filter_input(INPUT_POST, 'circunferencia_panturrilha', FILTER_VALIDATE_FLOAT) ?: null;
    $percentual_gordura = filter_input(INPUT_POST, 'percentual_gordura', FILTER_VALIDATE_FLOAT) ?: null;
    $observacoes = filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_STRING);
    
    // Calcula o IMC
    $imc = $peso / ($altura * $altura);
    
    // Insere as medidas
    $stmt = $conn->prepare("
        INSERT INTO medidas_usuario (
            usuario_id, data_registro, peso, altura, imc,
            circunferencia_braco, circunferencia_antebraco,
            circunferencia_peito, circunferencia_cintura,
            circunferencia_quadril, circunferencia_coxa,
            circunferencia_panturrilha, percentual_gordura,
            observacoes
        ) VALUES (
            ?, CURDATE(), ?, ?, ?,
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?
        )
    ");
    
    $stmt->bind_param("idddddddddds",
        $_SESSION['usuario_id'],
        $peso,
        $altura,
        $imc,
        $circunferencia_braco,
        $circunferencia_antebraco,
        $circunferencia_peito,
        $circunferencia_cintura,
        $circunferencia_quadril,
        $circunferencia_coxa,
        $circunferencia_panturrilha,
        $percentual_gordura,
        $observacoes
    );
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = 'Medidas registradas com sucesso!';
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = 'Erro ao registrar as medidas. Tente novamente.';
        $_SESSION['flash_type'] = 'danger';
    }
    
    header('Location: ../../views/perfil/medidas.php');
    exit;
}

header('Location: ../../views/perfil/medidas.php');
exit; 
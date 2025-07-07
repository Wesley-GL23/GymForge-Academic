<?php
/**
 * Funções de Validação do GymForge
 * 
 * Este arquivo contém todas as funções de validação utilizadas no sistema.
 * Inclui validações de entrada de dados, sanitização e helpers de segurança.
 */

// Validação de Email
function validar_email($email) {
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'valido' => false,
            'mensagem' => 'Por favor, insira um endereço de e-mail válido.'
        ];
    }
    return ['valido' => true, 'valor' => $email];
}

// Validação de Senha
function validar_senha($senha) {
    $senha = trim($senha);
    $erros = [];
    
    if (strlen($senha) < 8) {
        $erros[] = 'A senha deve ter pelo menos 8 caracteres.';
    }
    if (!preg_match('/[A-Z]/', $senha)) {
        $erros[] = 'A senha deve conter pelo menos uma letra maiúscula.';
    }
    if (!preg_match('/[a-z]/', $senha)) {
        $erros[] = 'A senha deve conter pelo menos uma letra minúscula.';
    }
    if (!preg_match('/[0-9]/', $senha)) {
        $erros[] = 'A senha deve conter pelo menos um número.';
    }
    if (!preg_match('/[^A-Za-z0-9]/', $senha)) {
        $erros[] = 'A senha deve conter pelo menos um caractere especial.';
    }
    
    if (!empty($erros)) {
        return [
            'valido' => false,
            'mensagem' => implode(' ', $erros)
        ];
    }
    return ['valido' => true, 'valor' => $senha];
}

// Validação de Nome
function validar_nome($nome) {
    $nome = trim($nome);
    if (strlen($nome) < 2) {
        return [
            'valido' => false,
            'mensagem' => 'O nome deve ter pelo menos 2 caracteres.'
        ];
    }
    if (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nome)) {
        return [
            'valido' => false,
            'mensagem' => 'O nome deve conter apenas letras e espaços.'
        ];
    }
    return ['valido' => true, 'valor' => $nome];
}

// Validação de URL
function validar_url($url) {
    $url = filter_var(trim($url), FILTER_SANITIZE_URL);
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return [
            'valido' => false,
            'mensagem' => 'Por favor, insira uma URL válida.'
        ];
    }
    return ['valido' => true, 'valor' => $url];
}

// Validação de Data
function validar_data($data) {
    $data = trim($data);
    $data_obj = DateTime::createFromFormat('Y-m-d', $data);
    if (!$data_obj || $data_obj->format('Y-m-d') !== $data) {
        return [
            'valido' => false,
            'mensagem' => 'Por favor, insira uma data válida no formato AAAA-MM-DD.'
        ];
    }
    return ['valido' => true, 'valor' => $data];
}

// Validação de Número de Telefone
function validar_telefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    if (strlen($telefone) < 10 || strlen($telefone) > 11) {
        return [
            'valido' => false,
            'mensagem' => 'Por favor, insira um número de telefone válido com DDD.'
        ];
    }
    return ['valido' => true, 'valor' => $telefone];
}

// Validação de CPF
function validar_cpf($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11) {
        return [
            'valido' => false,
            'mensagem' => 'O CPF deve conter 11 dígitos.'
        ];
    }
    
    if (preg_match('/^(\d)\1+$/', $cpf)) {
        return [
            'valido' => false,
            'mensagem' => 'CPF inválido.'
        ];
    }
    
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return [
                'valido' => false,
                'mensagem' => 'CPF inválido.'
            ];
        }
    }
    
    return ['valido' => true, 'valor' => $cpf];
}

// Validação de Upload de Arquivo
function validar_upload($arquivo, $tipos_permitidos = [], $tamanho_maximo = 5242880) {
    if (!isset($arquivo['error']) || is_array($arquivo['error'])) {
        return [
            'valido' => false,
            'mensagem' => 'Parâmetros inválidos.'
        ];
    }

    switch ($arquivo['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return [
                'valido' => false,
                'mensagem' => 'Nenhum arquivo foi enviado.'
            ];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return [
                'valido' => false,
                'mensagem' => 'O arquivo excede o tamanho máximo permitido.'
            ];
        default:
            return [
                'valido' => false,
                'mensagem' => 'Erro no upload do arquivo.'
            ];
    }

    if ($arquivo['size'] > $tamanho_maximo) {
        return [
            'valido' => false,
            'mensagem' => 'O arquivo excede o tamanho máximo permitido de ' . ($tamanho_maximo / 1024 / 1024) . 'MB.'
        ];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipo_arquivo = $finfo->file($arquivo['tmp_name']);

    if (!empty($tipos_permitidos) && !in_array($tipo_arquivo, $tipos_permitidos)) {
        return [
            'valido' => false,
            'mensagem' => 'Tipo de arquivo não permitido.'
        ];
    }

    return ['valido' => true, 'valor' => $arquivo];
}

// Sanitização de Entrada
function sanitizar_entrada($dados) {
    if (is_array($dados)) {
        return array_map('sanitizar_entrada', $dados);
    }
    return htmlspecialchars(strip_tags(trim($dados)), ENT_QUOTES, 'UTF-8');
}

// Validação de Token CSRF
function validar_csrf($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return [
            'valido' => false,
            'mensagem' => 'Token de segurança inválido.'
        ];
    }
    return ['valido' => true];
}

// Gerar Token CSRF
function gerar_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Campo Hidden com Token CSRF
function campo_csrf() {
    $token = gerar_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// Validação de Força da Senha
function validar_forca_senha($senha) {
    $pontuacao = 0;
    $feedback = [];
    
    // Comprimento
    if (strlen($senha) >= 12) {
        $pontuacao += 2;
    } elseif (strlen($senha) >= 8) {
        $pontuacao += 1;
    } else {
        $feedback[] = 'A senha deve ter pelo menos 8 caracteres.';
    }
    
    // Letras maiúsculas
    if (preg_match('/[A-Z]/', $senha)) {
        $pontuacao += 1;
    } else {
        $feedback[] = 'Adicione pelo menos uma letra maiúscula.';
    }
    
    // Letras minúsculas
    if (preg_match('/[a-z]/', $senha)) {
        $pontuacao += 1;
    } else {
        $feedback[] = 'Adicione pelo menos uma letra minúscula.';
    }
    
    // Números
    if (preg_match('/[0-9]/', $senha)) {
        $pontuacao += 1;
    } else {
        $feedback[] = 'Adicione pelo menos um número.';
    }
    
    // Caracteres especiais
    if (preg_match('/[^A-Za-z0-9]/', $senha)) {
        $pontuacao += 1;
    } else {
        $feedback[] = 'Adicione pelo menos um caractere especial.';
    }
    
    // Avaliação final
    $forca = '';
    if ($pontuacao >= 5) {
        $forca = 'forte';
    } elseif ($pontuacao >= 3) {
        $forca = 'média';
    } else {
        $forca = 'fraca';
    }
    
    return [
        'forca' => $forca,
        'pontuacao' => $pontuacao,
        'feedback' => $feedback
    ];
}

// Validação de Idade
function validar_idade($data_nascimento, $idade_minima = 13) {
    $data = DateTime::createFromFormat('Y-m-d', $data_nascimento);
    if (!$data) {
        return [
            'valido' => false,
            'mensagem' => 'Data de nascimento inválida.'
        ];
    }
    
    $hoje = new DateTime();
    $idade = $hoje->diff($data)->y;
    
    if ($idade < $idade_minima) {
        return [
            'valido' => false,
            'mensagem' => "Você deve ter pelo menos {$idade_minima} anos para se cadastrar."
        ];
    }
    
    return ['valido' => true, 'valor' => $data_nascimento];
}

// Validação de Campos Obrigatórios
function validar_campos_obrigatorios($dados, $campos) {
    $erros = [];
    foreach ($campos as $campo) {
        if (!isset($dados[$campo]) || trim($dados[$campo]) === '') {
            $erros[] = "O campo '{$campo}' é obrigatório.";
        }
    }
    
    if (!empty($erros)) {
        return [
            'valido' => false,
            'mensagem' => implode(' ', $erros)
        ];
    }
    
    return ['valido' => true];
}

// Validação de Formato de Arquivo
function validar_formato_arquivo($nome_arquivo, $extensoes_permitidas) {
    $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
    if (!in_array($extensao, $extensoes_permitidas)) {
        return [
            'valido' => false,
            'mensagem' => 'Formato de arquivo não permitido. Formatos aceitos: ' . implode(', ', $extensoes_permitidas)
        ];
    }
    return ['valido' => true, 'valor' => $extensao];
}

// Validação de Número
function validar_numero($numero, $min = null, $max = null) {
    if (!is_numeric($numero)) {
        return [
            'valido' => false,
            'mensagem' => 'Por favor, insira um número válido.'
        ];
    }
    
    if ($min !== null && $numero < $min) {
        return [
            'valido' => false,
            'mensagem' => "O número deve ser maior ou igual a {$min}."
        ];
    }
    
    if ($max !== null && $numero > $max) {
        return [
            'valido' => false,
            'mensagem' => "O número deve ser menor ou igual a {$max}."
        ];
    }
    
    return ['valido' => true, 'valor' => $numero];
}

// Validação de Texto
function validar_texto($texto, $min_caracteres = 1, $max_caracteres = null) {
    $texto = trim($texto);
    $length = mb_strlen($texto);
    
    if ($length < $min_caracteres) {
        return [
            'valido' => false,
            'mensagem' => "O texto deve ter pelo menos {$min_caracteres} caracteres."
        ];
    }
    
    if ($max_caracteres !== null && $length > $max_caracteres) {
        return [
            'valido' => false,
            'mensagem' => "O texto deve ter no máximo {$max_caracteres} caracteres."
        ];
    }
    
    return ['valido' => true, 'valor' => $texto];
} 
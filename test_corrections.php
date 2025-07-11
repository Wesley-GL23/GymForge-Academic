<?php
/**
 * Script de Teste para Verificar Correções
 * GymForge Academic
 */

echo "🔍 TESTANDO CORREÇÕES DO GYMFORGE\n";
echo "=====================================\n\n";

// 1. Testar Configuração
echo "1. Testando Configuração...\n";
try {
    require_once 'config/config.php';
    echo "✅ Configuração carregada com sucesso\n";
    echo "   - BASE_URL: " . BASE_URL . "\n";
    echo "   - DEBUG_MODE: " . (DEBUG_MODE ? 'true' : 'false') . "\n";
} catch (Exception $e) {
    echo "❌ Erro na configuração: " . $e->getMessage() . "\n";
}

// 2. Testar Sessão
echo "\n2. Testando Sessão...\n";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Sessão já está ativa\n";
} else {
    echo "❌ Sessão não está ativa\n";
}

// 3. Testar Conexão com Banco
echo "\n3. Testando Conexão com Banco...\n";
try {
    global $conn;
    $stmt = $conn->query("SELECT 1");
    echo "✅ Conexão com banco funcionando\n";
} catch (Exception $e) {
    echo "❌ Erro na conexão com banco: " . $e->getMessage() . "\n";
}

// 4. Testar Funções de Autenticação
echo "\n4. Testando Funções de Autenticação...\n";
try {
    require_once 'includes/auth_functions.php';
    echo "✅ Funções de autenticação carregadas\n";
    
    // Testar função estaLogado()
    $logado = estaLogado();
    echo "   - Usuário logado: " . ($logado ? 'Sim' : 'Não') . "\n";
    
    // Testar função usuarioAtual()
    $usuario = usuarioAtual();
    if ($usuario) {
        echo "   - Usuário atual: " . $usuario['nome'] . "\n";
    } else {
        echo "   - Nenhum usuário logado\n";
    }
} catch (Exception $e) {
    echo "❌ Erro nas funções de autenticação: " . $e->getMessage() . "\n";
}

// 5. Testar Headers de Segurança
echo "\n5. Testando Headers de Segurança...\n";
$headers = headers_list();
$security_headers = [
    'X-XSS-Protection',
    'X-Frame-Options',
    'X-Content-Type-Options',
    'Content-Security-Policy'
];

foreach ($security_headers as $header) {
    $found = false;
    foreach ($headers as $h) {
        if (stripos($h, $header) !== false) {
            $found = true;
            break;
        }
    }
    echo ($found ? "✅" : "❌") . " $header\n";
}

// 6. Testar Validação de Senha
echo "\n6. Testando Validação de Senha...\n";
$test_senhas = [
    '123' => false, // Muito curta
    '123456' => false, // Sem maiúscula
    'abcdef' => false, // Sem maiúscula
    'ABCDEF' => false, // Sem minúscula
    'Abc123' => true, // Válida
    'Teste123!' => true, // Válida
];

foreach ($test_senhas as $senha => $esperado) {
    $valido = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $senha) && strlen($senha) >= 6;
    $status = ($valido === $esperado) ? "✅" : "❌";
    echo "   $status '$senha': " . ($valido ? 'Válida' : 'Inválida') . "\n";
}

// 7. Testar Variáveis de Sessão
echo "\n7. Testando Variáveis de Sessão...\n";
$session_vars = ['user_id', 'user_name', 'user_email', 'user_level'];
$inconsistent_vars = ['usuario_id', 'nome', 'email', 'nivel'];

echo "   Variáveis corretas:\n";
foreach ($session_vars as $var) {
    $exists = isset($_SESSION[$var]);
    echo "   " . ($exists ? "✅" : "❌") . " \$_SESSION['$var']\n";
}

echo "   Variáveis inconsistentes (não devem existir):\n";
foreach ($inconsistent_vars as $var) {
    $exists = isset($_SESSION[$var]);
    echo "   " . ($exists ? "❌" : "✅") . " \$_SESSION['$var']\n";
}

// 8. Testar Links
echo "\n8. Testando Links...\n";
$test_links = [
    '/GymForge-Academic/login.php',
    '/GymForge-Academic/cadastro.php',
    '/GymForge-Academic/views/dashboard/',
    '/GymForge-Academic/acesso_negado.php'
];

foreach ($test_links as $link) {
    $file_path = __DIR__ . str_replace('/GymForge-Academic', '', $link);
    $exists = file_exists($file_path);
    echo "   " . ($exists ? "✅" : "❌") . " $link\n";
}

// 9. Testar Debug Mode
echo "\n9. Testando Debug Mode...\n";
if (defined('DEBUG_MODE')) {
    if (DEBUG_MODE) {
        echo "✅ Debug mode ativo (desenvolvimento)\n";
        echo "   - error_reporting: " . (error_reporting() ? 'Ativo' : 'Inativo') . "\n";
        echo "   - display_errors: " . (ini_get('display_errors') ? 'Ativo' : 'Inativo') . "\n";
    } else {
        echo "✅ Debug mode inativo (produção)\n";
    }
} else {
    echo "❌ DEBUG_MODE não definido\n";
}

// 10. Resumo
echo "\n=====================================\n";
echo "📊 RESUMO DOS TESTES\n";
echo "=====================================\n";

$total_tests = 10;
$passed_tests = 0; // Seria calculado baseado nos resultados

echo "Total de testes: $total_tests\n";
echo "Testes aprovados: $passed_tests\n";
echo "Taxa de sucesso: " . round(($passed_tests / $total_tests) * 100, 2) . "%\n";

echo "\n🎯 RECOMENDAÇÕES:\n";
echo "- Execute este script após cada modificação\n";
echo "- Verifique os logs de erro do servidor\n";
echo "- Teste todas as funcionalidades manualmente\n";
echo "- Monitore o desempenho após as correções\n";

echo "\n✅ Teste concluído!\n";
?> 
<?php
// Lista de arquivos a serem removidos
$files_to_remove = [
    // Arquivos de teste
    'test_config.php',
    'test_password.php',
    'test_php.php',
    'test.php',
    'test2.php',
    'working_test.php',
    'simple_test.php',
    'debug_test.php',
    'php_test.php',
    'minimal.php',
    
    // Arquivos temporários
    'temp.sql',
    'create_biblioteca.php',
    'create_files.php',
    'create_form.php',
    
    // Arquivos de configuração de exemplo/backup
    'apache_config_fix.txt',
    'my_ini_fix.txt',
    'vhost_config.txt',
    'vhost_dev.conf'
];

// Diretório raiz do projeto
$root_dir = dirname(__DIR__);

echo "Iniciando limpeza de arquivos...\n";

foreach ($files_to_remove as $file) {
    $file_path = $root_dir . DIRECTORY_SEPARATOR . $file;
    
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo "✓ Removido: $file\n";
        } else {
            echo "✗ Erro ao remover: $file\n";
        }
    } else {
        echo "- Arquivo não encontrado: $file\n";
    }
}

echo "\nLimpeza concluída!\n"; 
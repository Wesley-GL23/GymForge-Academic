# Script para modificar o arquivo my.ini do MySQL
$myIniPath = "C:\xampp\mysql\bin\my.ini"

# Lê o conteúdo do arquivo
$content = Get-Content $myIniPath -Raw

# Verifica se as configurações já existem
if ($content -notmatch "explicit_defaults_for_timestamp=0") {
    # Adiciona as configurações na seção [mysqld]
    $content = $content -replace "(\[mysqld\])", "`$1`nexplicit_defaults_for_timestamp=0`nsql_mode=NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION"
    
    # Salva o arquivo modificado
    Set-Content $myIniPath $content -Encoding UTF8
    
    Write-Host "Configurações adicionadas ao my.ini com sucesso!"
    Write-Host "Reinicie o MySQL do XAMPP para aplicar as mudanças."
} else {
    Write-Host "As configurações já existem no arquivo my.ini."
} 
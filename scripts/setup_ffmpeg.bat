@echo off
setlocal enabledelayedexpansion

:: Verifica se está sendo executado como administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo Este script precisa ser executado como administrador.
    echo Clique com o botão direito e selecione "Executar como administrador"
    pause
    exit /b 1
)

:: Define o diretório do FFmpeg
set FFMPEG_DIR=%~dp0..\bin\ffmpeg
set DOWNLOAD_DIR=%TEMP%\ffmpeg_download

:: Cria os diretórios necessários
if not exist "%FFMPEG_DIR%" mkdir "%FFMPEG_DIR%"
if not exist "%DOWNLOAD_DIR%" mkdir "%DOWNLOAD_DIR%"

:: Baixa e extrai o FFmpeg
echo Baixando FFmpeg...
powershell -Command "& {Invoke-WebRequest -Uri 'https://github.com/BtbN/FFmpeg-Builds/releases/download/latest/ffmpeg-master-latest-win64-gpl.zip' -OutFile '%DOWNLOAD_DIR%\ffmpeg.zip'}"

echo Extraindo FFmpeg...
powershell -Command "& {Expand-Archive -Path '%DOWNLOAD_DIR%\ffmpeg.zip' -DestinationPath '%DOWNLOAD_DIR%' -Force}"

:: Move os arquivos necessários
echo Movendo arquivos...
move /Y "%DOWNLOAD_DIR%\ffmpeg-master-latest-win64-gpl\bin\ffmpeg.exe" "%FFMPEG_DIR%"
move /Y "%DOWNLOAD_DIR%\ffmpeg-master-latest-win64-gpl\bin\ffprobe.exe" "%FFMPEG_DIR%"

:: Limpa os arquivos temporários
echo Limpando arquivos temporários...
rmdir /S /Q "%DOWNLOAD_DIR%"

:: Adiciona o diretório ao PATH do sistema
echo Adicionando FFmpeg ao PATH do sistema...
set "PATH_TO_ADD=%FFMPEG_DIR%"
for /f "tokens=2*" %%a in ('reg query "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /v Path') do set "CURRENT_PATH=%%b"
setx /M PATH "%PATH_TO_ADD%;%CURRENT_PATH%"

echo.
echo Instalação concluída!
echo O FFmpeg foi instalado em: %FFMPEG_DIR%
echo.
echo Por favor, reinicie o terminal para que as alterações no PATH tenham efeito.
pause 
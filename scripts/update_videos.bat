@echo off
setlocal enabledelayedexpansion

:: Define os diretórios
set SCRIPT_DIR=%~dp0
set PROJECT_DIR=%SCRIPT_DIR%..
set PYTHON_SCRIPT=%PROJECT_DIR%\scripts\video_updater.py
set LOG_FILE=%PROJECT_DIR%\logs\video_update.log

:: Cria o diretório de logs se não existir
if not exist "%PROJECT_DIR%\logs" mkdir "%PROJECT_DIR%\logs"

:: Executa o script Python e registra a saída
echo Iniciando atualização de vídeos em %date% %time% >> "%LOG_FILE%"
python "%PYTHON_SCRIPT%" >> "%LOG_FILE%" 2>&1
echo Atualização concluída em %date% %time% >> "%LOG_FILE%"

:: Se for executado manualmente, pausa para mostrar o resultado
if "%1" neq "scheduled" pause 
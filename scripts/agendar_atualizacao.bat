@echo off
echo Configurando tarefa agendada para atualização do sistema de têmpera...

:: Define o caminho do PHP e do script
set PHP_PATH=C:\xampp\php\php.exe
set SCRIPT_PATH=%~dp0update_tempering.php

:: Remove a tarefa se já existir
schtasks /query /tn "GymForge_Tempering_Update" >nul 2>&1
if %errorLevel% equ 0 (
    echo Removendo agendamento anterior...
    schtasks /delete /tn "GymForge_Tempering_Update" /f
)

:: Cria a nova tarefa agendada
schtasks /create /tn "GymForge_Tempering_Update" /tr "%PHP_PATH% %SCRIPT_PATH%" /sc hourly /mo 1 /ru SYSTEM /f

if %errorlevel% equ 0 (
    echo Tarefa agendada criada com sucesso!
    echo A atualização do sistema de têmpera será executada a cada hora.
) else (
    echo Erro ao criar tarefa agendada.
    echo Por favor, execute este script como administrador.
)

pause
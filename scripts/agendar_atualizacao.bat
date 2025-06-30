@echo off
echo Agendando atualizacao semanal da biblioteca de videos do GYMFORGE...

:: Obter o caminho completo do diretório atual
set "SCRIPT_PATH=%~dp0update_videos.bat"

:: Criar tarefa agendada para rodar toda segunda-feira às 3:00 da manhã
schtasks /create /tn "GYMFORGE_VideoUpdate" /tr "\"%SCRIPT_PATH%\"" /sc weekly /d MON /st 03:00 /ru "%USERNAME%" /f

echo.
echo Tarefa agendada com sucesso!
echo A biblioteca sera atualizada toda segunda-feira as 3:00 da manha.
echo.
pause 
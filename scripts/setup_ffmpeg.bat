@echo off
echo Instalando FFmpeg para o GYMFORGE...

:: Criar pasta para FFmpeg
if not exist "tools" mkdir tools
cd tools

:: Baixar FFmpeg
echo Baixando FFmpeg...
curl -L "https://www.gyan.dev/ffmpeg/builds/ffmpeg-release-essentials.zip" -o ffmpeg.zip

:: Extrair
echo Extraindo...
powershell -command "Expand-Archive -Force ffmpeg.zip ."

:: Mover para o local correto
echo Configurando...
move /y ffmpeg-*\bin\ffmpeg.exe ..\scripts\
move /y ffmpeg-*\bin\ffprobe.exe ..\scripts\

:: Limpar
echo Limpando arquivos temporários...
cd ..
rmdir /s /q tools

:: Adicionar ao PATH do sistema
setx PATH "%PATH%;%~dp0..\scripts" /M

echo.
echo FFmpeg instalado com sucesso!
echo.
pause 
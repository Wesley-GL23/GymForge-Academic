@echo off
echo Configurando FFmpeg para o GYMFORGE...

:: Criar diretórios necessários
mkdir ffmpeg_temp
cd ffmpeg_temp

:: Baixar FFmpeg
echo Baixando FFmpeg...
powershell -Command "& {Invoke-WebRequest -Uri 'https://github.com/BtbN/FFmpeg-Builds/releases/download/latest/ffmpeg-master-latest-win64-gpl.zip' -OutFile 'ffmpeg.zip'}"

:: Extrair
echo Extraindo...
powershell -Command "& {Expand-Archive -Path 'ffmpeg.zip' -DestinationPath '.'}"

:: Mover para local correto
echo Movendo arquivos...
move "ffmpeg-master-latest-win64-gpl\bin\ffmpeg.exe" "..\ffmpeg.exe"
move "ffmpeg-master-latest-win64-gpl\bin\ffprobe.exe" "..\ffprobe.exe"

:: Limpar
echo Limpando...
cd ..
rmdir /s /q ffmpeg_temp

echo.
echo FFmpeg configurado com sucesso!
echo.
pause 
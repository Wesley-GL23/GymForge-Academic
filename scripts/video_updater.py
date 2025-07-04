#!/usr/bin/env python3
import os
import sys
import json
import mysql.connector
import subprocess
from datetime import datetime
from pathlib import Path

# Configurações
CONFIG = {
    'db': {
        'host': 'localhost',
        'user': 'root',
        'password': '',
        'database': 'gymforge'
    },
    'video_path': '../assets/videos',
    'temp_path': '../temp',
    'ffmpeg_path': '../bin/ffmpeg/ffmpeg.exe',
    'ffprobe_path': '../bin/ffmpeg/ffprobe.exe'
}

def get_video_info(video_path):
    """Obtém informações do vídeo usando ffprobe"""
    try:
        cmd = [
            CONFIG['ffprobe_path'],
            '-v', 'quiet',
            '-print_format', 'json',
            '-show_format',
            '-show_streams',
            video_path
        ]
        result = subprocess.run(cmd, capture_output=True, text=True)
        return json.loads(result.stdout)
    except Exception as e:
        print(f"Erro ao obter informações do vídeo {video_path}: {e}")
        return None

def process_video(input_path, output_path):
    """Processa o vídeo para o formato padrão"""
    try:
        cmd = [
            CONFIG['ffmpeg_path'],
            '-i', input_path,
            '-c:v', 'libx264',
            '-preset', 'medium',
            '-crf', '23',
            '-c:a', 'aac',
            '-b:a', '128k',
            '-movflags', '+faststart',
            '-y',
            output_path
        ]
        subprocess.run(cmd, check=True)
        return True
    except Exception as e:
        print(f"Erro ao processar vídeo {input_path}: {e}")
        return False

def connect_db():
    """Conecta ao banco de dados"""
    try:
        return mysql.connector.connect(**CONFIG['db'])
    except Exception as e:
        print(f"Erro ao conectar ao banco de dados: {e}")
        sys.exit(1)

def update_video_status(conn, video_id, status, error=None):
    """Atualiza o status do vídeo no banco de dados"""
    try:
        cursor = conn.cursor()
        sql = """
            UPDATE videos 
            SET status = %s, 
                error_message = %s,
                updated_at = %s 
            WHERE id = %s
        """
        cursor.execute(sql, (status, error, datetime.now(), video_id))
        conn.commit()
    except Exception as e:
        print(f"Erro ao atualizar status do vídeo {video_id}: {e}")
        conn.rollback()

def main():
    # Cria diretórios se não existirem
    for path in [CONFIG['video_path'], CONFIG['temp_path']]:
        Path(path).mkdir(parents=True, exist_ok=True)

    # Conecta ao banco de dados
    conn = connect_db()
    cursor = conn.cursor(dictionary=True)

    try:
        # Busca vídeos pendentes
        cursor.execute("""
            SELECT id, filename, original_filename 
            FROM videos 
            WHERE status = 'pending' 
            OR (status = 'error' AND error_count < 3)
        """)
        
        videos = cursor.fetchall()
        
        for video in videos:
            print(f"Processando vídeo {video['original_filename']}...")
            
            input_path = os.path.join(CONFIG['temp_path'], video['original_filename'])
            output_path = os.path.join(CONFIG['video_path'], video['filename'])
            
            # Verifica se o arquivo original existe
            if not os.path.exists(input_path):
                update_video_status(conn, video['id'], 'error', 'Arquivo original não encontrado')
                continue
            
            # Processa o vídeo
            if process_video(input_path, output_path):
                # Verifica se o arquivo foi gerado corretamente
                if os.path.exists(output_path):
                    video_info = get_video_info(output_path)
                    if video_info:
                        update_video_status(conn, video['id'], 'ready')
                        print(f"Vídeo {video['original_filename']} processado com sucesso")
                        
                        # Remove o arquivo original
                        try:
                            os.remove(input_path)
                        except:
                            pass
                    else:
                        update_video_status(conn, video['id'], 'error', 'Erro ao validar vídeo processado')
                else:
                    update_video_status(conn, video['id'], 'error', 'Arquivo processado não encontrado')
            else:
                update_video_status(conn, video['id'], 'error', 'Erro ao processar vídeo')

    except Exception as e:
        print(f"Erro durante o processamento: {e}")
    finally:
        cursor.close()
        conn.close()

if __name__ == "__main__":
    main() 
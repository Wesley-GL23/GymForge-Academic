import os
import json
import requests
from bs4 import BeautifulSoup
from datetime import datetime
import time
import logging
import subprocess
from urllib.parse import urlparse
import hashlib
import shutil

# Configuração de logging
logging.basicConfig(
    filename='video_updater.log',
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

class PixabayVideoUpdater:
    def __init__(self):
        self.base_url = "https://pixabay.com/videos/search/"  # Busca em todo Pixabay
        self.download_path = "assets/videos"
        self.preview_path = "assets/videos/previews"
        self.db_file = "scripts/downloaded_videos.json"
        self.credits_file = "scripts/video_credits.json"
        self.max_file_size_mb = 10  # Tamanho máximo por vídeo em MB
        
        # Configurar diretórios
        self.setup_directories()
        
        # Carregar dados
        self.downloaded_videos = self.load_json_file(self.db_file, {"last_update": "", "videos": []})
        self.video_credits = self.load_json_file(self.credits_file, {})
        
        self.categories = {
            "musculacao": [
                "musculação", "weightlifting", "bodybuilding", "força", "strength",
                "peso", "weights", "academia", "gym", "muscle", "músculos"
            ],
            "crossfit": [
                "crossfit", "funcional", "hiit", "conditioning", "circuito",
                "circuit training", "functional", "intenso", "intense"
            ],
            "alongamento": [
                "alongamento", "stretch", "stretching", "flexibility", "mobilidade",
                "mobility", "yoga", "pilates", "flexibilidade"
            ],
            "cardio": [
                "cardio", "aeróbico", "aerobic", "running", "corrida", "jump",
                "pular", "jumping", "resistência", "endurance"
            ],
            "core": [
                "core", "abdomen", "abdominal", "abs", "prancha", "plank",
                "estabilidade", "stability"
            ],
            "treino_em_casa": [
                "home workout", "treino em casa", "bodyweight", "peso corporal",
                "sem equipamento", "no equipment"
            ]
        }

    def setup_directories(self):
        """Configura estrutura de diretórios"""
        os.makedirs(self.preview_path, exist_ok=True)
        for category in self.categories.keys():
            os.makedirs(os.path.join(self.download_path, category), exist_ok=True)
            os.makedirs(os.path.join(self.preview_path, category), exist_ok=True)

    def load_json_file(self, file_path, default_data):
        """Carrega arquivo JSON ou retorna dados padrão"""
        if os.path.exists(file_path):
            with open(file_path, 'r') as f:
                return json.load(f)
        return default_data

    def save_json_file(self, file_path, data):
        """Salva dados em arquivo JSON"""
        with open(file_path, 'w') as f:
            json.dump(data, f, indent=4)

    def optimize_video(self, input_path, output_path, is_preview=False):
        """Otimiza vídeo usando FFmpeg"""
        try:
            # Configurações para preview ou vídeo principal
            if is_preview:
                cmd = [
                    'ffmpeg', '-i', input_path,
                    '-vf', 'scale=480:-1',  # Reduz resolução
                    '-c:v', 'libvpx-vp9',   # Codec WebM
                    '-crf', '35',           # Maior compressão
                    '-b:v', '500k',         # Bitrate menor
                    '-movflags', '+faststart',
                    output_path
                ]
            else:
                cmd = [
                    'ffmpeg', '-i', input_path,
                    '-c:v', 'libvpx-vp9',   # Codec WebM
                    '-crf', '28',           # Boa qualidade
                    '-b:v', '1M',           # Bitrate razoável
                    '-movflags', '+faststart',
                    output_path
                ]
            
            subprocess.run(cmd, check=True, capture_output=True)
            return True
        except Exception as e:
            logging.error(f"Erro ao otimizar vídeo: {str(e)}")
            return False

    def check_file_size(self, file_path):
        """Verifica se arquivo está dentro do limite de tamanho"""
        size_mb = os.path.getsize(file_path) / (1024 * 1024)
        return size_mb <= self.max_file_size_mb

    def download_video(self, video_url, filename, category, author_info):
        """Baixa e processa vídeo"""
        try:
            temp_path = os.path.join(self.download_path, 'temp_' + filename)
            final_path = os.path.join(self.download_path, category, filename.replace('.mp4', '.webm'))
            preview_path = os.path.join(self.preview_path, category, 'preview_' + filename.replace('.mp4', '.webm'))

            # Download do vídeo original
            response = requests.get(video_url, stream=True)
            if response.status_code == 200:
                with open(temp_path, 'wb') as f:
                    for chunk in response.iter_content(chunk_size=1024):
                        if chunk:
                            f.write(chunk)

                # Verificar tamanho
                if not self.check_file_size(temp_path):
                    os.remove(temp_path)
                    logging.warning(f"Vídeo muito grande: {filename}")
                    return False

                # Otimizar vídeo e criar preview
                if self.optimize_video(temp_path, final_path) and \
                   self.optimize_video(temp_path, preview_path, is_preview=True):
                    # Salvar créditos
                    video_id = hashlib.md5(video_url.encode()).hexdigest()
                    self.video_credits[video_id] = author_info
                    self.save_json_file(self.credits_file, self.video_credits)
                    
                    # Limpar arquivo temporário
                    os.remove(temp_path)
                    return True

            return False
        except Exception as e:
            logging.error(f"Erro ao processar vídeo {filename}: {str(e)}")
            return False

    def search_videos(self, category, keywords):
        """Busca vídeos no Pixabay por categoria"""
        for keyword in keywords:
            try:
                url = self.base_url + keyword.replace(" ", "+")
                response = requests.get(url)
                if response.status_code == 200:
                    soup = BeautifulSoup(response.text, 'html.parser')
                    videos = soup.find_all('div', class_='item-video')
                    
                    for video in videos:
                        video_id = video.get('data-id')
                        if video_id in self.downloaded_videos['videos']:
                            continue

                        # Verificar licença Pixabay
                        license_info = video.find('div', class_='license')
                        if not license_info or 'Pixabay License' not in license_info.text:
                            continue

                        # Coletar informações do autor
                        author = video.find('a', class_='username')
                        author_info = {
                            'name': author.text if author else 'Unknown',
                            'profile': author['href'] if author else '',
                            'license': 'Pixabay License',
                            'downloaded_at': datetime.now().isoformat()
                        }

                        video_url = video.find('source').get('src')
                        title = video.get('data-title', '').replace(' ', '_')
                        filename = f"{video_id}_{title}.mp4"

                        if self.download_video(video_url, filename, category, author_info):
                            self.downloaded_videos['videos'].append(video_id)
                            logging.info(f"Novo vídeo baixado: {filename} na categoria {category}")

                time.sleep(1)  # Evitar sobrecarga
            except Exception as e:
                logging.error(f"Erro na busca de {keyword}: {str(e)}")

    def update_library(self):
        """Atualiza biblioteca de vídeos"""
        logging.info("Iniciando atualização da biblioteca...")
        
        for category, keywords in self.categories.items():
            self.search_videos(category, keywords)

        self.downloaded_videos['last_update'] = datetime.now().isoformat()
        self.save_json_file(self.db_file, self.downloaded_videos)
        
        logging.info("Atualização concluída.")

if __name__ == "__main__":
    updater = PixabayVideoUpdater()
    updater.update_library() 
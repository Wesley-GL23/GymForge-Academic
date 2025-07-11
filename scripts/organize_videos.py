#!/usr/bin/env python3
import os
import json
import shutil
from datetime import datetime

# Configurações
SOURCE_DIR = '../assets/videos'
TARGET_DIR = '../assets/exercises/videos'
EXERCISES_JSON = '../database/exercises.json'
LOG_FILE = 'video_organization.log'

def load_exercises_mapping():
    """Carrega o mapeamento de exercícios do arquivo JSON"""
    with open(EXERCISES_JSON, 'r', encoding='utf-8') as f:
        data = json.load(f)
        return {ex['video']: ex['category'] for ex in data['exercises']}

def get_video_pairs():
    """Agrupa os vídeos em pares (tiny e small)"""
    videos = os.listdir(SOURCE_DIR)
    pairs = {}
    
    for video in videos:
        base_name = video.replace('_tiny', '').replace('_small', '')
        if base_name not in pairs:
            pairs[base_name] = {'tiny': None, 'small': None}
        
        if '_tiny' in video:
            pairs[base_name]['tiny'] = video
        elif '_small' in video:
            pairs[base_name]['small'] = video
            
    return pairs

def create_category_dirs():
    """Cria os diretórios das categorias se não existirem"""
    categories = ['musculacao', 'cardio', 'funcional', 'alongamento', 'yoga', 'pilates']
    for category in categories:
        category_dir = os.path.join(TARGET_DIR, category)
        if not os.path.exists(category_dir):
            os.makedirs(category_dir)

def log_operation(message):
    """Registra uma operação no arquivo de log"""
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    with open(LOG_FILE, 'a', encoding='utf-8') as f:
        f.write(f'[{timestamp}] {message}\n')

def main():
    # Inicializa o arquivo de log
    with open(LOG_FILE, 'w', encoding='utf-8') as f:
        f.write(f'=== Início da organização de vídeos - {datetime.now()} ===\n\n')
    
    # Carrega o mapeamento de exercícios
    try:
        exercises_mapping = load_exercises_mapping()
        log_operation(f'Mapeamento carregado: {len(exercises_mapping)} exercícios encontrados')
    except Exception as e:
        log_operation(f'Erro ao carregar mapeamento: {str(e)}')
        return
    
    # Cria diretórios das categorias
    create_category_dirs()
    log_operation('Diretórios das categorias criados/verificados')
    
    # Agrupa os vídeos em pares
    video_pairs = get_video_pairs()
    log_operation(f'Encontrados {len(video_pairs)} pares de vídeos')
    
    # Processa cada par de vídeos
    for base_name, versions in video_pairs.items():
        try:
            # Determina qual versão usar (prefere tiny)
            video_file = versions['tiny'] or versions['small']
            if not video_file:
                log_operation(f'Nenhuma versão encontrada para {base_name}')
                continue
            
            # Determina a categoria
            category = exercises_mapping.get(video_file, 'outros')
            if category == 'strength':
                category = 'musculacao'
            elif category == 'core':
                category = 'funcional'
            
            # Define os caminhos
            source_path = os.path.join(SOURCE_DIR, video_file)
            target_path = os.path.join(TARGET_DIR, category, video_file)
            
            # Move o arquivo
            if os.path.exists(source_path):
                shutil.copy2(source_path, target_path)
                log_operation(f'Movido: {video_file} -> {category}/')
            else:
                log_operation(f'Arquivo não encontrado: {video_file}')
            
        except Exception as e:
            log_operation(f'Erro ao processar {base_name}: {str(e)}')
    
    log_operation('\n=== Organização de vídeos concluída ===')

if __name__ == '__main__':
    main() 
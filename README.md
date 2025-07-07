# GymForge - Sistema de Gerenciamento de Academia

GymForge é uma plataforma web moderna para gerenciamento de treinos e acompanhamento de progresso fitness. O sistema combina funcionalidades robustas com uma interface intuitiva e gamificação para tornar a jornada fitness mais engajadora.

## 🚀 Funcionalidades Principais

- 👥 Gerenciamento de usuários com níveis de acesso
- 💪 Biblioteca completa de exercícios com vídeos e instruções
- 📋 Criação e personalização de treinos
- 📊 Acompanhamento de progresso com gráficos
- 🎮 Sistema de gamificação com níveis e conquistas
- 📱 Design responsivo para todas as telas

## 🛠️ Tecnologias Utilizadas

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5.3
- Font Awesome 6.4
- FFmpeg (para processamento de vídeos)
- Redis (opcional, para rate limiting)

## 📋 Pré-requisitos

- Servidor web (Apache/Nginx)
- PHP 7.4 ou superior
  - Extensões: PDO, PDO_MySQL, GD, mbstring, xml
- MySQL 5.7 ou superior
- FFmpeg (para processamento de vídeos)
- Redis (opcional)

## 🔧 Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/gymforge.git
cd gymforge
```

2. Configure o ambiente:
```bash
# Copie os arquivos de configuração
cp config/db_config.example.php config/db_config.php
cp .htaccess.example .htaccess

# Configure o banco de dados em config/db_config.php
nano config/db_config.php
```

3. Crie e configure o banco de dados:
```bash
# Acesse o MySQL
mysql -u root -p

# Crie o banco de dados
CREATE DATABASE gymforge_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Importe a estrutura inicial
mysql -u root -p gymforge_php < database/gymforge.sql
```

4. Configure as permissões:
```bash
# Crie os diretórios necessários
mkdir -p uploads/videos uploads/gifs logs temp

# Configure as permissões
chmod -R 755 .
chmod -R 777 uploads/
chmod -R 777 logs/
chmod -R 777 temp/
```

5. Instale e configure o FFmpeg:
```bash
# Windows (usando o script de instalação)
scripts/setup_ffmpeg.bat

# Linux (via apt)
sudo apt update
sudo apt install ffmpeg
```

6. Configure o servidor web:

Para Apache, verifique se o arquivo `.htaccess` está correto:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Proteção de diretórios
Options -Indexes

# Limites de upload
php_value upload_max_filesize 10M
php_value post_max_size 10M
```

Para Nginx, adicione ao bloco do servidor:
```nginx
server {
    listen 80;
    server_name gymforge.local;
    root /path/to/gymforge;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }

    # Proteção de arquivos
    location ~ /\. {
        deny all;
    }

    # Cache de arquivos estáticos
    location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
```

7. Configure o virtual host (opcional):
```bash
# Copie o arquivo de configuração
sudo cp vhost_dev.conf /etc/apache2/sites-available/gymforge.conf

# Edite o arquivo com suas configurações
sudo nano /etc/apache2/sites-available/gymforge.conf

# Ative o site
sudo a2ensite gymforge.conf
sudo service apache2 restart
```

8. Inicialize o sistema:
```bash
# Execute o script de setup
php scripts/setup_database.php

# Verifique os logs por erros
tail -f logs/error.log
```

## 🚀 Primeiro Acesso

1. Acesse o sistema:
```
http://localhost/gymforge
# ou
http://gymforge.local (se configurou virtual host)
```

2. Faça login com as credenciais padrão:
```
Email: admin@gymforge.com
Senha: admin123
```

3. Altere a senha padrão imediatamente após o primeiro login.

## 📁 Estrutura do Projeto

```
gymforge/
├── actions/         # Controladores de ações
├── assets/         # Arquivos estáticos (CSS, JS, imagens)
│   ├── css/       # Arquivos CSS
│   ├── js/        # Arquivos JavaScript
│   └── img/       # Imagens
├── config/         # Arquivos de configuração
├── database/       # Scripts SQL
├── docs/          # Documentação
├── includes/       # Funções e classes
├── logs/          # Logs do sistema
├── temp/          # Arquivos temporários
├── uploads/       # Uploads de usuários
└── views/         # Templates e páginas
```

## 🔐 Segurança

O sistema implementa diversas medidas de segurança:

- Proteção contra XSS
- Proteção contra CSRF
- Rate limiting (requer Redis)
- Validação de entrada
- Sanitização de saída
- Headers de segurança
- Sessões seguras
- Proteção contra SQL Injection
- Senhas com hash seguro (password_hash)

## 📝 Documentação

A documentação completa está disponível em:

- [Diagramas do Sistema](docs/DIAGRAMAS.md)
- [Documentação da API](docs/API.md)
- [Guia da Marca](docs/BRAND_GUIDELINES.md)
- [Documentação do Site](docs/SITE_DOCUMENTATION.md)
- [Arquitetura](docs/ARCHITECTURE.md)

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 🐛 Reportando Bugs

1. Verifique se o bug já não foi reportado
2. Abra uma issue com:
   - Título claro e descritivo
   - Passos para reproduzir
   - Comportamento esperado
   - Screenshots (se aplicável)
   - Ambiente (SO, navegador, versões)

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## ✨ Agradecimentos

- [Bootstrap](https://getbootstrap.com/)
- [Font Awesome](https://fontawesome.com/)
- [PHP](https://www.php.net/)
- [MySQL](https://www.mysql.com/)
- [FFmpeg](https://ffmpeg.org/)
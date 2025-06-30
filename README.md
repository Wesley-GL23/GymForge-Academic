# GymForge Academic

Versão acadêmica do GymForge desenvolvida em PHP procedural, MySQL e Bootstrap. Projeto para disciplina de Desenvolvimento Web.

## 🏋️‍♂️ Sobre o Projeto

GymForge é um sistema web para gerenciamento de academia que permite:
- Gerenciamento de exercícios com demonstrações em GIF
- Criação e atribuição de treinos personalizados
- Sistema de autenticação com múltiplos níveis de acesso
- Interface responsiva e intuitiva

## 🛠️ Tecnologias

- PHP (Procedural)
- MySQL
- Bootstrap 5
- JavaScript
- HTML5/CSS3

## 📦 Pré-requisitos

- PHP 7.4+
- MySQL 5.7+
- Apache/XAMPP
- Navegador moderno

## 🚀 Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/GymForge-PHP.git
```

2. Importe o banco de dados:
```bash
mysql -u root -p < database/gymforge.sql
```

3. Configure a conexão em `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'gymforge');
```

4. Inicie o servidor Apache e MySQL (XAMPP)

5. Acesse o sistema:
```
http://localhost/GymForge-PHP
```

## 👥 Níveis de Acesso

1. **Visitante**
   - Visualizar página inicial
   - Realizar cadastro
   - Efetuar login

2. **Cliente**
   - Visualizar seus treinos
   - Ver exercícios disponíveis
   - Acompanhar progresso

3. **Administrador**
   - Gerenciar usuários
   - Cadastrar exercícios
   - Criar e atribuir treinos
   - Gerenciar todo o sistema

## 📁 Estrutura do Projeto

```
GymForge-PHP/
├── actions/      # Processamento de formulários
├── assets/       # Recursos estáticos (CSS, JS, imagens)
├── config/       # Configurações do sistema
├── database/     # Scripts SQL
├── docs/         # Documentação
├── forms/        # Formulários
├── includes/     # Funções compartilhadas
└── views/        # Páginas de visualização
```

## 🔒 Segurança

- Prepared Statements para prevenção de SQL Injection
- Senhas com hash seguro
- Validação de dados server-side
- Proteção contra CSRF
- Controle de sessão
- Validação de uploads

## 📚 Documentação

- [Relatório Final](docs/relatorio_final.md)
- [Uso de IA](docs/uso_ia.md)
- [Pontos de Estudo](docs/pontos_estudo.md)

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## ✨ Agradecimentos

- Professor e monitores da disciplina
- Comunidade PHP
- Cursor (IA) pelo suporte no desenvolvimento

## Configuração do FFmpeg

Para processar vídeos e gifs, este projeto requer o FFmpeg. Siga os passos abaixo para configurar:

1. Baixe o FFmpeg para Windows:
   - Acesse [FFmpeg Downloads](https://www.gyan.dev/ffmpeg/builds/)
   - Baixe a versão "ffmpeg-release-essentials"
   - Extraia os arquivos `ffmpeg.exe` e `ffprobe.exe`
   - Coloque os arquivos na pasta `scripts/` do projeto

Alternativamente, você pode executar o script `scripts/setup_ffmpeg.bat` que fará o download e configuração automaticamente 
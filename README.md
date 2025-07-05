# GymForge - Sistema de Gerenciamento de Treinos

GymForge é um sistema web desenvolvido em PHP para gerenciamento de treinos e exercícios, permitindo que usuários criem e acompanhem seus programas de treinamento.

## Requisitos do Sistema

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache 2.4 ou superior
- Extensões PHP: PDO, MySQL, JSON

## Instalação

1. Clone o repositório para sua pasta htdocs do XAMPP:
```bash
git clone https://github.com/seu-usuario/GymForge-PHP.git
```

2. Configure o banco de dados:
   - Importe o arquivo `database/gymforge_simple.sql`
   - Copie `config/db_config.example.php` para `config/db_config.php`
   - Ajuste as credenciais do banco em `config/db_config.php`

3. Configure o Virtual Host:
   - Adicione ao arquivo `hosts` (C:\Windows\System32\drivers\etc\hosts):
     ```
     127.0.0.1   gymforge.local
     ```
   - Adicione ao arquivo `httpd-vhosts.conf` do Apache:
     ```apache
     <VirtualHost *:80>
         DocumentRoot "C:/xampp/htdocs/GymForge-PHP"
         ServerName gymforge.local
         <Directory "C:/xampp/htdocs/GymForge-PHP">
             AllowOverride All
             Require all granted
         </Directory>
     </VirtualHost>
     ```

4. Reinicie o Apache

## Credenciais de Teste

- **Administrador**
  - Email: admin@gymforge.com
  - Senha: password

## Estrutura do Projeto

- `/actions` - Controladores e ações do sistema
- `/assets` - Arquivos estáticos (CSS, JS, imagens)
- `/config` - Arquivos de configuração
- `/database` - Scripts SQL e migrations
- `/docs` - Documentação e diagramas
- `/forms` - Formulários do sistema
- `/includes` - Funções auxiliares e componentes
- `/views` - Páginas e templates

## Funcionalidades

### Usuário Comum
- Cadastro e login
- Visualização de exercícios
- Criação e gerenciamento de treinos
- Registro de progresso

### Administrador
- Gerenciamento de exercícios
- Gerenciamento de usuários
- Visualização de relatórios

## Documentação

- [Diagramas do Sistema](docs/DIAGRAMAS.md)
- [Arquitetura](docs/ARCHITECTURE.md)
- [Guia da Marca](docs/BRAND_GUIDELINES.md)

## Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.
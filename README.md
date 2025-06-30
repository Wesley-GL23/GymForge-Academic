# GymForge-PHP

Sistema de Gerenciamento de Treinos - Versão Acadêmica

## Descrição

GymForge-PHP é uma versão acadêmica do sistema GymForge, desenvolvida como projeto para a disciplina de [Nome da Disciplina]. O sistema permite o gerenciamento de treinos, exercícios e usuários, com funcionalidades específicas para diferentes níveis de acesso.

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor Web (Apache/Nginx)
- Extensões PHP:
  - mysqli
  - session
  - gd (para manipulação de imagens)

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/GymForge-PHP.git
```

2. Configure o banco de dados:
   - Crie um banco de dados MySQL
   - Importe o arquivo `database/gymforge.sql`
   - Configure as credenciais em `config/conexao.php`

3. Configure o servidor web:
   - Aponte o document root para a pasta do projeto
   - Certifique-se que o mod_rewrite está habilitado (Apache)

4. Configure as permissões:
```bash
chmod 755 -R GymForge-PHP/
chmod 777 -R GymForge-PHP/assets/gifs/
```

5. Acesse o sistema:
   - URL: http://localhost/GymForge-PHP
   - Login Admin: admin@gymforge.com
   - Senha: admin123

## Estrutura do Projeto

```
GymForge-PHP/
├── actions/          # Arquivos de processamento de formulários
├── assets/          # Recursos estáticos (CSS, JS, imagens)
├── config/          # Arquivos de configuração
├── database/        # Scripts SQL
├── docs/            # Documentação (DER, Casos de Uso)
├── forms/           # Formulários do sistema
├── includes/        # Arquivos incluídos em múltiplas páginas
└── views/           # Páginas de visualização
```

## Funcionalidades

- Sistema de autenticação com 3 níveis de acesso
- Gerenciamento de exercícios com GIFs demonstrativos
- Criação e gerenciamento de treinos
- Sistema de notificações
- Perfil do usuário
- Dicas de treino
- Interface responsiva com Bootstrap

## Desenvolvimento

- Tecnologias: PHP (procedural), MySQL, Bootstrap
- Validação client-side e server-side
- Organização modular do código
- Práticas de segurança implementadas

## Documentação

- Diagrama de Casos de Uso: `docs/casos-de-uso.png`
- Diagrama Entidade-Relacionamento: `docs/der.png`
- Relatório de uso de IA: `docs/relatorio-ia.pdf`

## Contribuição

Este é um projeto acadêmico desenvolvido para fins educacionais. Contribuições são bem-vindas através de pull requests.

## Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## Autor

[Seu Nome]
[Sua Instituição de Ensino]
[Seu Email] 
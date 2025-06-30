# Relatório Final - GymForge

## 1. Identificação
- **Aluno**: Wesley Guilherme Lourenço
- **RA**: 20241TBOR0R10010045
- **Disciplina**: Programação Web
- **Professor**: Paulo Ricardo de Souza Silva
- **Data**: 06/07/2025

## 2. Descrição do Sistema

### 2.1 Visão Geral
O GymForge é um sistema web para gerenciamento de academia, desenvolvido em PHP procedural, MySQL e Bootstrap. O sistema permite o gerenciamento de exercícios, treinos e usuários, com diferentes níveis de acesso.

### 2.2 Objetivos
- Facilitar o gerenciamento de treinos personalizados
- Permitir o acompanhamento dos treinos pelos clientes
- Fornecer uma interface intuitiva para administradores e clientes
- Garantir a segurança e integridade dos dados

### 2.3 Público-Alvo
- Administradores de academia
- Personal trainers
- Clientes da academia

## 3. Modelagem do Sistema

### 3.1 Diagrama de Casos de Uso
[Inserir imagem do diagrama de casos de uso]

Principais funcionalidades:
- Gerenciamento de usuários (admin)
- Gerenciamento de exercícios (admin)
- Gerenciamento de treinos (admin)
- Visualização de treinos (cliente)
- Acompanhamento de progresso (cliente)

### 3.2 Diagrama Entidade-Relacionamento
[Inserir imagem do DER]

Tabelas principais:
- usuarios
- exercicios
- treinos
- treino_exercicios
- dicas

## 4. Tecnologias Utilizadas

### 4.1 Backend
- PHP (procedural) - Linguagem principal do projeto
- MySQL - Sistema de gerenciamento de banco de dados
- Apache - Servidor web local via XAMPP

### 4.2 Frontend
- HTML5 - Estruturação das páginas
- CSS3 - Estilização personalizada
- JavaScript - Interatividade e validações client-side
- Bootstrap 5 - Framework CSS para design responsivo
- Font Awesome - Biblioteca de ícones

### 4.3 Ferramentas de Desenvolvimento
- Visual Studio Code - Editor de código principal
- XAMPP - Ambiente de desenvolvimento local
- Git/GitHub - Controle de versão e hospedagem do código
- Cursor (IA) - Assistente de desenvolvimento para otimização do código

## 5. Estrutura do Projeto

### 5.1 Organização de Diretórios
```
GymForge-PHP/
  ├── actions/       # Processamento de formulários
  ├── assets/        # Recursos estáticos (CSS, JS, imagens)
  ├── config/        # Configurações do sistema
  ├── database/      # Scripts SQL
  ├── docs/          # Documentação
  ├── forms/         # Formulários
  ├── includes/      # Funções compartilhadas
  └── views/         # Páginas de visualização
```

### 5.2 Principais Funcionalidades Implementadas
1. Sistema de Autenticação
   - Sistema completo de login/logout com sessões PHP
   - Três níveis de acesso: visitante, cliente e administrador
   - Proteção de rotas com verificação de nível de acesso
   - Recuperação de senha via email (planejado)

2. Gerenciamento de Exercícios
   - CRUD completo de exercícios
   - Sistema de upload e exibição de GIFs demonstrativos
   - Categorização por grupo muscular
   - Descrições detalhadas e instruções de execução
   - Busca e filtros avançados

3. Gerenciamento de Treinos
   - Criação personalizada de treinos para clientes
   - Seleção dinâmica de exercícios do banco de dados
   - Definição de séries, repetições e carga
   - Sistema de progressão de carga
   - Agendamento e periodicidade dos treinos

4. Interface do Cliente
   - Dashboard personalizado com resumo de atividades
   - Visualização detalhada dos treinos atribuídos
   - Acompanhamento de progresso
   - Sistema de dicas diárias de saúde e treino
   - Área de feedback e comunicação com instrutor

## 6. Uso de Inteligência Artificial

### 6.1 Funcionalidades Desenvolvidas com IA
- Sistema de transações para cadastro e edição de treinos
- Queries SQL complexas para relacionamento entre treinos e exercícios
- Interface dinâmica com JavaScript para seleção de exercícios
- Sistema de upload e validação de arquivos GIF
- Sistema de autenticação e autorização multinível
- Validações de formulários client e server-side

### 6.2 Como a IA Foi Utilizada
A IA (Cursor) foi utilizada principalmente para:
- Estruturação de queries SQL complexas para relacionamentos many-to-many
- Implementação de transações seguras para operações críticas
- Desenvolvimento de interfaces dinâmicas com JavaScript
- Sistema robusto de autenticação e autorização
- Otimização de código e boas práticas de segurança
- Debugging e resolução de problemas complexos

## 7. Segurança

### 7.1 Medidas Implementadas
- Prepared statements para prevenção de SQL injection em todas as queries
- Validação rigorosa de dados no servidor usando PHP
- Sistema de proteção de rotas baseado em níveis de usuário
- Validação de uploads de arquivos (tipo, tamanho, extensão)
- Senhas armazenadas com hash seguro (password_hash)
- Proteção contra CSRF em formulários
- Sessões seguras com regeneração de ID

### 7.2 Validações
- Validações client-side com HTML5 e JavaScript
- Validações server-side com PHP para todos os inputs
- Sanitização de dados antes de exibição (htmlspecialchars)
- Sistema robusto de verificação de permissões
- Validação de tipos de arquivo para uploads
- Verificação de autenticidade de sessão
- Escape de caracteres especiais em queries SQL

## 8. Conclusão

### 8.1 Resultados Alcançados
- Sistema funcional e seguro
- Interface responsiva e intuitiva
- Gerenciamento eficiente de treinos
- Experiência personalizada para usuários

### 8.2 Dificuldades Encontradas
- Implementação de transações complexas para garantir integridade dos dados
- Gerenciamento de estados na interface durante seleção de exercícios
- Sistema de upload e validação de arquivos GIF
- Queries complexas para relacionamento entre treinos e exercícios
- Implementação do sistema de autenticação multinível
- Tratamento de concorrência em atualizações de treino

### 8.3 Aprendizados
- Desenvolvimento web estruturado com separação de responsabilidades
- Implementação de boas práticas de segurança em PHP
- Uso eficiente e ético de IA no desenvolvimento
- Modelagem de dados relacional com múltiplos relacionamentos
- Importância da validação de dados em múltiplas camadas
- Gerenciamento eficiente de sessões e autenticação
- Práticas de UI/UX para melhor experiência do usuário

## 9. Referências
1. PHP Documentation - https://www.php.net/docs.php
   - Manual PHP
   - Referência da Linguagem
   - Segurança
   
2. Bootstrap 5 Documentation - https://getbootstrap.com/docs/5.0/
   - Components
   - Forms
   - Utilities
   
3. MySQL 8.0 Reference Manual - https://dev.mysql.com/doc/refman/8.0/en/
   - SQL Statements
   - Functions and Operators
   - Security
   
4. MDN Web Docs - https://developer.mozilla.org/
   - JavaScript Guide
   - Web APIs
   - HTML Forms
   
5. OWASP Web Security - https://owasp.org/
   - Security Best Practices
   - Input Validation
   - Session Management

## 10. Anexos

### 10.1 Link do Projeto
GitHub: [https://github.com/Wesley-GL23/GymForge-Academic.git](https://github.com/Wesley-GL23/GymForge-Academic.git)

### 10.2 Screenshots
[Inserir screenshots das principais telas do sistema]

### 10.3 Instruções de Instalação
1. Clonar o repositório
2. Importar o banco de dados
3. Configurar conexão
4. Acessar o sistema 
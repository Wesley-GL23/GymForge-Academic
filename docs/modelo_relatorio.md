# Relatório Final - GymForge

## 1. Identificação
- **Aluno**: [Seu Nome]
- **RA**: [Seu RA]
- **Disciplina**: Programação Web
- **Professor**: [Nome do Professor]
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
- PHP 7.4 (procedural)
- MySQL 8.0
- Apache 2.4

### 4.2 Frontend
- HTML5
- CSS3
- JavaScript
- Bootstrap 5.1
- Font Awesome 5.15

### 4.3 Ferramentas de Desenvolvimento
- Visual Studio Code
- XAMPP
- Git/GitHub
- Cursor (IA)

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
   - Login/logout
   - Níveis de acesso
   - Proteção de rotas

2. Gerenciamento de Exercícios
   - CRUD completo
   - Upload de GIFs
   - Categorização

3. Gerenciamento de Treinos
   - Criação personalizada
   - Seleção de exercícios
   - Definição de séries/repetições

4. Interface do Cliente
   - Visualização de treinos
   - Dashboard personalizado
   - Dicas diárias

## 6. Uso de Inteligência Artificial

### 6.1 Funcionalidades Desenvolvidas com IA
- Sistema de transações para treinos
- Queries SQL complexas
- Interface dinâmica com JavaScript
- Sistema de upload de arquivos

### 6.2 Como a IA Foi Utilizada
A IA (Cursor) foi utilizada principalmente para:
- Estruturação de queries complexas
- Implementação de transações seguras
- Desenvolvimento de interfaces dinâmicas
- Sistema de autenticação e autorização

## 7. Segurança

### 7.1 Medidas Implementadas
- Prepared statements para prevenção de SQL injection
- Validação de dados server-side
- Proteção de rotas por nível de usuário
- Validação de uploads de arquivos

### 7.2 Validações
- Client-side com HTML5 e Bootstrap
- Server-side com PHP
- Sanitização de inputs
- Verificação de permissões

## 8. Conclusão

### 8.1 Resultados Alcançados
- Sistema funcional e seguro
- Interface responsiva e intuitiva
- Gerenciamento eficiente de treinos
- Experiência personalizada para usuários

### 8.2 Dificuldades Encontradas
- Implementação de transações complexas
- Gerenciamento de estados na interface
- Upload e manipulação de arquivos
- Queries com múltiplos relacionamentos

### 8.3 Aprendizados
- Desenvolvimento web estruturado
- Boas práticas de segurança
- Uso consciente de IA
- Modelagem de dados relacional

## 9. Referências
1. Documentação PHP: https://www.php.net/docs.php
2. Bootstrap Documentation: https://getbootstrap.com/docs/
3. MySQL Documentation: https://dev.mysql.com/doc/
4. [Outras referências utilizadas]

## 10. Anexos

### 10.1 Link do Projeto
GitHub: [URL do seu repositório]

### 10.2 Screenshots
[Inserir screenshots das principais telas do sistema]

### 10.3 Instruções de Instalação
1. Clonar o repositório
2. Importar o banco de dados
3. Configurar conexão
4. Acessar o sistema 
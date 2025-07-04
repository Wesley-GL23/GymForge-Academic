# Arquitetura do GymForge

## Visão Geral

O GymForge é uma aplicação web desenvolvida em PHP para gerenciamento de academia, permitindo o cadastro de usuários, exercícios e treinos. A aplicação segue uma arquitetura MVC simplificada e utiliza MySQL como banco de dados.

## Estrutura de Diretórios

```
GymForge-PHP/
├── actions/           # Controladores que processam formulários
├── assets/           # Recursos estáticos (CSS, JS, imagens)
├── config/           # Configurações da aplicação
├── database/         # Scripts SQL e migrações
├── docs/            # Documentação
├── forms/           # Formulários da aplicação
├── includes/        # Arquivos PHP reutilizáveis
├── scripts/         # Scripts de utilitários
└── views/           # Páginas da aplicação
```

## Componentes Principais

### 1. Sistema de Autenticação
- Gerenciamento de sessões
- Login/Logout
- Recuperação de senha
- Níveis de acesso (admin/cliente)

### 2. Gerenciamento de Usuários
- Cadastro de usuários
- Perfil do usuário
- Atualização de dados
- Controle de medidas

### 3. Gerenciamento de Exercícios
- Cadastro de exercícios
- Upload de vídeos demonstrativos
- Categorização por grupo muscular
- Biblioteca de exercícios

### 4. Gerenciamento de Treinos
- Criação de treinos
- Associação de exercícios
- Definição de séries e repetições
- Acompanhamento de progresso

### 5. Sistema de Mídia
- Upload de vídeos
- Processamento automático (FFmpeg)
- CDN para distribuição
- Cache de arquivos

## Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Bootstrap 5
- **Processamento de Vídeo**: FFmpeg
- **Bibliotecas**:
  - jQuery
  - DataTables
  - Chart.js
  - SweetAlert2

## Segurança

### Autenticação
- Senhas criptografadas com `password_hash()`
- Proteção contra SQL Injection
- Validação de entrada
- Tokens CSRF

### Uploads
- Validação de tipos de arquivo
- Limite de tamanho
- Renomeação segura
- Processamento assíncrono

### Sessões
- Timeout configurável
- Regeneração de ID
- Validação de IP

## Banco de Dados

### Tabelas Principais
- `usuarios`
- `exercicios`
- `treinos`
- `treino_exercicios`
- `medidas`
- `videos`

### Relacionamentos
- Um usuário pode ter vários treinos
- Um treino pode ter vários exercícios
- Um exercício pode estar em vários treinos
- Um usuário pode ter várias medidas

## Processamento de Vídeos

### Fluxo
1. Upload do vídeo original
2. Armazenamento temporário
3. Processamento com FFmpeg
4. Validação do resultado
5. Distribuição via CDN

### Configurações FFmpeg
- Codec: H.264
- Qualidade: CRF 23
- Áudio: AAC 128k
- Otimização para web

## Manutenção

### Logs
- Logs de erro
- Logs de acesso
- Logs de processamento
- Rotação automática

### Backup
- Backup diário do banco
- Backup semanal dos arquivos
- Retenção configurável
- Verificação de integridade

## Desenvolvimento

### Padrões de Código
- PSR-1 e PSR-12
- Documentação PHPDoc
- Commits semânticos
- Code review

### Ambiente
- XAMPP para desenvolvimento
- Git para controle de versão
- GitHub para colaboração
- Ambiente de staging

## Roadmap

### Curto Prazo
- [ ] Implementação de testes automatizados
- [ ] Melhorias na interface mobile
- [ ] Otimização de consultas SQL

### Médio Prazo
- [ ] API REST para integração
- [ ] Sistema de notificações
- [ ] Relatórios avançados

### Longo Prazo
- [ ] App mobile
- [ ] Integração com wearables
- [ ] IA para recomendações 
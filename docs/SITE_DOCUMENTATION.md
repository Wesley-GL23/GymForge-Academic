# Documentação do Site GymForge

## Visão Geral
O GymForge é uma plataforma web desenvolvida para gerenciamento de academias e acompanhamento de treinos, oferecendo uma experiência moderna e intuitiva para usuários e administradores.

## Estrutura do Site

### 1. Páginas Principais
- **Home (index.php)**
  - Hero section com chamada para ação
  - Seção de benefícios
  - Depoimentos de usuários
  - Planos e preços
  - Call-to-action para registro

- **Painel do Usuário (dashboard.php)**
  - Visão geral dos treinos
  - Progresso e estatísticas
  - Últimas atividades
  - Notificações

- **Biblioteca de Exercícios (exercises.php)**
  - Catálogo de exercícios
  - Filtros por categoria
  - Vídeos demonstrativos
  - Instruções detalhadas

- **Área de Treinos (workouts.php)**
  - Criação de treinos
  - Templates pré-definidos
  - Histórico de treinos
  - Métricas de progresso

### 2. Componentes Reutilizáveis

#### Header (includes/header.php)
```php
- Navbar responsiva
- Menu de navegação principal
- Área de usuário logado
- Busca integrada
```

#### Footer (includes/footer.php)
```php
- Links rápidos
- Redes sociais
- Newsletter
- Informações de contato
```

#### Cards de Exercício (components/exercise-card.php)
```php
- Imagem do exercício
- Título e descrição
- Nível de dificuldade
- Grupos musculares
```

#### Modais (components/modals/)
```php
- Login/Registro
- Detalhes do exercício
- Confirmações
- Formulários
```

### 3. Assets e Recursos

#### CSS
- **styles.css**: Estilos globais
- **components.css**: Componentes reutilizáveis
- **utilities.css**: Classes utilitárias
- **animations.css**: Animações e transições

#### JavaScript
- **main.js**: Funcionalidades globais
- **exercises.js**: Lógica da biblioteca de exercícios
- **workouts.js**: Gerenciamento de treinos
- **charts.js**: Gráficos e visualizações

#### Imagens
- **Logo**: Variações do logo GymForge
- **Icons**: Ícones personalizados
- **Exercises**: Imagens dos exercícios
- **Backgrounds**: Padrões e texturas

### 4. Funcionalidades Principais

#### Sistema de Autenticação
- Registro de usuário
- Login/Logout
- Recuperação de senha
- Níveis de acesso

#### Gerenciamento de Treinos
- Criação de treinos
- Seleção de exercícios
- Definição de séries/repetições
- Acompanhamento de progresso

#### Biblioteca de Exercícios
- Catálogo completo
- Categorização
- Vídeos demonstrativos
- Instruções detalhadas

#### Sistema de Progresso
- Registro de treinos
- Métricas e estatísticas
- Gráficos de evolução
- Conquistas e níveis

### 5. Padrões de Design

#### Cores
```css
--forge-primary: #007FFF    /* Azul Principal */
--forge-secondary: #4682B4  /* Azul Secundário */
--forge-dark: #051C2C      /* Azul Escuro */
--forge-accent: #FF6F20    /* Laranja Destaque */
--forge-white: #FFFFFF     /* Branco */
--forge-gray: #F5F5F5     /* Cinza Claro */
```

#### Tipografia
```css
/* Títulos */
font-family: 'Montserrat', sans-serif
font-weight: 700, 600

/* Corpo */
font-family: 'Inter', sans-serif
font-weight: 400, 500
```

#### Espaçamento
```css
--spacing-xs: 0.25rem  /* 4px */
--spacing-sm: 0.5rem   /* 8px */
--spacing-md: 1rem     /* 16px */
--spacing-lg: 1.5rem   /* 24px */
--spacing-xl: 2rem     /* 32px */
```

#### Breakpoints
```css
--mobile: 576px
--tablet: 768px
--desktop: 992px
--wide: 1200px
```

### 6. Boas Práticas

#### Performance
- Otimização de imagens
- Minificação de CSS/JS
- Lazy loading
- Cache estratégico

#### Acessibilidade
- Semântica HTML5
- ARIA labels
- Contraste adequado
- Navegação por teclado

#### SEO
- Meta tags otimizadas
- Estrutura semântica
- URLs amigáveis
- Sitemap XML

#### Segurança
- Validação de inputs
- Proteção contra XSS
- CSRF tokens
- Sanitização de dados

### 7. Fluxos de Usuário

#### Registro e Onboarding
1. Cadastro inicial
2. Verificação de email
3. Completar perfil
4. Tour guiado

#### Criação de Treino
1. Seleção de objetivo
2. Escolha de exercícios
3. Definição de parâmetros
4. Revisão e ativação

#### Acompanhamento de Progresso
1. Registro de treino
2. Atualização de métricas
3. Visualização de evolução
4. Ajustes e adaptações

### 8. Manutenção

#### Backups
- Banco de dados: diário
- Arquivos: semanal
- Configurações: versionado
- Logs: rotação mensal

#### Monitoramento
- Logs de erro
- Métricas de performance
- Uso de recursos
- Atividade de usuários

#### Atualizações
- Correções de bugs
- Novas funcionalidades
- Melhorias de segurança
- Otimizações de performance

### 9. Suporte

#### Canais
- Email: suporte@gymforge.com
- Chat: horário comercial
- FAQ: base de conhecimento
- Tutoriais em vídeo

#### Tempos de Resposta
- Crítico: 2 horas
- Alto: 4 horas
- Médio: 24 horas
- Baixo: 48 horas

### 10. Roadmap

#### Curto Prazo (3 meses)
- [ ] Implementação de PWA
- [ ] Sistema de notificações
- [ ] Integração com wearables
- [ ] Melhorias de UX/UI

#### Médio Prazo (6 meses)
- [ ] App mobile nativo
- [ ] API pública
- [ ] Marketplace de treinos
- [ ] Sistema de gamificação

#### Longo Prazo (12 meses)
- [ ] IA para recomendações
- [ ] Realidade aumentada
- [ ] Integração com academias
- [ ] Plataforma de conteúdo

---

## Notas de Versão

### v1.0.0 (Atual)
- Lançamento inicial
- CRUD completo de usuários e treinos
- Sistema básico de autenticação
- Interface responsiva

### Próxima Versão (v1.1.0)
- Sistema de notificações
- Melhorias na interface mobile
- Novos templates de treino
- Correções de bugs reportados 
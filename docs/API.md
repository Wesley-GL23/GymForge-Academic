# API GymForge

## Autenticação

### Login
- **Endpoint**: `/actions/usuario/login.php`
- **Método**: POST
- **Parâmetros**:
  - `email`: Email do usuário
  - `senha`: Senha do usuário
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Login realizado com sucesso",
    "user": {
      "id": 1,
      "nome": "Nome do Usuário",
      "nivel": "cliente"
    }
  }
  ```

### Logout
- **Endpoint**: `/actions/usuario/logout.php`
- **Método**: POST
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Logout realizado com sucesso"
  }
  ```

### Cadastro
- **Endpoint**: `/actions/usuario/cadastrar.php`
- **Método**: POST
- **Parâmetros**:
  - `nome`: Nome completo
  - `email`: Email
  - `senha`: Senha
  - `confirmar_senha`: Confirmação da senha
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Cadastro realizado com sucesso"
  }
  ```

## Exercícios

### Listar Exercícios
- **Endpoint**: `/actions/exercicio/crud.php`
- **Método**: GET
- **Parâmetros**:
  - `action`: "list"
  - `categoria` (opcional): Filtrar por categoria
  - `grupo_muscular` (opcional): Filtrar por grupo muscular
- **Retorno**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "nome": "Supino Reto",
        "descricao": "Exercício para peitoral",
        "categoria": "musculacao",
        "grupo_muscular": "peito",
        "nivel_dificuldade": "iniciante"
      }
    ]
  }
  ```

### Criar Exercício
- **Endpoint**: `/actions/exercicio/crud.php`
- **Método**: POST
- **Parâmetros**:
  - `action`: "create"
  - `nome`: Nome do exercício
  - `descricao`: Descrição
  - `categoria`: Categoria
  - `grupo_muscular`: Grupo muscular
  - `nivel_dificuldade`: Nível de dificuldade
  - `instrucoes`: Instruções
  - `dicas_seguranca`: Dicas de segurança
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Exercício criado com sucesso",
    "id": 1
  }
  ```

### Atualizar Exercício
- **Endpoint**: `/actions/exercicio/crud.php`
- **Método**: POST
- **Parâmetros**:
  - `action`: "update"
  - `id`: ID do exercício
  - `nome`: Nome do exercício
  - `descricao`: Descrição
  - `categoria`: Categoria
  - `grupo_muscular`: Grupo muscular
  - `nivel_dificuldade`: Nível de dificuldade
  - `instrucoes`: Instruções
  - `dicas_seguranca`: Dicas de segurança
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Exercício atualizado com sucesso"
  }
  ```

### Excluir Exercício
- **Endpoint**: `/actions/exercicio/crud.php`
- **Método**: POST
- **Parâmetros**:
  - `action`: "delete"
  - `id`: ID do exercício
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Exercício excluído com sucesso"
  }
  ```

## Treinos

### Listar Treinos
- **Endpoint**: `/actions/treino/crud.php`
- **Método**: GET
- **Parâmetros**:
  - `action`: "list"
  - `usuario_id` (opcional): Filtrar por usuário
  - `status` (opcional): Filtrar por status
- **Retorno**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "nome": "Treino A",
        "descricao": "Treino de força",
        "tipo": "normal",
        "nivel_dificuldade": "iniciante",
        "status": "ativo"
      }
    ]
  }
  ```

### Criar Treino
- **Endpoint**: `/actions/treino/crud.php`
- **Método**: POST
- **Parâmetros**:
  - `action`: "create"
  - `nome`: Nome do treino
  - `descricao`: Descrição
  - `tipo`: Tipo do treino
  - `nivel_dificuldade`: Nível de dificuldade
  - `data_inicio`: Data de início
  - `data_fim`: Data de fim (opcional)
  - `exercicios`: Array de exercícios com séries e repetições
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Treino criado com sucesso",
    "id": 1
  }
  ```

### Atualizar Treino
- **Endpoint**: `/actions/treino/crud.php`
- **Método**: POST
- **Parâmetros**:
  - `action`: "update"
  - `id`: ID do treino
  - `nome`: Nome do treino
  - `descricao`: Descrição
  - `tipo`: Tipo do treino
  - `nivel_dificuldade`: Nível de dificuldade
  - `data_inicio`: Data de início
  - `data_fim`: Data de fim (opcional)
  - `exercicios`: Array de exercícios com séries e repetições
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Treino atualizado com sucesso"
  }
  ```

### Excluir Treino
- **Endpoint**: `/actions/treino/crud.php`
- **Método**: POST
- **Parâmetros**:
  - `action`: "delete"
  - `id`: ID do treino
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Treino excluído com sucesso"
  }
  ```

## Progresso

### Registrar Progresso
- **Endpoint**: `/actions/treino/progresso.php`
- **Método**: POST
- **Parâmetros**:
  - `treino_id`: ID do treino
  - `exercicio_id`: ID do exercício
  - `series_completadas`: Número de séries completadas
  - `peso_utilizado`: Peso utilizado (opcional)
  - `dificuldade_percebida`: Dificuldade percebida (1-10)
  - `observacoes`: Observações (opcional)
- **Retorno**:
  ```json
  {
    "success": true,
    "message": "Progresso registrado com sucesso"
  }
  ```

### Listar Progresso
- **Endpoint**: `/actions/treino/progresso.php`
- **Método**: GET
- **Parâmetros**:
  - `treino_id`: ID do treino
  - `data_inicio` (opcional): Filtrar por data inicial
  - `data_fim` (opcional): Filtrar por data final
- **Retorno**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "treino_id": 1,
        "exercicio_id": 1,
        "series_completadas": 3,
        "peso_utilizado": 20.5,
        "dificuldade_percebida": 7,
        "data_execucao": "2024-03-15 10:30:00"
      }
    ]
  }
  ``` 
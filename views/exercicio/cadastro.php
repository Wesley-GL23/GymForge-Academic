<?php
require_once '../../includes/header.php';
require_once '../../includes/auth_functions.php';

// Verifica se é admin
requireAdmin();
?>

<div class="container">
    <nav aria-label="breadcrumb" class="mt-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/views/exercicio/listar.php">Exercícios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Novo Exercício</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h1 class="card-title"><i class="fas fa-plus"></i> Novo Exercício</h1>
        </div>
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/actions/exercicio/cadastrar.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome *</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>

                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoria *</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Selecione...</option>
                                <option value="Superiores">Superiores</option>
                                <option value="Inferiores">Inferiores</option>
                                <option value="Abdômen">Abdômen</option>
                                <option value="Costas">Costas</option>
                                <option value="Funcionais">Funcionais</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição *</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="instrucoes" class="form-label">Instruções</label>
                            <textarea class="form-control" id="instrucoes" name="instrucoes" rows="3"></textarea>
                            <div class="form-text">Passo a passo de como realizar o exercício corretamente.</div>
                        </div>

                        <div class="mb-3">
                            <label for="dicas" class="form-label">Dicas</label>
                            <textarea class="form-control" id="dicas" name="dicas" rows="3"></textarea>
                            <div class="form-text">Dicas para melhor execução e aproveitamento do exercício.</div>
                        </div>

                        <div class="mb-3">
                            <label for="gif" class="form-label">GIF Demonstrativo</label>
                            <input type="file" class="form-control" id="gif" name="gif" accept=".gif">
                            <div class="form-text">Upload de GIF demonstrando a execução do exercício.</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/exercicio/listar.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 
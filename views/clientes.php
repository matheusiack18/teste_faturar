<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matheus iack</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../assets/images/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                <div class="logo-fallback" style="display: none;">S</div>
                Sistema de Clientes
            </a>
        </div>
    </nav>

    <div class="container main-container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-people-fill me-2"></i>Clientes</h2>
                    <button type="button" class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#clienteModal" onclick="openCreateModal()">
                        <i class="bi bi-plus-lg me-2"></i>Adicionar Cliente
                    </button>
                </div>
                
                <div class="card card-simple">
                    <div class="card-body p-0">
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                <?php echo htmlspecialchars($_GET['success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?php echo htmlspecialchars($_GET['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div id="loading" class="text-center py-5" style="display: none;">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2 mb-0">Carregando clientes...</p>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-clean mb-0" id="clientesTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">ID</th>
                                        <th>Nome</th>
                                        <th>CPF/CNPJ</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th class="text-center" width="140">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="clientesTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clienteModalLabel">
                        <i class="bi bi-person-plus me-2"></i>Novo Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="clienteForm">
                    <div class="modal-body">
                        <input type="hidden" id="clienteId" name="id" value="">
                        <input type="hidden" id="formAction" name="action" value="create">
                        
                        <div id="modalAlerts"></div>
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome completo *</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome completo" required maxlength="100">
                            <div class="form-text">Mínimo 2 caracteres, máximo 100 caracteres</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cpf_cnpj" class="form-label">CPF/CNPJ *</label>
                            <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" placeholder="000.000.000-00 ou 00.000.000/0000-00" required>
                            <div class="form-text">Digite apenas números ou use a formatação padrão</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail *</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="exemplo@dominio.com" required maxlength="100">
                            <div class="form-text">Máximo 100 caracteres</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone *</label>
                            <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>
                            <div class="form-text">Digite com ou sem formatação: (00) 00000-0000</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-lg me-1"></i>Salvar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/clientes.js"></script>
</body>
</html>

<?php
require_once '../config/database.php';
require_once '../models/Cliente.php';

$cliente = new Cliente($db);
$clienteData = null;
$error = null;
$success = null;

if (isset($_GET['id'])) {
    $clienteData = $cliente->getById($_GET['id']);
    if (!$clienteData) {
        header('Location: clientes.php?error=' . urlencode('Cliente não encontrado'));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'];
        $data = [
            'nome' => trim($_POST['nome']),
            'cpf_cnpj' => trim($_POST['cpf_cnpj']),
            'email' => trim($_POST['email']),
            'telefone' => trim($_POST['telefone'])
        ];
        
        if (empty($data['nome'])) {
            throw new Exception('O campo Nome é obrigatório e não pode estar vazio');
        }
        
        if (empty($data['cpf_cnpj'])) {
            throw new Exception('O campo CPF/CNPJ é obrigatório e não pode estar vazio');
        }
        
        if (empty($data['email'])) {
            throw new Exception('O campo E-mail é obrigatório e não pode estar vazio');
        }
        
        if (empty($data['telefone'])) {
            throw new Exception('O campo Telefone é obrigatório e não pode estar vazio');
        }

        if (strlen($data['nome']) < 2) {
            throw new Exception('O nome deve ter pelo menos 2 caracteres');
        }
        
        if (strlen($data['nome']) > 100) {
            throw new Exception('O nome não pode ter mais de 100 caracteres');
        }

        if (!$cliente->validateCpfCnpj($data['cpf_cnpj'])) {
            throw new Exception('CPF/CNPJ inválido. Verifique se os dígitos estão corretos');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('E-mail inválido. Use o formato: exemplo@dominio.com');
        }
        
        if (strlen($data['email']) > 100) {
            throw new Exception('O e-mail não pode ter mais de 100 caracteres');
        }

        $telefone_clean = preg_replace('/[^0-9]/', '', $data['telefone']);
        if (strlen($telefone_clean) < 10 || strlen($telefone_clean) > 11) {
            throw new Exception('Telefone inválido. Use o formato: (00) 00000-0000');
        }

        if ($cliente->exists($data['cpf_cnpj'], $id)) {
            throw new Exception('CPF/CNPJ já está cadastrado para outro cliente');
        }

        if ($cliente->emailExists($data['email'], $id)) {
            throw new Exception('E-mail já está cadastrado para outro cliente');
        }

        if ($cliente->update($id, $data)) {
            header('Location: clientes.php?success=' . urlencode('Cliente atualizado com sucesso! Todas as alterações foram salvas.'));
            exit;
        } else {
            throw new Exception('Falha inesperada ao atualizar o cliente. Tente novamente.');
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        $clienteData['nome'] = $_POST['nome'] ?? $clienteData['nome'];
        $clienteData['cpf_cnpj'] = $_POST['cpf_cnpj'] ?? $clienteData['cpf_cnpj'];
        $clienteData['email'] = $_POST['email'] ?? $clienteData['email'];
        $clienteData['telefone'] = $_POST['telefone'] ?? $clienteData['telefone'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="clientes.php">
                <img src="../assets/images/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                <div class="logo-fallback" style="display: none;">S</div>
                Sistema de Clientes
            </a>
        </div>
    </nav>

    <div class="container main-container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Editar Cliente
                        </h4>
                        <a href="clientes.php" class="btn btn-outline-light btn-back">
                            <i class="bi bi-arrow-left me-1"></i>Voltar
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <strong>Erro:</strong> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Sucesso:</strong> <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($clienteData): ?>
                            <form method="POST" novalidate>
                                <input type="hidden" name="id" value="<?php echo $clienteData['id']; ?>">
                                
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome completo *</label>
                                    <input type="text" 
                                           class="form-control <?php echo $error && strpos($error, 'nome') !== false ? 'is-invalid' : ''; ?>" 
                                           id="nome" 
                                           name="nome" 
                                           value="<?php echo htmlspecialchars($clienteData['nome']); ?>" 
                                           required
                                           maxlength="100"
                                           placeholder="Digite o nome completo">
                                    <div class="form-text">Mínimo 2 caracteres, máximo 100 caracteres</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="cpf_cnpj" class="form-label">CPF/CNPJ *</label>
                                    <input type="text" 
                                           class="form-control <?php echo $error && strpos($error, 'CPF') !== false ? 'is-invalid' : ''; ?>" 
                                           id="cpf_cnpj" 
                                           name="cpf_cnpj" 
                                           value="<?php echo htmlspecialchars($clienteData['cpf_cnpj']); ?>" 
                                           required
                                           placeholder="000.000.000-00 ou 00.000.000/0000-00">
                                    <div class="form-text">Digite apenas números ou use a formatação padrão</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail *</label>
                                    <input type="email" 
                                           class="form-control <?php echo $error && strpos($error, 'mail') !== false ? 'is-invalid' : ''; ?>" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo htmlspecialchars($clienteData['email']); ?>" 
                                           required
                                           maxlength="100"
                                           placeholder="exemplo@dominio.com">
                                    <div class="form-text">Máximo 100 caracteres</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone *</label>
                                    <input type="tel" 
                                           class="form-control <?php echo $error && strpos($error, 'elefone') !== false ? 'is-invalid' : ''; ?>" 
                                           id="telefone" 
                                           name="telefone" 
                                           value="<?php echo htmlspecialchars($clienteData['telefone']); ?>" 
                                           required
                                           placeholder="(00) 00000-0000">
                                    <div class="form-text">Digite com ou sem formatação: (00) 00000-0000</div>
                                </div>
                                
                                <div class="d-flex justify-content-between gap-3 mt-4">
                                    <a href="clientes.php" class="btn btn-secondary btn-lg">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Atualizar Cliente
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Cliente não encontrado ou ID inválido.
                            </div>
                            <div class="mt-3">
                                <a href="clientes.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Voltar para Lista de Clientes
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('cpf_cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                // CPF
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                // CNPJ
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
            }
            
            e.target.value = value;
        });

        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            
            e.target.value = value;
        });

        document.querySelectorAll('.form-control').forEach(function(input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html>

<?php
require_once '../config/database.php';
require_once '../models/Cliente.php';

$cliente = new Cliente($db);
$clienteData = null;

if (isset($_GET['id'])) {
    $clienteData = $cliente->getById($_GET['id']);
    if (!$clienteData) {
        header('Location: clientes.php?error=Cliente não encontrado');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_exclusao'])) {
    $id = $_POST['id'];
    
    if ($cliente->delete($id)) {
        header('Location: clientes.php?success=Cliente excluído com sucesso');
        exit;
    } else {
        $error = 'Erro ao excluir cliente';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Cliente</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
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

    <div class="container main-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-delete">
                    <div class="card-header-delete d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Excluir Cliente
                        </h4>
                        <a href="clientes.php" class="btn btn-outline-light btn-back">
                            <i class="bi bi-arrow-left me-1"></i>Voltar
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($clienteData): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>Atenção!</strong> Esta ação não pode ser desfeita.
                            </div>
                            
                            <p class="lead mb-3">Tem certeza que deseja excluir o seguinte cliente?</p>
                            
                            <div class="card client-preview">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-person-fill me-2"></i>
                                        <?php echo htmlspecialchars($clienteData['nome']); ?>
                                    </h5>
                                    <div class="client-details">
                                        <p class="mb-2">
                                            <i class="bi bi-card-text me-2"></i>
                                            <strong>CPF/CNPJ:</strong> <?php echo htmlspecialchars($clienteData['cpf_cnpj']); ?>
                                        </p>
                                        <p class="mb-2">
                                            <i class="bi bi-envelope me-2"></i>
                                            <strong>Email:</strong> <?php echo htmlspecialchars($clienteData['email']); ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="bi bi-telephone me-2"></i>
                                            <strong>Telefone:</strong> <?php echo htmlspecialchars($clienteData['telefone']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <form method="POST" class="mt-4">
                                <input type="hidden" name="id" value="<?php echo $clienteData['id']; ?>">
                                
                                <div class="d-flex justify-content-between gap-3">
                                    <a href="clientes.php" class="btn btn-secondary btn-lg flex-fill">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" name="confirmar_exclusao" class="btn btn-danger btn-lg flex-fill">
                                        <i class="bi bi-trash3 me-2"></i>Confirmar Exclusão
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert">
                                Cliente não encontrado.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

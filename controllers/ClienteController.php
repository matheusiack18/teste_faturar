<?php
require_once '../config/database.php';
require_once '../models/Cliente.php';

class ClienteController
{
    private $cliente;

    public function __construct()
    {
        global $db;
        $this->cliente = new Cliente($db);
    }

    public function handleRequest()
    {
        try {
            $action = $_POST['action'] ?? $_GET['action'] ?? '';

            switch ($action) {
                case 'list':
                    $this->listClientes();
                    break;
                case 'get':
                    $this->getCliente();
                    break;
                case 'create':
                    $this->createCliente();
                    break;
                case 'update':
                    $this->updateCliente();
                    break;
                case 'delete':
                    $this->deleteCliente();
                    break;
                default:
                    echo json_encode(['success' => false, 'message' => 'Ação inválida solicitada']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    private function listClientes()
    {
        try {
            $clientes = $this->cliente->getAll();
            echo json_encode(['success' => true, 'data' => $clientes, 'message' => 'Clientes carregados com sucesso']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao carregar lista de clientes: ' . $e->getMessage()]);
        }
    }

    private function getCliente()
    {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id || !is_numeric($id)) {
                echo json_encode(['success' => false, 'message' => 'ID do cliente inválido']);
                return;
            }
            
            $cliente = $this->cliente->getById($id);
            if ($cliente) {
                echo json_encode(['success' => true, 'data' => $cliente, 'message' => 'Cliente encontrado com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cliente não encontrado com o ID informado']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar cliente: ' . $e->getMessage()]);
        }
    }

    private function createCliente()
    {
        try {
            $data = [
                'nome' => trim($_POST['nome'] ?? ''),
                'cpf_cnpj' => trim($_POST['cpf_cnpj'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefone' => trim($_POST['telefone'] ?? '')
            ];

            $validation = $this->validateDataDetailed($data);
            if (!$validation['valid']) {
                echo json_encode(['success' => false, 'message' => $validation['message']]);
                return;
            }

            if ($this->cliente->exists($data['cpf_cnpj'])) {
                echo json_encode(['success' => false, 'message' => 'CPF/CNPJ já está cadastrado para outro cliente']);
                return;
            }

            if ($this->cliente->emailExists($data['email'])) {
                echo json_encode(['success' => false, 'message' => 'E-mail já está cadastrado para outro cliente']);
                return;
            }

            if ($this->cliente->create($data)) {
                echo json_encode(['success' => true, 'message' => 'Cliente cadastrado com sucesso! Todos os dados foram salvos corretamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Falha inesperada ao salvar o cliente. Tente novamente.']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function updateCliente()
    {
        try {
            $id = $_POST['id'] ?? 0;
            
            if (!$id || !is_numeric($id)) {
                echo json_encode(['success' => false, 'message' => 'ID do cliente inválido para atualização']);
                return;
            }

            if (!$this->cliente->getById($id)) {
                echo json_encode(['success' => false, 'message' => 'Cliente não encontrado para atualização']);
                return;
            }

            $data = [
                'nome' => trim($_POST['nome'] ?? ''),
                'cpf_cnpj' => trim($_POST['cpf_cnpj'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefone' => trim($_POST['telefone'] ?? '')
            ];

            $validation = $this->validateDataDetailed($data);
            if (!$validation['valid']) {
                echo json_encode(['success' => false, 'message' => $validation['message']]);
                return;
            }

            if ($this->cliente->exists($data['cpf_cnpj'], $id)) {
                echo json_encode(['success' => false, 'message' => 'CPF/CNPJ já está cadastrado para outro cliente']);
                return;
            }

            if ($this->cliente->emailExists($data['email'], $id)) {
                echo json_encode(['success' => false, 'message' => 'E-mail já está cadastrado para outro cliente']);
                return;
            }

            if ($this->cliente->update($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'Cliente atualizado com sucesso! Todas as alterações foram salvas.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Falha inesperada ao atualizar o cliente. Tente novamente.']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function deleteCliente()
    {
        try {
            $id = $_POST['id'] ?? 0;
            
            if (!$id || !is_numeric($id)) {
                echo json_encode(['success' => false, 'message' => 'ID do cliente inválido para exclusão']);
                return;
            }

            if ($this->cliente->delete($id)) {
                echo json_encode(['success' => true, 'message' => 'Cliente excluído com sucesso! Todos os dados foram removidos permanentemente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Falha inesperada ao excluir o cliente. Tente novamente.']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function validateDataDetailed($data)
    {
        if (empty($data['nome'])) {
            return ['valid' => false, 'message' => 'O campo Nome é obrigatório e não pode estar vazio'];
        }
        
        if (empty($data['cpf_cnpj'])) {
            return ['valid' => false, 'message' => 'O campo CPF/CNPJ é obrigatório e não pode estar vazio'];
        }
        
        if (empty($data['email'])) {
            return ['valid' => false, 'message' => 'O campo E-mail é obrigatório e não pode estar vazio'];
        }
        
        if (empty($data['telefone'])) {
            return ['valid' => false, 'message' => 'O campo Telefone é obrigatório e não pode estar vazio'];
        }

        if (!$this->cliente->validateNome($data['nome'])) {
            return ['valid' => false, 'message' => 'Nome inválido. Use apenas letras e espaços, sem números ou caracteres especiais'];
        }
        
        if (strlen($data['nome']) < 2) {
            return ['valid' => false, 'message' => 'O nome deve ter pelo menos 2 caracteres'];
        }
        
        if (strlen($data['nome']) > 100) {
            return ['valid' => false, 'message' => 'O nome não pode ter mais de 100 caracteres'];
        }

        $cpf_cnpj_clean = preg_replace('/[^0-9]/', '', $data['cpf_cnpj']);
        
        if (empty($cpf_cnpj_clean)) {
            return ['valid' => false, 'message' => 'CPF/CNPJ deve conter apenas números'];
        }
        
        if (!ctype_digit($cpf_cnpj_clean)) {
            return ['valid' => false, 'message' => 'CPF/CNPJ deve conter apenas números válidos'];
        }
        
        if (strlen($cpf_cnpj_clean) != 11 && strlen($cpf_cnpj_clean) != 14) {
            return ['valid' => false, 'message' => 'CPF deve ter 11 dígitos ou CNPJ deve ter 14 dígitos'];
        }
        
        if (!$this->cliente->validateCpfCnpj($data['cpf_cnpj'])) {
            $tipo = strlen($cpf_cnpj_clean) == 11 ? 'CPF' : 'CNPJ';
            return ['valid' => false, 'message' => $tipo . ' inválido. Verifique se os dígitos estão corretos'];
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'E-mail inválido. Use o formato: exemplo@dominio.com'];
        }
        
        if (strlen($data['email']) > 100) {
            return ['valid' => false, 'message' => 'O e-mail não pode ter mais de 100 caracteres'];
        }

        $telefone_clean = preg_replace('/[^0-9]/', '', $data['telefone']);
        
        if (empty($telefone_clean)) {
            return ['valid' => false, 'message' => 'Telefone deve conter apenas números'];
        }
        
        if (!ctype_digit($telefone_clean)) {
            return ['valid' => false, 'message' => 'Telefone deve conter apenas números válidos'];
        }
        
        if (!$this->cliente->validateTelefone($data['telefone'])) {
            return ['valid' => false, 'message' => 'Telefone inválido. Use um DDD válido e formato: (00) 00000-0000'];
        }

        return ['valid' => true, 'message' => 'Dados válidos'];
    }
}

$controller = new ClienteController();
$controller->handleRequest();

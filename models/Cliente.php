<?php
class Cliente {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function getAll() {
        $sql = "SELECT * FROM informacoes ORDER BY nome";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM informacoes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $this->validateAllFields($data);
        
        try {
            $sql = "INSERT INTO informacoes (nome, cpf_cnpj, email, telefone) VALUES (:nome, :cpf_cnpj, :email, :telefone)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nome' => $data['nome'],
                ':cpf_cnpj' => $data['cpf_cnpj'],
                ':email' => $data['email'],
                ':telefone' => $data['telefone']
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                if (strpos($e->getMessage(), 'cpf_cnpj') !== false) {
                    throw new Exception('CPF/CNPJ já está cadastrado no sistema');
                }
                if (strpos($e->getMessage(), 'email') !== false) {
                    throw new Exception('E-mail já está cadastrado no sistema');
                }
                throw new Exception('Dados duplicados encontrados');
            }
            throw new Exception('Erro interno do banco de dados: ' . $e->getMessage());
        }
    }
    
    public function update($id, $data) {
        $this->validateAllFields($data);
        
        try {
            $sql = "UPDATE informacoes SET nome = :nome, cpf_cnpj = :cpf_cnpj, email = :email, telefone = :telefone WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nome' => $data['nome'],
                ':cpf_cnpj' => $data['cpf_cnpj'],
                ':email' => $data['email'],
                ':telefone' => $data['telefone']
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                if (strpos($e->getMessage(), 'cpf_cnpj') !== false) {
                    throw new Exception('CPF/CNPJ já está cadastrado para outro cliente');
                }
                if (strpos($e->getMessage(), 'email') !== false) {
                    throw new Exception('E-mail já está cadastrado para outro cliente');
                }
                throw new Exception('Dados duplicados encontrados');
            }
            throw new Exception('Erro interno do banco de dados: ' . $e->getMessage());
        }
    }
    
    public function delete($id) {
        try {
            if (!$this->getById($id)) {
                throw new Exception('Cliente não encontrado para exclusão');
            }
            
            $sql = "DELETE FROM informacoes WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Nenhum cliente foi excluído. Verifique se o ID está correto');
            }
            
            return $result;
        } catch (PDOException $e) {
            throw new Exception('Erro ao excluir cliente: ' . $e->getMessage());
        }
    }
    
    public function exists($cpf_cnpj, $id = null) {
        try {
            $sql = "SELECT COUNT(*) FROM informacoes WHERE cpf_cnpj = :cpf_cnpj";
            if ($id) {
                $sql .= " AND id != :id";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cpf_cnpj', $cpf_cnpj);
            if ($id) {
                $stmt->bindParam(':id', $id);
            }
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception('Erro ao verificar duplicação de CPF/CNPJ: ' . $e->getMessage());
        }
    }
    
    public function emailExists($email, $id = null) {
        try {
            $sql = "SELECT COUNT(*) FROM informacoes WHERE email = :email";
            if ($id) {
                $sql .= " AND id != :id";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            if ($id) {
                $stmt->bindParam(':id', $id);
            }
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception('Erro ao verificar duplicação de e-mail: ' . $e->getMessage());
        }
    }
    
    private function validateAllFields($data) {
        if (!$this->validateNome($data['nome'])) throw new Exception('Nome inválido');
        if (!$this->validateCpfCnpj($data['cpf_cnpj'])) throw new Exception('CPF/CNPJ inválido');
        if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) throw new Exception('E-mail inválido');
        if (!$this->validateTelefone($data['telefone'])) throw new Exception('Telefone inválido');
    }
    
    public function validateNome($nome) {
        $nome = trim($nome);
        return strlen($nome) >= 2 && preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nome);
    }
    
    public function validateEmail($email) {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public function validateCpfCnpj($doc) {
        $n = preg_replace('/\D/', '', $doc);
        $l = strlen($n);
        return ($l == 11 || $l == 14) && ctype_digit($n);
    }
    
    public function validateTelefone($tel) {
        $n = preg_replace('/\D/', '', $tel);
        $l = strlen($n);
        return ($l >= 10 && $l <= 11) && ctype_digit($n);
    }
}
?>

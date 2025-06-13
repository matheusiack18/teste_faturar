# Sistema de Gerenciamento de Clientes

Sistema de gerenciamento de clientes com PHP e MySQL.

## Como executar?

### 1. Instalar XAMPP
- Baixe e instale o XAMPP em: https://www.apachefriends.org
- Inicie Apache e MySQL no painel de controle

### 2. Configurar o projeto
- Extraia os arquivos em: `d:\xampp\htdocs\teste_faturar\`
- Acesse: http://localhost/phpmyadmin

### 3. Criar banco de dados
No phpMyAdmin, execute este SQL:

```sql
CREATE DATABASE cliente;
USE cliente;

CREATE TABLE informacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf_cnpj VARCHAR(18) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4. Acessar a aplicação
Abra no navegador: **http://localhost/teste_faturar**

## Funcionalidades
- ✅ Listagem de clientes;
- ✅ Adicionar cliente;
- ✅ Editar cliente;
- ✅ Excluir cliente;

## Problemas comuns

**Erro de conexão:**
- Verifique se MySQL está rodando no XAMPP
- Confirme se o banco `cliente` foi criado
- Verifique se a tabela `informacoes` existe

**Página não carrega:**
- Verifique se Apache está rodando
- Acesse: http://localhost/teste_faturar/views/clientes.php

---
*Desenvolvido por Matheus iack com PHP + MySQL + Bootstrap*

*Utilizando o seguinte site gratuito para hospedagem:*
- https://dash.infinityfree.com/

## Link do sistema em produção: 
- https://matheusiackfaturar.infinityfreeapp.com/views/clientes.php
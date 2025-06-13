<?php
// Configuração de conexão com o banco (PRODUCAO)
$dbHost = 'sql300.infinityfree.com';
$dbName = 'if0_39192018_cliente';
$dbUser = 'if0_39192018';
$dbPass = 'V6gxstoQl7';
$port = '3306'; 

// Configuração de conexão com o banco (Teste)
// $dbHost = 'localhost';
// $dbName = 'cliente';
// $dbUser = 'root';
// $dbPass = '';
// $port = '3306';

try {
    $db = new PDO("mysql:host=$dbHost;port=$port;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

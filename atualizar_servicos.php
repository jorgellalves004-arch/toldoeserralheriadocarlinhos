<?php
// atualizar_servicos.php
//$dbHost = 'localhost';
//$dbUsername = 'root';
//$dbPassword = '';
//$dbName = 'serralheria';
//$port = 3306;
$dbHost = 'i943okdfa47xqzpy.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
$dbUsername = 'b4ckk7473jmyp5ae';
$dbPassword = 'rzo90wykdpyfioa0';
$dbName = 'hr26yrza1xe0we9t';
$port = 3306;
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
if ($conn->connect_errno) {
    die(json_encode(['error' => $conn->connect_error]));
}

// Busca a quantidade de serviços finalizados
$result = $conn->query("SELECT COUNT(*) AS total FROM trabalhos WHERE data_fim IS NOT NULL");
$row = $result->fetch_assoc();
$servicos_finalizados = $row['total'] ?? 0;

// Atualiza a tabela estatisticas
$conn->query("UPDATE estatisticas SET servicos_finalizados = $servicos_finalizados WHERE id = 1");

header('Content-Type: application/json');
echo json_encode(['servicos_finalizados' => $servicos_finalizados]);

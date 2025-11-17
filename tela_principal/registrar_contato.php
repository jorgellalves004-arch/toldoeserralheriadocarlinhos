<?php
// ==========================
// CONEXÃO
// ==========================
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'serralheria';
$port = 3306;

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
if ($conn->connect_errno) {
    die(json_encode(['error'=>$conn->connect_error]));
}

// Incrementa contatos apenas quando chamado via POST
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $conn->query("UPDATE estatisticas SET contatos = contatos + 1 WHERE id = 1");
}

// Retorna o valor atualizado
$result = $conn->query("SELECT contatos FROM estatisticas WHERE id = 1");
$data = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($data);

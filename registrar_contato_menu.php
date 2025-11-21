<?php
// registrar_contato_menu.php
header('Content-Type: application/json');

$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'serralheria';
$port = 3306;

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
if ($conn->connect_errno) {
    echo json_encode(['erro' => 'Erro na conexão']);
    exit;
}

$conn->query("UPDATE estatisticas SET contatos = contatos + 1 WHERE id = 1");
$result = $conn->query("SELECT contatos FROM estatisticas WHERE id = 1");
$row = $result ? $result->fetch_assoc() : ['contatos' => 0];

echo json_encode(['contatos' => (int)$row['contatos']]);
$conn->close();

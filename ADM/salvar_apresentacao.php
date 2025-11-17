<?php
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'serralheria';
$port = 3306;
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
if ($conn->connect_errno) die("Erro: " . $conn->connect_error);

$titulo = $_POST['titulo'] ?? '';
$conteudo = $_POST['conteudo'] ?? '';
$secao = $_POST['secao'] ?? '';
$autor = $_POST['autor'] ?? '';
$status = $_POST['status'] ?? 'ativo';

$stmt = $conn->prepare("INSERT INTO apresentacao_empresa (titulo, conteudo, secao, autor, status, data_publicacao) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("sssss", $titulo, $conteudo, $secao, $autor, $status);
$stmt->execute();

header("Location: adm.php");
exit;
?>

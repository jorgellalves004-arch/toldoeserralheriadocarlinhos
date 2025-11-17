<?php
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'serralheria';
$port = 3306;
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
if ($conn->connect_errno) die("Erro: " . $conn->connect_error);

$id = $_GET['id'] ?? null;
if ($id) {
    $conn->query("DELETE FROM comentarios_imagens WHERE id = $id");
}
header("Location: adm.php");
exit;
?>

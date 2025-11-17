<?php
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'serralheria';
$port = 3306;

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);

if ($conn->connect_errno) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>

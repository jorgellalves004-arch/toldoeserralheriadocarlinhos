<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'serralheria', 3306);
if ($conn->connect_errno) die("Erro: " . $conn->connect_error);

$servicos = intval($_POST['servicos_finalizados'] ?? 0);

// Atualiza a estatística
$stmt = $conn->prepare("UPDATE estatisticas SET servicos_finalizados = ? WHERE id = 1");
$stmt->bind_param("i", $servicos);

if ($stmt->execute()) {
    header("Location: adm.php?ok=1");
} else {
    header("Location: adm.php?erro=1");
}

$stmt->close();
$conn->close();
?>

<?php
header('Content-Type: application/json');

// ==========================
// CONFIGURAÇÃO DE CONEXÃO
// ==========================
$dbHost = 'i943okdfa47xqzpy.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
$dbUsername = 'b4ckk7473jmyp5ae';
$dbPassword = 'rzo90wykdpyfioa0';
$dbName = 'hr26yrza1xe0we9t';
$port = 3306;

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
if ($conn->connect_errno) {
  echo json_encode([]);
  exit;
}

// ==========================
// FILTRO DE CATEGORIA
// ==========================
$categoria = $_GET['categoria'] ?? '';

$sql = "SELECT nome_trabalho, imagem, data_inicio, data_fim 
        FROM trabalhos 
        WHERE categoria = ? 
        ORDER BY data_inicio DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $categoria);
$stmt->execute();
$result = $stmt->get_result();

$fotos = [];
while ($row = $result->fetch_assoc()) {
  // Converte para formato ISO (YYYY-MM-DD) para funcionar no filtro JS
  if (!empty($row['data_inicio'])) {
    $row['data_inicio'] = date('Y-m-d', strtotime($row['data_inicio']));
  }
  if (!empty($row['data_fim'])) {
    $row['data_fim'] = date('Y-m-d', strtotime($row['data_fim']));
  }
  $fotos[] = $row;
}

echo json_encode($fotos);
?>

<?php
include 'conexao.php';

$nome = $_POST['nome_trabalho'] ?? '';
$data_inicio = $_POST['data_inicio'] ?? '';
$data_fim = $_POST['data_fim'] ?? '';
$categoria = $_POST['categoria'] ?? '';

$query = "SELECT * FROM trabalhos WHERE categoria = ?";
$params = [$categoria];
$types = "s";

if (!empty($nome)) {
  $query .= " AND nome_trabalho LIKE ?";
  $params[] = "%$nome%";
  $types .= "s";
}

if (!empty($data_inicio)) {
  $query .= " AND data_inicio >= ?";
  $params[] = $data_inicio;
  $types .= "s";
}

if (!empty($data_fim)) {
  $query .= " AND data_fim <= ?";
  $params[] = $data_fim;
  $types .= "s";
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "
      <div class='col-6 col-md-3'>
        <div class='card shadow-sm'>
          <img src='uploads/{$row['imagem']}' class='card-img-top'>
          <div class='estrela'>
            <i class='bi bi-star-fill'></i> {$row['estrelas']}
          </div>
          <div class='card-body p-2'>
            <p class='small mb-1 fw-bold'>{$row['nome_trabalho']}</p>
            <p class='text-muted small mb-0'>
              <i class='bi bi-calendar'></i> {$row['data_inicio']} - {$row['data_fim']}
            </p>
          </div>
        </div>
      </div>
    ";
  }
} else {
  echo "<div class='text-center text-muted'>Nenhum trabalho encontrado.</div>";
}

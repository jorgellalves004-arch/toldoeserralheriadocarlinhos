<?php
// ==========================
// CONEXÃO
// ==========================
$dbHost = 'i943okdfa47xqzpy.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
$dbUsername = 'b4ckk7473jmyp5ae';
$dbPassword = 'rzo90wykdpyfioa0';
$dbName = 'hr26yrza1xe0we9t';
$port = 3306;
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
if ($conn->connect_errno) die("Erro: " . $conn->connect_error);

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido.");

$trabalho = $conn->query("SELECT * FROM trabalhos WHERE id_trabalho = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_trabalho'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $categoria = $_POST['categoria'];
    $estrelas = $_POST['estrelas'];
    $imagem = $trabalho['imagem'];

    if (!empty($_FILES['imagem']['name'])) {
        $dir = "uploads/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $nomeArquivo = time() . "_" . basename($_FILES['imagem']['name']);
        $caminho = $dir . $nomeArquivo;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho);
        $imagem = $caminho;
    }

    $stmt = $conn->prepare("UPDATE trabalhos SET nome_trabalho=?, data_inicio=?, data_fim=?, categoria=?, imagem=?, estrelas=? WHERE id_trabalho=?");
    $stmt->bind_param("ssssssi", $nome, $data_inicio, $data_fim, $categoria, $imagem, $estrelas, $id);
    $stmt->execute();

    header("Location: adm.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Editar Trabalho</title>
<link rel="stylesheet" href="styles.css">
<style>
body { background:#0b0e14; color:white; font-family:Arial; padding:20px; }
form { max-width:500px; margin:auto; background:rgba(255,255,255,0.08); padding:20px; border-radius:10px; }
input, select { width:100%; margin-bottom:10px; padding:8px; border:none; border-radius:6px; background:rgba(255,255,255,0.1); color:white; }
button { background:#4caf50; color:white; border:none; padding:10px; border-radius:6px; }
</style>
</head>
<body>
<h2>Editar Trabalho</h2>
<form method="POST" enctype="multipart/form-data">
  <input type="text" name="nome_trabalho" value="<?= htmlspecialchars($trabalho['nome_trabalho']) ?>" required>
  <input type="date" name="data_inicio" value="<?= $trabalho['data_inicio'] ?>" required>
  <input type="date" name="data_fim" value="<?= $trabalho['data_fim'] ?>">
  <input type="text" name="categoria" value="<?= htmlspecialchars($trabalho['categoria']) ?>">
  <input type="file" name="imagem">
  <button type="submit">Salvar Alterações</button>
</form>
</body>
</html>

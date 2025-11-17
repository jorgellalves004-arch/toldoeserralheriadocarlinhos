<?php
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'serralheria';
$port = 3306;
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
if ($conn->connect_errno) die("Erro: " . $conn->connect_error);

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido.");

$ap = $conn->query("SELECT * FROM apresentacao_empresa WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $secao = $_POST['secao'];
    $autor = $_POST['autor'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE apresentacao_empresa SET titulo=?, conteudo=?, secao=?, autor=?, status=? WHERE id=?");
    $stmt->bind_param("sssssi", $titulo, $conteudo, $secao, $autor, $status, $id);
    $stmt->execute();

    header("Location: adm.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Editar Apresentação</title>
<style>
body { background:#0b0e14; color:white; font-family:Arial; padding:20px; }
form { max-width:500px; margin:auto; background:rgba(255,255,255,0.08); padding:20px; border-radius:10px; }
input, textarea, select { width:100%; margin-bottom:10px; padding:8px; border:none; border-radius:6px; background:rgba(255,255,255,0.1); color:white; }
button { background:#4caf50; color:white; border:none; padding:10px; border-radius:6px; }
</style>
</head>
<body>
<h2>Editar Apresentação</h2>
<form method="POST">
  <input type="text" name="titulo" value="<?= htmlspecialchars($ap['titulo']) ?>" required>
  <textarea name="conteudo"><?= htmlspecialchars($ap['conteudo']) ?></textarea>
  <select name="secao">
    <?php foreach(['historia','missao','valores','equipe','servicos'] as $s): ?>
      <option value="<?= $s ?>" <?= $ap['secao']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
    <?php endforeach; ?>
  </select>
  <input type="text" name="autor" value="<?= htmlspecialchars($ap['autor']) ?>">
  <select name="status">
    <option value="ativo" <?= $ap['status']=='ativo'?'selected':'' ?>>Ativo</option>
    <option value="inativo" <?= $ap['status']=='inativo'?'selected':'' ?>>Inativo</option>
  </select>
  <button type="submit">Salvar Alterações</button>
</form>
</body>
</html>

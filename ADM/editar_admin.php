<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header('Location: login.php');
  exit;
}

$conn = new mysqli('localhost', 'root', '', 'serralheria');
if ($conn->connect_errno) die("Erro na conexão: " . $conn->connect_error);

$id = intval($_GET['id'] ?? 0);
$admin = null;
if ($id > 0) {
    $stmt = $conn->prepare("SELECT id, usuario FROM usuarios_admin WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $admin = $res->fetch_assoc();
}

if (!$admin) die("Administrador não encontrado.");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Editar Administrador</title>
<style>
body { background:#0b0e14; color:#fff; font-family:Arial; display:flex;align-items:center;justify-content:center;height:100vh;}
form { background:rgba(255,255,255,0.06); padding:20px; border-radius:8px; width:320px; }
input { width:100%; padding:8px; margin:8px 0; border-radius:6px; border:none; background:rgba(255,255,255,0.08); color:#fff; }
button { background:linear-gradient(45deg,#4caf50,#388e3c); border:none; padding:10px; border-radius:6px; color:#fff; cursor:pointer; }
</style>
</head>
<body>
<form method="POST" action="salvar_admin.php">
  <h2>Editar Administrador</h2>
  <input type="hidden" name="id" value="<?= $admin['id'] ?>">
  <input type="text" name="usuario" value="<?= htmlspecialchars($admin['usuario']) ?>" required>
  <input type="password" name="senha" placeholder="Nova senha (deixe em branco para manter)">
  <button type="submit">Salvar</button>
</form>
</body>
</html>

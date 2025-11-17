<?php
session_start();
$db = new mysqli('localhost', 'root', '', 'serralheria');
if ($db->connect_errno) die("Erro na conexão: " . $db->connect_error);

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $db->prepare("SELECT id, senha FROM usuarios_admin WHERE usuario = ?");
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($user = $res->fetch_assoc()) {
        $senhaArmazenada = $user['senha'];

        // Aceita senha em texto puro OU senha hash (password_verify)
        $ok = false;
        if ($senhaArmazenada === $senha) {
            $ok = true;
        } elseif (password_verify($senha, $senhaArmazenada)) {
            // caso alguma senha esteja como hash (compatibilidade)
            $ok = true;
        }

        if ($ok) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_nome'] = $usuario;
            header('Location: adm.php');
            exit;
        }
    }

    $erro = "Usuário ou senha incorretos!";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Login Administrativo</title>
<style>
body { background:#0b0e14; color:#fff; display:flex; align-items:center; justify-content:center; height:100vh; font-family:Arial; }
form { background:rgba(255,255,255,0.06); padding:25px; border-radius:8px; width:320px; text-align:center; }
input { width:90%; margin:8px 0; padding:8px; border-radius:6px; border:none; background:rgba(255,255,255,0.08); color:#fff; }
button { background:linear-gradient(45deg,#4caf50,#388e3c); border:none; color:#fff; padding:10px; border-radius:6px; cursor:pointer; }
.error { color:#ff6666; margin-bottom:8px; }
</style>
</head>
<body>
<form method="POST">
  <h2>Login Administrativo</h2>
  <?php if (!empty($erro)) echo "<div class='error'>{$erro}</div>"; ?>
  <input type="text" name="usuario" placeholder="Usuário" required>
  <input type="password" name="senha" placeholder="Senha" required>
  <button type="submit">Entrar</button>
</form>
</body>
</html>

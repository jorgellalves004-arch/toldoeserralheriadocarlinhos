<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header('Location: login.php');
  exit;
}

$conn = new mysqli('localhost', 'root', '', 'serralheria');
$id = intval($_GET['id']);

if ($id) {
  $conn->query("DELETE FROM usuarios_admin WHERE id = $id");
}

header('Location: adm.php');
exit;
?>

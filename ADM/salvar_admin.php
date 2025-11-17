<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header('Location: login.php');
  exit;
}

$conn = new mysqli('localhost', 'root', '', 'serralheria');
if ($conn->connect_errno) die("Erro na conexão: " . $conn->connect_error);

$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$usuario = trim($_POST['usuario'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if ($usuario === '') {
    echo "Preencha o usuário!";
    exit;
}

if ($id) {
    // Atualizar: se senha vazia, atualiza somente usuário
    if ($senha === '') {
        $stmt = $conn->prepare("UPDATE usuarios_admin SET usuario = ? WHERE id = ?");
        $stmt->bind_param('si', $usuario, $id);
    } else {
        // ATENÇÃO: salvando senha em texto puro conforme solicitado
        $stmt = $conn->prepare("UPDATE usuarios_admin SET usuario = ?, senha = ? WHERE id = ?");
        $stmt->bind_param('ssi', $usuario, $senha, $id);
    }
} else {
    // Inserir: exige senha
    if ($senha === '') {
        echo "Preencha a senha!";
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO usuarios_admin (usuario, senha) VALUES (?, ?)");
    $stmt->bind_param('ss', $usuario, $senha);
}

if ($stmt->execute()) {
    header('Location: adm.php');
    exit;
} else {
    echo "Erro ao salvar: " . $conn->error;
}
?>

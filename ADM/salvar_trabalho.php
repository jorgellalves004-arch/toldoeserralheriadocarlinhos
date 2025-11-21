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

// ==========================
// TRATAMENTO DOS DADOS
// ==========================
$nome = $_POST['nome_trabalho'] ?? '';
$data_inicio = $_POST['data_inicio'] ?? '';
$data_fim = $_POST['data_fim'] ?? null;
$categoria = $_POST['categoria'] ?? '';
$estrelas = $_POST['estrelas'] ?? '';
$imagem = null;

// upload da imagem
if (!empty($_FILES['imagem']['name'])) {
    $dirUpload = "../imagens/"; // caminho físico correto
    $dirBanco = "imagens/";     // caminho salvo no banco

    if (!is_dir($dirUpload)) mkdir($dirUpload, 0777, true);

    $nomeArquivo = time() . "_" . basename($_FILES['imagem']['name']);
    $caminhoFisico = $dirUpload . $nomeArquivo;
    $caminhoBanco = $dirBanco . $nomeArquivo;

    move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoFisico);
    $imagem = $caminhoBanco; // salva o caminho limpo no banco
}

// ==========================
// INSERÇÃO NO BANCO
// ==========================
$stmt = $conn->prepare("INSERT INTO trabalhos (nome_trabalho, data_inicio, data_fim, categoria, imagem, estrelas) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssi", $nome, $data_inicio, $data_fim, $categoria, $imagem, $estrelas);
$stmt->execute();

header("Location: adm.php");
exit;
?>

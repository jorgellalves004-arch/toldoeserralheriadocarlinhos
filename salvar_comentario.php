<?php 
$db = new mysqli('i943okdfa47xqzpy.cbetxkdyhwsb.us-east-1.rds.amazonaws.com', 'b4ckk7473jmyp5ae', 'rzo90wykdpyfioa0', 'hr26yrza1xe0we9t');

$imagem = $_POST['imagem'] ?? '';
$nome_usuario = $_POST['nome_usuario'] ?? '';
$comentario = $_POST['comentario'] ?? '';
$estrelas = intval($_POST['estrelas'] ?? 0);

$res = ['success'=>false];

// 🔧 Remove o prefixo "http://localhost/serralheria/" caso exista
$imagem = str_replace(['http://localhost/serralheria/', './'], '', $imagem);

if($imagem && $comentario && $nome_usuario && $estrelas > 0){
    $stmt = $db->prepare("
        INSERT INTO comentarios_imagens (imagem, nome_usuario, comentario, estrelas) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param('sssi', $imagem, $nome_usuario, $comentario, $estrelas);
    if($stmt->execute()) $res['success'] = true;
}

header('Content-Type: application/json');
echo json_encode($res);

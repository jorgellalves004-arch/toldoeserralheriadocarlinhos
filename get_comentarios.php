<?php
// get_comentarios.php
header('Content-Type: application/json');
$db = new mysqli('i943okdfa47xqzpy.cbetxkdyhwsb.us-east-1.rds.amazonaws.com', 'b4ckk7473jmyp5ae', 'rzo90wykdpyfioa0', 'hr26yrza1xe0we9t');

$imagem = $_GET['imagem'] ?? '';
$data = [];

if ($imagem) {
    // 🔧 Remove prefixo "http://localhost/serralheria/" ou "./"
    $imagem = str_replace(['http://localhost/serralheria/', './'], '', $imagem);

    $stmt = $db->prepare("
        SELECT nome_usuario, comentario, estrelas, 
               DATE_FORMAT(data,'%d/%m/%Y %H:%i') as data
        FROM comentarios_imagens 
        WHERE imagem = ?
        ORDER BY data DESC
    ");
    $stmt->bind_param('s', $imagem);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);

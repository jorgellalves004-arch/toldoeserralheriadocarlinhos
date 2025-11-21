<?php
// get_comentarios.php
header('Content-Type: application/json');
$db = new mysqli('localhost', 'root', '', 'serralheria');

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

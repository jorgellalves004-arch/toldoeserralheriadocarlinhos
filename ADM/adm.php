<?php

session_start();
if (!isset($_SESSION['admin_id'])) {
  header('Location: login.php');
  exit;
}


// ==========================
// CONFIGURAÇÃO DE CONEXÃO
// ==========================
$dbHost = 'i943okdfa47xqzpy.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
$dbUsername = 'b4ckk7473jmyp5ae';
$dbPassword = 'rzo90wykdpyfioa0';
$dbName = 'hr26yrza1xe0we9t';
$port = 3306;

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
if ($conn->connect_errno) die("Erro na conexão: " . $conn->connect_error);

// ==========================
// BUSCA DE DADOS GERAIS
// ==========================
$trabalhos = $conn->query("SELECT * FROM trabalhos ORDER BY id_trabalho DESC");
$comentarios = $conn->query("SELECT * FROM comentarios_imagens ORDER BY data DESC");
$estatisticas = $conn->query("SELECT * FROM estatisticas WHERE id = 1")->fetch_assoc();
$apresentacoes = $conn->query("SELECT * FROM apresentacao_empresa ORDER BY data_publicacao DESC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Painel Administrativo - Serralheria do Carlinho </title>
<link rel="stylesheet" href="styles.css?v=13">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<style>
body {
  background: #0b0e14;
  color: white;
  font-family: Arial, Helvetica, sans-serif;
  padding-top: 80px;
}
header {
  background: #1e293b;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 25px;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 10;
}
header h1 { font-size: 1.3rem; }

main {
  max-width: 1100px;
  margin: auto;
  padding: 20px;
}
section {
  background: rgba(255,255,255,0.08);
  border-radius: 12px;
  margin-bottom: 25px;
  padding: 20px;
  box-shadow: 0 0 10px rgba(255,255,255,0.05);
  width: 1200px;
}
section h2 {
  margin-top: 0;
  border-bottom: 1px solid rgba(255,255,255,0.2);
  padding-bottom: 8px;
  color: #ffcc66;
}

/* Adiciona rolagem interna apenas à seção de comentários */
section#comentarios {
  max-height: 450px; /* Altura máxima visível */
  overflow-y: auto;  /* Habilita a rolagem vertical */
  scroll-behavior: smooth;
}

/* Deixa o scroll mais elegante */
section#comentarios::-webkit-scrollbar {
  width: 8px;
}
section#comentarios::-webkit-scrollbar-thumb {
  background-color: rgba(255,255,255,0.3);
  border-radius: 10px;
}
section#comentarios::-webkit-scrollbar-thumb:hover {
  background-color: rgba(255,255,255,0.5);
}


table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}
table th, table td {
  padding: 10px;
  border-bottom: 1px solid rgba(255,255,255,0.2);
  text-align: left;
}
table th {
  color: #ffcc66;
  font-weight: bold;
}
.btn {
  padding: 6px 12px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
}
.btn-add { background: linear-gradient(45deg,#4caf50,#388e3c); color: #fff; }
.btn-edit { background: linear-gradient(45deg,#ffb84d,#ff9933); color: #000; margin-right: 100px; }
.btn-del { background: linear-gradient(45deg,#f44336,#c62828); color: #fff; }
form input, form select, form textarea {
  width: 100%;
  margin-bottom: 8px;
  padding: 8px;
  border-radius: 6px;
  border: none;
  background: rgba(255,255,255,0.15);
  color: white;
}
form textarea { min-height: 80px; }
footer {
  text-align: center;
  color: #ccc;
  margin-top: 40px;
  padding: 15px;
  border-top: 1px solid rgba(255,255,255,0.2);
}

</style>
</head>
<body>

<header>
  <h1>Painel Administrativo</h1><h1 id="txttitulo">TOLDO E SERRALHERIA DO CARLINHO</h1><a href="logout.php" class="btn btn-del">Sair</a>

  <a href="/serralheria/index.php" class="btn btn-edit">⮜ Voltar ao site</a>
</header>

<main>
  <?php if (isset($_GET['ok'])): ?>
  <div style="background:green;padding:10px;border-radius:8px;margin-bottom:15px;">
    ✔ Serviços finalizados atualizados com sucesso!
  </div>
<?php endif; ?>

  <!-- ========================== -->
  <!-- ESTATÍSTICAS -->
  <!-- ========================== -->
  <section>
    <h2>Estatísticas do Site</h2>
    <p><b>Acessos:</b> <?= $estatisticas['acessos'] ?? 0 ?></p>
    <p><b>Contatos:</b> <?= $estatisticas['contatos'] ?? 0 ?></p>
    <p><b>Serviços Finalizados:</b> <?= $estatisticas['servicos_finalizados'] ?? 0 ?></p>
  </section>

  <!-- ========================== -->
  <!-- TRABALHOS -->
  <!-- ========================== -->
  <section style="padding:16px;">
  <h2>Cadastrar Trabalhos</h2>

  <!-- Formulário -->
  <form action="salvar_trabalho.php" method="POST" enctype="multipart/form-data" style="margin-bottom:20px;">
    <input type="hidden" name="id_trabalho" value="">
    <input type="text" name="nome_trabalho" placeholder="Nome do trabalho" required>
    <input type="date" name="data_inicio" required>
    <input type="date" name="data_fim">
    <input type="text" name="categoria" placeholder="Categoria">
    <input type="file" name="imagem" accept="image/*">
    <button class="btn btn-add" type="submit">Adicionar Trabalho</button>
  </form>

  <h2>Excluir/Editar Trabalhos</h2>

  <?php
  // Função para exibir estrelas visuais
  function gerarEstrelasHTMLSimples($media) {
      $html = '';
      for ($i = 1; $i <= 5; $i++) {
          if ($media >= $i) {
              $html .= '<i class="bi bi-star-fill" style="color:gold; margin-right:2px;"></i>';
          } else {
              $html .= '<i class="bi bi-star" style="color:#777; margin-right:2px;"></i>';
          }
      }
      return $html;
  }

  // Busca todas as categorias de trabalhos
  $categoriasQuery = $conn->query("SELECT DISTINCT categoria FROM trabalhos ORDER BY categoria ASC");

  if ($categoriasQuery && $categoriasQuery->num_rows > 0):
      while ($catRow = $categoriasQuery->fetch_assoc()):
          $categoria = $catRow['categoria'] ?: 'Sem categoria';

          // Calcula a média dos COMENTÁRIOS feitos nas imagens dessa categoria
          $mediaQuery = $conn->prepare("
              SELECT AVG(c.estrelas) AS media, COUNT(*) AS total
              FROM comentarios_imagens c
              INNER JOIN trabalhos t ON c.imagem = t.imagem
              WHERE t.categoria = ?
          ");
          $mediaQuery->bind_param('s', $categoria);
          $mediaQuery->execute();
          $mediaRes = $mediaQuery->get_result()->fetch_assoc();

          $media = round($mediaRes['media'] ?? 0, 1);
          $total = $mediaRes['total'] ?? 0;
  ?>

      <div style="margin-top:25px;">
        <h3 style="margin-bottom:8px; color:#ffcc66;"><?= htmlspecialchars($categoria) ?></h3>

        <?php if ($total > 0): ?>
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
            <?= gerarEstrelasHTMLSimples($media) ?>
            <span><strong><?= $media ?></strong> de 5 (<?= $total ?> avaliações)</span>
          </div>
        <?php else: ?>
          <p style="color:#aaa; margin-bottom:10px;">Nenhuma avaliação ainda nesta categoria.</p>
        <?php endif; ?>

        <div style="overflow-x:auto; border:1px solid rgba(255,255,255,0.1); border-radius:8px;">
          <table style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="background:rgba(255,255,255,0.1);">
                <th style="padding:8px; text-align:left;">ID</th>
                <th style="padding:8px; text-align:left;">Nome</th>
                <th style="padding:8px; text-align:left;">Imagem</th>
                <th style="padding:8px; text-align:left;">Datas</th>
      
                <th id="acoes" style="padding:8px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Exibe os trabalhos dessa categoria
              $trabQuery = $conn->prepare("
                  SELECT id_trabalho, nome_trabalho, imagem, data_inicio, data_fim, estrelas
                  FROM trabalhos
                  WHERE categoria = ?
                  ORDER BY data_inicio DESC
              ");
              $trabQuery->bind_param('s', $categoria);
              $trabQuery->execute();
              $trabalhosRes = $trabQuery->get_result();

              if ($trabalhosRes->num_rows > 0):
                  while ($t = $trabalhosRes->fetch_assoc()):
              ?>
                <tr style="border-bottom:1px solid rgba(255,255,255,0.1);">
                  <td style="padding:8px;"><?= $t['id_trabalho'] ?></td>
                  <td style="padding:8px;"><?= htmlspecialchars($t['nome_trabalho']) ?></td>
                  <td style="padding:8px;"><img src="../<?= htmlspecialchars($t['imagem']) ?>" width="80" style="border-radius:6px;"></td>
                  <td style="padding:8px;"><?= $t['data_inicio'] ?> - <?= $t['data_fim'] ?: '—' ?></td>
                  
                  <td style="padding:8px;">
                    <a class="btn btn-edit" href="editar_trabalho.php?id=<?= $t['id_trabalho'] ?>">Editar</a>
                    <a class="btn btn-del" href="excluir_trabalho.php?id=<?= $t['id_trabalho'] ?>" onclick="return confirm('Excluir este trabalho?')">Excluir</a>
                  </td>
                </tr>
              <?php endwhile; else: ?>
                <tr><td colspan="6" style="padding:8px; color:#aaa;">Nenhum trabalho nesta categoria.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
  <?php endwhile; else: ?>
      <p>Nenhuma categoria de trabalho cadastrada.</p>
  <?php endif; ?>
</section>


  <!-- ========================== -->
  <!-- COMENTÁRIOS -->
  <!-- ========================== -->


  <section id="comentarios">
       <?php
// Calcula a média de estrelas e o total de avaliações
$mediaQuery = $conn->query("SELECT AVG(estrelas) AS media, COUNT(*) AS total FROM comentarios_imagens");
$mediaData = $mediaQuery->fetch_assoc();
$media = round($mediaData['media'] ?? 0, 1);
$totalAvaliacoes = $mediaData['total'] ?? 0;
?>
  <h2>Comentários e Avaliações</h2>
  <?php
function gerarEstrelasHTML($media) {
  $html = '';
  for ($i = 1; $i <= 5; $i++) {
    if ($media >= $i) {
      $html .= '<i class="bi bi-star-fill" style="color:gold;"></i>';
    } elseif ($media >= $i - 0.5) {
      $html .= '<i class="bi bi-star-half" style="color:gold;"></i>';
    } else {
      $html .= '<i class="bi bi-star" style="color:#777;"></i>';
    }
  }
  return $html;
}
?>
<p style="font-size:1.1rem; margin-bottom:10px;">
  <?= gerarEstrelasHTML($media) ?>
  <b><?= $media ?></b> de 5 (<?= $totalAvaliacoes ?> avaliações)
</p>

  ⭐ <b>Média das Avaliações:</b>
  <?= $totalAvaliacoes > 0 ? "$media de 5 ($totalAvaliacoes avaliações)" : "Nenhuma avaliação ainda." ?>
</p>

  <table>
    <tr><th>Imagem</th><th>Usuário</th><th>Comentário</th><th>Estrelas</th><th>Data</th><th>Ações</th></tr>
    <?php while($c = $comentarios->fetch_assoc()): ?>
      <tr>
        <td><img src="../<?= htmlspecialchars($c['imagem']) ?>" width="80"></td>
        <td><?= htmlspecialchars($c['nome_usuario']) ?></td>
        <td><?= htmlspecialchars($c['comentario']) ?></td>
        <td><?= $c['estrelas'] ?>⭐</td>
        <td><?= $c['data'] ?></td>
        <td><a class="btn btn-del" href="excluir_comentario.php?id=<?= $c['id'] ?>" onclick="return confirm('Excluir este comentário?')">Excluir</a></td>
      </tr>
    <?php endwhile; ?>
  </table>
</section>


  <!-- ========================== -->
  <!-- APRESENTAÇÃO DA EMPRESA -->
  <!-- ========================== -->
  <section>
    <h2>Apresentação da Empresa</h2>
    <form action="salvar_apresentacao.php" method="POST">
      <input type="hidden" name="id" value="">
      <input type="text" name="titulo" placeholder="Título" required>
      <textarea name="conteudo" placeholder="Conteúdo..." required></textarea>
      <select name="secao">
        <option value="historia">História</option>
        <option value="missao">Missão</option>
        <option value="valores">Valores</option>
        <option value="equipe">Equipe</option>
        <option value="servicos">Serviços</option>
      </select>
      <input type="text" name="autor" placeholder="Autor">
      <select name="status">
        <option value="ativo">Ativo</option>
        <option value="inativo">Inativo</option>
      </select>
      <button class="btn btn-add" type="submit">Publicar</button>
    </form>

    <table>
      <tr><th>Título</th><th>Seção</th><th>Autor</th><th>Status</th><th>Data</th><th>Ações</th></tr>
      <?php while($a = $apresentacoes->fetch_assoc()): ?>
        <tr>
          <h2>Excluir/editar</h2>
          <td><?= htmlspecialchars($a['titulo']) ?></td>
          <td><?= htmlspecialchars($a['secao']) ?></td>
          <td><?= htmlspecialchars($a['autor']) ?></td>
          <td><?= htmlspecialchars($a['status']) ?></td>
          <td><?= $a['data_publicacao'] ?></td>
          <td>
            <a class="btn btn-edit" href="editar_apresentacao.php?id=<?= $a['id'] ?>">Editar</a>
            <a class="btn btn-del" href="excluir_apresentacao.php?id=<?= $a['id'] ?>" onclick="return confirm('Excluir esta seção?')">Excluir</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
      <!-- ========================== -->
  <!-- GERENCIAR ADMINISTRADORES -->
  <!-- ========================== -->
  <section>
    <h2>Gerenciar Administradores</h2>

    <!-- Formulário de cadastro -->
    <form action="salvar_admin.php" method="POST" style="margin-bottom:20px;">
      <input type="hidden" name="id" value="">
      <input type="text" name="usuario" placeholder="Nome de usuário" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <button class="btn btn-add" type="submit">Cadastrar Admin</button>
    </form>

    <section>
  <h2>Atualizar Serviços Finalizados</h2>

  <form action="atualizar_servicos.php" method="POST">
    <label for="servicos_finalizados"><b>Serviços Finalizados:</b></label>
    <input 
      type="number" 
      name="servicos_finalizados" 
      id="servicos_finalizados" 
      value="<?= $estatisticas['servicos_finalizados'] ?>" 
      required
      min="0">

    <button class="btn btn-add" type="submit">Salvar</button>
  </form>
</section>

    <!-- Lista de administradores -->
    <h3 style="color:#ffcc66; margin-top:10px;">Administradores Cadastrados</h3>
    <table>
      <tr><th>ID</th><th>Usuário</th><th>Ações</th></tr>
      <?php
      $admins = $conn->query("SELECT * FROM usuarios_admin ORDER BY id DESC");
      if ($admins->num_rows > 0):
          while($ad = $admins->fetch_assoc()):
      ?>
        <tr>
          <td><?= $ad['id'] ?></td>
          <td><?= htmlspecialchars($ad['usuario']) ?></td>
          <td>
            <a class="btn btn-edit" href="editar_admin.php?id=<?= $ad['id'] ?>">Editar</a>
            <a class="btn btn-del" href="excluir_admin.php?id=<?= $ad['id'] ?>" onclick="return confirm('Deseja excluir este administrador?')">Excluir</a>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="3" style="color:#aaa;">Nenhum administrador cadastrado.</td></tr>
      <?php endif; ?>
    </table>
  </section>

  </section>
</main>

<footer>
  &copy; <?= date('Y') ?> Serralheria do Carlinho | Painel Administrativo
</footer>

</body>
</html>

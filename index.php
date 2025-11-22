<?php
// index.php (completo e atualizado)

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
// MÉDIA DE ESTRELAS DE TODAS AS IMAGENS
// ==========================
$result_avaliacoes = $conn->query("
    SELECT AVG(estrelas) AS media_estrelas 
    FROM comentarios_imagens
");
$media_estrelas = 0;
if($result_avaliacoes && $row = $result_avaliacoes->fetch_assoc()){
    $media_estrelas = round((float)$row['media_estrelas'], 1); // arredonda 1 casa
}

// ==========================
// GARANTE EXISTÊNCIA DO REGISTRO DE ESTATÍSTICAS
// ==========================
$conn->query("
    INSERT INTO estatisticas (id, acessos, contatos, servicos_finalizados)
    SELECT 1, 0, 0, 0 FROM DUAL
    WHERE NOT EXISTS (SELECT 1 FROM estatisticas WHERE id = 1)
");

// ==========================
// CONTADOR DE ACESSOS
// ==========================
$conn->query("UPDATE estatisticas SET acessos = acessos + 1 WHERE id = 1");

// ==========================
// DADOS DA EMPRESA
// ==========================
$empresa = $conn->query("SELECT * FROM empresa LIMIT 1")->fetch_assoc();

// ==========================
// CONTADORES
// ==========================
$result_servicos = $conn->query("SELECT COUNT(*) AS total_finalizados FROM trabalhos WHERE data_fim IS NOT NULL");
$servicos_count = ($result_servicos && $row = $result_servicos->fetch_assoc()) ? (int)$row['total_finalizados'] : 0;

$estatisticas_row = $conn->query("SELECT acessos, contatos, servicos_finalizados FROM estatisticas WHERE id = 1");
$estatisticas = ($estatisticas_row) ? $estatisticas_row->fetch_assoc() : ['acessos'=>0,'contatos'=>0,'servicos_finalizados'=>0];

// ==========================
// CATEGORIAS DE TRABALHOS
// ==========================
$categorias_result = $conn->query("SELECT DISTINCT categoria FROM trabalhos ORDER BY categoria");

// função auxiliar para construir caminho da imagem com segurança
function caminho_imagem($raw) {
    $raw = trim((string)$raw);
    if ($raw === '') return '';
    // já é URL absoluta?
    if (preg_match('#^https?://#i', $raw)) return $raw;
    // já começa com slash -> caminho absoluto
    if (strpos($raw, '/') === 0) return $raw;
    // já começa com "imagens/" provavelmente ok
    if (strpos($raw, 'imagens/') === 0) return $raw;
    // caso contrário prefixa "imagens/"
    return 'imagens/' . $raw;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Toldo e Serralheria do Carlinho</title>
<link rel="stylesheet" href="styles.css?v=13">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
/* === MODAIS MODERNOS === */

/* Fundo escurecido com desfoque */
.modal-overlay { 
  position: fixed; inset: 0;
  background: rgba(10, 10, 10, 0.8);
  backdrop-filter: blur(6px);
  display: none;
  align-items: center; justify-content: center;
  z-index: 9999; padding: 25px;
  animation: fadeIn .25s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.96); }
  to { opacity: 1; transform: scale(1); }
}

.modal-content {margin-top: -50px;
  width: 95%; max-width: 1000px; max-height: 90vh;

  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 16px;
  padding: 25px 35px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 0 25px rgba(0,0,0,0.3);
  color: #fff;
  animation: slideUp .35s ease;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}

.modal-close {
  position: absolute;
  right: 18px; top: 14px;
  font-size: 26px;
  border: none; background: none;
  color: #fff; cursor: pointer;
  transition: transform .2s, color .2s;
}
.modal-close:hover { transform: scale(1.15); color: #ffcc66; }

/* === GALERIA DE FOTOS === */
.modal-gallery {
  display: flex; flex-wrap: wrap;
  gap: 16px; justify-content: center;
  margin-top: 15px;
}

.modal-gallery img,
.carousel-item img,
.modal-thumb {
  max-width: 260px; height: auto;
  border-radius: 12px;
  object-fit: cover;
  transition: transform .25s ease, box-shadow .25s;
  cursor: pointer;
}
.modal-gallery img:hover,
.carousel-item img:hover,
.modal-thumb:hover {
  transform: scale(1.05);
  box-shadow: 0 0 20px rgba(255,255,255,0.2);
}

/* === MODAL DE COMENTÁRIOS === */
.modal-body {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  align-items: flex-start;
  justify-content: space-between;
}

/* --- Imagem fixa --- */
.modal-img-container {
  flex: 0 0 100%;
  display: flex; 
  justify-content: center; 
  align-items: center;
  min-height: 180px;
  margin-bottom: 10px;
}

.modal-img-container img {
  width: 320px;
  height: 220px;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 0 18px rgba(0,0,0,0.4);
}

/* --- Container geral (lado a lado) --- */
.modal-comments-wrapper {
  display: flex;
  justify-content: space-between;
  width: 100%;
  gap: 20px;
}

/* --- Comentários existentes --- */
.modal-comments-list {
  flex: 1 1 55%;
  background: rgba(255,255,255,0.1);
  border-radius: 14px;
  padding: 18px;
  color: #fff;
  overflow-y: auto;
  max-height: 300px;
}

.modal-comments-list h3 {
  text-align: center;
  margin-bottom: 10px;
  font-size: 1.3rem;
  border-bottom: 1px solid rgba(255,255,255,0.2);
  padding-bottom: 8px;
}

#comentariosList {
  overflow-y: auto;
  padding-right: 6px;
  margin-bottom: 10px;
  max-height: 250px;
}

#comentariosList div {
  background: rgba(255,255,255,0.08);
  border-radius: 10px;
  padding: 10px;
  margin-bottom: 8px;
  transition: background .25s;
}
#comentariosList div:hover {
  background: rgba(255,255,255,0.15);
}
#comentariosList strong {
  color: #ffcc66;
}

/* --- Formulário de novo comentário --- */
.modal-comment-form {
  flex: 1 1 40%;
  background: rgba(255,255,255,0.1);
  border-radius: 14px;
  padding: 18px;
  display: flex;
  flex-direction: column;
  color: #fff;
  max-height: 300px;
}

.modal-comment-form h3 {
  text-align: center;
  margin-bottom: 10px;
  font-size: 1.3rem;
  border-bottom: 1px solid rgba(255,255,255,0.2);
  padding-bottom: 8px;
}

#nomeUsuario, #novoComentario {
  width: 90%;
  border: none;
  border-radius: 8px;
  padding: 10px;
  margin-bottom: 8px;
  background: rgba(255,255,255,0.15);
  color: #fff;
  outline: none;
  font-size: 0.95rem;
}
#nomeUsuario::placeholder, #novoComentario::placeholder { color: #ccc; }

#btnEnviarComentario {
  align-self: flex-end;
  padding: 10px 20px;
  background: linear-gradient(45deg, #ffb84d, #ff9933);
  border: none; border-radius: 8px;
  cursor: pointer;
  color: #000; font-weight: bold;
  transition: transform .2s, box-shadow .2s;
}
#btnEnviarComentario:hover {
  transform: scale(1.05);
  box-shadow: 0 0 15px rgba(255,180,80,0.4);
}

/* --- Scroll estilizado --- */
.modal-comments-list::-webkit-scrollbar,
#comentariosList::-webkit-scrollbar {
  width: 8px;
}
.modal-comments-list::-webkit-scrollbar-thumb,
#comentariosList::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.2);
  border-radius: 6px;
}

/* --- Responsividade --- */
@media(max-width: 768px) {
  .modal-comments-wrapper {
    flex-direction: column;
  }
  .modal-comments-list,
  .modal-comment-form {
    width: 100%;
  }
}

.star-rating {
  display: flex;
  justify-content: center;
  gap: 5px;
  margin-top: 10px;
}

.star-rating i {
  font-size: 24px;
  color: #ccc;
  cursor: pointer;
  transition: color 0.2s;
}

.star-rating i.selected,
.star-rating i:hover,
.star-rating i:hover ~ i {
  color: gold;
}

.comentario-item {
  background: #f8f8f8;
  padding: 10px;
  margin-bottom: 8px;
  border-radius: 8px;
}

.comentario-cabecalho {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 4px;
}

.comentario-cabecalho i {
  font-size: 18px;
}

</style>

</head>
<body>

<header>
    <div><h1 id="txttitulo">TOLDO E SERRALHERIA DO CARLINHO</h1></div>
    <div class="buscarSer">
        <input type="text" id="buscarServico" placeholder="Buscar categoria e pressione Enter...">
    </div>
    <div class="box" id="menuBtn">
    <img src="https://cdn-icons-png.flaticon.com/512/56/56763.png" alt="Menu" />
  </div>

  <!-- Opções que aparecem ao clicar -->
  <div class="menuOpcoes" id="menuOpcoes">
    <a href="historia.php">Nossa Historia</a>
    <a id="btnFalarConosco" href="https://wa.me/553299134284?text=Olá,%20tenho%20interesse%20em%20seus%20serviços!">Falar Conosco</a>

  </div>
    <div class="header-right">
            <img class="imglogo" src="imagens/logo.jpg" alt="Logo">
        </div>
</header>
 
<!-- Botão WhatsApp -->
<a href="https://wa.me/553299134284?text=Olá,%20tenho%20interesse%20em%20seus%20serviços!" class="whatsapp-float" target="_blank" id="whatsappBtn">
    <i class="bi bi-telephone-fill"></i>
    <span class="whatsapp-text">Falar no WhatsApp</span>
</a>

<!-- Trabalhos por Categoria -->
<div class="titulot"><h2 style="text-align:center; margin-top:110px;">Nossos Trabalhos</h2></div>
<div class="categorias-wrapper" id="categoriasWrapper">
<?php
if ($categorias_result && $categorias_result->num_rows > 0) {
    while ($categoria_row = $categorias_result->fetch_assoc()) {
        $categoria = $categoria_row['categoria'];
        echo "<div class='categoria-bloco' data-category=\"" . htmlspecialchars($categoria, ENT_QUOTES) . "\">";
        echo "<h3 class='category-title'>" . htmlspecialchars($categoria) . "</h3>";
        echo "<div class='carousel'><div class='carousel-track'>";
        $trabalhos_result = $conn->query("SELECT * FROM trabalhos WHERE categoria = '" . $conn->real_escape_string($categoria) . "' ORDER BY data_inicio DESC");
        if ($trabalhos_result && $trabalhos_result->num_rows > 0) {
            while ($trabalho = $trabalhos_result->fetch_assoc()) {
                // monta caminho da imagem com segurança
                $img_raw = $trabalho['imagem'] ?? '';
                $img = caminho_imagem($img_raw);
                $nome = htmlspecialchars($trabalho['nome_trabalho']);
                $data_inicio = !empty($trabalho['data_inicio']) ? date('d/m/Y', strtotime($trabalho['data_inicio'])) : '';
                $data_fim = !empty($trabalho['data_fim']) ? date('d/m/Y', strtotime($trabalho['data_fim'])) : 'Em andamento';
                echo "<div class='carousel-item'>
                        <img src=\"{$img}\" alt=\"{$nome}\" data-nome=\"{$nome}\" loading=\"lazy\">
                        <div class='carousel-item-info'>
                            <div class='carousel-item-title'>{$nome}</div>
                            <div class='carousel-item-date'>Início: {$data_inicio} | Fim: {$data_fim}</div>
                        </div>
                      </div>";
            }
        } else {
            echo "<div style='padding:18px;'>Nenhum trabalho nesta categoria.</div>";
        }
        echo "</div>";
        echo "<div class='carousel-buttons'>
                <button class='carousel-btn prev-btn' aria-label='Anterior'><i class='bi bi-chevron-left'></i></button>
                <button class='carousel-btn next-btn' aria-label='Próximo'><i class='bi bi-chevron-right'></i></button>
              </div>";
        echo "</div></div>";
    }
} else {
    echo "<p style='padding:20px'>Nenhuma categoria encontrada.</p>";
}
?>
</div>

<!-- Estatísticas -->
<div class="quadroAva">
    <div class="quad-item"><i class="bi bi-eye"></i><div class="label">Acessos ao site</div><div class="value" id="acessos"><?= (int)$estatisticas['acessos'] ?></div></div>
    <div class="quad-item"><i class="bi bi-hammer"></i><div class="label">Serviços finalizados</div><div class="value" id="servicos"><?= (int)$estatisticas['servicos_finalizados'] ?></div></div>
    <div class="quad-item">
    <i class="bi bi-telephone"></i>
    <div class="label">Entraram em contato</div>
    <div class="value" id="contatos"><?= (int)$estatisticas['contatos'] ?></div>
</div>
<div class="quad-item">
    <i class="bi bi-star-fill"></i>
    <div class="label">Média de avaliações</div>
    <div class="value" id="mediaEstrelas"><?= $media_estrelas ?> ⭐</div>
</div>

<!-- ===== MODAL DE COMENTÁRIOS (visual + funcional) ===== -->
<div class="modal-overlay" id="modalComentarioOverlay" aria-hidden="true">
  <div class="modal-content" role="dialog" aria-modal="true">
    <button class="modal-close" id="modalComentarioClose" aria-label="Fechar">&times;</button>
    <div class="modal-body">
      <div class="modal-img-container" style="flex-direction: column;">
        <!-- placeholder vazio para evitar 404; preenchido dinamicamente -->
        <img id="imagemProdutoComentario" src="" alt="Produto" />
        <!-- Campo de avaliação -->
        <div class="star-rating" id="starRating">
          <i class="bi bi-star" data-value="1"></i>
          <i class="bi bi-star" data-value="2"></i>
          <i class="bi bi-star" data-value="3"></i>
          <i class="bi bi-star" data-value="4"></i>
          <i class="bi bi-star" data-value="5"></i>
        </div>
      </div>

      <div class="modal-comments-wrapper" id="cmw1">
        <div class="modal-comments-list">
          <h3>Comentários</h3>
          <div id="comentariosList"></div>
        </div>

        <div class="modal-comment-form">
          <h3>Deixe seu comentário</h3>
          <input type="text" id="nomeUsuario" placeholder="Seu nome">
          <textarea id="novoComentario" placeholder="Escreva seu comentário..."></textarea>
          <button id="btnEnviarComentario">Enviar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL DE CATEGORIA (galeria) -->
<div id="categoriaModal" class="modal-overlay" aria-hidden="true">
  <div class="modal-content" role="dialog" aria-modal="true">
    <button class="modal-close" aria-label="Fechar" id="categoriaModalClose">&times;</button>
    <h2 id="modalTitulo" style="text-align:center; font-size:1.6rem; margin-bottom:15px; color:#ffcc66;"></h2>

    <div class="modal-body">
      <!-- === FILTRO DE SERVIÇOS === -->
      <div id="filtroCategoria" style="display:flex; flex-wrap:wrap; gap:10px; justify-content:center; margin-bottom:15px;">
        <input type="text" id="filtroNomeServico" placeholder="Filtrar por nome do serviço..." 
          style="padding:8px; border-radius:8px; border:none; width:220px; background:rgba(255,255,255,0.2); color:white;">
        
        <input type="date" id="filtroDataInicio" 
          style="padding:8px; border-radius:8px; border:none; background:rgba(255,255,255,0.2); color:white;">
        
        <input type="date" id="filtroDataFim" 
          style="padding:8px; border-radius:8px; border:none; background:rgba(255,255,255,0.2); color:white;">
        
        <button id="btnAplicarFiltro" 
          style="padding:8px 16px; border:none; border-radius:8px; background:linear-gradient(45deg, #ffb84d, #ff9933); font-weight:bold; cursor:pointer;">
          Aplicar Filtro
        </button>

        <button id="btnLimparFiltro" 
          style="padding:8px 16px; border:none; border-radius:8px; background:linear-gradient(45deg, #999, #666); font-weight:bold; cursor:pointer; color:white;">
          Limpar Filtro
        </button>
      </div>

      <div id="modalImagens" class="modal-gallery" aria-live="polite"></div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="site-footer">
  <div class="footer-container">
    <div class="footer-left"><h3>Serralheria do Carlinho</h3><p>Excelência em serviços de serralheria desde 2010.</p></div>
    <div class="footer-center">
      <p><i class="bi bi-geo-alt-fill"></i> Boa Nova - Santo Antônio de Pádua</p>
      <p><i class="bi bi-telephone-fill"></i> (32) 99134-284</p>
      <p><i class="bi bi-envelope-fill"></i> toldo.e.serralheria.do.carlinho@gmail.com</p>
    </div>
    <div class="footer-right">
      <h4>Siga-nos</h4>
      <div class="social-icons">
        <a href="#"><i class="bi bi-facebook"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="https://wa.me/553299134284?text=Olá,%20tenho%20interesse%20em%20seus%20serviços!"><i class="bi bi-whatsapp"></i></a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; <?= date("Y"); ?> Toldo e Serralheria do Carlinho - Todos os direitos reservados.</p>
  </div>
</footer>

<script>
(function () {
  // estado
  let currentImg = '';
  let currentStars = 0;
  let imagensCategoria = []; // usado quando abrir modal de categoria

  // util
  const $ = (id) => document.getElementById(id);

  // =================== MENU USUÁRIO ===================
  const trigger = $("menuBtn");
  const menu = $("menuOpcoes");

  if (trigger && menu) {
    trigger.addEventListener("click", (e) => {
      e.stopPropagation();
      menu.style.display = menu.style.display === "flex" ? "none" : "flex";
    });

    window.addEventListener("click", (e) => {
      if (!menu.contains(e.target) && !trigger.contains(e.target)) {
        menu.style.display = "none";
      }
    });
  }

  // =================== CONTATO WHATSAPP ===================
  function registrarContato(url) {
    fetch("registrar_contato.php", { method: "POST" })
      .then((res) => res.ok ? res.json() : Promise.reject(res))
      .then((data) => {
        if (data.contatos !== undefined && $("contatos")) {
          $("contatos").textContent = data.contatos;
        }
      })
      .catch((err) => console.error(err))
      .finally(() => {
        if (url) window.open(url, "_blank");
      });
  }

  $("whatsappBtn")?.addEventListener("click", (e) => {
    e.preventDefault();
    registrarContato(e.currentTarget.href);
  });

  $("btnFalarConosco")?.addEventListener("click", (e) => {
    e.preventDefault();
    registrarContato(e.currentTarget.href);
  });

  // =================== CARROSSEL: botões e clique inicial ===================
  document.querySelectorAll(".categoria-bloco").forEach((bloco) => {
    const track = bloco.querySelector(".carousel-track");
    bloco.querySelector(".prev-btn")?.addEventListener("click", (ev) => {
      ev.stopPropagation();
      track.scrollBy({ left: -416.5385, behavior: "smooth" });
    });
    bloco.querySelector(".next-btn")?.addEventListener("click", (ev) => {
      ev.stopPropagation();
      track.scrollBy({ left: 416.5385, behavior: "smooth" });
    });

    // clique na imagem do carrossel -> abrir modal de categoria (galeria)
    bloco.querySelectorAll(".carousel-item img").forEach((img) => {
      img.addEventListener("click", (ev) => {
        ev.stopPropagation();
        abrirModalCategoria(bloco.getAttribute("data-category") || "");
      });
    });
  });

  // =================== BUSCA ===================
  const inputBusca = $("buscarServico");
  if (inputBusca) {
    inputBusca.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        const termo = inputBusca.value.trim().toLowerCase();
        if (!termo) return;
        let encontrado = null;
        document.querySelectorAll(".categoria-bloco").forEach((b) => {
          b.classList.remove("highlight");
          const cat = (b.getAttribute("data-category") || "").toLowerCase();
          if (cat.includes(termo)) encontrado = encontrado || b;
        });
        if (encontrado) {
          encontrado.scrollIntoView({ behavior: "smooth", block: "center" });
          encontrado.classList.add("highlight");
          setTimeout(() => encontrado.classList.remove("highlight"), 2500);
        } else alert("Categoria não encontrada.");
      }
    });
  }

  // =================== MODAL DE CATEGORIAS (GALERIA) ===================
  const categoriaModal = $("categoriaModal");
  const modalTitulo = $("modalTitulo");
  const modalImagens = $("modalImagens");
  const categoriaModalClose = document.getElementById("categoriaModalClose");

  categoriaModalClose?.addEventListener("click", () => {
    categoriaModal.style.display = "none";
    categoriaModal.setAttribute("aria-hidden", "true");
  });

  categoriaModal?.addEventListener("click", (e) => {
    if (e.target === categoriaModal) {
      categoriaModal.style.display = "none";
      categoriaModal.setAttribute("aria-hidden", "true");
    }
  });

  // abrir modal de categoria: busca via endpoint e monta thumbs
  function abrirModalCategoria(categoria) {
    if (!categoria) return;
    modalTitulo.textContent = "Carregando...";
    modalImagens.innerHTML = "<p style='padding:20px'>Carregando imagens...</p>";
    categoriaModal.style.display = "flex";
    categoriaModal.setAttribute("aria-hidden", "false");

    fetch(`get_fotos_categoria.php?categoria=${encodeURIComponent(categoria)}`)
      .then((res) => {
        if (!res.ok) throw new Error("Erro ao carregar fotos");
        return res.json();
      })
      .then((data) => {
        modalTitulo.textContent = categoria;
        modalImagens.innerHTML = "";

        if (!Array.isArray(data) || data.length === 0) {
          modalImagens.innerHTML = "<p style='padding:18px'>Nenhum trabalho encontrado nesta categoria.</p>";
          return;
        }

        imagensCategoria = data; // guarda para filtros

        data.forEach((item) => {
          const img = document.createElement("img");
          // respeita caminhos absolutos ou relativos vindos da API
          img.src = (item.imagem && !item.imagem.startsWith("http") && !item.imagem.startsWith("/")) ? ('imagens/' + item.imagem.replace(/^\/+/, '')) : item.imagem || '';
          img.alt = item.nome_trabalho || categoria;
          img.classList.add("modal-thumb");
          img.setAttribute("loading", "lazy");
          // ao clicar numa thumb da galeria -> abrir modal de comentário
          img.addEventListener("click", () => abrirModalComentario(img.src));
          modalImagens.appendChild(img);
        });
      })
      .catch((err) => {
        console.error(err);
        modalTitulo.textContent = categoria;
        modalImagens.innerHTML = "<p style='padding:18px'>Erro ao carregar imagens.</p>";
      });
  }

  // =================== FILTRO NO MODAL DE CATEGORIA ===================
  const filtroNome = $("filtroNomeServico");
  const filtroInicio = $("filtroDataInicio");
  const filtroFim = $("filtroDataFim");
  const btnFiltro = $("btnAplicarFiltro");
  const btnLimpar = $("btnLimparFiltro");

  function parseDataSQL(dataStr) {
    if (!dataStr) return null;
    const partes = dataStr.split("-");
    if (partes.length === 3) return new Date(Number(partes[0]), Number(partes[1]) - 1, Number(partes[2]));
    const partesBR = dataStr.split("/");
    if (partesBR.length === 3) return new Date(Number(partesBR[2]), Number(partesBR[1]) - 1, Number(partesBR[0]));
    return null;
  }

  function aplicarFiltroCategoria() {
    if (!Array.isArray(imagensCategoria)) return;
    const termo = filtroNome.value.trim().toLowerCase();
    const dataInicio = filtroInicio.value ? new Date(filtroInicio.value) : null;
    const dataFim = filtroFim.value ? new Date(filtroFim.value) : null;

    modalImagens.innerHTML = "";

    const filtradas = imagensCategoria.filter((item) => {
      const nomeMatch = termo ? (item.nome_trabalho || '').toLowerCase().includes(termo) : true;
      const dataTrabalho = parseDataSQL(item.data_inicio);
      let dataMatch = true;
      if (dataInicio && dataTrabalho) dataMatch = dataMatch && dataTrabalho >= dataInicio;
      if (dataFim && dataTrabalho) dataMatch = dataMatch && dataTrabalho <= dataFim;
      return nomeMatch && dataMatch;
    });

    if (filtradas.length === 0) {
      modalImagens.innerHTML = "<p style='padding:18px'>Nenhum trabalho encontrado com esses filtros.</p>";
      return;
    }

    filtradas.forEach((item) => {
      const img = document.createElement("img");
      img.src = (item.imagem && !item.imagem.startsWith("http") && !item.imagem.startsWith("/")) ? ('imagens/' + item.imagem.replace(/^\/+/, '')) : item.imagem || '';
      img.alt = item.nome_trabalho;
      img.classList.add("modal-thumb");
      img.setAttribute("loading", "lazy");
      img.addEventListener("click", () => abrirModalComentario(img.src));
      modalImagens.appendChild(img);
    });
  }

  btnFiltro?.addEventListener("click", aplicarFiltroCategoria);

  btnLimpar?.addEventListener("click", () => {
    filtroNome.value = "";
    filtroInicio.value = "";
    filtroFim.value = "";

    modalImagens.innerHTML = "";
    if (Array.isArray(imagensCategoria)) {
      imagensCategoria.forEach((item) => {
        const img = document.createElement("img");
        img.src = (item.imagem && !item.imagem.startsWith("http") && !item.imagem.startsWith("/")) ? ('imagens/' + item.imagem.replace(/^\/+/, '')) : item.imagem || '';
        img.alt = item.nome_trabalho;
        img.classList.add("modal-thumb");
        img.setAttribute("loading", "lazy");
        img.addEventListener("click", () => abrirModalComentario(img.src));
        modalImagens.appendChild(img);
      });
    }
  });

  // =================== MODAL DE COMENTÁRIOS ===================
  const modalComentario = $("modalComentarioOverlay");
  const modalComentarioClose = $("modalComentarioClose");
  const comentariosList = $("comentariosList");
  const imagemProdutoComentario = $("imagemProdutoComentario");
  const starRating = $("starRating");
  const stars = starRating ? Array.from(starRating.querySelectorAll("i")) : [];

  function marcarEstrelas(qtd) {
    stars.forEach(star => {
      star.classList.toggle("selected", parseInt(star.dataset.value, 10) <= qtd);
    });
  }

  // inicial click das estrelas
  stars.forEach(star => {
    star.addEventListener("click", () => {
      currentStars = parseInt(star.dataset.value, 10) || 0;
      marcarEstrelas(currentStars);
    });
  });

  function carregarComentarios(imagem) {
    if (!comentariosList) return;
    comentariosList.innerHTML = `<p style="text-align:center;color:#ddd;">Carregando...</p>`;
    fetch("buscar_comentarios.php?imagem=" + encodeURIComponent(imagem))
      .then(r => {
        if (!r.ok) throw new Error("Erro ao buscar comentários");
        return r.json();
      })
      .then(lista => {
        comentariosList.innerHTML = "";
        if (!Array.isArray(lista) || lista.length === 0) {
          comentariosList.innerHTML = "<p style='text-align:center;color:#ccc;'>Nenhum comentário ainda.</p>";
          return;
        }
        lista.forEach(item => {
          const div = document.createElement("div");
          div.innerHTML = `<strong>${item.nome}</strong><br>${item.comentario}<br><span style="color:gold;">${'★'.repeat(item.estrelas || 0)}${'☆'.repeat(5 - (item.estrelas || 0))}</span>`;
          comentariosList.appendChild(div);
        });
      })
      .catch(err => {
        console.error(err);
        comentariosList.innerHTML = "<p style='color:red;'>Erro ao carregar comentários.</p>";
      });
  }

  function abrirModalComentario(src) {
    currentImg = src || '';
    if (imagemProdutoComentario) imagemProdutoComentario.src = currentImg;
    if (comentariosList) carregarComentarios(currentImg);
    if (modalComentario) {
      modalComentario.style.display = "flex";
      modalComentario.setAttribute("aria-hidden", "false");
    }
    // reset estrelas visuais (não o valor salvo)
    currentStars = 0;
    marcarEstrelas(0);
  }

  modalComentarioClose?.addEventListener("click", () => {
    if (modalComentario) {
      modalComentario.style.display = "none";
      modalComentario.setAttribute("aria-hidden", "true");
    }
    currentStars = 0;
    marcarEstrelas(0);
  });

  window.addEventListener("click", (e) => {
    if (e.target === modalComentario) {
      modalComentario.style.display = "none";
      modalComentario.setAttribute("aria-hidden", "true");
    }
    if (e.target === categoriaModal) {
      categoriaModal.style.display = "none";
      categoriaModal.setAttribute("aria-hidden", "true");
    }
  });

  // abertura por thumbs dentro da galeria (delegação)
  document.addEventListener("click", (e) => {
    const t = e.target;
    if (t && t.classList && t.classList.contains("modal-thumb")) {
      abrirModalComentario(t.src);
    }
  });

  // =================== ENVIAR COMENTÁRIO ===================
  $("btnEnviarComentario")?.addEventListener("click", () => {
    const nome = ($("nomeUsuario")?.value || "").trim();
    const comentario = ($("novoComentario")?.value || "").trim();
    if (!nome || !comentario || currentStars === 0) {
      alert("Preencha nome, comentário e estrelas.");
      return;
    }

    const form = new URLSearchParams();
    form.append('nome', nome);
    form.append('comentario', comentario);
    form.append('imagem', currentImg);
    form.append('estrelas', String(currentStars));

    fetch("salvar_comentario.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: form.toString()
    })
      .then(r => {
        if (!r.ok) throw new Error("Erro ao salvar");
        return r.json();
      })
      .then(data => {
        if (data && (data.sucesso || data.success)) {
          // recarrega comentários para mostrar novo comentário
          carregarComentarios(currentImg);
          // limpa formulário
          if ($("nomeUsuario")) $("nomeUsuario").value = "";
          if ($("novoComentario")) $("novoComentario").value = "";
          currentStars = 0;
          marcarEstrelas(0);
        } else {
          alert("Erro ao salvar comentário.");
        }
      })
      .catch(err => {
        console.error(err);
        alert("Erro ao salvar comentário.");
      });
  });

})();
</script>

</body>
</html>

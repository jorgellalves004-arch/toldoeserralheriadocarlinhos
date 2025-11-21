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

.modal-gallery img {
  max-width: 260px; height: auto;
  border-radius: 12px;
  object-fit: cover;
  transition: transform .25s ease, box-shadow .25s;
  cursor: pointer;
}
.modal-gallery img:hover {
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
    <a href="/serralheria/historia.php">Nossa Historia</a>
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

<!-- Dados da Empresa -->





</div>

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
                $img = '../' . htmlspecialchars($trabalho['imagem']);
                $nome = htmlspecialchars($trabalho['nome_trabalho']);
                $data_inicio = !empty($trabalho['data_inicio']) ? date('d/m/Y', strtotime($trabalho['data_inicio'])) : '';
                $data_fim = !empty($trabalho['data_fim']) ? date('d/m/Y', strtotime($trabalho['data_fim'])) : 'Em andamento';
                echo "<div class='carousel-item'>
                        <img src=\"{$img}\" alt=\"{$nome}\" data-nome=\"{$nome}\">
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
<div class="modal-overlay" id="modalComentarioOverlay">
  <div class="modal-content">
    <button class="modal-close" id="modalComentarioClose">&times;</button>
    <div class="modal-body">
  <div class="modal-img-container" style="flex-direction: column;">
  <img id="imagemProdutoComentario" src="img/produto.jpg" alt="Produto">
  
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
<div id="categoriaModal" class="modal-overlay" aria-hidden="true">
  <div class="modal-content" role="dialog" aria-modal="true">
    <button class="modal-close" aria-label="Fechar">&times;</button>
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

      <div id="modalImagens" class="modal-gallery"></div>
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
        <a href="https://www.facebook.com/share/1BYCLKKKxc/"><i class="bi bi-facebook"></i></a>
        <a href="https://www.instagram.com/serralheria.do.carlinho?igsh=MTI4bXAybGZwMjRtNA=="><i class="bi bi-instagram"></i></a>
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
  let currentImg = '';
  let currentStars = 0;
  let imagensCategoria = [];

  // =================== MENU USUÁRIO ===================
  const trigger = document.getElementById("menuBtn");
  const menu = document.getElementById("menuOpcoes");

  if (trigger && menu) {
    // Abre/fecha o menu ao clicar no botão
    trigger.addEventListener("click", (e) => {
      e.stopPropagation();
      menu.style.display = menu.style.display === "flex" ? "none" : "flex";
    });

    // Fecha o menu ao clicar fora
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
        if (data.contatos !== undefined) {
          document.getElementById("contatos").textContent = data.contatos;
        }
      })
      .catch((err) => console.error(err))
      .finally(() => {
        if (url) window.open(url, "_blank");
      });
  }

  // Botões de contato
  document.getElementById("whatsappBtn")?.addEventListener("click", (e) => {
    e.preventDefault();
    registrarContato(e.currentTarget.href);
  });

  document.getElementById("btnContato")?.addEventListener("click", (e) => {
    e.preventDefault();
    registrarContato(e.currentTarget.href);
  });

  // =================== BOTÃO "FALAR CONOSCO" DO MENU ===================
  const btnFalarConosco = document.getElementById("btnFalarConosco");
  if (btnFalarConosco) {
    btnFalarConosco.addEventListener("click", () => {
      fetch("registrar_contato_menu.php", { method: "POST" })
        .then((res) => res.ok ? res.json() : Promise.reject(res))
        .then((data) => {
          if (data.contatos !== undefined) {
            document.getElementById("contatos").textContent = data.contatos;
          }
        })
        .catch((err) => console.error("Erro ao registrar contato:", err));
    });
  }

  // =================== CARROSSEL ===================
  document.querySelectorAll(".categoria-bloco").forEach((bloco) => {
    const track = bloco.querySelector(".carousel-track");
    bloco.querySelector(".prev-btn")?.addEventListener("click", () =>
      track.scrollBy({ left: -416.5385, behavior: "smooth" })
    );
    bloco.querySelector(".next-btn")?.addEventListener("click", () =>
      track.scrollBy({ left: 416.5385, behavior: "smooth" })
    );

    // Clique nas imagens do carrossel abre o modal da categoria
    bloco.querySelectorAll(".carousel-item img").forEach((img) => {
      img.addEventListener("click", () => {
        abrirModalCategoria(bloco.getAttribute("data-category") || "");
      });
    });
  });

  // =================== BUSCA ===================
  const inputBusca = document.getElementById("buscarServico");
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

  // =================== MODAL DE CATEGORIAS ===================
  const categoriaModal = document.getElementById("categoriaModal");
  const modalTitulo = document.getElementById("modalTitulo");
  const modalImagens = document.getElementById("modalImagens");

  categoriaModal?.querySelector(".modal-close")?.addEventListener("click", () => {
    categoriaModal.style.display = "none";
    categoriaModal.setAttribute("aria-hidden", "true");
  });

  categoriaModal?.addEventListener("click", (e) => {
    if (e.target === categoriaModal) {
      categoriaModal.style.display = "none";
      categoriaModal.setAttribute("aria-hidden", "true");
    }
  });

  // Abre modal de categoria ao clicar no título
  document.querySelectorAll(".category-title").forEach((title) => {
    title.addEventListener("click", () => {
      const bloco = title.closest(".categoria-bloco");
      abrirModalCategoria(bloco?.getAttribute("data-category") || title.textContent.trim());
    });
  });

  function abrirModalCategoria(categoria) {
    if (!categoria) return;
    modalTitulo.textContent = "Carregando...";
    modalImagens.innerHTML = "<p style='padding:20px'>Carregando imagens...</p>";
    categoriaModal.style.display = "flex";
    categoriaModal.setAttribute("aria-hidden", "false");

    fetch(`get_fotos_categoria.php?categoria=${encodeURIComponent(categoria)}`)
      .then((res) => res.ok ? res.json() : Promise.reject(res))
      .then((data) => {
        modalTitulo.textContent = categoria;
        modalImagens.innerHTML = "";

        if (!Array.isArray(data) || data.length === 0) {
          modalImagens.innerHTML = "<p style='padding:18px'>Nenhum trabalho encontrado nesta categoria.</p>";
          return;
        }

        imagensCategoria = data;

        data.forEach((item) => {
          const img = document.createElement("img");
          img.src = (item.imagem && !item.imagem.startsWith("http")) ? ('../' + item.imagem) : item.imagem;
          img.alt = item.nome_trabalho || categoria;
          img.addEventListener("click", () => abrirModalComentario(img.src));
          modalImagens.appendChild(img);
        });
      })
      .catch(() => {
        modalTitulo.textContent = categoria;
        modalImagens.innerHTML = "<p>Erro ao carregar imagens.</p>";
      });
  }
// =================== FILTRO NO MODAL DE CATEGORIA ===================
const filtroNome = document.getElementById("filtroNomeServico");
const filtroInicio = document.getElementById("filtroDataInicio");
const filtroFim = document.getElementById("filtroDataFim");
const btnFiltro = document.getElementById("btnAplicarFiltro");
const btnLimpar = document.getElementById("btnLimparFiltro");

function parseDataSQL(dataStr) {
  if (!dataStr) return null;
  // Tenta converter formato YYYY-MM-DD
  const partes = dataStr.split("-");
  if (partes.length === 3) {
    return new Date(Number(partes[0]), Number(partes[1]) - 1, Number(partes[2]));
  }
  // Tenta converter formato DD/MM/YYYY
  const partesBR = dataStr.split("/");
  if (partesBR.length === 3) {
    return new Date(Number(partesBR[2]), Number(partesBR[1]) - 1, Number(partesBR[0]));
  }
  return null;
}

function aplicarFiltroCategoria() {
  const termo = filtroNome.value.trim().toLowerCase();
  const dataInicio = filtroInicio.value ? new Date(filtroInicio.value) : null;
  const dataFim = filtroFim.value ? new Date(filtroFim.value) : null;

  modalImagens.innerHTML = "";

  const filtradas = imagensCategoria.filter((item) => {
    const nomeMatch = termo ? item.nome_trabalho.toLowerCase().includes(termo) : true;

    const dataTrabalho = parseDataSQL(item.data_inicio);
    let dataMatch = true;

    if (dataInicio && dataTrabalho) {
      dataMatch = dataMatch && dataTrabalho >= dataInicio;
    }
    if (dataFim && dataTrabalho) {
      dataMatch = dataMatch && dataTrabalho <= dataFim;
    }

    return nomeMatch && dataMatch;
  });

  if (filtradas.length === 0) {
    modalImagens.innerHTML = "<p style='padding:18px'>Nenhum trabalho encontrado com esses filtros.</p>";
    return;
  }

  filtradas.forEach((item) => {
    const img = document.createElement("img");
    img.src = (item.imagem && !item.imagem.startsWith("http")) ? ('../' + item.imagem) : item.imagem;
    img.alt = item.nome_trabalho;
    img.addEventListener("click", () => abrirModalComentario(img.src));
    modalImagens.appendChild(img);
  });
}

// Eventos
btnFiltro?.addEventListener("click", aplicarFiltroCategoria);

btnLimpar?.addEventListener("click", () => {
  filtroNome.value = "";
  filtroInicio.value = "";
  filtroFim.value = "";

  modalImagens.innerHTML = "";
  imagensCategoria.forEach((item) => {
    const img = document.createElement("img");
    img.src = (item.imagem && !item.imagem.startsWith("http")) ? ('../' + item.imagem) : item.imagem;
    img.alt = item.nome_trabalho;
    img.addEventListener("click", () => abrirModalComentario(img.src));
    modalImagens.appendChild(img);
  });
});


  // =================== ENVIO DE COMENTÁRIO ===================
  btnEnviarComentario?.addEventListener("click", () => {
    const comentario = novoComentario.value.trim();
    const nome = inputNome.value.trim();

    if (!comentario || !nome || currentStars === 0) {
      alert("Informe seu nome, comentário e quantidade de estrelas.");
      return;
    }

    fetch("salvar_comentario.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `imagem=${encodeURIComponent(currentImg)}&nome_usuario=${encodeURIComponent(nome)}&comentario=${encodeURIComponent(comentario)}&estrelas=${encodeURIComponent(currentStars)}`
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) abrirModalComentario(currentImg);
        else alert("Erro ao salvar comentário.");
      })
      .catch((err) => {
        console.error(err);
        alert("Erro ao salvar comentário.");
      });
  });
})();




</script>

</body>
</html>

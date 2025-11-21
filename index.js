document.addEventListener("DOMContentLoaded", () => {
  // ===== MENU DE OPÇÕES CABEÇALHO =====
  const trigger = document.getElementById("userMenuTrigger");
  const menu = document.getElementById("userOptionsBox");

  if (trigger && menu) {
    trigger.addEventListener("click", () => {
      menu.style.display = menu.style.display === "block" ? "none" : "block";
    });

    document.addEventListener("click", (e) => {
      if (!document.getElementById("customUserMenu").contains(e.target)) {
        menu.style.display = "none";
      }
    });
  }

  // ===== ANIMAÇÃO DOS NÚMEROS =====
  document.querySelectorAll('.value[data-target]').forEach(el => {
    const target = +el.getAttribute('data-target');
    let count = 0;
    const increment = target / 80;
    const update = () => {
      count += increment;
      if (count < target) {
        el.textContent = Math.floor(count);
        requestAnimationFrame(update);
      } else {
        el.textContent = target.toLocaleString('pt-BR');
      }
    };
    update();
  });

 

  // ===== ESTATÍSTICAS =====
  const animateCount = (el, endValue) => {
    if (!el) return;
    let start = 0;
    const duration = 1500;
    const step = Math.ceil(endValue / (duration / 16));
    const interval = setInterval(() => {
      start += step;
      if (start >= endValue) {
        el.innerText = endValue.toLocaleString("pt-BR");
        clearInterval(interval);
      } else {
        el.innerText = start.toLocaleString("pt-BR");
      }
    }, 16);
  };

  const atualizarEstatistica = (acao, callback) => {
    fetch("atualizar_estatisticas.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `acao=${acao}`
    })
      .then(res => res.ok ? res.json() : Promise.reject(res))
      .then(data => callback && callback(data))
      .catch(err => console.error("Erro ao atualizar estatísticas:", err));
  };

  const acessosEl = document.getElementById("acessos");
  if (acessosEl) {
    atualizarEstatistica("acesso", (data) => {
      if (data && data.acessos !== undefined) animateCount(acessosEl, data.acessos);
    });
  }

  const contatosEl = document.getElementById("contatos");
  const whatsappLinks = document.querySelectorAll("a[href*='wa.me']");
  if (whatsappLinks.length && contatosEl) {
    whatsappLinks.forEach(link => {
      link.addEventListener("click", () => {
        atualizarEstatistica("contato", (data) => {
          if (data && data.contatos !== undefined) animateCount(contatosEl, data.contatos);
        });
      });
    });
  }

  // ===== MENU WHATSAPP =====
  const btnContato = document.getElementById("btnContato");
  if (btnContato) {
    btnContato.addEventListener("click", (e) => {
      e.preventDefault();
      const whatsappURL = "https://wa.me/553299134284?text=Olá,%20Tenho%20interesse%20em%20seus%20serviços!";
      fetch("registrar_contato.php", { method: "POST" })
        .finally(() => window.open(whatsappURL, "_blank"));
    });
  }

  // ===== PESQUISA DE CATEGORIA =====
  const searchInput = document.getElementById("searchCategoria");
  const categorias = document.querySelectorAll(".categoria");
  const btnPrev = document.querySelector(".btn-prev");
  const btnNext = document.querySelector(".btn-next");
  let currentIndex = 0;

  const scrollToCategoria = (categoriaNome) => {
    const categoria = Array.from(categorias).find(cat =>
      cat.dataset.nome.toLowerCase() === categoriaNome.toLowerCase()
    );
    if (categoria) {
      categoria.scrollIntoView({ behavior: "smooth", block: "start" });
    } else {
      Swal.fire({
        icon: "info",
        title: "Categoria não encontrada",
        text: "Verifique o nome digitado.",
        confirmButtonText: "Ok"
      });
    }
  };

  if (searchInput) {
    searchInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        const termo = searchInput.value.trim();
        if (termo) scrollToCategoria(termo);
      }
    });
  }

  if (btnPrev && btnNext) {
    btnPrev.addEventListener("click", () => {
      if (currentIndex > 0) {
        currentIndex--;
        categorias[currentIndex].scrollIntoView({ behavior: "smooth", block: "center" });
      }
    });

    btnNext.addEventListener("click", () => {
      if (currentIndex < categorias.length - 1) {
        currentIndex++;
        categorias[currentIndex].scrollIntoView({ behavior: "smooth", block: "center" });
      }
    });
  }

  // ===== MODAL DE CATEGORIAS =====
  document.querySelectorAll(".category-title").forEach(title => {
    title.addEventListener("click", () => {
      const categoria = title.textContent.trim();
      fetch(`get_fotos_categoria.php?categoria=${encodeURIComponent(categoria)}`)
        .then(res => res.json())
        .then(data => {
          const modal = document.getElementById("categoriaModal");
          const modalTitulo = document.getElementById("modalTitulo");
          const modalImagens = document.getElementById("modalImagens");
          modalTitulo.textContent = categoria;
          modalImagens.innerHTML = "";

          if (data.length > 0) {
            data.forEach(item => {
              const img = document.createElement("img");
              img.src = `../${item.imagem}`;
              img.alt = item.nome_trabalho;
              modalImagens.appendChild(img);
            });
          } else {
            modalImagens.innerHTML = "<p style='text-align:center;'>Nenhum trabalho encontrado nesta categoria.</p>";
          }

          modal.style.display = "flex";
        });
    });
  });

  const modalClose = document.querySelector(".modal-close");
  const modal = document.getElementById("categoriaModal");
  if (modalClose && modal) {
    modalClose.addEventListener("click", () => modal.style.display = "none");
    modal.addEventListener("click", e => {
      if (e.target.id === "categoriaModal") modal.style.display = "none";
    });
  }
});

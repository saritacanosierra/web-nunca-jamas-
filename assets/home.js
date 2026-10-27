(function () {
  var cuentos = document.querySelector("[data-cuentos]");
  if (cuentos) {
    let slides = Array.prototype.slice.call(cuentos.querySelectorAll(".cuentos-banner__slide"));
    let dots = Array.prototype.slice.call(cuentos.querySelectorAll("[data-cuentos-dot]"));
    let index = 0;
    let timer;

    function showCuento(next) {
      index = (next + slides.length) % slides.length;
      slides.forEach(function (slide, i) {
        slide.classList.toggle("is-active", i === index);
      });
      dots.forEach(function (dot, i) {
        dot.classList.toggle("is-active", i === index);
      });
    }

    function playCuentos() {
      clearInterval(timer);
      timer = setInterval(function () { showCuento(index + 1); }, 6500);
    }

    cuentos.querySelector("[data-cuentos-prev]").addEventListener("click", function () {
      showCuento(index - 1);
      playCuentos();
    });
    cuentos.querySelector("[data-cuentos-next]").addEventListener("click", function () {
      showCuento(index + 1);
      playCuentos();
    });
    dots.forEach(function (dot) {
      dot.addEventListener("click", function () {
        showCuento(Number(dot.getAttribute("data-cuentos-dot")));
        playCuentos();
      });
    });
    playCuentos();
  }

  var header = document.querySelector(".site-header");
  var hero = document.querySelector("[data-hero]");
  var toggle = document.querySelector("[data-nav-toggle]");
  var drawer = document.querySelector("[data-drawer]");
  var searchBtn = document.querySelector("[data-search-open]");
  var searchBar = document.querySelector("[data-search]");
  var news = document.querySelector("[data-news]");

  if (header) {
    var onScroll = function () {
      header.classList.toggle("is-stuck", window.scrollY > 4);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  if (toggle && drawer) {
    toggle.addEventListener("click", function () {
      var open = drawer.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  var menuTimer;
  Array.prototype.forEach.call(document.querySelectorAll(".nav-item"), function (item) {
    if (!item.querySelector(".nav-panel")) {
      return;
    }
    item.addEventListener("mouseenter", function () {
      clearTimeout(menuTimer);
      Array.prototype.forEach.call(document.querySelectorAll(".nav-item.is-open"), function (open) {
        if (open !== item) {
          open.classList.remove("is-open");
        }
      });
      item.classList.add("is-open");
    });
    item.addEventListener("mouseleave", function () {
      clearTimeout(menuTimer);
      menuTimer = setTimeout(function () {
        item.classList.remove("is-open");
      }, 420);
    });
  });

  if (searchBtn && searchBar) {
    searchBtn.addEventListener("click", function () {
      var open = searchBar.classList.toggle("is-open");
      searchBtn.setAttribute("aria-expanded", open ? "true" : "false");
      if (open) {
        var input = searchBar.querySelector("input");
        if (input) input.focus();
      }
    });
  }

  if (hero) {
    var slides = Array.prototype.slice.call(hero.querySelectorAll(".hero__slide"));
    var dots = Array.prototype.slice.call(hero.querySelectorAll("[data-dot]"));
    var index = 0;
    var timer;

    function show(next) {
      index = (next + slides.length) % slides.length;
      slides.forEach(function (slide, i) {
        slide.classList.toggle("is-active", i === index);
      });
      dots.forEach(function (dot, i) {
        dot.classList.toggle("is-active", i === index);
      });
    }

    function play() {
      clearInterval(timer);
      timer = setInterval(function () { show(index + 1); }, 6500);
    }

    var prev = hero.querySelector("[data-prev]");
    var next = hero.querySelector("[data-next]");
    if (prev) prev.addEventListener("click", function () { show(index - 1); play(); });
    if (next) next.addEventListener("click", function () { show(index + 1); play(); });
    dots.forEach(function (dot) {
      dot.addEventListener("click", function () {
        show(Number(dot.getAttribute("data-dot")));
        play();
      });
    });
    play();
  }

  var tabs = document.querySelector("[data-tabs]");
  if (tabs) {
    var tabButtons = Array.prototype.slice.call(tabs.querySelectorAll("[data-tab]"));
    var panels = Array.prototype.slice.call(tabs.querySelectorAll("[data-panel]"));
    tabButtons.forEach(function (button) {
      button.addEventListener("click", function () {
        var id = button.getAttribute("data-tab");
        tabButtons.forEach(function (item) {
          var active = item === button;
          item.classList.toggle("is-active", active);
          item.setAttribute("aria-selected", active ? "true" : "false");
        });
        panels.forEach(function (panel) {
          panel.classList.toggle("is-active", panel.getAttribute("data-panel") === id);
        });
      });
    });
  }

  if (news) {
    news.addEventListener("submit", function (event) {
      event.preventDefault();
      var note = news.querySelector(".form-note");
      var input = news.querySelector("input");
      if (!input || !input.value || input.value.indexOf("@") === -1) {
        if (note) note.textContent = "Escribe un correo válido.";
        return;
      }
      if (note) note.textContent = "Listo. Te escribimos cuando haya una nueva colección.";
      news.reset();
    });
  }
})();

(function () {
  function njSwipe(area, onNext, onPrev) {
    if (!area) {
      return;
    }
    var startX = 0;
    var startY = 0;
    var tracking = false;
    var swiped = false;
    area.addEventListener("pointerdown", function (event) {
      if (event.pointerType === "mouse" && event.button !== 0) {
        return;
      }
      if (event.target.closest("button, input, textarea, select, label")) {
        return;
      }
      tracking = true;
      swiped = false;
      startX = event.clientX;
      startY = event.clientY;
    });
    function finish(event) {
      if (!tracking) {
        return;
      }
      tracking = false;
      var dx = event.clientX - startX;
      var dy = event.clientY - startY;
      if (Math.abs(dx) < 42 || Math.abs(dx) < Math.abs(dy)) {
        return;
      }
      swiped = true;
      if (dx < 0) {
        onNext();
      } else {
        onPrev();
      }
    }
    area.addEventListener("pointerup", finish);
    area.addEventListener("pointercancel", function () {
      tracking = false;
    });
    area.addEventListener("click", function (event) {
      if (!swiped) {
        return;
      }
      swiped = false;
      event.preventDefault();
      event.stopPropagation();
    }, true);
  }

  var cuentos = document.querySelector("[data-cuentos]");
  if (cuentos) {
    let slides = Array.prototype.slice.call(cuentos.querySelectorAll(".cuentos-banner__slide"));
    let dots = Array.prototype.slice.call(cuentos.querySelectorAll("[data-cuentos-dot]"));
    let index = 0;
    let timer;
    if (slides.length) {

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
    njSwipe(cuentos, function () {
      showCuento(index + 1);
      playCuentos();
    }, function () {
      showCuento(index - 1);
      playCuentos();
    });
    playCuentos();
    }
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
      hero.classList.toggle("is-build", slides[index].classList.contains("hero__slide--build"));
      var alto = Number(slides[index].getAttribute("data-alto") || 0);
      if (alto > 0) {
        hero.classList.add("has-alto");
        hero.style.setProperty("--hero-h", alto + "px");
      } else {
        hero.classList.remove("has-alto");
        hero.style.removeProperty("--hero-h");
      }
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
    njSwipe(hero, function () {
      show(index + 1);
      play();
    }, function () {
      show(index - 1);
      play();
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

  var faq = document.querySelector(".faq");
  if (faq) {
    faq.addEventListener("toggle", function (event) {
      var item = event.target;
      if (!item.open || item.tagName !== "DETAILS") {
        return;
      }
      Array.prototype.forEach.call(faq.querySelectorAll("details[open]"), function (other) {
        if (other !== item) {
          other.removeAttribute("open");
        }
      });
    }, true);
  }

  var looks = document.querySelector("[data-looks]");
  if (looks) {
    var track = looks.querySelector(".look-grid");
    var prevLook = looks.querySelector("[data-look-prev]");
    var nextLook = looks.querySelector("[data-look-next]");
    var lookTimer = 0;
    var lookBusy = false;
    var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    function lookCards() {
      return track ? Array.prototype.slice.call(track.children) : [];
    }

    function moveLooks(direction) {
      var cards = lookCards();
      if (!track || cards.length < 2 || lookBusy) {
        return;
      }
      var gap = parseFloat(window.getComputedStyle(track).columnGap || track.style.gap || 14) || 14;
      var width = cards[0].getBoundingClientRect().width + gap;
      if (!width) {
        return;
      }
      lookBusy = true;
      track.style.transition = reduceMotion ? "none" : "transform 0.45s ease";
      track.style.transform = "translateX(" + (direction > 0 ? -width : width) + "px)";
      window.setTimeout(function () {
        track.style.transition = "none";
        if (direction > 0) {
          track.appendChild(track.firstElementChild);
        } else if (track.lastElementChild) {
          track.insertBefore(track.lastElementChild, track.firstElementChild);
        }
        track.style.transform = "none";
        lookBusy = false;
      }, reduceMotion ? 0 : 460);
    }

    function playLooks() {
      window.clearInterval(lookTimer);
      if (reduceMotion || lookCards().length < 2) {
        return;
      }
      lookTimer = window.setInterval(function () { moveLooks(1); }, 4200);
    }

    if (prevLook) prevLook.addEventListener("click", function () { moveLooks(-1); playLooks(); });
    if (nextLook) nextLook.addEventListener("click", function () { moveLooks(1); playLooks(); });
    njSwipe(looks, function () { moveLooks(1); playLooks(); }, function () { moveLooks(-1); playLooks(); });
    looks.addEventListener("mouseenter", function () { window.clearInterval(lookTimer); });
    looks.addEventListener("mouseleave", playLooks);
    playLooks();
  }

  var voces = document.querySelector("[data-voces]");
  if (voces) {
    var fila = voces.querySelector(".testimonio-row");
    function moverVoces(direction) {
      if (!fila) {
        return;
      }
      var card = fila.querySelector(".testimonio");
      if (!card) {
        return;
      }
      var styles = window.getComputedStyle(fila);
      var gap = parseFloat(styles.columnGap || styles.gap) || 16;
      var suave = !(window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches);
      fila.scrollBy({
        left: direction * (card.getBoundingClientRect().width + gap),
        behavior: suave ? "smooth" : "auto"
      });
    }
    var prevVoces = voces.querySelector("[data-voces-prev]");
    var nextVoces = voces.querySelector("[data-voces-next]");
    if (prevVoces) {
      prevVoces.addEventListener("click", function () { moverVoces(-1); });
    }
    if (nextVoces) {
      nextVoces.addEventListener("click", function () { moverVoces(1); });
    }
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

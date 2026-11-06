(function ($) {
  function show(id) {
    var nav = document.querySelectorAll("[data-panel]");
    var screens = document.querySelectorAll("[data-screen]");
    var found = false;
    Array.prototype.forEach.call(screens, function (screen) {
      var on = screen.getAttribute("data-screen") === id;
      screen.classList.toggle("is-on", on);
      if (on) {
        found = true;
      }
    });
    if (!found) {
      id = "banner";
      show(id);
      return;
    }
    Array.prototype.forEach.call(nav, function (button) {
      button.classList.toggle("is-on", button.getAttribute("data-panel") === id);
    });
    if (window.history && window.history.replaceState) {
      window.history.replaceState(null, "", "#" + id);
    }
  }

  function reindexLooks(card) {
    if (!card) {
      return;
    }
    var lookIndex = Array.prototype.indexOf.call(document.querySelectorAll("[data-nj-look]"), card);
    var fields = card.querySelectorAll("[data-nj-point-fields]");
    Array.prototype.forEach.call(fields, function (box, index) {
      box.setAttribute("data-nj-point-fields", String(index));
      box.querySelectorAll("[name]").forEach(function (input) {
        input.name = input.name.replace(/espacios\[\d+\]\[points\]\[\d+\]/, "espacios[" + lookIndex + "][points][" + index + "]");
      });
    });
    var dots = card.querySelectorAll("[data-nj-point]");
    Array.prototype.forEach.call(dots, function (dot, index) {
      dot.setAttribute("data-nj-point", String(index));
      dot.textContent = String(index + 1);
    });
    var add = card.querySelector("[data-nj-point-add]");
    if (add) {
      add.disabled = fields.length >= 4;
    }
  }

  function addLookPoint(card) {
    if (!card) {
      return;
    }
    var list = card.querySelector("[data-nj-point-list]");
    var stage = card.querySelector("[data-nj-stage]");
    if (!list || !stage || list.querySelectorAll("[data-nj-point-fields]").length >= 4) {
      return;
    }
    var index = list.querySelectorAll("[data-nj-point-fields]").length;
    var lookIndex = Array.prototype.indexOf.call(document.querySelectorAll("[data-nj-look]"), card);
    var dot = document.createElement("button");
    dot.type = "button";
    dot.className = "nj-look-dot";
    dot.setAttribute("data-nj-point", String(index));
    dot.style.top = "50%";
    dot.style.left = "50%";
    dot.textContent = String(index + 1);
    stage.appendChild(dot);
    var box = document.createElement("div");
    box.className = "nj-look-fields";
    box.setAttribute("data-nj-point-fields", String(index));
    var base = "espacios[" + lookIndex + "][points][" + index + "]";
    box.innerHTML = ''
      + '<input data-top type="hidden" name="' + base + '[top]" value="50%">'
      + '<input data-left type="hidden" name="' + base + '[left]" value="50%">'
      + '<label>Nombre <input type="text" name="' + base + '[label]" value=""></label>'
      + '<label>Precio <input type="text" name="' + base + '[price]" value="" placeholder="$ 32.000"></label>'
      + '<label>Enlace <input type="url" name="' + base + '[href]" value="" placeholder="https://"></label>'
      + '<button type="button" class="button" data-nj-point-remove>Quitar punto</button>';
    list.appendChild(box);
    reindexLooks(card);
  }

  function pick(box) {
    if (!window.wp || !wp.media) {
      return;
    }
    var frame = wp.media({
      title: "Elige la foto",
      button: { text: "Usar esta foto" },
      library: { type: "image" },
      multiple: false
    });
    frame.on("select", function () {
      var file = frame.state().get("selection").first().toJSON();
      var input = box.querySelector("[data-nj-id]");
      var img = box.querySelector("img");
      if (input) {
        input.value = String(file.id || 0);
      }
      if (img) {
        img.src = file.url || "";
        img.hidden = !img.src;
      }
    });
    frame.open();
  }

  $(function () {
    document.addEventListener("click", function (event) {
      var pickButton = event.target.closest("[data-nj-pick]");
      if (pickButton) {
        var box = pickButton.closest("[data-nj-photo]");
        if (!box) {
          var look = pickButton.closest("[data-nj-look]");
          box = look ? look.querySelector("[data-nj-photo]") : null;
        }
        if (box) {
          pick(box);
        }
      }
      var clearButton = event.target.closest("[data-nj-clear]");
      if (clearButton) {
        var photo = clearButton.closest("[data-nj-photo]");
        if (!photo) {
          return;
        }
        var input = photo.querySelector("[data-nj-id]");
        var img = photo.querySelector("img");
        if (input) {
          input.value = "0";
        }
        if (img) {
          img.removeAttribute("src");
          img.hidden = true;
        }
      }
      var addPoint = event.target.closest("[data-nj-point-add]");
      if (addPoint) {
        addLookPoint(addPoint.closest("[data-nj-look]"));
      }
      var removePoint = event.target.closest("[data-nj-point-remove]");
      if (removePoint) {
        var card = removePoint.closest("[data-nj-look]");
        var fields = removePoint.closest("[data-nj-point-fields]");
        var id = fields ? fields.getAttribute("data-nj-point-fields") : "";
        if (fields) {
          fields.remove();
        }
        if (card && id !== "") {
          var dot = card.querySelector('[data-nj-point="' + id + '"]');
          if (dot) {
            dot.remove();
          }
        }
        reindexLooks(card);
      }
    });
    document.addEventListener("pointerdown", function (event) {
      var dot = event.target.closest("[data-nj-point]");
      if (!dot || event.button !== 0) {
        return;
      }
      var stage = dot.closest("[data-nj-stage]");
      var card = dot.closest("[data-nj-look]");
      if (!stage || !card) {
        return;
      }
      event.preventDefault();
      function move(ev) {
        var rect = stage.getBoundingClientRect();
        if (!rect.width || !rect.height) {
          return;
        }
        var x = ((ev.clientX - rect.left) / rect.width) * 100;
        var y = ((ev.clientY - rect.top) / rect.height) * 100;
        x = Math.max(2, Math.min(98, x));
        y = Math.max(2, Math.min(98, y));
        var left = x.toFixed(1).replace(/\.0$/, "") + "%";
        var top = y.toFixed(1).replace(/\.0$/, "") + "%";
        dot.style.left = left;
        dot.style.top = top;
        var fields = card.querySelector('[data-nj-point-fields="' + dot.getAttribute("data-nj-point") + '"]');
        if (!fields) {
          return;
        }
        var topInput = fields.querySelector("[data-top]");
        var leftInput = fields.querySelector("[data-left]");
        if (topInput) {
          topInput.value = top;
        }
        if (leftInput) {
          leftInput.value = left;
        }
      }
      function stop() {
        document.removeEventListener("pointermove", move);
        document.removeEventListener("pointerup", stop);
      }
      document.addEventListener("pointermove", move);
      document.addEventListener("pointerup", stop);
    });
    var root = document.querySelector(".nj-studio");
    if (!root) {
      return;
    }
    root.addEventListener("click", function (event) {
      var panel = event.target.closest("[data-panel]");
      if (panel) {
        show(panel.getAttribute("data-panel"));
      }
      var hide = event.target.closest(".nj-prenda-hide");
      if (hide && !window.confirm("La prenda deja de mostrarse en la tienda. Queda en Prendas ocultas por si quieres volver a mostrarla.")) {
        event.preventDefault();
      }
    });
    var initial = (window.location.hash || "#banner").replace("#", "");
    show(initial);
  });
})(jQuery);

/**
 * Colecciones en Mi tienda: looks, banda y el editor.
 */
(function () {
  var njLookRows = null;

  function njLookFold(value) {
    return String(value || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
  }

  function njLookCatalog() {
    if (njLookRows) {
      return njLookRows;
    }
    var node = document.getElementById("nj-look-catalog");
    njLookRows = [];
    try {
      njLookRows = node ? JSON.parse(node.textContent || "[]") : [];
    } catch (error) {
      njLookRows = [];
    }
    if (!Array.isArray(njLookRows)) {
      njLookRows = [];
    }
    return njLookRows;
  }

  function njLookChosen(box) {
    var ids = {};
    Array.prototype.forEach.call(box.querySelectorAll("[data-look-chosen] input"), function (input) {
      if (input.checked) {
        ids[String(input.value)] = true;
      }
    });
    return ids;
  }

  function njLookPaint(box) {
    var input = box.querySelector("[data-look-filter]");
    var list = box.querySelector("[data-look-results]");
    if (!input || !list) {
      return;
    }
    var query = njLookFold(input.value).trim();
    list.innerHTML = "";
    if (!query) {
      list.hidden = true;
      return;
    }
    var selfId = String(box.getAttribute("data-look-self") || "");
    var chosen = njLookChosen(box);
    var hits = njLookCatalog().filter(function (row) {
      var id = String(row.id);
      if (id === selfId || chosen[id]) {
        return false;
      }
      return njLookFold(row.name).indexOf(query) !== -1;
    }).slice(0, 8);
    if (!hits.length) {
      list.hidden = false;
      list.innerHTML = "<p class=\"nj-look-empty\">Ninguna prenda con ese nombre.</p>";
      return;
    }
    hits.forEach(function (row) {
      var button = document.createElement("button");
      button.type = "button";
      button.setAttribute("data-look-pick", String(row.id));
      button.textContent = row.name;
      list.appendChild(button);
    });
    list.hidden = false;
  }

  function njLookAdd(box, id, name) {
    var chosen = box.querySelector("[data-look-chosen]");
    var max = parseInt(box.getAttribute("data-look-max") || "4", 10);
    if (!chosen || chosen.querySelectorAll("input:checked").length >= max) {
      return;
    }
    var label = document.createElement("label");
    label.className = "nj-look-chip";
    var input = document.createElement("input");
    input.type = "checkbox";
    input.name = box.getAttribute("data-look-name") || "look[]";
    input.value = String(id);
    input.checked = true;
    label.appendChild(input);
    label.appendChild(document.createTextNode(" " + name));
    chosen.appendChild(label);
  }

  document.addEventListener("input", function (event) {
    var input = event.target.closest ? event.target.closest("[data-look-filter]") : null;
    if (!input) {
      return;
    }
    njLookPaint(input.closest("[data-look-search]"));
  });

  document.addEventListener("keydown", function (event) {
    var input = event.target.closest ? event.target.closest("[data-look-filter]") : null;
    if (!input || event.key !== "Enter") {
      return;
    }
    event.preventDefault();
    var pick = input.closest("[data-look-search]").querySelector("[data-look-pick]");
    if (pick) {
      pick.click();
    }
  });

  document.addEventListener("click", function (event) {
    var pick = event.target.closest ? event.target.closest("[data-look-pick]") : null;
    if (pick) {
      var box = pick.closest("[data-look-search]");
      njLookAdd(box, pick.getAttribute("data-look-pick"), pick.textContent || "");
      var field = box.querySelector("[data-look-filter]");
      if (field) {
        field.value = "";
      }
      njLookPaint(box);
      return;
    }
    if (!event.target.closest || !event.target.closest("[data-look-search]")) {
      Array.prototype.forEach.call(document.querySelectorAll("[data-look-results]"), function (list) {
        list.hidden = true;
      });
    }
  });

  document.addEventListener("change", function (event) {
    var input = event.target.closest ? event.target.closest(".nj-look-chip input") : null;
    if (input && !input.checked && input.parentNode) {
      input.parentNode.remove();
    }
    var modo = event.target.closest ? event.target.closest("[data-coleccion-modo]") : null;
    if (modo) {
      njColeccionModo(modo.closest("[data-coleccion]"));
    }
    var fondo = event.target.closest ? event.target.closest("[data-banda-fondo]") : null;
    if (fondo) {
      njBandaFondo(fondo.closest("[data-banda-ajuste]"));
    }
  });

  function njBandaFondo(box) {
    if (!box) {
      return;
    }
    var chosen = box.querySelector("[data-banda-fondo]:checked");
    var gradient = chosen && chosen.value === "degradado";
    Array.prototype.forEach.call(box.querySelectorAll("[data-banda-extra]"), function (node) {
      node.hidden = !gradient;
    });
  }

  Array.prototype.forEach.call(document.querySelectorAll("[data-banda-ajuste]"), njBandaFondo);

  function njColeccionModo(box) {
    if (!box) {
      return;
    }
    var chosen = box.querySelector("[data-coleccion-modo]:checked");
    var mode = chosen ? chosen.value : "imagen";
    Array.prototype.forEach.call(box.querySelectorAll("[data-coleccion-panel]"), function (panel) {
      panel.hidden = panel.getAttribute("data-coleccion-panel") !== mode;
    });
  }

  Array.prototype.forEach.call(document.querySelectorAll("[data-coleccion]"), njColeccionModo);

  document.addEventListener("click", function (event) {
    var button = event.target.closest ? event.target.closest("[data-coleccion-open]") : null;
    if (!button) {
      return;
    }
    var id = button.getAttribute("data-coleccion-open");
    var open = document.querySelector('[data-coleccion-editor="' + id + '"]');
    var already = button.classList.contains("is-on");
    Array.prototype.forEach.call(document.querySelectorAll("[data-coleccion-open]"), function (item) {
      item.classList.remove("is-on");
    });
    Array.prototype.forEach.call(document.querySelectorAll("[data-coleccion-editor]"), function (box) {
      box.hidden = true;
    });
    if (!already && open) {
      button.classList.add("is-on");
      open.hidden = false;
    }
  });
})();

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
      var fallback = screens.length ? screens[0].getAttribute("data-screen") : "";
      if (fallback && fallback !== id) {
        show(fallback);
      }
      return;
    }
    Array.prototype.forEach.call(nav, function (button) {
      button.classList.toggle("is-on", button.getAttribute("data-panel") === id);
    });
    if (window.history && window.history.replaceState) {
      window.history.replaceState(null, "", "#" + id);
    }
    var section = document.querySelector("[name=nj_tema_section]");
    if (section) {
      section.value = id;
    }
  }

  function reindexStyles(list) {
    if (!list) {
      return;
    }
    var cards = list.querySelectorAll("[data-nj-style]");
    Array.prototype.forEach.call(cards, function (card, index) {
      card.querySelectorAll("[name]").forEach(function (input) {
        input.name = input.name.replace(/espacios\[\d+\]/, "espacios[" + index + "]");
      });
    });
    var add = document.querySelector("[data-nj-style-add]");
    if (add) {
      add.disabled = cards.length >= 12;
    }
  }

  function addStyleCard(list) {
    if (!list || list.querySelectorAll("[data-nj-style]").length >= 12) {
      return;
    }
    var index = list.querySelectorAll("[data-nj-style]").length;
    var card = document.createElement("div");
    card.className = "nj-style-card";
    card.setAttribute("data-nj-photo", "");
    card.setAttribute("data-nj-style", "");
    card.innerHTML = ''
      + '<div class="nj-style-frame"><img alt="" hidden></div>'
      + '<div class="nj-photo-body">'
      + '<input data-nj-id type="hidden" name="espacios[' + index + '][image]" value="0">'
      + '<input type="hidden" name="espacios[' + index + '][src]" value="">'
      + '<input type="hidden" name="espacios[' + index + '][term]" value="0">'
      + '<label>Nombre <input type="text" name="espacios[' + index + '][label]" value="" placeholder="Nombre de la categoría"></label>'
      + '<div class="nj-style-actions"><button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button><button type="button" class="button" data-nj-style-remove>Eliminar</button></div>'
      + '</div>';
    list.appendChild(card);
    reindexStyles(list);
    var field = card.querySelector("input[type=text]");
    if (field) {
      field.focus();
    }
  }

  function reindexMenu(group) {
    if (!group) {
      return;
    }
    var prefix = group.getAttribute("data-nj-menu-name") || "";
    var rows = group.querySelectorAll("[data-nj-menu-children] .nj-menu-row");
    Array.prototype.forEach.call(rows, function (row, index) {
      var inputs = row.querySelectorAll("input");
      if (inputs[0]) {
        inputs[0].name = prefix + "[children][" + index + "][label]";
      }
      if (inputs[1]) {
        inputs[1].name = prefix + "[children][" + index + "][href]";
      }
    });
  }

  function filterPrendas() {
    var current = document.querySelector("[data-nj-cat-filter].is-on");
    var cat = current ? current.getAttribute("data-nj-cat-filter") : "";
    var stockButton = document.querySelector("[data-nj-stock-filter]");
    var onlyShort = stockButton && stockButton.classList.contains("is-on");
    var visible = 0;
    Array.prototype.forEach.call(document.querySelectorAll("[data-nj-prenda]"), function (tile) {
      var cats = (tile.getAttribute("data-nj-cats") || "").split(",");
      var level = tile.getAttribute("data-nj-level") || "ok";
      var catOk = cat === "" || cats.indexOf(cat) !== -1;
      var stockOk = !onlyShort || level === "low" || level === "out";
      var show = catOk && stockOk;
      tile.hidden = !show;
      if (show) {
        visible += 1;
      }
    });
    var empty = document.querySelector("[data-nj-prenda-empty]");
    if (empty) {
      empty.hidden = visible > 0;
    }
  }

  function lookTitle(box) {
    var name = box.querySelector("input[type=text]");
    var label = name && name.value.trim() ? name.value.trim() : "Sin nombre";
    var title = box.querySelector("[data-nj-point-title]");
    if (title) {
      title.textContent = (Number(box.getAttribute("data-nj-point-fields")) + 1) + ". " + label;
    }
  }

  function openLookPoint(card, id) {
    if (!card) {
      return;
    }
    Array.prototype.forEach.call(card.querySelectorAll("[data-nj-point-fields]"), function (box) {
      box.classList.toggle("is-open", box.getAttribute("data-nj-point-fields") === String(id));
      lookTitle(box);
    });
    Array.prototype.forEach.call(card.querySelectorAll("[data-nj-point]"), function (dot) {
      dot.classList.toggle("is-on", dot.getAttribute("data-nj-point") === String(id));
    });
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
      lookTitle(box);
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
      + '<button type="button" class="nj-look-summary" data-nj-point-toggle><span data-nj-point-title>Punto</span></button>'
      + '<div class="nj-look-body">'
      + '<input data-top type="hidden" name="' + base + '[top]" value="50%">'
      + '<input data-left type="hidden" name="' + base + '[left]" value="50%">'
      + '<label>Nombre <input type="text" name="' + base + '[label]" value=""></label>'
      + '<label>Precio <input type="text" name="' + base + '[price]" value="" placeholder="$ 32.000"></label>'
      + '<label>Enlace <input type="url" name="' + base + '[href]" value="" placeholder="https://"></label>'
      + '<button type="button" class="button" data-nj-point-remove>Quitar punto</button>'
      + '</div>';
    list.appendChild(box);
    reindexLooks(card);
    openLookPoint(card, box.getAttribute("data-nj-point-fields"));
  }

  function attrBox(node) {
    return node ? node.closest("[data-nj-attrs]") : null;
  }

  function colorNames(box) {
    var names = [];
    if (!box) {
      return names;
    }
    Array.prototype.forEach.call(box.querySelectorAll("[data-nj-attr=color] [data-nj-attr-row] input[type=text]"), function (input) {
      if (input.value.trim() !== "") {
        names.push(input.value.trim());
      }
    });
    return names;
  }

  function stockCell(color) {
    var label = document.createElement("label");
    label.className = "nj-celda";
    label.setAttribute("data-nj-stock", "");
    if (color) {
      var span = document.createElement("span");
      span.textContent = color;
      label.appendChild(span);
    }
    var input = document.createElement("input");
    input.type = "number";
    input.min = "0";
    input.max = "999";
    input.name = "talla_celda[]";
    input.value = "1";
    input.setAttribute("aria-label", "Existencias");
    label.appendChild(input);
    return label;
  }

  function rebuildStocks(box) {
    if (!box) {
      return;
    }
    var colors = colorNames(box);
    var labels = colors.length ? colors : [""];
    Array.prototype.forEach.call(box.querySelectorAll("[data-nj-attr=talla] [data-nj-attr-row]"), function (row) {
      var holder = row.querySelector("[data-nj-stocks]");
      if (!holder) {
        return;
      }
      var previous = [];
      Array.prototype.forEach.call(holder.querySelectorAll("input"), function (input) {
        previous.push(input.value);
      });
      holder.innerHTML = "";
      labels.forEach(function (color, index) {
        var cell = stockCell(color);
        var input = cell.querySelector("input");
        if (previous[index] !== undefined) {
          input.value = previous[index];
        }
        holder.appendChild(cell);
      });
    });
  }

  function addAttrRow(box, kind) {
    if (!box) {
      return;
    }
    var list = box.querySelector("[data-nj-attr=" + kind + "] [data-nj-attr-list]");
    if (!list || list.querySelectorAll("[data-nj-attr-row]").length >= 12) {
      return;
    }
    var row = document.createElement("div");
    row.className = "nj-attr-row";
    row.setAttribute("data-nj-attr-row", "");
    var input = document.createElement("input");
    input.type = "text";
    input.name = kind === "color" ? "color_nombre[]" : "talla_nombre[]";
    input.placeholder = kind === "color" ? "Color" : "Talla";
    row.appendChild(input);
    if (kind === "talla") {
      var stocks = document.createElement("div");
      stocks.setAttribute("data-nj-stocks", "");
      row.appendChild(stocks);
    }
    var button = document.createElement("button");
    button.type = "button";
    button.className = "button";
    button.setAttribute("data-nj-attr-remove", "");
    button.textContent = "Quitar";
    row.appendChild(button);
    list.appendChild(row);
    rebuildStocks(box);
    input.focus();
  }

  function appendGallery(list, id, url) {
    if (!list || list.querySelectorAll("[data-nj-gallery-item]").length >= 8) {
      return;
    }
    var item = document.createElement("span");
    item.className = "nj-gallery-item";
    item.setAttribute("data-nj-gallery-item", "");
    var img = document.createElement("img");
    img.alt = "";
    img.src = url || "";
    var input = document.createElement("input");
    input.type = "hidden";
    input.name = "galeria[]";
    input.value = String(id || 0);
    var button = document.createElement("button");
    button.type = "button";
    button.setAttribute("data-nj-gallery-remove", "");
    button.setAttribute("aria-label", "Quitar foto");
    button.textContent = "×";
    item.appendChild(img);
    item.appendChild(input);
    item.appendChild(button);
    list.appendChild(item);
  }

  function addGallery(list) {
    if (!list || !window.wp || !window.wp.media) {
      return;
    }
    var frame = window.wp.media({
      title: "Fotos de la prenda",
      button: { text: "Usar estas fotos" },
      library: { type: "image" },
      multiple: true
    });
    frame.on("select", function () {
      frame.state().get("selection").each(function (attachment) {
        var file = attachment.toJSON();
        appendGallery(list, file.id, file.sizes && file.sizes.thumbnail ? file.sizes.thumbnail.url : file.url);
      });
    });
    frame.open();
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
      var catFilter = event.target.closest("[data-nj-cat-filter]");
      if (catFilter) {
        Array.prototype.forEach.call(document.querySelectorAll("[data-nj-cat-filter]"), function (button) {
          button.classList.toggle("is-on", button === catFilter);
        });
        filterPrendas();
      }
      var stockFilter = event.target.closest("[data-nj-stock-filter]");
      if (stockFilter) {
        stockFilter.classList.toggle("is-on");
        filterPrendas();
      }
      var opener = event.target.closest("[data-nj-open]");
      if (opener) {
        var dialog = document.getElementById(opener.getAttribute("data-nj-open"));
        if (dialog && dialog.showModal) {
          dialog.showModal();
        }
      }
      var closer = event.target.closest("[data-nj-close]");
      if (closer) {
        var openDialog = closer.closest("dialog");
        if (openDialog) {
          openDialog.close();
        }
      }
      if (event.target.matches && event.target.matches("dialog.nj-modal")) {
        event.target.close();
      }
    });
    document.addEventListener("input", function (event) {
      var colorInput = event.target.closest("[data-nj-attr=color] input[type=text]");
      if (colorInput) {
        rebuildStocks(attrBox(colorInput));
      }
    });
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
      var addAttr = event.target.closest("[data-nj-attr-add]");
      if (addAttr) {
        addAttrRow(attrBox(addAttr), addAttr.getAttribute("data-nj-attr-add"));
      }
      var removeAttr = event.target.closest("[data-nj-attr-remove]");
      if (removeAttr) {
        var attrRow = removeAttr.closest("[data-nj-attr-row]");
        var attrs = attrBox(removeAttr);
        if (attrRow) {
          attrRow.remove();
        }
        rebuildStocks(attrs);
      }
      var addGalleryButton = event.target.closest("[data-nj-gallery-add]");
      if (addGalleryButton) {
        var block = addGalleryButton.closest(".nj-gallery-block");
        addGallery(block ? block.querySelector("[data-nj-gallery]") : null);
      }
      var removeGallery = event.target.closest("[data-nj-gallery-remove]");
      if (removeGallery) {
        var photoItem = removeGallery.closest("[data-nj-gallery-item]");
        if (photoItem) {
          photoItem.remove();
        }
      }
      var addStyle = event.target.closest("[data-nj-style-add]");
      if (addStyle) {
        addStyleCard(document.querySelector("[data-nj-style-list]"));
      }
      var removeStyle = event.target.closest("[data-nj-style-remove]");
      if (removeStyle) {
        var card = removeStyle.closest("[data-nj-style]");
        var name = "";
        if (card) {
          var nameInput = card.querySelector("input[type=text]");
          name = nameInput ? nameInput.value.trim() : "";
        }
        var message = name ? "¿Eliminar la categoría " + name + "? También se borra de la tienda al guardar." : "¿Eliminar esta categoría? También se borra de la tienda al guardar.";
        if (card && window.confirm(message)) {
          card.remove();
          reindexStyles(document.querySelector("[data-nj-style-list]"));
        }
      }
      var addMenu = event.target.closest("[data-nj-menu-add]");
      if (addMenu) {
        var group = addMenu.closest("[data-nj-menu-group]");
        var list = group ? group.querySelector("[data-nj-menu-children]") : null;
        if (list && list.children.length < 8) {
          var row = document.createElement("div");
          row.className = "nj-menu-row";
          row.innerHTML = '<input type="text" placeholder="Nombre"><input type="url" placeholder="https://"><button type="button" class="button" data-nj-menu-remove>Quitar</button>';
          list.appendChild(row);
          reindexMenu(group);
        }
      }
      var removeMenu = event.target.closest("[data-nj-menu-remove]");
      if (removeMenu) {
        var menuGroup = removeMenu.closest("[data-nj-menu-group]");
        var menuRow = removeMenu.closest(".nj-menu-row");
        if (menuRow) {
          menuRow.remove();
        }
        reindexMenu(menuGroup);
      }
      var pointToggle = event.target.closest("[data-nj-point-toggle]");
      if (pointToggle) {
        var pointBox = pointToggle.closest("[data-nj-point-fields]");
        var pointCard = pointToggle.closest("[data-nj-look]");
        if (pointBox && pointCard) {
          if (pointBox.classList.contains("is-open")) {
            pointBox.classList.remove("is-open");
            Array.prototype.forEach.call(pointCard.querySelectorAll("[data-nj-point]"), function (dot) {
              dot.classList.remove("is-on");
            });
          } else {
            openLookPoint(pointCard, pointBox.getAttribute("data-nj-point-fields"));
          }
        }
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
      var startX = event.clientX;
      var startY = event.clientY;
      var moved = false;
      function move(ev) {
        if (Math.abs(ev.clientX - startX) > 3 || Math.abs(ev.clientY - startY) > 3) {
          moved = true;
        }
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
        if (!moved) {
          openLookPoint(card, dot.getAttribute("data-nj-point"));
        }
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
    var initial = (window.location.hash || "").replace("#", "");
    if (!initial) {
      var first = root.querySelector("[data-screen]");
      initial = first ? first.getAttribute("data-screen") : "banner";
    }
    show(initial);
  });
})(jQuery);

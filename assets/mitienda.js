(function ($) {
  function show(id) {
    if (id === "menus") {
      id = "encabezado";
    }
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

  function reindexPills(list) {
    if (!list) {
      return;
    }
    var cards = list.querySelectorAll("[data-nj-pill]");
    Array.prototype.forEach.call(cards, function (card, index) {
      card.querySelectorAll("[name]").forEach(function (input) {
        input.name = input.name.replace(/espacios\[\d+\]/, "espacios[" + index + "]");
      });
    });
    var add = document.querySelector("[data-nj-pill-add]");
    if (add) {
      add.disabled = cards.length >= 24;
    }
  }

  function addPill(list) {
    if (!list || list.querySelectorAll("[data-nj-pill]").length >= 24) {
      return;
    }
    var index = list.querySelectorAll("[data-nj-pill]").length;
    var card = document.createElement("div");
    card.className = "nj-pill-row";
    card.setAttribute("data-nj-pill", "");
    card.innerHTML = ''
      + '<label>Nombre <input type="text" name="espacios[' + index + '][label]" value="" placeholder="Nombre de la categoría"></label>'
      + '<input type="hidden" name="espacios[' + index + '][term]" value="0">'
      + '<p class="nj-prenda-actions"><button type="button" class="button" data-nj-pill-up>Subir</button><button type="button" class="button" data-nj-pill-down>Bajar</button><button type="button" class="button" data-nj-pill-remove>Quitar</button></p>';
    list.appendChild(card);
    reindexPills(list);
    var field = card.querySelector("input[type=text]");
    if (field) {
      field.focus();
    }
  }

  function moveCard(card, direction, reindex) {
    if (!card || !card.parentNode) {
      return;
    }
    var sibling = direction < 0 ? card.previousElementSibling : card.nextElementSibling;
    if (!sibling) {
      return;
    }
    if (direction < 0) {
      card.parentNode.insertBefore(card, sibling);
    } else {
      card.parentNode.insertBefore(sibling, card);
    }
    reindex(card.parentNode);
  }

  function movePill(card, direction) {
    if (!card || !card.parentNode) {
      return;
    }
    moveCard(card, direction, reindexPills);
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
      + '<p class="nj-size"><span>400 × 400 px</span></p>'
      + '<label>Nombre <input type="text" name="espacios[' + index + '][label]" value="" placeholder="Nombre de la categoría"></label>'
      + '<div class="nj-style-actions"><button type="button" class="button" data-nj-style-up>Subir</button><button type="button" class="button" data-nj-style-down>Bajar</button><button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button><button type="button" class="button" data-nj-style-remove>Eliminar</button></div>'
      + '</div>';
    list.appendChild(card);
    reindexStyles(list);
    var field = card.querySelector("input[type=text]");
    if (field) {
      field.focus();
    }
  }

  function reindexCuentos(list) {
    if (!list) {
      return;
    }
    var cards = list.querySelectorAll("[data-nj-cuento]");
    Array.prototype.forEach.call(cards, function (card, index) {
      card.querySelectorAll("[name]").forEach(function (input) {
        input.name = input.name.replace(/espacios\[\d+\]/, "espacios[" + index + "]");
      });
    });
    var add = document.querySelector("[data-nj-cuento-add]");
    if (add) {
      add.disabled = cards.length >= 8;
    }
  }

  function addCuento(list) {
    if (!list || list.querySelectorAll("[data-nj-cuento]").length >= 8) {
      return;
    }
    var index = list.querySelectorAll("[data-nj-cuento]").length;
    var card = document.createElement("div");
    card.className = "nj-photo nj-photo--wide";
    card.setAttribute("data-nj-photo", "");
    card.setAttribute("data-nj-cuento", "");
    card.innerHTML = ''
      + '<img alt="" hidden>'
      + '<div class="nj-photo-body">'
      + '<input data-nj-id type="hidden" name="espacios[' + index + '][image]" value="0">'
      + '<input type="hidden" name="espacios[' + index + '][src]" value="">'
      + '<input type="hidden" name="espacios[' + index + '][alt]" value="">'
      + '<p class="nj-size"><span>1920 × 560 px</span></p>'
      + '<p class="nj-prenda-actions"><button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button><button type="button" class="button" data-nj-cuento-remove>Quitar</button></p>'
      + '<label>Texto pequeño <input type="text" name="espacios[' + index + '][kicker]" value="" placeholder="Cuentos"></label>'
      + '<label>Título <textarea name="espacios[' + index + '][title]" rows="2"></textarea></label>'
      + '</div>';
    list.appendChild(card);
    reindexCuentos(list);
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
    var label = group.querySelector("[data-nj-menu-label]");
    if (label) {
      label.name = prefix + "[label]";
    }
    var parent = group.querySelector(".nj-menu-parent");
    if (parent) {
      var href = parent.querySelector("input[type=url]");
      if (href) {
        href.name = prefix + "[href]";
      }
    }
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
    syncMenuCount(group);
  }

  function reindexDepts() {
    var list = document.querySelector("[data-nj-dept-list]");
    var bar = document.querySelector("[data-nj-dept-bar]");
    if (!list) {
      return;
    }
    var groups = list.querySelectorAll("[data-nj-menu-dept]");
    Array.prototype.forEach.call(groups, function (group, index) {
      group.setAttribute("data-nj-menu-key", "d-" + index);
      group.setAttribute("data-nj-menu-name", "menu[departments][" + index + "]");
      reindexMenu(group);
    });
    if (!bar) {
      return;
    }
    bar.innerHTML = "";
    Array.prototype.forEach.call(groups, function (group, index) {
      var button = document.createElement("button");
      var title = group.querySelector("[data-nj-menu-title]");
      button.type = "button";
      button.setAttribute("data-nj-menu-jump", "d-" + index);
      button.setAttribute("aria-expanded", group.classList.contains("is-open") ? "true" : "false");
      button.textContent = title && title.textContent.trim() ? title.textContent.trim() : "Menú";
      if (group.classList.contains("is-open")) {
        button.classList.add("is-on");
      }
      bar.appendChild(button);
    });
  }

  function deptGroupHtml(index) {
    var name = "menu[departments][" + index + "]";
    var key = "d-" + index;
    return ''
      + '<div class="nj-menu-group" data-nj-menu-group data-nj-menu-dept data-nj-menu-key="' + key + '" data-nj-menu-name="' + name + '">'
      + '<button type="button" class="nj-menu-summary" data-nj-menu-toggle aria-expanded="false"><span data-nj-menu-title>Nuevo menú</span><span class="nj-menu-count" data-nj-menu-count>0 enlaces</span></button>'
      + '<div class="nj-menu-body">'
      + '<div class="nj-menu-parent">'
      + '<label>Nombre en la barra <input type="text" data-nj-menu-label name="' + name + '[label]" value=""></label>'
      + '<label>Enlace <input type="url" name="' + name + '[href]" value="" placeholder="https://"></label>'
      + '</div>'
      + '<p class="nj-menu-subhead">Enlaces que se abren al tocarlo</p>'
      + '<div data-nj-menu-children></div>'
      + '<p><button type="button" class="button" data-nj-menu-add>Agregar enlace</button></p>'
      + '<p><button type="button" class="button" data-nj-dept-remove>Quitar este menú</button></p>'
      + '</div></div>';
  }

  function reindexLinks(list) {
    if (!list) {
      return;
    }
    var name = list.getAttribute("data-nj-link-name") || "";
    var rows = list.querySelectorAll(".nj-menu-row");
    Array.prototype.forEach.call(rows, function (row, index) {
      var inputs = row.querySelectorAll("input");
      if (inputs[0]) {
        inputs[0].name = name + "[" + index + "][label]";
      }
      if (inputs[1]) {
        inputs[1].name = name + "[" + index + "][href]";
      }
    });
  }

  function reindexColumns(list) {
    if (!list) {
      return;
    }
    var columns = list.querySelectorAll("[data-nj-foot-column]");
    Array.prototype.forEach.call(columns, function (column, index) {
      column.setAttribute("data-nj-menu-key", "col-" + index);
      var title = column.querySelector("[data-nj-column-title]");
      if (title) {
        title.name = "footer[columns][" + index + "][title]";
      }
      var links = column.querySelector("[data-nj-link-list]");
      if (links) {
        links.setAttribute("data-nj-link-name", "footer[columns][" + index + "][links]");
        reindexLinks(links);
      }
    });
  }

  function columnHtml() {
    return ''
      + '<div class="nj-menu-group" data-nj-menu-group data-nj-foot-column data-nj-menu-key="col-0">'
      + '<button type="button" class="nj-menu-summary" data-nj-menu-toggle aria-expanded="false"><span data-nj-menu-title>Nueva columna</span><span class="nj-menu-count">Columna</span></button>'
      + '<div class="nj-menu-body">'
      + '<label>Título <input type="text" data-nj-column-title name="footer[columns][0][title]" value=""></label>'
      + '<div data-nj-link-list data-nj-link-name="footer[columns][0][links]" data-nj-link-max="8"></div>'
      + '<p><button type="button" class="button" data-nj-link-add>Agregar enlace</button></p>'
      + '<p><button type="button" class="button" data-nj-column-remove>Quitar columna</button></p>'
      + '</div></div>';
  }

  function reindexHelp(list) {
    if (!list) {
      return;
    }
    var name = list.getAttribute("data-nj-help-name") || "";
    var kind = list.getAttribute("data-nj-help-kind") || "line";
    var keys = (list.getAttribute("data-nj-help-keys") || "q,a").split(",");
    var rows = list.querySelectorAll(".nj-help-line, .nj-help-row");
    Array.prototype.forEach.call(rows, function (row, index) {
      var inputs = row.querySelectorAll("input, textarea");
      if (kind === "line") {
        if (inputs[0]) {
          inputs[0].name = name + "[" + index + "]";
        }
        return;
      }
      if (inputs[0]) {
        inputs[0].name = name + "[" + index + "][" + keys[0] + "]";
      }
      if (inputs[1]) {
        inputs[1].name = name + "[" + index + "][" + keys[1] + "]";
      }
    });
  }

  function helpRowHtml(list) {
    var kind = list.getAttribute("data-nj-help-kind") || "line";
    var area = list.getAttribute("data-nj-help-area") === "1";
    if (kind === "line") {
      return '<div class="nj-menu-row nj-help-line"><input type="text" value=""><button type="button" class="button" data-nj-help-remove>Quitar</button></div>';
    }
    var second = area ? '<textarea rows="3"></textarea>' : '<input type="text" value="">';
    return '<div class="nj-help-row"><input type="text" value="">' + second + '<button type="button" class="button" data-nj-help-remove>Quitar</button></div>';
  }

  function syncMenuTitle(group) {
    if (!group) {
      return;
    }
    var input = group.querySelector("[data-nj-menu-label]");
    var title = group.querySelector("[data-nj-menu-title]");
    var text = input && input.value.trim() ? input.value.trim() : "Menú";
    if (title) {
      title.textContent = text;
    }
    var key = group.getAttribute("data-nj-menu-key");
    var jump = key ? document.querySelector('[data-nj-menu-jump="' + key + '"]') : null;
    if (jump) {
      jump.textContent = text;
    }
  }

  function syncMenuCount(group) {
    if (!group) {
      return;
    }
    var list = group.querySelector("[data-nj-menu-children]");
    var slot = group.querySelector("[data-nj-menu-count]");
    if (!list || !slot) {
      return;
    }
    var total = list.querySelectorAll(".nj-menu-row").length;
    slot.textContent = total === 1 ? "1 enlace" : total + " enlaces";
  }

  function toggleMenu(key) {
    if (!key) {
      return;
    }
    var current = document.querySelector('[data-nj-menu-group][data-nj-menu-key="' + key + '"].is-open');
    var next = current ? "" : key;
    Array.prototype.forEach.call(document.querySelectorAll("[data-nj-menu-group]"), function (group) {
      var open = group.getAttribute("data-nj-menu-key") === next;
      group.classList.toggle("is-open", open);
      var summary = group.querySelector("[data-nj-menu-toggle]");
      if (summary) {
        summary.setAttribute("aria-expanded", open ? "true" : "false");
      }
    });
    Array.prototype.forEach.call(document.querySelectorAll("[data-nj-menu-jump]"), function (button) {
      var open = button.getAttribute("data-nj-menu-jump") === next;
      button.classList.toggle("is-on", open);
      button.setAttribute("aria-expanded", open ? "true" : "false");
    });
    if (next) {
      var opened = document.querySelector('[data-nj-menu-group][data-nj-menu-key="' + next + '"]');
      if (opened && opened.scrollIntoView) {
        opened.scrollIntoView({ block: "nearest" });
      }
    }
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

  function reindexAllLooks() {
    var cards = document.querySelectorAll("[data-nj-look]");
    Array.prototype.forEach.call(cards, function (card, lookIndex) {
      var title = card.querySelector("[data-nj-look-title]");
      if (title) {
        title.textContent = "Look " + (lookIndex + 1);
      }
      card.querySelectorAll("[name]").forEach(function (input) {
        input.name = input.name.replace(/espacios\[\d+\]/, "espacios[" + lookIndex + "]");
      });
      reindexLooks(card);
    });
    var add = document.querySelector("[data-nj-look-add]");
    if (add) {
      add.disabled = cards.length >= 8;
    }
  }

  function addLook(list) {
    if (!list || list.querySelectorAll("[data-nj-look]").length >= 8) {
      return;
    }
    var index = list.querySelectorAll("[data-nj-look]").length;
    var card = document.createElement("div");
    card.className = "nj-look";
    card.setAttribute("data-nj-look", "");
    card.innerHTML = ''
      + '<strong data-nj-look-title>Look ' + (index + 1) + '</strong>'
      + '<div class="nj-look-stage" data-nj-stage data-nj-photo>'
      + '<img alt="" hidden>'
      + '<input data-nj-id type="hidden" name="espacios[' + index + '][image]" value="0">'
      + '<input type="hidden" name="espacios[' + index + '][src]" value="">'
      + '<input type="hidden" name="espacios[' + index + '][alt]" value="">'
      + '</div>'
      + '<p class="nj-size"><span>900 × 1200 px</span></p>'
      + '<p class="nj-prenda-actions"><button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button><button type="button" class="button" data-nj-look-remove>Quitar</button></p>'
      + '<div data-nj-point-list></div>'
      + '<p><button type="button" class="button" data-nj-point-add>Agregar punto</button></p>';
    list.appendChild(card);
    reindexAllLooks();
  }

  function reindexPromos(list) {
    if (!list) {
      return;
    }
    var cards = list.querySelectorAll("[data-nj-promo]");
    Array.prototype.forEach.call(cards, function (card, index) {
      card.querySelectorAll("[name]").forEach(function (input) {
        input.name = input.name.replace(/espacios\[\d+\]/, "espacios[" + index + "]");
      });
      var kind = card.getAttribute("data-nj-kind") || "";
      var copy = kind ? document.querySelector('[data-nj-promo-copy][data-nj-kind="' + kind + '"]') : null;
      if (copy) {
        copy.querySelectorAll("[name]").forEach(function (input) {
          input.name = input.name.replace(/espacios\[\d+\]/, "espacios[" + index + "]");
        });
      }
    });
    var add = document.querySelector("[data-nj-promo-add]");
    if (add) {
      add.disabled = cards.length >= 6;
    }
  }

  function addPromo(list) {
    if (!list || list.querySelectorAll("[data-nj-promo]").length >= 6) {
      return;
    }
    var index = list.querySelectorAll("[data-nj-promo]").length;
    var card = document.createElement("div");
    card.className = "nj-photo";
    card.setAttribute("data-nj-photo", "");
    card.setAttribute("data-nj-promo", "");
    card.innerHTML = ''
      + '<img alt="" hidden>'
      + '<div class="nj-photo-body">'
      + '<strong>Campaña</strong>'
      + '<p class="nj-size"><span>1200 × 900 px</span></p>'
      + '<input type="hidden" name="espacios[' + index + '][kind]" value="photo">'
      + '<input data-nj-id type="hidden" name="espacios[' + index + '][image]" value="0">'
      + '<input type="hidden" name="espacios[' + index + '][src]" value="">'
      + '<input type="hidden" name="espacios[' + index + '][alt]" value="">'
      + '<label>Nombre <input type="text" name="espacios[' + index + '][label]" value="" placeholder="Nombre de la campaña"></label>'
      + '<label class="nj-promo-alto">Alto del contenedor <input type="range" min="240" max="1200" step="10" name="espacios[' + index + '][h]" value="460"><output>460 px</output></label>'
      + '<label class="nj-promo-html">HTML <textarea name="espacios[' + index + '][html]" rows="6" spellcheck="false"></textarea></label>'
      + '<p class="nj-prenda-actions"><button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button><button type="button" class="button" data-nj-promo-remove>Quitar</button></p>'
      + '</div>';
    list.appendChild(card);
    reindexPromos(list);
    var field = card.querySelector("input[type=text]");
    if (field) {
      field.focus();
    }
  }

  function reindexFranjas(list) {
    if (!list) {
      return;
    }
    var cards = list.querySelectorAll("[data-nj-franja]");
    Array.prototype.forEach.call(cards, function (card, index) {
      card.querySelectorAll("[name]").forEach(function (input) {
        input.name = input.name.replace(/franjas\[\d+\]/, "franjas[" + index + "]");
      });
      var remove = card.querySelector("[data-nj-franja-remove]");
      if (remove) {
        remove.hidden = cards.length < 2;
      }
    });
    var add = document.querySelector("[data-nj-franja-add]");
    if (add) {
      add.disabled = cards.length >= 12;
    }
  }

  function addFranja(list) {
    if (!list || list.querySelectorAll("[data-nj-franja]").length >= 12) {
      return;
    }
    var current = list.querySelector("[data-nj-franja]");
    if (!current) {
      return;
    }
    var card = current.cloneNode(true);
    card.querySelectorAll("input, textarea").forEach(function (input) {
      if (input.type === "checkbox") {
        input.checked = input.value === "0";
        return;
      }
      if (input.type === "hidden") {
        input.value = "0";
      } else {
        input.value = "";
      }
    });
    var choice = card.querySelector(".nj-franja-places input");
    var img = card.querySelector("img");
    if (img) {
      img.removeAttribute("src");
      img.hidden = true;
    }
    list.appendChild(card);
    reindexFranjas(list);
    if (choice) {
      choice.focus();
    }
  }

  function attrBox(node) {
    return node ? node.closest("[data-nj-attrs]") : null;
  }

  function reindexPiezas(box) {
    if (!box) {
      return;
    }
    Array.prototype.forEach.call(box.querySelectorAll("[data-nj-color]"), function (block, colorIndex) {
      var colorInput = block.querySelector("[data-nj-color-name]");
      if (colorInput) {
        colorInput.name = "pieza[" + colorIndex + "][color]";
      }
      Array.prototype.forEach.call(block.querySelectorAll("[data-nj-size]"), function (row, sizeIndex) {
        var fields = row.querySelectorAll("input");
        if (fields[0]) {
          fields[0].name = "pieza[" + colorIndex + "][tallas][" + sizeIndex + "][nombre]";
        }
        if (fields[1]) {
          fields[1].name = "pieza[" + colorIndex + "][tallas][" + sizeIndex + "][qty]";
        }
      });
    });
  }

  function sizeRowHtml() {
    return '<div class="nj-attr-row" data-nj-size><input type="text" placeholder="Talla"><label class="nj-celda">Existencias<input type="number" min="0" max="999" value="1"></label><button type="button" class="button" data-nj-size-remove>Quitar</button></div>';
  }

  function addSizeRow(block) {
    var list = block ? block.querySelector("[data-nj-sizes]") : null;
    if (!list || list.querySelectorAll("[data-nj-size]").length >= 12) {
      return;
    }
    var wrap = document.createElement("div");
    wrap.innerHTML = sizeRowHtml();
    var row = wrap.firstElementChild;
    list.appendChild(row);
    reindexPiezas(attrBox(block));
    var field = row.querySelector("input");
    if (field) {
      field.focus();
    }
  }

  function addColorBlock(box) {
    var list = box ? box.querySelector("[data-nj-colors]") : null;
    if (!list || list.querySelectorAll("[data-nj-color]").length >= 8) {
      return;
    }
    var block = document.createElement("div");
    block.className = "nj-color-block";
    block.setAttribute("data-nj-color", "");
    block.innerHTML = '<div class="nj-color-head"><label>Color<input type="text" data-nj-color-name placeholder="Rosado"></label><button type="button" class="button" data-nj-color-remove>Quitar color</button></div><div data-nj-sizes></div><button type="button" class="button" data-nj-size-add>Agregar talla</button>';
    list.appendChild(block);
    addSizeRow(block);
    var field = block.querySelector("[data-nj-color-name]");
    if (field) {
      field.focus();
    }
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
    reindexFranjas(document.querySelector("[data-nj-franja-list]"));
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
        if (dialog) {
          if (dialog.show) {
            dialog.show();
          } else {
            dialog.setAttribute("open", "");
          }
          document.body.classList.add("nj-modal-open");
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
      if (event.target === document.body && document.body.classList.contains("nj-modal-open") && !event.target.closest(".media-modal")) {
        var front = document.querySelector("dialog.nj-modal[open]");
        if (front) {
          front.close();
        }
      }
    });
    document.querySelectorAll("dialog.nj-modal").forEach(function (dialog) {
      dialog.addEventListener("close", function () {
        if (!document.querySelector("dialog.nj-modal[open]")) {
          document.body.classList.remove("nj-modal-open");
        }
      });
    });
    document.addEventListener("keydown", function (event) {
      if (event.key !== "Escape" || document.querySelector(".media-modal")) {
        return;
      }
      var front = document.querySelector("dialog.nj-modal[open]");
      if (front) {
        front.close();
      }
    });
    document.addEventListener("input", function (event) {
      var colorInput = event.target.closest("[data-nj-color-name]");
      if (colorInput) {
        reindexPiezas(attrBox(colorInput));
      }
      var menuLabel = event.target.closest("[data-nj-menu-label]");
      if (menuLabel) {
        syncMenuTitle(menuLabel.closest("[data-nj-menu-group]"));
      }
      var columnTitle = event.target.closest("[data-nj-column-title]");
      if (columnTitle) {
        var columnCard = columnTitle.closest("[data-nj-foot-column]");
        var columnName = columnCard ? columnCard.querySelector("[data-nj-menu-title]") : null;
        if (columnName) {
          columnName.textContent = columnTitle.value.trim() ? columnTitle.value.trim() : "Columna";
        }
      }
      var promoName = event.target.closest("[data-nj-promo] input[type=text]");
      if (promoName) {
        var promoTitle = promoName.closest("[data-nj-promo]").querySelector("strong");
        if (promoTitle) {
          promoTitle.textContent = promoName.value.trim() || "Campaña";
        }
      }
    });
    function openCatBlock(key) {
      var blocks = document.querySelectorAll("[data-nj-cat-block]");
      Array.prototype.forEach.call(blocks, function (item) {
        item.classList.toggle("is-open", item.getAttribute("data-nj-cat-block") === key);
      });
      Array.prototype.forEach.call(document.querySelectorAll("[data-nj-cat-jump]"), function (button) {
        var on = button.getAttribute("data-nj-cat-jump") === key;
        button.classList.toggle("is-on", on);
        button.setAttribute("aria-selected", on ? "true" : "false");
      });
    }
    function njTestimonioIndex() {
      Array.prototype.forEach.call(document.querySelectorAll("[data-testimonio]"), function (row, index) {
        Array.prototype.forEach.call(row.querySelectorAll("[name]"), function (field) {
          field.name = field.name.replace(/items\[[^\]]+\]/, "items[" + index + "]");
        });
      });
    }
    var testimonioForm = document.querySelector(".nj-testimonio-form");
    if (testimonioForm) {
      testimonioForm.addEventListener("submit", njTestimonioIndex);
    }
    document.addEventListener("click", function (event) {
      var catJump = event.target.closest("[data-nj-cat-jump]");
      if (catJump) {
        openCatBlock(catJump.getAttribute("data-nj-cat-jump"));
      }
      var catToggle = event.target.closest("[data-nj-cat-toggle]");
      if (catToggle) {
        var catBlock = catToggle.closest("[data-nj-cat-block]");
        var catKey = catBlock ? catBlock.getAttribute("data-nj-cat-block") : "";
        if (catBlock && catBlock.classList.contains("is-open")) {
          catBlock.classList.remove("is-open");
          catToggle.setAttribute("aria-expanded", "false");
        } else if (catKey) {
          openCatBlock(catKey);
        }
      }
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
      var testimonioAdd = event.target.closest("[data-testimonio-add]");
      if (testimonioAdd) {
        var testimonioLista = document.querySelector("[data-testimonio-lista]");
        var testimonioTpl = document.getElementById("nj-testimonio-nuevo");
        if (testimonioLista && testimonioTpl) {
          testimonioLista.appendChild(testimonioTpl.content.cloneNode(true));
          njTestimonioIndex();
        }
      }
      var testimonioRemove = event.target.closest("[data-testimonio-remove]");
      if (testimonioRemove) {
        var testimonioRow = testimonioRemove.closest("[data-testimonio]");
        if (testimonioRow) {
          testimonioRow.remove();
          njTestimonioIndex();
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
      var addColor = event.target.closest("[data-nj-color-add]");
      if (addColor) {
        addColorBlock(attrBox(addColor));
      }
      var removeColor = event.target.closest("[data-nj-color-remove]");
      if (removeColor) {
        var colorBlock = removeColor.closest("[data-nj-color]");
        var colorBox = attrBox(removeColor);
        if (colorBlock && colorBox && colorBox.querySelectorAll("[data-nj-color]").length > 1) {
          colorBlock.remove();
          reindexPiezas(colorBox);
        }
      }
      var addSize = event.target.closest("[data-nj-size-add]");
      if (addSize) {
        addSizeRow(addSize.closest("[data-nj-color]"));
      }
      var removeSize = event.target.closest("[data-nj-size-remove]");
      if (removeSize) {
        var sizeRow = removeSize.closest("[data-nj-size]");
        var sizeBox = attrBox(removeSize);
        if (sizeRow) {
          sizeRow.remove();
        }
        reindexPiezas(sizeBox);
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
      var addCuentoButton = event.target.closest("[data-nj-cuento-add]");
      if (addCuentoButton) {
        addCuento(document.querySelector("[data-nj-cuento-list]"));
      }
      var removeCuento = event.target.closest("[data-nj-cuento-remove]");
      if (removeCuento) {
        var cuento = removeCuento.closest("[data-nj-cuento]");
        if (cuento && window.confirm("¿Quitar este cuento del carrusel?")) {
          cuento.remove();
          reindexCuentos(document.querySelector("[data-nj-cuento-list]"));
        }
      }
      var addLookButton = event.target.closest("[data-nj-look-add]");
      if (addLookButton) {
        addLook(document.querySelector("[data-nj-look-list]"));
      }
      var removeLook = event.target.closest("[data-nj-look-remove]");
      if (removeLook) {
        var lookCard = removeLook.closest("[data-nj-look]");
        if (lookCard && window.confirm("¿Quitar este look?")) {
          lookCard.remove();
          reindexAllLooks();
        }
      }
      var addFranjaButton = event.target.closest("[data-nj-franja-add]");
      if (addFranjaButton) {
        addFranja(document.querySelector("[data-nj-franja-list]"));
      }
      var removeFranja = event.target.closest("[data-nj-franja-remove]");
      if (removeFranja) {
        var franja = removeFranja.closest("[data-nj-franja]");
        var franjaList = franja ? franja.parentNode : null;
        if (franja && franjaList && franjaList.querySelectorAll("[data-nj-franja]").length > 1 && window.confirm("¿Quitar esta franja?")) {
          franja.remove();
          reindexFranjas(franjaList);
        }
      }
      var addPromoButton = event.target.closest("[data-nj-promo-add]");
      if (addPromoButton) {
        addPromo(document.querySelector("[data-nj-promo-list]"));
      }
      var removePromo = event.target.closest("[data-nj-promo-remove]");
      if (removePromo) {
        var promoCard = removePromo.closest("[data-nj-promo]");
        if (promoCard && window.confirm("¿Quitar esta campaña?")) {
          var promoKind = promoCard.getAttribute("data-nj-kind") || "";
          var promoCopy = promoKind ? document.querySelector('[data-nj-promo-copy][data-nj-kind="' + promoKind + '"]') : null;
          if (promoCopy) {
            promoCopy.remove();
          }
          promoCard.remove();
          reindexPromos(document.querySelector("[data-nj-promo-list]"));
        }
      }
      var addPillButton = event.target.closest("[data-nj-pill-add]");
      if (addPillButton) {
        addPill(document.querySelector("[data-nj-pill-list]"));
      }
      var removePill = event.target.closest("[data-nj-pill-remove]");
      if (removePill) {
        var pill = removePill.closest("[data-nj-pill]");
        if (pill && window.confirm("¿Quitar esta categoría de la fila? Los productos siguen en su categoría.")) {
          var pillList = pill.parentNode;
          pill.remove();
          reindexPills(pillList);
        }
      }
      var pillUp = event.target.closest("[data-nj-pill-up]");
      if (pillUp) {
        movePill(pillUp.closest("[data-nj-pill]"), -1);
      }
      var pillDown = event.target.closest("[data-nj-pill-down]");
      if (pillDown) {
        movePill(pillDown.closest("[data-nj-pill]"), 1);
      }
      var styleUp = event.target.closest("[data-nj-style-up]");
      if (styleUp) {
        moveCard(styleUp.closest("[data-nj-style]"), -1, reindexStyles);
      }
      var styleDown = event.target.closest("[data-nj-style-down]");
      if (styleDown) {
        moveCard(styleDown.closest("[data-nj-style]"), 1, reindexStyles);
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
      var menuJump = event.target.closest("[data-nj-menu-jump]");
      if (menuJump) {
        toggleMenu(menuJump.getAttribute("data-nj-menu-jump"));
      }
      var menuToggle = event.target.closest("[data-nj-menu-toggle]");
      if (menuToggle) {
        var toggled = menuToggle.closest("[data-nj-menu-group]");
        if (toggled) {
          toggleMenu(toggled.getAttribute("data-nj-menu-key"));
        }
      }
      var addMenu = event.target.closest("[data-nj-menu-add]");
      if (addMenu) {
        var group = addMenu.closest("[data-nj-menu-group]");
        var list = group ? group.querySelector("[data-nj-menu-children]") : null;
        if (list && list.children.length < 12) {
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
      var addDept = event.target.closest("[data-nj-dept-add]");
      if (addDept) {
        var deptList = document.querySelector("[data-nj-dept-list]");
        if (deptList && deptList.querySelectorAll("[data-nj-menu-dept]").length < 6) {
          var deptIndex = deptList.querySelectorAll("[data-nj-menu-dept]").length;
          var dept = document.createElement("div");
          dept.innerHTML = deptGroupHtml(deptIndex);
          var deptGroup = dept.firstElementChild;
          deptList.appendChild(deptGroup);
          reindexDepts();
          toggleMenu(deptGroup.getAttribute("data-nj-menu-key"));
          var deptLabel = deptGroup.querySelector("[data-nj-menu-label]");
          if (deptLabel) {
            deptLabel.focus();
          }
        }
      }
      var removeDept = event.target.closest("[data-nj-dept-remove]");
      if (removeDept) {
        var deptCard = removeDept.closest("[data-nj-menu-dept]");
        if (deptCard && window.confirm("¿Quitar este menú de la barra?")) {
          deptCard.remove();
          reindexDepts();
        }
      }
      var addLink = event.target.closest("[data-nj-link-add]");
      if (addLink) {
        var linkList = addLink.parentElement ? addLink.parentElement.previousElementSibling : null;
        if (!linkList || !linkList.hasAttribute("data-nj-link-list")) {
          linkList = addLink.closest(".nj-menu-body, .nj-screen, .nj-studio-main");
          linkList = linkList ? linkList.querySelector("[data-nj-link-list]") : null;
        }
        if (linkList) {
          var linkMax = Number(linkList.getAttribute("data-nj-link-max") || "8");
          if (linkList.querySelectorAll(".nj-menu-row").length < linkMax) {
            var linkRow = document.createElement("div");
            linkRow.className = "nj-menu-row";
            linkRow.innerHTML = '<input type="text" placeholder="Nombre"><input type="url" placeholder="https://"><button type="button" class="button" data-nj-link-remove>Quitar</button>';
            linkList.appendChild(linkRow);
            reindexLinks(linkList);
            var linkInput = linkRow.querySelector("input");
            if (linkInput) {
              linkInput.focus();
            }
          }
        }
      }
      var removeLink = event.target.closest("[data-nj-link-remove]");
      if (removeLink) {
        var linkRowRemove = removeLink.closest(".nj-menu-row");
        var linkOwner = removeLink.closest("[data-nj-link-list]");
        if (linkRowRemove) {
          linkRowRemove.remove();
        }
        reindexLinks(linkOwner);
        reindexColumns(document.querySelector("[data-nj-foot-columns]"));
      }
      var addColumn = event.target.closest("[data-nj-column-add]");
      if (addColumn) {
        var columnList = document.querySelector("[data-nj-foot-columns]");
        if (columnList && columnList.querySelectorAll("[data-nj-foot-column]").length < 4) {
          var column = document.createElement("div");
          column.innerHTML = columnHtml();
          var columnCard = column.firstElementChild;
          columnList.appendChild(columnCard);
          reindexColumns(columnList);
          toggleMenu(columnCard.getAttribute("data-nj-menu-key"));
          var columnTitle = columnCard.querySelector("[data-nj-column-title]");
          if (columnTitle) {
            columnTitle.focus();
          }
        }
      }
      var removeColumn = event.target.closest("[data-nj-column-remove]");
      if (removeColumn) {
        var columnCardRemove = removeColumn.closest("[data-nj-foot-column]");
        if (columnCardRemove && window.confirm("¿Quitar esta columna del pie?")) {
          columnCardRemove.remove();
          reindexColumns(document.querySelector("[data-nj-foot-columns]"));
        }
      }
      var addPill = event.target.closest("[data-nj-pill-add]");
      if (addPill) {
        var pillList = addPill.parentElement ? addPill.parentElement.previousElementSibling : null;
        if (pillList && pillList.hasAttribute("data-nj-pill-list")) {
          var pillMax = Number(pillList.getAttribute("data-nj-pill-max") || "8");
          var pillName = pillList.getAttribute("data-nj-pill-name") || "";
          if (pillList.querySelectorAll(".nj-menu-row").length < pillMax) {
            var pillRow = document.createElement("div");
            pillRow.className = "nj-menu-row nj-pill-edit";
            pillRow.innerHTML = '<input type="text" name="' + pillName + '[]" value=""><button type="button" class="button" data-nj-pill-remove>Quitar</button>';
            pillList.appendChild(pillRow);
            var pillInput = pillRow.querySelector("input");
            if (pillInput) {
              pillInput.focus();
            }
          }
        }
      }
      var removePill = event.target.closest("[data-nj-pill-remove]");
      if (removePill) {
        var pillRowRemove = removePill.closest(".nj-menu-row");
        if (pillRowRemove) {
          pillRowRemove.remove();
        }
      }
      var addHelp = event.target.closest("[data-nj-help-add]");
      if (addHelp) {
        var helpParagraph = addHelp.closest("p");
        var helpList = helpParagraph ? helpParagraph.previousElementSibling : null;
        if (helpList && helpList.hasAttribute("data-nj-help-list")) {
          var helpMax = Number(helpList.getAttribute("data-nj-help-max") || "8");
          if (helpList.querySelectorAll(".nj-help-line, .nj-help-row").length < helpMax) {
            var helpWrap = document.createElement("div");
            helpWrap.innerHTML = helpRowHtml(helpList);
            var helpRow = helpWrap.firstElementChild;
            helpList.appendChild(helpRow);
            reindexHelp(helpList);
            var helpInput = helpRow.querySelector("input, textarea");
            if (helpInput) {
              helpInput.focus();
            }
          }
        }
      }
      var removeHelp = event.target.closest("[data-nj-help-remove]");
      if (removeHelp) {
        var helpRowRemove = removeHelp.closest(".nj-help-line, .nj-help-row");
        var helpOwner = removeHelp.closest("[data-nj-help-list]");
        if (helpRowRemove) {
          helpRowRemove.remove();
        }
        reindexHelp(helpOwner);
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
    function promoField(card, key) {
      return card.querySelector('[name$="[' + key + ']"]');
    }
    function placePromoChip(card) {
      var chip = card.querySelector("[data-nj-promo-chip]");
      var x = promoField(card, "x");
      var y = promoField(card, "y");
      if (!chip || !x || !y) {
        return;
      }
      chip.style.left = x.value + "%";
      chip.style.top = y.value + "%";
    }
    root.querySelectorAll("[data-nj-promo-copy]").forEach(placePromoChip);
    root.addEventListener("pointerdown", function (event) {
      var chip = event.target.closest("[data-nj-promo-chip]");
      if (!chip || event.button !== 0) {
        return;
      }
      var card = chip.closest("[data-nj-promo-copy]");
      var stage = chip.closest("[data-nj-promo-stage]");
      var x = card ? promoField(card, "x") : null;
      var y = card ? promoField(card, "y") : null;
      if (!card || !stage || !x || !y) {
        return;
      }
      event.preventDefault();
      function move(ev) {
        var rect = stage.getBoundingClientRect();
        if (!rect.width || !rect.height) {
          return;
        }
        var nextX = Math.round(((ev.clientX - rect.left) / rect.width) * 100);
        var nextY = Math.round(((ev.clientY - rect.top) / rect.height) * 100);
        x.value = String(Math.max(0, Math.min(100, nextX)));
        y.value = String(Math.max(0, Math.min(100, nextY)));
        placePromoChip(card);
      }
      function stop() {
        document.removeEventListener("pointermove", move);
        document.removeEventListener("pointerup", stop);
      }
      document.addEventListener("pointermove", move);
      document.addEventListener("pointerup", stop);
    });
    root.addEventListener("input", function (event) {
      var card = event.target.closest("[data-nj-promo-copy]");
      if (!card) {
        return;
      }
      if (event.target.name && event.target.name.indexOf("[title]") !== -1) {
        var chip = card.querySelector("[data-nj-promo-chip]");
        var line = String(event.target.value || "").split(/\r?\n/)[0].trim();
        if (chip) {
          chip.textContent = line || "Texto";
        }
      }
      if (event.target.name && (event.target.name.indexOf("[x]") !== -1 || event.target.name.indexOf("[y]") !== -1)) {
        placePromoChip(card);
      }
    });
    root.querySelectorAll(".nj-promo-alto input").forEach(function (input) {
      var out = input.parentNode.querySelector("output");
      if (out) {
        out.textContent = input.value + " px";
      }
    });
    root.addEventListener("input", function (event) {
      var range = event.target.closest(".nj-promo-alto input");
      if (!range) {
        return;
      }
      var out = range.parentNode.querySelector("output");
      if (out) {
        out.textContent = range.value + " px";
      }
    });
    var initial = (window.location.hash || "").replace("#", "");
    if (!initial && window.URLSearchParams) {
      var pedidos = new URLSearchParams(window.location.search);
      if (pedidos.get("nj_vista") || pedidos.get("updated") === "pedido" || pedidos.get("updated") === "guia" || pedidos.get("updated") === "entregado") {
        initial = "pedidos";
      }
    }
    if (!initial) {
      var first = root.querySelector("[data-screen]");
      initial = first ? first.getAttribute("data-screen") : "banner";
    }
    show(initial);
  });

})(jQuery);

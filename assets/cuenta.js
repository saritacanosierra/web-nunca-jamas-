(function () {
  var sheet = document.querySelector("[data-cuenta]");
  var storageKey = "nj-favoritos";

  function read() {
    try {
      var raw = JSON.parse(localStorage.getItem(storageKey) || "[]");
      if (!Array.isArray(raw)) {
        return [];
      }
      var ids = [];
      raw.forEach(function (id) {
        id = parseInt(id, 10);
        if (id > 0 && ids.indexOf(id) === -1) {
          ids.push(id);
        }
      });
      return ids.slice(0, 40);
    } catch (error) {
      return [];
    }
  }

  function productId(button) {
    var attr = parseInt(button.getAttribute("data-save-id") || "", 10);
    if (attr) {
      return attr;
    }
    var card = button.closest(".product-card");
    var data = card ? card.querySelector(".product-card__data") : null;
    if (!data) {
      return 0;
    }
    try {
      return parseInt(JSON.parse(data.textContent).id, 10) || 0;
    } catch (error) {
      return 0;
    }
  }

  function paint() {
    var ids = read();
    Array.prototype.forEach.call(document.querySelectorAll("[data-save]"), function (button) {
      var on = ids.indexOf(productId(button)) !== -1;
      button.setAttribute("aria-pressed", on ? "true" : "false");
      var label = button.querySelector("span");
      if (label) {
        label.textContent = on ? "Guardado" : "Guardar";
      }
    });
  }

  function remember(ids) {
    localStorage.setItem(storageKey, JSON.stringify(ids));
    paint();
  }

  function sync(ids) {
    if (!window.njCuenta || !njCuenta.logged) {
      return;
    }
    var body = new URLSearchParams();
    body.set("action", "nj_cuenta_guardar");
    body.set("nonce", njCuenta.nonce);
    ids.forEach(function (id) {
      body.append("ids[]", String(id));
    });
    fetch(njCuenta.ajax, { method: "POST", credentials: "same-origin", body: body });
  }

  function closeSheet() {
    if (!sheet) {
      return;
    }
    sheet.hidden = true;
    document.body.classList.remove("is-cuenta");
    Array.prototype.forEach.call(document.querySelectorAll("[data-cuenta-open]"), function (button) {
      button.setAttribute("aria-expanded", "false");
    });
  }

  document.addEventListener("click", function (event) {
    var opener = event.target.closest("[data-cuenta-open]");
    if (opener && sheet) {
      event.preventDefault();
      sheet.hidden = false;
      document.body.classList.add("is-cuenta");
      opener.setAttribute("aria-expanded", "true");
      var nav = document.querySelector("[data-drawer]");
      var toggle = document.querySelector("[data-nav-toggle]");
      if (nav) {
        nav.classList.remove("is-open");
      }
      if (toggle) {
        toggle.setAttribute("aria-expanded", "false");
      }
      return;
    }
    if (event.target.closest("[data-cuenta-close]")) {
      closeSheet();
      return;
    }
    var save = event.target.closest("[data-save]");
    if (!save) {
      return;
    }
    event.preventDefault();
    var id = productId(save);
    if (!id) {
      return;
    }
    var ids = read();
    var index = ids.indexOf(id);
    if (index === -1) {
      ids.push(id);
    } else {
      ids.splice(index, 1);
    }
    remember(ids.slice(0, 40));
    sync(read());
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeSheet();
    }
  });

  var server = [];
  if (window.njCuenta && Array.isArray(njCuenta.saved)) {
    njCuenta.saved.forEach(function (id) {
      id = parseInt(id, 10);
      if (id > 0 && server.indexOf(id) === -1) {
        server.push(id);
      }
    });
  }
  var merged = read();
  server.forEach(function (id) {
    if (merged.indexOf(id) === -1) {
      merged.push(id);
    }
  });
  remember(merged.slice(0, 40));
  if (window.njCuenta && njCuenta.logged && merged.join(",") !== server.join(",")) {
    sync(read());
  }

  var list = document.querySelector("[data-favoritos-list]");
  if (!list || !window.njCuenta) {
    return;
  }
  var ids = read();
  var empty = document.querySelector("[data-favoritos-empty]");
  var missing = document.querySelector("[data-favoritos-missing]");
  if (!ids.length) {
    if (empty) {
      empty.hidden = false;
    }
    return;
  }
  var body = new URLSearchParams();
  body.set("action", "nj_cuenta_lista");
  body.set("nonce", njCuenta.nonce);
  ids.forEach(function (id) {
    body.append("ids[]", String(id));
  });
  fetch(njCuenta.ajax, { method: "POST", credentials: "same-origin", body: body })
    .then(function (response) { return response.text(); })
    .then(function (html) {
      list.innerHTML = html;
      paint();
      if (!list.children.length && missing) {
        missing.hidden = false;
      }
    })
    .catch(function () {
      if (missing) {
        missing.hidden = false;
      }
    });
})();

(function () {
  var drawer = document.querySelector("[data-bag]");
  if (!drawer) {
    return;
  }

  var freeShipping = Number(drawer.getAttribute("data-free-shipping")) || 0;
  var linesNode = drawer.querySelector("[data-bag-lines]");
  var emptyNode = drawer.querySelector("[data-bag-empty]");
  var shopMode = window.njShop && window.njShop.bag && window.njShop.nonce;

  function money(value) {
    var amount = String(Math.max(0, Math.round(value)));
    return "$ " + amount.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  function open() {
    drawer.hidden = false;
    drawer.classList.add("is-open");
    document.body.classList.add("bag-open");
  }

  function close() {
    drawer.classList.remove("is-open");
    drawer.hidden = true;
    document.body.classList.remove("bag-open");
  }

  function readState() {
    var node = document.querySelector("[data-bag-state]");
    if (!node) {
      return null;
    }
    try {
      return JSON.parse(node.textContent);
    } catch (error) {
      return null;
    }
  }

  function paint(lines, sum, note) {
    linesNode.innerHTML = "";
    lines.forEach(function (line, index) {
      var row = document.createElement("article");
      var regular = line.regular > line.price ? "<del>" + money(line.regular) + "</del>" : "";
      row.className = "bag-line";
      row.innerHTML =
        '<img src="" alt="">' +
        '<div class="bag-line__copy"><h3></h3><p class="bag-line__size"></p><p class="bag-line__price"><strong></strong>' + regular + "</p></div>" +
        '<div class="bag-line__qty"><button type="button" data-qty="-1" aria-label="Quitar una">−</button><span></span><button type="button" data-qty="1" aria-label="Agregar una">+</button></div>' +
        '<button class="bag-line__remove" type="button" data-remove aria-label="Eliminar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 7h14M9 7V5h6v2M8 7l1 12h6l1-12"/></svg></button>';
      row.querySelector("img").src = line.image || "";
      row.querySelector("h3").textContent = line.name;
      row.querySelector(".bag-line__size").textContent = line.size ? "Talla " + line.size : "";
      row.querySelector("strong").textContent = money(line.price);
      row.querySelector(".bag-line__qty span").textContent = String(line.qty);
      row.setAttribute("data-index", String(index));
      if (line.key) {
        row.setAttribute("data-key", line.key);
      }
      linesNode.appendChild(row);
    });
    emptyNode.hidden = lines.length > 0;
    drawer.querySelector("[data-bag-subtotal]").textContent = money(sum.subtotal);
    drawer.querySelector("[data-bag-discount]").textContent = (sum.discount ? "-" : "") + money(sum.discount);
    drawer.querySelector("[data-bag-total]").textContent = money(sum.total);
    var missing = freeShipping - sum.total;
    var shipping = drawer.querySelector("[data-bag-shipping]");
    var meter = drawer.querySelector("[data-bag-meter]");
    if (!lines.length) {
      shipping.textContent = "Elige una talla para armar tu bolsa.";
      meter.style.width = "0%";
    } else if (missing > 0) {
      shipping.textContent = "¡Te hacen falta " + money(missing) + " para obtener envío gratis!";
      meter.style.width = Math.min(100, (sum.total / freeShipping) * 100) + "%";
    } else {
      shipping.textContent = "¡Ya tienes envío gratis!";
      meter.style.width = "100%";
    }
    Array.prototype.forEach.call(document.querySelectorAll(".bag-count"), function (node) {
      node.textContent = String(sum.count);
    });
    if (note) {
      var couponNote = drawer.querySelector("[data-bag-coupon-note]");
      if (couponNote) {
        couponNote.textContent = note;
      }
    }
  }

  function applyFragments(fragments) {
    if (!fragments || !window.jQuery) {
      return;
    }
    window.jQuery.each(fragments, function (key, value) {
      window.jQuery(key).replaceWith(value);
    });
  }

  if (shopMode) {
    function renderShop() {
      var state = readState() || { lines: [], subtotal: 0, discount: 0, total: 0, count: 0, note: "" };
      paint(state.lines || [], state, state.note || "");
    }

    function writeState(state) {
      var node = document.querySelector("[data-bag-state]");
      if (node) {
        node.textContent = JSON.stringify(state);
      }
      paint(state.lines || [], state, state.note || "");
    }

    function totalsFrom(lines) {
      var subtotal = 0;
      var total = 0;
      var count = 0;
      lines.forEach(function (line) {
        var regular = line.regular > line.price ? line.regular : line.price;
        subtotal += regular * line.qty;
        total += line.price * line.qty;
        count += line.qty;
      });
      return {
        lines: lines,
        subtotal: subtotal,
        discount: subtotal - total,
        total: total,
        count: count,
        note: ""
      };
    }

    function updateShop(body, optimistic) {
      if (optimistic) {
        writeState(optimistic);
      }
      body.append("nonce", window.njShop.nonce);
      fetch(window.njShop.bag, { method: "POST", body: body, credentials: "same-origin" })
        .then(function (response) { return response.json(); })
        .then(function (payload) {
          var html = payload && payload.fragments && payload.fragments["script[data-bag-state]"];
          if (html) {
            var parsed = new DOMParser().parseFromString(html, "text/html");
            var next = parsed.querySelector("[data-bag-state]");
            var node = document.querySelector("[data-bag-state]");
            if (next && node) {
              node.textContent = next.textContent;
            }
          }
          if (payload && payload.fragments && window.jQuery) {
            window.jQuery(document.body).trigger("removed_from_cart", [payload.fragments, payload.cart_hash]);
          }
          renderShop();
        })
        .catch(function () {
          renderShop();
        });
    }

    window.njBag = {
      add: function () {},
      open: open,
      refresh: renderShop
    };

    document.addEventListener("click", function (event) {
      if (event.target.closest("[data-bag-open]")) {
        open();
      }
    });

    drawer.addEventListener("click", function (event) {
      if (event.target.closest("[data-bag-close]")) {
        close();
        return;
      }
      var row = event.target.closest(".bag-line");
      if (!row) {
        return;
      }
      var key = row.getAttribute("data-key");
      var qty = Number(row.querySelector(".bag-line__qty span").textContent);
      var current = readState() || { lines: [] };
      var lines = (current.lines || []).map(function (line) {
        return Object.assign({}, line);
      });
      if (event.target.closest("[data-remove]")) {
        var removeBody = new FormData();
        removeBody.append("bag_action", "qty");
        removeBody.append("key", key);
        removeBody.append("qty", "0");
        updateShop(removeBody, totalsFrom(lines.filter(function (line) {
          return line.key !== key;
        })));
        return;
      }
      var qtyButton = event.target.closest("[data-qty]");
      if (!qtyButton || !key) {
        return;
      }
      var nextQty = qty + Number(qtyButton.getAttribute("data-qty"));
      var qtyBody = new FormData();
      qtyBody.append("bag_action", "qty");
      qtyBody.append("key", key);
      qtyBody.append("qty", String(Math.max(0, nextQty)));
      updateShop(qtyBody, totalsFrom(lines.reduce(function (list, line) {
        if (line.key !== key) {
          list.push(line);
          return list;
        }
        if (nextQty > 0) {
          line.qty = nextQty;
          list.push(line);
        }
        return list;
      }, [])));
    });

    var shopCouponButton = drawer.querySelector("[data-bag-coupon]");
    var shopCouponForm = drawer.querySelector("[data-bag-coupon-form]");
    shopCouponButton.addEventListener("click", function () {
      shopCouponForm.hidden = !shopCouponForm.hidden;
    });
    shopCouponForm.addEventListener("submit", function (event) {
      event.preventDefault();
      var input = shopCouponForm.querySelector("input");
      var body = new FormData();
      body.append("bag_action", "coupon");
      body.append("coupon", input.value.trim());
      updateShop(body);
    });

    if (window.jQuery) {
      window.jQuery(document.body).on("added_to_cart wc_fragments_refreshed", renderShop);
    }
    renderShop();
    return;
  }

  var storageKey = "nj-bolsa";
  var lines = [];

  try {
    lines = JSON.parse(localStorage.getItem(storageKey)) || [];
  } catch (error) {
    lines = [];
  }

  function totals() {
    var subtotal = 0;
    var total = 0;
    var count = 0;
    lines.forEach(function (line) {
      var regular = line.regular > line.price ? line.regular : line.price;
      subtotal += regular * line.qty;
      total += line.price * line.qty;
      count += line.qty;
    });
    return { subtotal: subtotal, total: total, discount: subtotal - total, count: count };
  }

  function render() {
    paint(lines, totals(), "");
  }

  function save() {
    localStorage.setItem(storageKey, JSON.stringify(lines));
    render();
  }

  function add(item) {
    var key = item.url + "|" + item.size;
    var found = null;
    lines.forEach(function (line) {
      if (line.key === key) {
        found = line;
      }
    });
    if (found) {
      found.qty += 1;
    } else {
      lines.push({
        key: key,
        name: item.name,
        image: item.image,
        url: item.url,
        size: item.size,
        price: Number(item.price) || 0,
        regular: Number(item.regular) || Number(item.price) || 0,
        qty: 1
      });
    }
    save();
    open();
  }

  window.njBag = { add: add, open: open, refresh: render };

  document.addEventListener("click", function (event) {
    if (event.target.closest("[data-bag-open]")) {
      open();
    }
  });

  drawer.addEventListener("click", function (event) {
    if (event.target.closest("[data-bag-close]")) {
      close();
      return;
    }
    var row = event.target.closest(".bag-line");
    if (!row) {
      return;
    }
    var index = Number(row.getAttribute("data-index"));
    if (event.target.closest("[data-remove]")) {
      lines.splice(index, 1);
      save();
      return;
    }
    var qtyButton = event.target.closest("[data-qty]");
    if (!qtyButton) {
      return;
    }
    lines[index].qty += Number(qtyButton.getAttribute("data-qty"));
    if (lines[index].qty < 1) {
      lines.splice(index, 1);
    }
    save();
  });

  var couponButton = drawer.querySelector("[data-bag-coupon]");
  var couponForm = drawer.querySelector("[data-bag-coupon-form]");
  couponButton.addEventListener("click", function () {
    couponForm.hidden = !couponForm.hidden;
  });
  couponForm.addEventListener("submit", function (event) {
    event.preventDefault();
    var input = couponForm.querySelector("input");
    var note = couponForm.querySelector("[data-bag-coupon-note]");
    note.textContent = input.value.trim() ? "Cupón guardado. Lo aplicamos al pagar." : "Escribe el cupón.";
  });

  render();
})();

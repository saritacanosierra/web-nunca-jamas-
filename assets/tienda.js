(function () {
  function shopSizeMatch(data, size) {
    var rows = data.variations || [];
    var wanted = String(size).toLowerCase();
    for (var i = 0; i < rows.length; i++) {
      if (String(rows[i].size).toLowerCase() === wanted) {
        return rows[i];
      }
    }
    return null;
  }

  function syncCartForm(size) {
    var form = document.querySelector("form.cart");
    if (!form) {
      return;
    }
    Array.prototype.forEach.call(form.querySelectorAll("select"), function (select) {
      var match = null;
      Array.prototype.forEach.call(select.options, function (option) {
        if (option.value.toLowerCase() === String(size).toLowerCase() || option.text.toLowerCase() === String(size).toLowerCase()) {
          match = option;
        }
      });
      if (!match) {
        return;
      }
      select.value = match.value;
      select.dispatchEvent(new Event("change", { bubbles: true }));
    });
  }

  function shopAdd(data, size) {
    syncCartForm(size);
    if (window.njShop && window.njShop.ajax && data.id) {
      var body = new FormData();
      body.append("quantity", "1");
      var variation = shopSizeMatch(data, size);
      if (variation && variation.id) {
        body.append("product_id", String(variation.id));
        body.append("variation_id", String(variation.id));
        Object.keys(variation.attributes || {}).forEach(function (key) {
          body.append(key, variation.attributes[key]);
        });
      } else {
        body.append("product_id", String(data.id));
      }
      fetch(window.njShop.ajax, { method: "POST", body: body, credentials: "same-origin" })
        .then(function (response) { return response.json(); })
        .then(function (payload) {
          if (!payload || payload.error) {
            return;
          }
          if (payload.fragments && window.jQuery) {
            window.jQuery.each(payload.fragments, function (key, value) {
              window.jQuery(key).replaceWith(value);
            });
            window.jQuery(document.body).trigger("added_to_cart", [payload.fragments, payload.cart_hash]);
          }
          if (window.njBag && window.njBag.refresh) {
            window.njBag.refresh();
          }
          if (window.njBag) {
            window.njBag.open();
          }
        })
        .catch(function () {});
      return;
    }
    if (window.njBag) {
      window.njBag.add({
        name: data.name,
        image: data.image,
        url: data.url,
        price: data.price,
        regular: data.regular,
        size: size
      });
    }
  }

  var pills = document.querySelector("[data-pills]");
  if (pills) {
    var track = pills.querySelector("[data-pills-track]");
    pills.querySelector("[data-pills-prev]").addEventListener("click", function () {
      track.scrollBy({ left: -220, behavior: "smooth" });
    });
    pills.querySelector("[data-pills-next]").addEventListener("click", function () {
      track.scrollBy({ left: 220, behavior: "smooth" });
    });
  }

  var filtersButton = document.querySelector("[data-filters]");
  var filtersPanel = document.querySelector("[data-filters-panel]");
  if (filtersButton && filtersPanel) {
    filtersButton.addEventListener("click", function () {
      var open = filtersPanel.hidden;
      filtersPanel.hidden = !open;
      filtersButton.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  var sortForm = document.querySelector("[data-sort-form]");
  if (sortForm) {
    sortForm.querySelector("select").addEventListener("change", function () {
      sortForm.submit();
    });
  }

  var quickView = document.querySelector("[data-quick-view]");
  if (quickView) {
    var quickPhoto = quickView.querySelector("[data-quick-photo]");
    var quickThumbs = quickView.querySelector("[data-quick-thumbs]");
    var quickSizes = quickView.querySelector("[data-quick-sizes]");
    var quickNote = quickView.querySelector("[data-quick-note]");
    var quickText = quickView.querySelector("[data-quick-text]");
    var quickComposition = quickView.querySelector("[data-quick-composition]");
    var quickHeading = quickView.querySelector("[data-quick-heading]");
    var quickHint = quickView.querySelector("[data-quick-hint]");
    var quickData = null;

    function showQuickPhoto(src) {
      quickPhoto.src = src;
      Array.prototype.forEach.call(quickThumbs.querySelectorAll("button"), function (button) {
        button.classList.toggle("is-selected", button.getAttribute("data-src") === src);
      });
    }

    function fillQuickLine(node, value) {
      node.hidden = !value;
      node.textContent = value || "";
    }

    document.addEventListener("click", function (event) {
      var opener = event.target.closest("[data-quick-view-open]");
      if (!opener) {
        return;
      }
      var card = opener.closest(".product-card");
      var dataNode = card ? card.querySelector(".product-card__data") : null;
      if (!dataNode) {
        return;
      }
      var data = JSON.parse(dataNode.textContent);
      quickData = data;
      var gallery = data.gallery && data.gallery.length ? data.gallery : [data.image];
      quickView.querySelector("[data-quick-title]").textContent = data.name;
      quickView.querySelector("[data-quick-meta]").textContent = data.ref ? "Ref. " + data.ref + " | " + data.kicker : data.kicker;
      quickView.querySelector("[data-quick-price]").innerHTML = data.price_html;
      quickView.querySelector("[data-quick-buy]").href = data.url;
      fillQuickLine(quickNote, data.note);
      fillQuickLine(quickText, data.description);
      fillQuickLine(quickComposition, data.composition);
      quickHeading.hidden = !data.description && !data.composition;
      quickThumbs.innerHTML = "";
      gallery.forEach(function (src) {
        var button = document.createElement("button");
        var image = document.createElement("img");
        button.type = "button";
        button.setAttribute("data-src", src);
        image.src = src;
        image.alt = "";
        button.appendChild(image);
        quickThumbs.appendChild(button);
      });
      showQuickPhoto(gallery[0]);
      quickSizes.innerHTML = "";
      (data.sizes || []).forEach(function (size) {
        var button = document.createElement("button");
        button.type = "button";
        button.textContent = size;
        quickSizes.appendChild(button);
      });
      quickSizes.hidden = !data.sizes || !data.sizes.length;
      quickHint.hidden = quickSizes.hidden;
      quickView.showModal();
    });

    quickThumbs.addEventListener("click", function (event) {
      var button = event.target.closest("button");
      if (button) {
        showQuickPhoto(button.getAttribute("data-src"));
      }
    });

    quickSizes.addEventListener("click", function (event) {
      var button = event.target.closest("button");
      if (!button) {
        return;
      }
      Array.prototype.forEach.call(quickSizes.querySelectorAll("button"), function (item) {
        item.classList.toggle("is-selected", item === button);
      });
      if (window.njBag && quickData) {
        quickView.close();
        shopAdd(quickData, button.textContent.trim());
      }
    });

    quickView.querySelector("[data-quick-close]").addEventListener("click", function () {
      quickView.close();
    });

    quickView.addEventListener("click", function (event) {
      if (event.target === quickView) {
        quickView.close();
      }
    });
  }

  Array.prototype.forEach.call(document.querySelectorAll(".product-card__sizes"), function (group) {
    group.addEventListener("click", function (event) {
      var button = event.target.closest("button");
      if (!button) return;
      Array.prototype.forEach.call(group.querySelectorAll("button"), function (item) {
        item.classList.toggle("is-selected", item === button);
      });
      var card = button.closest(".product-card");
      var dataNode = card ? card.querySelector(".product-card__data") : null;
      if (!dataNode || !window.njBag) {
        return;
      }
      var data = JSON.parse(dataNode.textContent);
      shopAdd(data, button.textContent.trim());
    });
  });

  Array.prototype.forEach.call(document.querySelectorAll("[data-save]"), function (button) {
    button.addEventListener("click", function () {
      var saved = button.getAttribute("aria-pressed") === "true";
      button.setAttribute("aria-pressed", saved ? "false" : "true");
    });
  });

  var ficha = document.querySelector("[data-ficha]");
  if (ficha) {
    var fichaData = JSON.parse(ficha.querySelector("[data-ficha-data]").textContent);
    var fichaTrack = ficha.querySelector("[data-ficha-track]");
    var fichaSize = "";
    var fichaAlert = ficha.querySelector("[data-ficha-alert]");
    var fichaSelect = ficha.querySelector("[data-ficha-size]");

    function fichaStep() {
      var slide = fichaTrack.querySelector(".ficha__slide");
      return slide ? slide.getBoundingClientRect().width + 10 : 320;
    }

    function chooseFichaSize(size) {
      fichaSize = size || "";
      Array.prototype.forEach.call(ficha.querySelectorAll("[data-ficha-size-button]"), function (button) {
        button.classList.toggle("is-selected", button.getAttribute("data-size") === fichaSize);
      });
      if (fichaSelect && fichaSelect.value !== fichaSize) {
        fichaSelect.value = fichaSize;
      }
      if (fichaAlert) {
        fichaAlert.hidden = true;
      }
      syncCartForm(fichaSize);
    }

    function addFicha() {
      if (!fichaSize) {
        if (fichaAlert) {
          fichaAlert.hidden = false;
        }
        return;
      }
      if (window.njBag) {
        shopAdd(fichaData, fichaSize);
      }
    }

    ficha.querySelector("[data-ficha-prev]").addEventListener("click", function () {
      fichaTrack.scrollBy({ left: -fichaStep(), behavior: "smooth" });
    });
    ficha.querySelector("[data-ficha-next]").addEventListener("click", function () {
      fichaTrack.scrollBy({ left: fichaStep(), behavior: "smooth" });
    });
    ficha.addEventListener("click", function (event) {
      var sizeButton = event.target.closest("[data-ficha-size-button]");
      if (sizeButton) {
        chooseFichaSize(sizeButton.getAttribute("data-size"));
        return;
      }
      var tab = event.target.closest("[data-ficha-tab]");
      if (tab) {
        var id = tab.getAttribute("data-ficha-tab");
        Array.prototype.forEach.call(ficha.querySelectorAll("[data-ficha-tab]"), function (item) {
          var active = item === tab;
          item.classList.toggle("is-active", active);
          item.setAttribute("aria-selected", active ? "true" : "false");
        });
        Array.prototype.forEach.call(ficha.querySelectorAll("[data-ficha-panel]"), function (panel) {
          panel.classList.toggle("is-active", panel.getAttribute("data-ficha-panel") === id);
        });
        return;
      }
      if (event.target.closest("[data-ficha-add]")) {
        addFicha();
      }
    });
    if (fichaSelect) {
      fichaSelect.addEventListener("change", function () {
        chooseFichaSize(fichaSelect.value);
      });
    }
  }

  var checkout = document.querySelector(".wc-block-checkout");
  if (checkout) {
    var checkoutPhrases = [
      ["Add coupons", "¿Tienes un cupón?"],
      ["I would like to receive exclusive emails with discounts and product information", "Quiero recibir novedades y descuentos por correo"],
      ["Realizar el pedido", "Confirmar pedido"]
    ];

    function checkoutAmount(text) {
      var digits = String(text || "").replace(/[^\d]/g, "");
      return digits ? parseInt(digits, 10) : 0;
    }

    function checkoutPesos(value) {
      return "$ " + String(value).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function checkoutTranslate(root) {
      var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
      var node;
      while ((node = walker.nextNode())) {
        var value = node.nodeValue;
        if (!value) {
          continue;
        }
        checkoutPhrases.forEach(function (phrase) {
          if (value.indexOf(phrase[0]) !== -1) {
            node.nodeValue = value.split(phrase[0]).join(phrase[1]);
          }
        });
      }
    }

    function checkoutTotal() {
      var total = document.querySelector(".wc-block-components-totals-footer-item .wc-block-formatted-money-amount");
      return total ? total.textContent.trim() : "";
    }

    function checkoutConfirm() {
      var real = document.querySelector(".wc-block-components-checkout-place-order-button");
      if (real) {
        real.click();
      }
    }

    function checkoutLead() {
      if (document.querySelector(".nj-checkout-lead")) {
        return;
      }
      var lead = document.createElement("div");
      lead.className = "nj-checkout-lead";
      lead.innerHTML = "<p>Tu prenda ya está lista. Completa el envío y confirma el pedido.</p><ol class=\"nj-checkout-steps\"><li data-nj-step=\"contact\">1 · Tus datos</li><li data-nj-step=\"ship\">2 · Entrega</li><li data-nj-step=\"pay\">3 · Pago</li></ol>";
      checkout.parentNode.insertBefore(lead, checkout);
    }

    function checkoutMark() {
      var ship = document.querySelector(".wc-block-checkout__shipping-fields");
      var pay = document.querySelector(".wc-block-checkout__payment-method");
      var line = window.innerHeight * 0.4;
      var current = "contact";
      if (pay && pay.getBoundingClientRect().top < line) {
        current = "pay";
      } else if (ship && ship.getBoundingClientRect().top < line) {
        current = "ship";
      }
      Array.prototype.forEach.call(document.querySelectorAll("[data-nj-step]"), function (item) {
        item.classList.toggle("is-current", item.getAttribute("data-nj-step") === current);
      });
    }

    function checkoutClose() {
      var content = document.querySelector(".wc-block-components-checkout-order-summary__content");
      if (!content) {
        return;
      }
      var saved = 0;
      Array.prototype.forEach.call(content.querySelectorAll(".wc-block-components-order-summary-item"), function (item) {
        var regular = item.querySelector(".wc-block-components-product-price__regular");
        var sale = item.querySelector(".wc-block-components-product-price__value");
        var qtyNode = item.querySelector(".wc-block-components-order-summary-item__quantity [aria-hidden='true']");
        var qty = qtyNode ? checkoutAmount(qtyNode.textContent) || 1 : 1;
        if (regular && sale) {
          var diff = checkoutAmount(regular.textContent) - checkoutAmount(sale.textContent);
          if (diff > 0) {
            saved += diff * qty;
          }
        }
      });
      var save = content.querySelector(".nj-checkout-save");
      if (saved > 0) {
        if (!save) {
          save = document.createElement("p");
          save.className = "nj-checkout-save";
          var totals = content.querySelector(".wp-block-woocommerce-checkout-order-summary-totals-block");
          content.insertBefore(save, totals);
        }
        save.textContent = "Ahorras " + checkoutPesos(saved) + " en este pedido";
      } else if (save) {
        save.remove();
      }
      if (!content.querySelector(".nj-checkout-close")) {
        var panel = document.createElement("div");
        panel.className = "nj-checkout-close";
        panel.innerHTML = "<button type=\"button\" class=\"nj-checkout-close__button\">Confirmar pedido</button><p class=\"nj-checkout-close__note\">Pagas en Wompi con Nequi, tarjeta o PSE.</p><ul class=\"nj-checkout-trust\"><li>Nequi, tarjeta o PSE</li><li>Envío a Colombia</li><li>Cambios por correo</li></ul>";
        content.appendChild(panel);
        panel.querySelector("button").addEventListener("click", checkoutConfirm);
      }
      var button = content.querySelector(".nj-checkout-close__button");
      var total = checkoutTotal();
      var label = total ? "Confirmar pedido · " + total : "Confirmar pedido";
      if (button && button.textContent !== label) {
        button.textContent = label;
      }
    }

    function checkoutBar() {
      if (document.querySelector(".nj-checkout-bar")) {
        return;
      }
      var bar = document.createElement("div");
      bar.className = "nj-checkout-bar";
      bar.innerHTML = "<div><span>Total</span><strong></strong></div><button type=\"button\">Confirmar pedido</button>";
      document.body.appendChild(bar);
      bar.querySelector("button").addEventListener("click", checkoutConfirm);
    }

    function checkoutSyncBar() {
      var strong = document.querySelector(".nj-checkout-bar strong");
      if (strong) {
        strong.textContent = checkoutTotal();
      }
    }

    function checkoutRefresh() {
      checkoutLead();
      checkoutClose();
      checkoutBar();
      checkoutSyncBar();
      checkoutTranslate(checkout);
      checkoutMark();
    }

    var checkoutQueued = false;
    var checkoutObserver = new MutationObserver(function () {
      if (checkoutQueued) {
        return;
      }
      checkoutQueued = true;
      window.requestAnimationFrame(function () {
        checkoutQueued = false;
        checkoutRefresh();
      });
    });
    checkoutObserver.observe(checkout, { childList: true, subtree: true, characterData: true });
    window.addEventListener("scroll", checkoutMark, { passive: true });
    checkoutRefresh();
  }
})();

(function () {
  function shopSizeMatch(data, size, color) {
    var rows = data.variations || [];
    var wanted = String(size).toLowerCase();
    var wantedColor = color ? String(color).toLowerCase() : "";
    for (var i = 0; i < rows.length; i++) {
      if (String(rows[i].size).toLowerCase() !== wanted) {
        continue;
      }
      if (wantedColor && String(rows[i].color || "").toLowerCase() !== wantedColor) {
        continue;
      }
      return rows[i];
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

  function shopAdd(data, size, color, extra) {
    if (data.personalizado && !extra && data.url) {
      window.location.href = data.url;
      return;
    }
    extra = extra || {};
    syncCartForm(size);
    if (window.njShop && window.njShop.ajax && data.id) {
      var body = new FormData();
      body.append("quantity", "1");
      if (extra.nombre) {
        body.append("nj_nombre", extra.nombre);
        body.append("nj_fuente", extra.fuente || "cursiva");
      }
      var variation = shopSizeMatch(data, size, color);
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

  var findForm = document.querySelector("[data-find]");
  if (findForm) {
    var findInput = findForm.querySelector("input[name=nj]");
    var findTimer = 0;
    var findTicket = 0;
    var findGroups = [];
    var findData = findForm.querySelector("[data-nj-busca-grupos]");
    if (findData) {
      try {
        findGroups = JSON.parse(findData.textContent || "[]");
      } catch (error) {
        findGroups = [];
      }
    }

    function findKey(value) {
      var map = { "á": "a", "é": "e", "í": "i", "ó": "o", "ú": "u", "ñ": "n", "ü": "u" };
      return String(value || "").toLowerCase().replace(/[áéíóúñü]/g, function (letter) {
        return map[letter] || letter;
      }).replace(/[^a-z0-9]/g, "").replace(/(.)\1+/g, "$1");
    }

    function findDistance(left, right) {
      var rows = [];
      var i = 0;
      var j = 0;
      for (i = 0; i <= left.length; i += 1) {
        rows[i] = [i];
      }
      for (j = 0; j <= right.length; j += 1) {
        rows[0][j] = j;
      }
      for (i = 1; i <= left.length; i += 1) {
        for (j = 1; j <= right.length; j += 1) {
          rows[i][j] = Math.min(
            rows[i - 1][j] + 1,
            rows[i][j - 1] + 1,
            rows[i - 1][j - 1] + (left.charAt(i - 1) === right.charAt(j - 1) ? 0 : 1)
          );
        }
      }
      return rows[left.length][right.length];
    }

    function findNear(left, right) {
      if (!left || !right) {
        return false;
      }
      if (left === right || left.indexOf(right) !== -1 || right.indexOf(left) !== -1) {
        return true;
      }
      if (left.length < 5 || right.length < 5 || Math.abs(left.length - right.length) > 2) {
        return false;
      }
      return findDistance(left, right) <= 2;
    }

    function findNeedles(text) {
      var words = String(text || "").trim().split(/\s+/);
      var groups = [];
      words.forEach(function (word) {
        var key = findKey(word);
        if (key.length < 3 || groups.length >= 4) {
          return;
        }
        var needles = [key];
        findGroups.forEach(function (group) {
          var hit = false;
          group.forEach(function (item) {
            if (!hit && findNear(key, item)) {
              hit = true;
            }
          });
          if (hit) {
            needles = needles.concat(group);
          }
        });
        groups.push(needles);
      });
      return groups;
    }

    function findMatch(text, query) {
      var groups = findNeedles(query);
      if (!groups.length) {
        return true;
      }
      var key = findKey(text);
      var parts = key.split(/[^a-z0-9]+/).filter(Boolean);
      return groups.every(function (needles) {
        return needles.some(function (needle) {
          if (needle && key.indexOf(needle) !== -1) {
            return true;
          }
          return parts.some(function (part) {
            return findNear(part, needle);
          });
        });
      });
    }

    function findLocal(query) {
      var box = document.querySelector("[data-catalog-results]");
      if (!box) {
        return;
      }
      var context = box.getAttribute("data-busca-contexto") || "";
      var visible = 0;
      Array.prototype.forEach.call(box.querySelectorAll(".product-card"), function (card) {
        var info = card.querySelector(".product-card__info");
        var show = findMatch((info ? info.textContent : "") + " " + context, query);
        card.hidden = !show;
        if (show) {
          visible += 1;
        }
      });
      var empty = box.querySelector("[data-busca-vacio]");
      if (empty) {
        empty.hidden = visible > 0 || findNeedles(query).length === 0;
      }
    }

    function findRemote(query) {
      var box = document.querySelector("[data-catalog-results]");
      if (!box || !window.fetch) {
        return;
      }
      var data = new FormData(findForm);
      data.set("nj", query);
      var params = new URLSearchParams();
      data.forEach(function (value, key) {
        if (value !== "") {
          params.append(key, value);
        }
      });
      var action = findForm.getAttribute("action") || window.location.pathname;
      var next = action + (action.indexOf("?") === -1 ? "?" : "&") + params.toString();
      var ticket = findTicket + 1;
      findTicket = ticket;
      window.fetch(next, { headers: { "X-Requested-With": "XMLHttpRequest" } }).then(function (response) {
        return response.text();
      }).then(function (html) {
        if (ticket !== findTicket) {
          return;
        }
        var doc = new DOMParser().parseFromString(html, "text/html");
        var fresh = doc.querySelector("[data-catalog-results]");
        if (fresh) {
          box.innerHTML = fresh.innerHTML;
        }
        if (window.history && window.history.replaceState) {
          window.history.replaceState(null, "", next);
        }
      }).catch(function () {});
    }

    function findRun() {
      var query = findInput ? findInput.value : "";
      findLocal(query);
      window.clearTimeout(findTimer);
      findTimer = window.setTimeout(function () {
        findRemote(query);
      }, 280);
    }

    if (findInput) {
      findInput.addEventListener("input", findRun);
    }
    findForm.addEventListener("submit", function (event) {
      event.preventDefault();
      findRun();
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
      var addiQuick = quickView.querySelector("[data-addi-quick]");
      if (addiQuick) {
        var addiAmount = parseInt(data.price, 10) || 0;
        var addiWidget = addiQuick.querySelector("addi-widget");
        if (addiAmount >= 50000) {
          if (!addiWidget) {
            addiWidget = document.createElement("addi-widget");
            addiWidget.setAttribute("ally-slug", addiQuick.getAttribute("data-addi-slug") || "");
            addiQuick.appendChild(addiWidget);
          }
          addiWidget.setAttribute("price", String(addiAmount));
          addiQuick.hidden = false;
        } else {
          addiQuick.hidden = true;
        }
      }
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
      var quickColors = [];
      (data.variations || []).forEach(function (row) {
        if (row.color && quickColors.indexOf(row.color) === -1) {
          quickColors.push(row.color);
        }
      });
      if (quickColors.length > 1) {
        (data.variations || []).forEach(function (row) {
          if (!row.size) {
            return;
          }
          var button = document.createElement("button");
          button.type = "button";
          button.textContent = row.color ? row.size + " · " + row.color : row.size;
          button.setAttribute("data-size", row.size);
          button.setAttribute("data-color", row.color || "");
          quickSizes.appendChild(button);
        });
      } else {
        (data.sizes || []).forEach(function (size) {
          var button = document.createElement("button");
          button.type = "button";
          button.textContent = size;
          button.setAttribute("data-size", size);
          button.setAttribute("data-color", quickColors[0] || "");
          quickSizes.appendChild(button);
        });
      }
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
        shopAdd(quickData, button.getAttribute("data-size") || button.textContent.trim(), button.getAttribute("data-color") || "");
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
      shopAdd(data, button.getAttribute("data-size") || button.textContent.trim());
    });
  });

  window.njShopAdd = shopAdd;
  window.njSyncCart = syncCartForm;
})();

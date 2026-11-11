(function () {
  var shopAdd = window.njShopAdd;
  var syncCartForm = window.njSyncCart;
  if (!shopAdd || !syncCartForm) {
    return;
  }
  var ficha = document.querySelector("[data-ficha]");
  if (ficha) {
    var fichaData = JSON.parse(ficha.querySelector("[data-ficha-data]").textContent);
    var fichaTrack = ficha.querySelector("[data-ficha-track]");
    var fichaSize = "";
    var fichaColor = "";
    var fichaFont = "cursiva";
    var fichaAlert = ficha.querySelector("[data-ficha-alert]");
    var fichaSelect = ficha.querySelector("[data-ficha-size]");
    var fichaName = ficha.querySelector("[data-ficha-name]");

    function cleanFichaName(value) {
      return String(value || "").replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ '\-]/g, "").replace(/\s+/g, " ").slice(0, 12);
    }

    function paintFichaName() {
      if (!fichaName) {
        return;
      }
      var nombre = cleanFichaName(fichaName.value);
      if (fichaName.value !== nombre) {
        fichaName.value = nombre;
      }
      var count = ficha.querySelector("[data-ficha-count]");
      if (count) {
        count.textContent = String(nombre.trim().length) + "/12";
      }
      Array.prototype.forEach.call(ficha.querySelectorAll("[data-ficha-embroider]"), function (layer) {
        layer.textContent = nombre.trim();
        layer.hidden = nombre.trim() === "";
        layer.classList.toggle("is-cursiva", fichaFont === "cursiva");
        layer.classList.toggle("is-imprenta", fichaFont === "imprenta");
      });
      var extra = ficha.querySelector("[data-ficha-extra]");
      if (extra) {
        extra.hidden = nombre.trim() === "";
      }
    }

    function fichaStep() {
      var slide = fichaTrack.querySelector(".ficha__slide");
      return slide ? slide.getBoundingClientRect().width + 8 : 320;
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

    function paintFichaSizes() {
      var allowed = null;
      if (fichaColor) {
        allowed = {};
        (fichaData.variations || []).forEach(function (row) {
          if (String(row.color || "").toLowerCase() === String(fichaColor).toLowerCase() && row.size) {
            allowed[String(row.size).toLowerCase()] = true;
          }
        });
      }
      Array.prototype.forEach.call(ficha.querySelectorAll("[data-ficha-size-button]"), function (button) {
        var size = String(button.getAttribute("data-size") || "").toLowerCase();
        var on = !allowed || !!allowed[size];
        button.hidden = !on;
        if (!on && button.classList.contains("is-selected")) {
          fichaSize = "";
          button.classList.remove("is-selected");
        }
      });
      if (fichaSelect) {
        Array.prototype.forEach.call(fichaSelect.options, function (option) {
          if (!option.value) {
            return;
          }
          var on = !allowed || !!allowed[String(option.value).toLowerCase()];
          option.hidden = !on;
          option.disabled = !on;
          if (!on && fichaSelect.value === option.value) {
            fichaSelect.value = "";
            fichaSize = "";
          }
        });
      }
    }

    function chooseFichaColor(color) {
      fichaColor = color || "";
      Array.prototype.forEach.call(ficha.querySelectorAll("[data-ficha-color-button]"), function (button) {
        button.classList.toggle("is-selected", button.getAttribute("data-color") === fichaColor);
      });
      paintFichaSizes();
      if (fichaAlert) {
        fichaAlert.hidden = true;
      }
    }

    function addFicha() {
      var needsColor = ficha.querySelector("[data-ficha-color-button]");
      if (!fichaSize || (needsColor && !fichaColor)) {
        if (fichaAlert) {
          fichaAlert.hidden = false;
          fichaAlert.textContent = needsColor && !fichaColor ? "Elige un color y una talla para agregarla a la bolsa." : "Elige una talla para agregarla a la bolsa.";
        }
        return;
      }
      if (window.njBag) {
        shopAdd(fichaData, fichaSize, fichaColor, {
          nombre: fichaName ? cleanFichaName(fichaName.value).trim() : "",
          fuente: fichaFont
        });
      }
    }

    var fichaBelt = document.createElement("div");
    fichaBelt.className = "ficha__belt";
    var fichaOriginals = Array.prototype.slice.call(fichaTrack.querySelectorAll(".ficha__slide"));
    fichaOriginals.forEach(function (slide) {
      fichaBelt.appendChild(slide);
    });
    fichaTrack.appendChild(fichaBelt);
    if (fichaOriginals.length) {
      while (fichaBelt.querySelectorAll(".ficha__slide").length < 5) {
        fichaOriginals.forEach(function (slide) {
          fichaBelt.appendChild(slide.cloneNode(true));
        });
      }
      var fichaSet = Array.prototype.slice.call(fichaBelt.children);
      fichaSet.forEach(function (slide) {
        var copy = slide.cloneNode(true);
        copy.setAttribute("aria-hidden", "true");
        fichaBelt.appendChild(copy);
      });
    }
    var fichaOffset = 0;
    function fichaHalf() {
      return fichaBelt.scrollWidth / 2;
    }
    function fichaDraw() {
      var half = fichaHalf();
      if (half > 0) {
        while (fichaOffset < 0) {
          fichaOffset += half;
        }
        while (fichaOffset >= half) {
          fichaOffset -= half;
        }
      }
      fichaBelt.style.transform = "translateX(" + (-fichaOffset) + "px)";
    }
    function fichaZoomOpen() {
      return !!(zoomDialog && (zoomDialog.open || zoomDialog.hasAttribute("open")));
    }
    function fichaTick() {
      if (!fichaDrag && !fichaZoomOpen() && Date.now() > fichaHold && fichaHalf() > fichaTrack.clientWidth + 8) {
        fichaOffset += 0.32;
        fichaDraw();
      }
      window.requestAnimationFrame(fichaTick);
    }
    window.requestAnimationFrame(fichaTick);
    ficha.querySelector("[data-ficha-prev]").addEventListener("click", function () {
      fichaOffset -= fichaStep();
      fichaDraw();
      fichaHold = Date.now() + 4000;
    });
    ficha.querySelector("[data-ficha-next]").addEventListener("click", function () {
      fichaOffset += fichaStep();
      fichaDraw();
      fichaHold = Date.now() + 4000;
    });
    var fichaHold = 0;
    var fichaStartX = 0;
    var fichaStartY = 0;
    var fichaDrag = false;
    var fichaSwiped = false;
    fichaTrack.addEventListener("pointerdown", function (event) {
      if (event.target.closest("[data-ficha-prev], [data-ficha-next], input, textarea, select, label")) {
        return;
      }
      fichaDrag = true;
      fichaSwiped = false;
      fichaStartX = event.clientX;
      fichaStartY = event.clientY;
      fichaHold = Date.now() + 8000;
    });
    fichaTrack.addEventListener("pointerup", function (event) {
      if (!fichaDrag) {
        return;
      }
      fichaDrag = false;
      var dx = event.clientX - fichaStartX;
      var dy = event.clientY - fichaStartY;
      if (Math.abs(dx) < 42 || Math.abs(dx) < Math.abs(dy)) {
        fichaHold = Date.now() + 1200;
        return;
      }
      fichaSwiped = true;
      fichaOffset -= dx;
      fichaDraw();
      fichaHold = Date.now() + 4000;
    });
    fichaTrack.addEventListener("pointercancel", function () {
      fichaDrag = false;
    });
    fichaTrack.addEventListener("click", function (event) {
      if (!fichaSwiped) {
        return;
      }
      fichaSwiped = false;
      event.preventDefault();
      event.stopPropagation();
    }, true);
    var zoomDialog = ficha.querySelector("[data-ficha-zoom]");
    var zoomStage = zoomDialog ? zoomDialog.querySelector("[data-ficha-zoom-stage]") : null;
    var zoomPhoto = zoomDialog ? zoomDialog.querySelector("[data-ficha-zoom-photo]") : null;
    var zoomThumbs = zoomDialog ? zoomDialog.querySelector("[data-ficha-zoom-thumbs]") : null;
    var zoomList = [];
    var zoomIndex = 0;
    var zoomOn = false;
    function zoomSeen() {
      var seen = {};
      zoomList = [];
      fichaOriginals.forEach(function (slide) {
        var img = slide.querySelector("img");
        if (!img) {
          return;
        }
        var src = img.getAttribute("data-zoom") || img.getAttribute("src") || "";
        var thumb = img.getAttribute("src") || src;
        if (!src || seen[src]) {
          return;
        }
        seen[src] = true;
        zoomList.push({ src: src, thumb: thumb, alt: img.getAttribute("alt") || "" });
      });
    }
    function paintZoom(index) {
      if (!zoomList.length || !zoomPhoto) {
        return;
      }
      zoomIndex = (index + zoomList.length) % zoomList.length;
      zoomOn = false;
      zoomStage.classList.remove("is-zoomed");
      zoomPhoto.style.transformOrigin = "center center";
      zoomPhoto.src = zoomList[zoomIndex].src;
      zoomPhoto.alt = zoomList[zoomIndex].alt;
      Array.prototype.forEach.call(zoomThumbs.querySelectorAll("button"), function (button, item) {
        var on = item === zoomIndex;
        button.classList.toggle("is-on", on);
        button.setAttribute("aria-current", on ? "true" : "false");
      });
    }
    function openZoom(src) {
      if (!zoomDialog || !zoomList.length) {
        return;
      }
      var index = 0;
      zoomList.forEach(function (item, itemIndex) {
        if (item.src === src || item.thumb === src) {
          index = itemIndex;
        }
      });
      paintZoom(index);
      if (zoomDialog.showModal) {
        zoomDialog.showModal();
      } else {
        zoomDialog.setAttribute("open", "");
      }
    }
    function closeZoom() {
      if (!zoomDialog) {
        return;
      }
      zoomOn = false;
      if (zoomStage) {
        zoomStage.classList.remove("is-zoomed");
      }
      if (zoomDialog.close && zoomDialog.hasAttribute("open")) {
        zoomDialog.close();
      } else {
        zoomDialog.removeAttribute("open");
      }
    }
    if (zoomDialog && zoomThumbs) {
      zoomSeen();
      zoomDialog.classList.toggle("is-single", zoomList.length < 2);
      zoomList.forEach(function (item, itemIndex) {
        var button = document.createElement("button");
        button.type = "button";
        button.setAttribute("aria-label", "Foto " + (itemIndex + 1));
        var thumb = document.createElement("img");
        thumb.alt = "";
        thumb.src = item.thumb;
        button.appendChild(thumb);
        button.addEventListener("click", function () {
          paintZoom(itemIndex);
        });
        zoomThumbs.appendChild(button);
      });
      zoomStage.addEventListener("click", function (event) {
        zoomOn = !zoomOn;
        zoomStage.classList.toggle("is-zoomed", zoomOn);
        if (!zoomOn) {
          zoomPhoto.style.transformOrigin = "center center";
          return;
        }
        var rect = zoomStage.getBoundingClientRect();
        var x = ((event.clientX - rect.left) / rect.width) * 100;
        var y = ((event.clientY - rect.top) / rect.height) * 100;
        zoomPhoto.style.transformOrigin = Math.max(0, Math.min(100, x)) + "% " + Math.max(0, Math.min(100, y)) + "%";
      });
      zoomStage.addEventListener("mousemove", function (event) {
        if (!zoomOn) {
          return;
        }
        var rect = zoomStage.getBoundingClientRect();
        var x = ((event.clientX - rect.left) / rect.width) * 100;
        var y = ((event.clientY - rect.top) / rect.height) * 100;
        zoomPhoto.style.transformOrigin = Math.max(0, Math.min(100, x)) + "% " + Math.max(0, Math.min(100, y)) + "%";
      });
      zoomDialog.querySelector("[data-ficha-zoom-close]").addEventListener("click", closeZoom);
      zoomDialog.addEventListener("click", function (event) {
        if (event.target === zoomDialog) {
          closeZoom();
        }
      });
      zoomDialog.addEventListener("close", function () {
        zoomOn = false;
        zoomStage.classList.remove("is-zoomed");
      });
      document.addEventListener("keydown", function (event) {
        if (!zoomDialog.hasAttribute("open")) {
          return;
        }
        if (event.key === "ArrowRight") {
          paintZoom(zoomIndex + 1);
        }
        if (event.key === "ArrowLeft") {
          paintZoom(zoomIndex - 1);
        }
      });
      fichaTrack.addEventListener("click", function (event) {
        var opener = event.target.closest("[data-ficha-open-zoom]");
        if (!opener) {
          return;
        }
        var img = opener.querySelector("img");
        if (!img) {
          return;
        }
        openZoom(img.getAttribute("data-zoom") || img.getAttribute("src") || "");
      });
    }
    ficha.addEventListener("click", function (event) {
      var colorButton = event.target.closest("[data-ficha-color-button]");
      if (colorButton) {
        chooseFichaColor(colorButton.getAttribute("data-color"));
        return;
      }
      var fontButton = event.target.closest("[data-ficha-font]");
      if (fontButton) {
        fichaFont = fontButton.getAttribute("data-ficha-font") || "cursiva";
        Array.prototype.forEach.call(ficha.querySelectorAll("[data-ficha-font]"), function (item) {
          item.classList.toggle("is-selected", item === fontButton);
        });
        paintFichaName();
        return;
      }
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
        return;
      }
      var lookAdd = event.target.closest("[data-look-add]");
      if (lookAdd) {
        var lookItem = lookAdd.closest("[data-look-item]");
        var lookJson = lookItem ? lookItem.querySelector("[data-look-data]") : null;
        var lookAlert = lookItem ? lookItem.querySelector("[data-look-alert]") : null;
        var lookSize = lookItem ? lookItem.querySelector("[data-look-size]") : null;
        var lookData = {};
        try {
          lookData = lookJson ? JSON.parse(lookJson.textContent || "{}") : {};
        } catch (error) {
          lookData = {};
        }
        var chosen = lookSize ? lookSize.value : "";
        if (lookSize && !chosen) {
          if (lookAlert) {
            lookAlert.hidden = false;
          }
          return;
        }
        if (lookAlert) {
          lookAlert.hidden = true;
        }
        shopAdd(lookData, chosen, "", {});
      }
    });
    if (fichaSelect) {
      fichaSelect.addEventListener("change", function () {
        chooseFichaSize(fichaSelect.value);
      });
    }
    if (fichaName) {
      fichaName.addEventListener("input", paintFichaName);
    }
  }
})();

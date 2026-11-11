(function () {
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

    var checkoutTried = false;

    function checkoutText(value) {
      return String(value || "").replace(/\s+/g, " ").trim();
    }

    function checkoutEscape(value) {
      return checkoutText(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
    }

    function checkoutLabel(input) {
      var box = input.closest(".wc-block-components-text-input, .wc-block-components-combobox, .wc-block-components-checkbox, .wc-block-components-radio-control");
      var label = box ? box.querySelector("label") : null;
      var text = label ? label.textContent : input.getAttribute("aria-label");
      return checkoutText(text).replace(/\(opcional\)/ig, "").replace(/\*/g, "").trim() || "Este dato";
    }

    function checkoutProblems() {
      var problems = [];
      var seen = [];
      Array.prototype.forEach.call(checkout.querySelectorAll("input, select, textarea"), function (input) {
        if (input.disabled || input.type === "hidden" || input.type === "radio") {
          return;
        }
        var label = checkoutLabel(input);
        var box = input.closest(".wc-block-components-text-input, .wc-block-components-combobox, .wc-block-components-checkbox");
        var error = box ? box.querySelector(".wc-block-components-validation-error") : null;
        var message = error ? checkoutText(error.textContent) : "";
        var value = checkoutText(input.value);
        var cedula = /c[eé]dula/i.test(label);
        var digits = value.replace(/\D/g, "");
        if (cedula && (digits.length < 6 || digits.length > 12)) {
          message = "Escribe la cédula, solo números, para poder enviar el pedido.";
        } else if (input.required && value === "" && input.type !== "checkbox") {
          message = "Completa " + label.charAt(0).toLowerCase() + label.slice(1) + ".";
        } else if (input.type === "checkbox" && input.required && !input.checked) {
          message = "Acepta " + label.charAt(0).toLowerCase() + label.slice(1) + ".";
        } else if (!message && (input.getAttribute("aria-invalid") === "true" || (input.willValidate && !input.checkValidity()))) {
          message = "Revisa " + label.charAt(0).toLowerCase() + label.slice(1) + ".";
        }
        if (!message || seen.indexOf(label) !== -1) {
          return;
        }
        seen.push(label);
        problems.push({ input: input, label: label, message: message });
      });
      var pay = checkout.querySelector(".wc-block-checkout__payment-method input[type='radio']");
      var picked = checkout.querySelector(".wc-block-checkout__payment-method input[type='radio']:checked");
      if (pay && !picked) {
        problems.push({ input: pay, label: "Pago", message: "Elige cómo quieres pagar." });
      }
      return problems;
    }

    function checkoutAlert(problems) {
      var lead = document.querySelector(".nj-checkout-lead");
      var box = document.querySelector(".nj-checkout-alert");
      Array.prototype.forEach.call(checkout.querySelectorAll(".is-nj-invalid"), function (node) {
        node.classList.remove("is-nj-invalid");
      });
      if (!checkoutTried || !problems.length) {
        if (box) {
          box.remove();
        }
        var gone = document.querySelector(".nj-checkout-close__error");
        if (gone) {
          gone.remove();
        }
        return;
      }
      if (!box) {
        box = document.createElement("div");
        box.className = "nj-checkout-alert";
        box.setAttribute("role", "alert");
        if (lead) {
          lead.insertBefore(box, lead.firstChild);
        } else {
          checkout.parentNode.insertBefore(box, checkout);
        }
      }
      var items = problems.map(function (item, index) {
        return "<li><button type=\"button\" data-nj-error=\"" + index + "\"><strong>" + checkoutEscape(item.label) + ".</strong> " + checkoutEscape(item.message) + "</button></li>";
      }).join("");
      var html = "<strong>No se puede pagar todavía.</strong><p>Falta un dato. Revisa esto:</p><ul>" + items + "</ul>";
      if (box.innerHTML !== html) {
        box.innerHTML = html;
        Array.prototype.forEach.call(box.querySelectorAll("[data-nj-error]"), function (button) {
          button.addEventListener("click", function () {
            var item = problems[Number(button.getAttribute("data-nj-error"))];
            if (!item || !item.input) {
              return;
            }
            var shown = document.querySelector(".nj-checkout-alert");
            var clearance = shown ? Math.ceil(shown.getBoundingClientRect().bottom) + 16 : 180;
            item.input.style.scrollMarginTop = clearance + "px";
            item.input.style.scrollMarginBottom = "120px";
            item.input.scrollIntoView({ behavior: "smooth", block: "start" });
            item.input.focus({ preventScroll: true });
          });
        });
      }
      problems.forEach(function (item) {
        var field = item.input.closest(".wc-block-components-text-input, .wc-block-components-combobox, .wc-block-components-checkbox, .wc-block-components-checkout-step");
        if (field) {
          field.classList.add("is-nj-invalid");
        }
      });
      var sidebar = document.querySelector(".wc-block-checkout__sidebar");
      var near = document.querySelector(".nj-checkout-close__error");
      if (sidebar) {
        if (!near) {
          near = document.createElement("p");
          near.className = "nj-checkout-close__error";
          sidebar.insertBefore(near, sidebar.firstChild);
        }
        near.textContent = "No se puede pagar. Falta " + problems.map(function (item) {
          return item.label.toLowerCase();
        }).join(", ") + ".";
      }
    }

    function checkoutConfirm() {
      checkoutTried = true;
      var problems = checkoutProblems();
      checkoutAlert(problems);
      var alertBox = document.querySelector(".nj-checkout-alert");
      if (alertBox) {
        alertBox.scrollIntoView({ behavior: "auto", block: "start" });
      }
      var real = document.querySelector(".wc-block-components-checkout-place-order-button");
      if (real && !real.disabled) {
        real.click();
      }
    }

    function checkoutWatch() {
      var real = document.querySelector(".wc-block-components-checkout-place-order-button");
      if (!real || real.getAttribute("data-nj-watch") === "1") {
        return;
      }
      real.setAttribute("data-nj-watch", "1");
      real.addEventListener("click", function () {
        checkoutTried = true;
        window.setTimeout(function () {
          var problems = checkoutProblems();
          checkoutAlert(problems);
          var alertBox = document.querySelector(".nj-checkout-alert");
          if (alertBox) {
            alertBox.scrollIntoView({ behavior: "auto", block: "start" });
          }
        }, 80);
      });
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
        panel.innerHTML = "<button type=\"button\" class=\"nj-checkout-close__button\">Confirmar pedido</button><p class=\"nj-checkout-close__note\">Pagas en Wompi con Nequi, tarjeta o PSE. El número de la tarjeta no se escribe aquí.</p><ul class=\"nj-checkout-trust\"><li>Nequi, tarjeta o PSE</li><li>Envío a Colombia</li><li>Cambio si la talla no queda</li></ul>";
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
      checkoutWatch();
      checkoutClose();
      checkoutBar();
      checkoutSyncBar();
      checkoutTranslate(checkout);
      checkoutMark();
      if (checkoutTried) {
        checkoutAlert(checkoutProblems());
      }
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

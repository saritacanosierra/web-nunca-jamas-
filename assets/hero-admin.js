(function ($) {
  var frame = null;
  var maxSlides = 6;

  function field(card, name) {
    return card.querySelector('[data-field="' + name + '"]');
  }

  function paint(card) {
    ["eyebrow", "title", "text", "button"].forEach(function (key) {
      var input = field(card, key);
      var out = card.querySelector('[data-preview="' + key + '"]');
      if (input && out) {
        out.textContent = input.value;
      }
    });
    var color = field(card, "color");
    var canvas = card.querySelector(".nj-hero-canvas");
    if (color && canvas) {
      canvas.style.setProperty("--hero-bg", color.value);
    }
  }

  function reindex(list) {
    Array.prototype.forEach.call(list.querySelectorAll(".nj-hero-slide"), function (slide, index) {
      slide.querySelectorAll("[name]").forEach(function (input) {
        input.name = input.name.replace(/nj_hero\[[^\]]+\]/, "nj_hero[" + index + "]");
      });
      var badge = slide.querySelector(".nj-hero-index");
      if (badge) {
        badge.textContent = String(index + 1);
      }
    });
    var add = document.querySelector("[data-hero-add]");
    if (add) {
      add.disabled = list.querySelectorAll(".nj-hero-slide").length >= maxSlides;
    }
  }

  function setMode(card, mode) {
    card.classList.toggle("is-image", mode === "image");
    card.classList.toggle("is-build", mode === "build");
    var input = field(card, "type");
    if (input) {
      input.value = mode;
    }
  }

  function pick(card) {
    if (!window.wp || !wp.media) {
      return;
    }
    frame = wp.media({
      title: "Imagen del hero",
      button: { text: "Usar esta imagen" },
      library: { type: "image" },
      multiple: false
    });
    frame.on("select", function () {
      var file = frame.state().get("selection").first().toJSON();
      var image = field(card, "image");
      var src = field(card, "src");
      var photo = card.querySelector(".nj-hero-photo");
      if (image) {
        image.value = String(file.id || 0);
      }
      if (src) {
        src.value = "";
      }
      if (photo) {
        photo.src = file.url || "";
        photo.hidden = !photo.src;
      }
    });
    frame.open();
  }

  $(function () {
    var list = document.querySelector("[data-hero-list]");
    if (!list) {
      return;
    }
    reindex(list);
    list.querySelectorAll(".nj-hero-slide").forEach(paint);

    list.addEventListener("click", function (event) {
      var card = event.target.closest(".nj-hero-slide");
      if (!card) {
        return;
      }
      if (event.target.closest(".nj-hero-mode")) {
        setMode(card, event.target.closest(".nj-hero-mode").getAttribute("data-mode"));
      }
      if (event.target.closest(".nj-hero-pick")) {
        pick(card);
      }
      if (event.target.closest(".nj-hero-clear")) {
        var image = field(card, "image");
        var src = field(card, "src");
        var photo = card.querySelector(".nj-hero-photo");
        if (image) {
          image.value = "0";
        }
        if (src) {
          src.value = "";
        }
        if (photo) {
          photo.removeAttribute("src");
          photo.hidden = true;
        }
      }
      if (event.target.closest(".nj-hero-remove")) {
        if (list.querySelectorAll(".nj-hero-slide").length === 1) {
          return;
        }
        card.remove();
        reindex(list);
      }
      if (event.target.closest(".nj-hero-up") && card.previousElementSibling) {
        list.insertBefore(card, card.previousElementSibling);
        reindex(list);
      }
      if (event.target.closest(".nj-hero-down") && card.nextElementSibling) {
        list.insertBefore(card.nextElementSibling, card);
        reindex(list);
      }
    });

    list.addEventListener("input", function (event) {
      var card = event.target.closest(".nj-hero-slide");
      if (card) {
        paint(card);
      }
    });

    var form = list.closest("form");
    if (form) {
      form.addEventListener("submit", function (event) {
        var reset = event.submitter && event.submitter.name === "nj_hero_reset";
        if (reset && !window.confirm("Se quitan las diapositivas guardadas y vuelven los banners del tema.")) {
          event.preventDefault();
        }
      });
    }

    var add = document.querySelector("[data-hero-add]");
    var template = document.getElementById("nj-hero-template");
    if (add && template) {
      add.addEventListener("click", function () {
        if (list.querySelectorAll(".nj-hero-slide").length >= maxSlides) {
          return;
        }
        var node = template.content.firstElementChild.cloneNode(true);
        list.appendChild(node);
        reindex(list);
        paint(node);
        node.scrollIntoView({ behavior: "smooth", block: "center" });
      });
    }
  });
})(jQuery);

// Language Switcher

document.addEventListener("DOMContentLoaded", function () {
  const switchers = document.querySelectorAll(
    ".lang-switcher, .lang-switcher-mobile",
  );

  const buttons = document.querySelectorAll(
    ".lang-active, .lang-active-mobile",
  );

  if (!switchers.length || !buttons.length) return;

  buttons.forEach(function (button) {
    button.addEventListener("click", function (e) {
      e.stopPropagation();

      const switcher = button.closest(".lang-switcher, .lang-switcher-mobile");

      if (!switcher) return;

      switcher.classList.toggle("open");
    });
  });

  document.addEventListener("click", function () {
    switchers.forEach(function (switcher) {
      switcher.classList.remove("open");
    });
  });
});
// Main Page Post Slider
document.addEventListener("DOMContentLoaded", function () {
  const track = document.querySelector(".articles-track");
  const next = document.querySelector(".art-btn.next");
  const prev = document.querySelector(".art-btn.prev");

  if (!track || !next || !prev) return;

  let index = 0;
  const gap = 15;

  function getItemWidth() {
    const item = track.children[0];
    return item.offsetWidth + gap;
  }

  const totalItems = track.children.length;
  const visibleItems = 4;
  const maxIndex = Math.max(0, totalItems - visibleItems);

  next.addEventListener("click", () => {
    if (index < maxIndex) {
      index++;
      updateSlider();
    }
  });

  prev.addEventListener("click", () => {
    if (index > 0) {
      index--;
      updateSlider();
    }
  });

  function updateSlider() {
    const move = getItemWidth() * index;
    track.style.transform = `translateX(-${move}px)`;
  }
});

// Post Page Share Button
const share = document.querySelector(".share-wrapper");
if (share) {
  share.querySelector(".share-toggle").addEventListener("click", function (e) {
    e.stopPropagation();
    share.classList.toggle("active");
  });

  document.addEventListener("click", function () {
    share.classList.remove("active");
  });
}

// Post Page Copy Button
const copy = document.querySelector(".copy-link");
if (copy) {
  copy.onclick = function () {
    navigator.clipboard.writeText(window.location.href);
    alert("لینک کپی شد");
  };
}

// Favorite Button
const favBtn = document.querySelector(".favorite-btn");
if (favBtn) {
  favBtn.addEventListener("click", () => {
    favBtn.classList.toggle("active");
  });
}

// Comment Form Validation (FIXED)
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("commentform");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let valid = true;

    const author = document.getElementById("author");
    const phone = document.getElementById("phone");

    clearError(author);
    clearError(phone);

    const authorValue = author.value.trim();
    const phoneValue = phone.value.trim();

    // Reviewing first and last names
    if (!/^[\p{L}\s]+$/u.test(authorValue)) {
      showError(author, "نام و نام خانوادگی معتبر نیست.");
      valid = false;
    }

    // Check contact number
    if (!/^09\d{9}$/.test(phoneValue)) {
      showError(phone, "شماره تماس معتبر نیست.");
      valid = false;
    }

    if (!valid) {
      e.preventDefault();
    }
  });

  function showError(input, message) {
    if (!input) return;
    input.classList.add("input-error");

    const errorElement = input.parentElement.querySelector(".form-error");
    if (errorElement) {
      errorElement.textContent = message;
    }
  }

  function clearError(input) {
    if (!input) return;
    input.classList.remove("input-error");

    const errorElement = input.parentElement.querySelector(".form-error");
    if (errorElement) {
      errorElement.textContent = "";
    }
  }
});

// Mobile Navigation
document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;
  const menuToggle = document.getElementById("mobileMenuToggle");
  const menuClose = document.getElementById("mobileMenuClose");
  const menuOverlay = document.getElementById("mobileMenuOverlay");

  if (!menuToggle || !menuClose || !menuOverlay) {
    return;
  }

  const openMenu = () => {
    body.classList.add("mobile-menu-open");
    menuToggle.setAttribute("aria-expanded", "true");
    menuOverlay.setAttribute("aria-hidden", "false");
  };

  const closeMenu = () => {
    body.classList.remove("mobile-menu-open");
    menuToggle.setAttribute("aria-expanded", "false");
    menuOverlay.setAttribute("aria-hidden", "true");
  };

  menuToggle.addEventListener("click", openMenu);
  menuClose.addEventListener("click", closeMenu);
  menuOverlay.addEventListener("click", closeMenu);

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeMenu();
    }
  });
});

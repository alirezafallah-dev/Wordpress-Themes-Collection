/**
 * =========================================================
 * Archive Filter System - Professional Version
 * =========================================================
 * Handles AJAX filtering, search, sort, and pagination for archive pages
 */
document.addEventListener("DOMContentLoaded", () => {
  // ==========================================
  // DOM Elements
  // ==========================================
  const postsContainer = document.querySelector("#posts-container");
  const pagination = document.querySelector("#pagination");
  const searchInput = document.querySelector("#search-input");
  const sortSelect = document.querySelector("#archive-sort");
  const categories = document.querySelectorAll(".archive-categories li");
  const postsCount = document.querySelector(".posts-count");

// Checking for the presence of essential elements
  if (!postsContainer || !pagination) {
    console.error("Archive: Required DOM elements not found");
    return;
  }

  // ==========================================
  // State Management
  // ==========================================
  let state = {
    page: 1,
    category: "all",
    search: "",
    sort: "date_desc",
  };

  let debounceTimer;

  // ==========================================
  // Loading State
  // ==========================================
  function loading(show = true) {
    if (show) {
      postsContainer.style.opacity = "0.5";
      postsContainer.style.pointerEvents = "none";
    } else {
      postsContainer.style.opacity = "1";
      postsContainer.style.pointerEvents = "auto";
    }
  }

  // ==========================================
  // Load Posts via AJAX
  // ==========================================
  async function loadPosts(push = true) {
    loading(true);

    const form = new FormData();
    form.append("action", "archive_filter");
    form.append(
      "nonce",
      typeof archive_ajax !== "undefined" ? archive_ajax.nonce : "",
    );
    form.append("page", state.page);
    form.append("category", state.category);
    form.append("search", state.search);
    form.append("sort", state.sort);

    const ajaxUrl =
      typeof archive_ajax !== "undefined"
        ? archive_ajax.ajax_url
        : "/wp-admin/admin-ajax.php";

    try {
      const res = await fetch(ajaxUrl, {
        method: "POST",
        body: form,
      });

      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }

      const data = await res.json();

      if (data.posts) {
        postsContainer.innerHTML = data.posts;
      } else {
        postsContainer.innerHTML =
          '<p class="no-posts">خطا در بارگذاری پست‌ها.</p>';
      }

      if (data.pagination) {
        pagination.innerHTML = data.pagination;
      } else {
        pagination.innerHTML = "";
      }

      if (data.count !== undefined && postsCount) {
        postsCount.textContent = data.count + " Posts";
      }

      loading(false);
      bindPagination();

      if (push) {
        const url = new URL(window.location);
        url.searchParams.set("page", state.page);
        url.searchParams.set("category", state.category);
        url.searchParams.set("search", state.search);
        url.searchParams.set("sort", state.sort);
        history.pushState(state, "", url);
      }
    } catch (error) {
      console.error("Archive load error:", error);
      postsContainer.innerHTML =
        '<p class="no-posts">خطا در ارتباط با سرور.</p>';
      loading(false);
    }
  }

  // ==========================================
  // Search Handler (with Debounce)
  // ==========================================
  if (searchInput) {
    searchInput.addEventListener("keyup", () => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        state.search = searchInput.value;
        state.page = 1;
        loadPosts();
      }, 400);
    });
  }

  // ==========================================
  // Sort Handler
  // ==========================================
  if (sortSelect) {
    sortSelect.addEventListener("change", function () {
      state.sort = this.value;
      state.page = 1;
      loadPosts();
    });
  }

  // ==========================================
  // Category Filter Handler
  // ==========================================
  categories.forEach((item) => {
    item.addEventListener("click", function () {
      categories.forEach((i) => i.classList.remove("active"));
      this.classList.add("active");
      state.category = this.dataset.category || "all";
      state.page = 1;
      loadPosts();
    });
  });

  // ==========================================
  // Pagination Handler
  // ==========================================
  function bindPagination() {
    document.querySelectorAll(".page-numbers").forEach((item) => {
      item.addEventListener("click", function (e) {
        e.preventDefault();
        let href = this.getAttribute("href");
        if (!href) return;

        try {
          const url = new URL(href, window.location.origin);
          state.page = url.searchParams.get("paged") || 1;
          loadPosts();

          // Scroll to toolbar
          const toolbar = document.querySelector(".archive-toolbar");
          if (toolbar) {
            window.scrollTo({
              top: toolbar.offsetTop - 30,
              behavior: "smooth",
            });
          }
        } catch (error) {
          console.error("Pagination error:", error);
        }
      });
    });
  }

  // ==========================================
  // Browser Back/Forward Handler
  // ==========================================
  window.addEventListener("popstate", function (e) {
    if (e.state) {
      state = e.state;
      if (searchInput) searchInput.value = state.search;
      if (sortSelect) sortSelect.value = state.sort;
      loadPosts(false);
    }
  });

  // ==========================================
  // Initial Load
  // ==========================================
  bindPagination();
});

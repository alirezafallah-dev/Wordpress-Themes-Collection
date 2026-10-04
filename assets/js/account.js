document.addEventListener("DOMContentLoaded", () => {
  // ==========================================
  // Tab Switching
  // ==========================================
  const navItems = document.querySelectorAll(".nav-item");
  const tabContents = document.querySelectorAll(".tab-content");

  navItems.forEach((item) => {
    item.addEventListener("click", () => {
      const targetTab = item.dataset.tab;

      navItems.forEach((i) => i.classList.remove("active"));
      item.classList.add("active");

      tabContents.forEach((content) => {
        content.hidden = true;
        content.classList.remove("active");
      });

      const targetContent = document.getElementById(`tab-${targetTab}`);
      if (targetContent) {
        targetContent.hidden = false;
        targetContent.classList.add("active");
      }
    });
  });

  // ==========================================
  // Support System (SPA Pattern)
  // ==========================================
  const supportListView = document.getElementById("supportListView");
  const supportDetailView = document.getElementById("supportDetailView");
  const supportNewView = document.getElementById("supportNewView");
  const ticketsDataEl = document.getElementById("support-tickets-data");
  const ticketsData = ticketsDataEl
    ? JSON.parse(ticketsDataEl.textContent)
    : [];

  function showSupportView(viewId) {
    [supportListView, supportDetailView, supportNewView].forEach((v) => {
      if (v) {
        v.hidden = true;
        v.classList.remove("active");
      }
    });

    const target = document.getElementById(viewId);
    if (target) {
      target.hidden = false;
      target.classList.add("active");
    }
  }

  // Navigation Buttons (FIXED)
  const showNewTicketBtn = document.getElementById("showNewTicketBtn");
  if (showNewTicketBtn) {
    showNewTicketBtn.addEventListener("click", () =>
      showSupportView("supportNewView"),
    );
  }

  const backFromNewBtn = document.getElementById("backFromNewBtn");
  if (backFromNewBtn) {
    backFromNewBtn.addEventListener("click", () =>
      showSupportView("supportListView"),
    );
  }

  const backToListBtn = document.getElementById("backToListBtn");
  if (backToListBtn) {
    backToListBtn.addEventListener("click", () =>
      showSupportView("supportListView"),
    );
  }

  // ==========================================
  // Open Ticket Detail (Event Delegation)
  // ==========================================
  document.addEventListener("click", (e) => {
    const ticketCard = e.target.closest(".ticket-card");
    if (!ticketCard) return;

    const ticketId = parseInt(ticketCard.dataset.ticketId);
    const ticket = ticketsData.find((t) => t.id === ticketId);

    if (!ticket) {
      console.error(
        "Ticket not found:",
        ticketId,
        "Available tickets:",
        ticketsData,
      );
      alert("تیکت مورد نظر یافت نشد.");
      return;
    }

    const detailSubject = document.getElementById("detailSubject");
    if (detailSubject) detailSubject.textContent = ticket.subject;

    const replyTicketId = document.getElementById("replyTicketId");
    if (replyTicketId) replyTicketId.value = ticket.id;

    const convBox = document.getElementById("conversationBox");
    if (convBox) {
      convBox.innerHTML = "";

      // 1. Initial Message (User)
      const initialBubble = document.createElement("div");
      initialBubble.className = "chat-bubble user-bubble";
      initialBubble.innerHTML = `
        <div class="bubble-meta">شما • ${ticket.created_at}</div>
        <div class="bubble-text">${ticket.message.replace(/\n/g, "<br>")}</div>
      `;
      convBox.appendChild(initialBubble);

      // 2. Replies (Admin & User)
      if (ticket.replies && ticket.replies.length > 0) {
        ticket.replies.forEach((r) => {
          const isAdmin = r.author_role === "admin";
          const author = isAdmin ? "پشتیبانی سایت" : "شما";
          const cls = isAdmin ? "admin-bubble" : "user-bubble";
          const replyBubble = document.createElement("div");
          replyBubble.className = `chat-bubble ${cls}`;
          replyBubble.innerHTML = `
            <div class="bubble-meta">${author} • ${r.created_at}</div>
            <div class="bubble-text">${r.message.replace(/\n/g, "<br>")}</div>
          `;
          convBox.appendChild(replyBubble);
        });
      }
    }

    showSupportView("supportDetailView");
    setTimeout(() => {
      if (convBox) convBox.scrollTop = convBox.scrollHeight;
    }, 100);
  });

  // ==========================================
  // Submit New Ticket
  // ==========================================
  const newTicketForm = document.getElementById("newTicketForm");
  if (newTicketForm) {
    newTicketForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const btn = newTicketForm.querySelector(".btn-primary");
      const msgBox = document.getElementById("newTicketMessage");

      if (!btn || !msgBox) return;

      btn.disabled = true;
      btn.textContent = "در حال ارسال...";
      msgBox.className = "form-feedback";
      msgBox.textContent = "";

      const formData = new FormData(newTicketForm);
      formData.append("action", "submit_support_ticket");

      const nonceInput = newTicketForm.querySelector(
        'input[name="ticket_nonce"]',
      );
      if (nonceInput) {
        formData.append("nonce", nonceInput.value);
      }

      try {
        const res = await fetch(accountConfig.ajaxUrl, {
          method: "POST",
          body: formData,
        });
        const data = await res.json();

        if (data.success) {
          msgBox.textContent = data.data.message;
          msgBox.classList.add("success");
          setTimeout(() => location.reload(), 1500);
        } else {
          msgBox.textContent = data.data.message || "خطایی رخ داد.";
          msgBox.classList.add("error");
          btn.disabled = false;
          btn.textContent = "ارسال پیام";
        }
      } catch (err) {
        msgBox.textContent = "ارتباط با سرور برقرار نشد.";
        msgBox.classList.add("error");
        btn.disabled = false;
        btn.textContent = "ارسال پیام";
      }
    });
  }

  // ==========================================
  // Submit Reply to Ticket
  // ==========================================
  const replyForm = document.getElementById("replyForm");
  if (replyForm) {
    replyForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const btn = replyForm.querySelector(".btn-primary");

      if (!btn) return;

      btn.disabled = true;
      btn.textContent = "در حال ارسال...";

      const formData = new FormData(replyForm);
      formData.append("action", "submit_ticket_reply");

      const replyNonceInput = replyForm.querySelector(
        'input[name="reply_nonce"]',
      );
      if (replyNonceInput) {
        formData.append("nonce", replyNonceInput.value);
      }

      try {
        const res = await fetch(accountConfig.ajaxUrl, {
          method: "POST",
          body: formData,
        });
        const data = await res.json();

        if (data.success) {
          setTimeout(() => location.reload(), 1000);
        } else {
          alert(data.data.message || "خطا در ارسال پاسخ.");
          btn.disabled = false;
          btn.textContent = "ارسال پاسخ";
        }
      } catch (err) {
        alert("خطای ارتباط با سرور.");
        btn.disabled = false;
        btn.textContent = "ارسال پاسخ";
      }
    });
  }

  // ==========================================
  // Support Ticket Form (Old Form - Compatibility)
  // ==========================================
  const ticketForm = document.getElementById("supportTicketForm");
  if (ticketForm) {
    ticketForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const btn = ticketForm.querySelector(".btn-primary");
      const msgBox = document.getElementById("ticketMessage");

      if (!btn || !msgBox) return;

      btn.disabled = true;
      btn.textContent = "در حال ارسال...";
      msgBox.className = "form-feedback";
      msgBox.textContent = "";

      const formData = new FormData(ticketForm);
      formData.append("action", "submit_support_ticket");

      const ticketNonce = document.getElementById("ticket_nonce");
      if (ticketNonce) {
        formData.append("nonce", ticketNonce.value);
      }

      try {
        const res = await fetch(accountConfig.ajaxUrl, {
          method: "POST",
          body: formData,
        });
        const data = await res.json();

        if (data.success) {
          msgBox.textContent = data.data.message;
          msgBox.classList.add("success");
          ticketForm.reset();
          setTimeout(() => location.reload(), 1500);
        } else {
          msgBox.textContent = data.data.message || "خطایی رخ داد.";
          msgBox.classList.add("error");
        }
      } catch (err) {
        msgBox.textContent = "ارتباط با سرور برقرار نشد.";
        msgBox.classList.add("error");
      } finally {
        btn.disabled = false;
        btn.textContent = "ارسال پیام";
      }
    });
  }

  // ==========================================
  // Profile Save (Name + Mobile)
  // ==========================================
  const saveBtn = document.getElementById("accountSaveButton");
  if (saveBtn) {
    saveBtn.addEventListener("click", async () => {
      const nameInput = document.getElementById("accountDisplayName");
      const mobileInput = document.getElementById("accountMobile");
      const msgBox = document.getElementById("accountMessage");

      if (!nameInput || !msgBox) return;

      const displayName = nameInput.value.trim();
      const mobile = mobileInput ? mobileInput.value.trim() : "";

      // Name validation
      if (!displayName) {
        msgBox.textContent = "نام نمی‌تواند خالی باشد.";
        msgBox.className = "form-feedback error";
        return;
      }

      // Mobile validation (if entered)
      if (mobile && !/^09\d{9}$/.test(mobile)) {
        msgBox.textContent = "شماره موبایل معتبر نیست. (مثال: 09123456789)";
        msgBox.className = "form-feedback error";
        if (mobileInput) mobileInput.focus();
        return;
      }

      saveBtn.disabled = true;
      saveBtn.textContent = "در حال ذخیره...";
      msgBox.className = "form-feedback";
      msgBox.textContent = "";

      try {
        // 1. Save name
        const nameFormData = new FormData();
        nameFormData.append("action", "auth_update_account");
        nameFormData.append(
          "nonce",
          accountConfig.accountNonce || authGoogleConfig.accountNonce,
        );
        nameFormData.append("display_name", displayName);

        const nameRes = await fetch(
          accountConfig.ajaxUrl || authGoogleConfig.ajaxUrl,
          {
            method: "POST",
            body: nameFormData,
            credentials: "same-origin",
          },
        );
        const nameData = await nameRes.json();

        if (!nameData.success) {
          throw new Error(nameData.data?.message || "خطا در ذخیره نام.");
        }

        // 2. Save mobile number (if provided)
        if (mobile) {
          const mobileFormData = new FormData();
          mobileFormData.append("action", "save_booking_mobile");
          mobileFormData.append(
            "nonce",
            accountConfig.mobileNonce || authGoogleConfig.smsNonce,
          );
          mobileFormData.append("mobile", mobile);

          const mobileRes = await fetch(
            accountConfig.ajaxUrl || authGoogleConfig.ajaxUrl,
            {
              method: "POST",
              body: mobileFormData,
              credentials: "same-origin",
            },
          );
          const mobileData = await mobileRes.json();

          if (!mobileData.success) {
            throw new Error(
              mobileData.data?.message || "خطا در ذخیره شماره موبایل.",
            );
          }
        }

        msgBox.textContent = "تغییرات با موفقیت ذخیره شد.";
        msgBox.className = "form-feedback success";
      } catch (err) {
        msgBox.textContent = err.message || "خطایی رخ داد.";
        msgBox.className = "form-feedback error";
      } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = "ذخیره تغییرات";
      }
    });
  }
});

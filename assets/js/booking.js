/**
 * =========================================================
 * Lawyer Booking System - Professional Version
 * =========================================================
 * Handles calendar rendering, time slot selection, and booking submission
 */
document.addEventListener("DOMContentLoaded", () => {
  // 1. Retrieve data from JSON
  const dataElement = document.getElementById("booking-data");
  if (!dataElement) {
    console.error("Booking: booking-data element not found");
    return;
  }

  let DATA;
  try {
    DATA = JSON.parse(dataElement.textContent);
  } catch (e) {
    console.error("Booking: Invalid JSON data", e);
    return;
  }
  
  // 2. DOM Elements
  const calGrid = document.getElementById("calGrid");
  const calTitle = document.getElementById("calTitle");
  const timeSlots = document.getElementById("timeSlots");
  const summaryBox = document.getElementById("summaryBox");
  const confirmBtn = document.getElementById("confirmBookingBtn");

  if (!calGrid || !calTitle || !timeSlots || !summaryBox || !confirmBtn) {
    console.error("Booking: Required DOM elements not found");
    return;
  }

  // 3. State
  let selectedConsultation = null;
  let selectedBookingTime = null;
  let calendarDate = new Date();
  let currentCalendar = "persian";

  // ==========================================
  // Convert to Solar (Shamsi) date
  // ==========================================
  function getPersianDateStr(date) {
    const parts = new Intl.DateTimeFormat("fa-IR-u-ca-persian", {
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
    }).formatToParts(date);

    const toEn = (str) => str.replace(/[۰-۹]/g, (d) => "۰۱۲۳۴۵۶۷۸۹".indexOf(d));

    const y = toEn(parts.find((p) => p.type === "year").value);
    const m = toEn(parts.find((p) => p.type === "month").value).padStart(
      2,
      "0",
    );
    const d = toEn(parts.find((p) => p.type === "day").value).padStart(2, "0");

    return `${y}/${m}/${d}`;
  }

  // ==========================================
  // Calendar render
  // ==========================================
  function renderCalendar() {
    calGrid.innerHTML = "";

    if (!selectedConsultation) {
      calTitle.textContent = "ابتدا نوع مشاوره را انتخاب کنید";
      return;
    }

    const formatter = new Intl.DateTimeFormat(`fa-IR-u-ca-${currentCalendar}`, {
      year: "numeric",
      month: "numeric",
      day: "numeric",
    });

    const parts = formatter.formatToParts(calendarDate);
    const toEn = (str) => str.replace(/[۰-۹]/g, (d) => "۰۱۲۳۴۵۶۷۸۹".indexOf(d));

    const year = parseInt(toEn(parts.find((p) => p.type === "year").value));
    const month = parseInt(toEn(parts.find((p) => p.type === "month").value));
    const day = parseInt(toEn(parts.find((p) => p.type === "day").value));

    const firstDay = new Date(calendarDate);
    firstDay.setDate(firstDay.getDate() - (day - 1));

    calTitle.textContent = new Intl.DateTimeFormat(
      `fa-IR-u-ca-${currentCalendar}`,
      { year: "numeric", month: "long" },
    ).format(firstDay);

    const gregorianWeekday = firstDay.getDay();
    const startDayOffset = (gregorianWeekday + 1) % 7;

    let daysInMonth = 0;
    for (let i = 1; i <= 32; i++) {
      const testDate = new Date(firstDay);
      testDate.setDate(firstDay.getDate() + (i - 1));
      const testMonth = parseInt(
        toEn(
          formatter.formatToParts(testDate).find((p) => p.type === "month")
            .value,
        ),
      );
      if (testMonth !== month) break;
      daysInMonth = i;
    }

    // Adding empty cells for the start of the week
    for (let i = 0; i < startDayOffset; i++) {
      calGrid.appendChild(document.createElement("div"));
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let d = 1; d <= daysInMonth; d++) {
      const currentDate = new Date(firstDay);
      currentDate.setDate(firstDay.getDate() + (d - 1));

      const gYear = currentDate.getFullYear();
      const gMonth = String(currentDate.getMonth() + 1).padStart(2, "0");
      const gDay = String(currentDate.getDate()).padStart(2, "0");
      const gregorianStr = `${gYear}-${gMonth}-${gDay}`;

      const persianStr = getPersianDateStr(currentDate);
      const isHoliday = DATA.holidays && DATA.holidays.includes(persianStr);
      const isFriday = currentDate.getDay() === 5;
      const dayOfWeek = currentDate.getDay();
      const schedule = DATA.workingHours ? DATA.workingHours[dayOfWeek] : null;
      const isWorkingDay = schedule && schedule.is_active === 1;
      const isPast = currentDate < today;

      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "calendar-day";
      btn.textContent = d;
      btn.dataset.date = gregorianStr;

      if (isPast || !isWorkingDay || isHoliday || isFriday) {
        btn.classList.add("disabled");
        btn.disabled = true;
        if (isHoliday) btn.title = "تعطیل رسمی";
        else if (isFriday) btn.title = "تعطیل هفتگی";
        else if (isPast) btn.title = "تاریخ گذشته";
        else btn.title = "روز غیرکاری";
      } else {
        btn.addEventListener("click", () => {
          document
            .querySelectorAll(".calendar-day.selected")
            .forEach((el) => el.classList.remove("selected"));
          btn.classList.add("selected");
          renderTimeSlots(gregorianStr);
        });
      }

      calGrid.appendChild(btn);
    }
  }

  // ==========================================
  // Time-based rendering
  // ==========================================
  function renderTimeSlots(date) {
    timeSlots.innerHTML = "";
    selectedBookingTime = null;
    updateSummary();

    const selectedDate = new Date(date + "T00:00:00");
    const dayOfWeek = selectedDate.getDay();
    const schedule = DATA.workingHours ? DATA.workingHours[dayOfWeek] : null;

    if (!schedule || schedule.is_active !== 1) {
      timeSlots.innerHTML =
        '<p class="empty-state">این روز قابل رزرو نیست.</p>';
      return;
    }

    const [startH, startM] = schedule.start_time.split(":").map(Number);
    const [endH, endM] = schedule.end_time.split(":").map(Number);

    let currentMinutes = startH * 60 + startM;
    const endMinutes = endH * 60 + endM;
    let hasSlot = false;

    while (currentMinutes + selectedConsultation.duration <= endMinutes) {
      const sH = Math.floor(currentMinutes / 60);
      const sM = currentMinutes % 60;
      const startTime = `${String(sH).padStart(2, "0")}:${String(sM).padStart(2, "0")}`;

      const endTotal = currentMinutes + selectedConsultation.duration;
      const eH = Math.floor(endTotal / 60);
      const eM = endTotal % 60;
      const endTime = `${String(eH).padStart(2, "0")}:${String(eM).padStart(2, "0")}`;

      // Reviewing previous reservations
      const isBooked =
        DATA.bookedSlots &&
        DATA.bookedSlots.some(
          (slot) =>
            slot.booking_date === date &&
            startTime < slot.end_time.substring(0, 5) &&
            endTime > slot.start_time.substring(0, 5),
        );

      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "time-btn";
      btn.textContent = `${startTime} - ${endTime}`;

      if (isBooked) {
        btn.classList.add("booked");
        btn.disabled = true;
        btn.title = "این ساعت قبلاً رزرو شده";
      } else {
        hasSlot = true;
        btn.addEventListener("click", () => {
          document
            .querySelectorAll(".time-btn.selected")
            .forEach((el) => el.classList.remove("selected"));
          btn.classList.add("selected");
          selectedBookingTime = { date, startTime, endTime };
          updateSummary();
        });
      }

      timeSlots.appendChild(btn);
      currentMinutes += selectedConsultation.duration;
    }

    if (!hasSlot) {
      timeSlots.innerHTML =
        '<p class="empty-state">هیچ ساعت خالی برای این روز وجود ندارد.</p>';
    }
  }

  // ==========================================
  // Booking Summary Update (with type display)
  // ==========================================
  function updateSummary() {
    if (selectedConsultation && selectedBookingTime) {
      summaryBox.hidden = false;

      const typeCategory = document.getElementById("sumTypeCategory");
      if (typeCategory) {
        if (selectedConsultation.type === "offline") {
          typeCategory.innerHTML =
            '<span class="summary-badge offline">حضوری</span>';
        } else {
          typeCategory.innerHTML =
            '<span class="summary-badge online">تلفنی / آنلاین</span>';
        }
      }

      const sumType = document.getElementById("sumType");
      if (sumType) sumType.textContent = selectedConsultation.title;

      const sumDate = document.getElementById("sumDate");
      if (sumDate) sumDate.textContent = selectedBookingTime.date;

      const sumTime = document.getElementById("sumTime");
      if (sumTime) {
        sumTime.textContent = `${selectedBookingTime.startTime} تا ${selectedBookingTime.endTime}`;
      }

      const sumPrice = document.getElementById("sumPrice");
      if (sumPrice) {
        sumPrice.textContent =
          new Intl.NumberFormat("fa-IR").format(selectedConsultation.price) +
          " تومان";
      }

      confirmBtn.disabled = false;
    } else {
      summaryBox.hidden = true;
      confirmBtn.disabled = true;
    }
  }

  // ==========================================
  // Event Listeners - Consultation Items
  // ==========================================
  document.querySelectorAll(".consultation-item").forEach((item) => {
    item.addEventListener("click", () => {
      document
        .querySelectorAll(".consultation-item")
        .forEach((i) => i.classList.remove("active"));
      item.classList.add("active");

      selectedConsultation = {
        id: item.dataset.id,
        title: item.dataset.title,
        duration: parseInt(item.dataset.duration),
        price: parseInt(item.dataset.price),
        type: item.dataset.type || "online",
      };

      renderCalendar();
    });
  });

  // ==========================================
  // Calendar Switcher
  // ==========================================
  document.querySelectorAll(".calendar-switcher button").forEach((btn) => {
    btn.addEventListener("click", () => {
      document
        .querySelectorAll(".calendar-switcher button")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      currentCalendar = btn.dataset.cal;
      renderCalendar();
    });
  });

  // ==========================================
  // Change of month
  // ==========================================
  function changeMonth(offset) {
    const formatter = new Intl.DateTimeFormat(`fa-IR-u-ca-${currentCalendar}`, {
      year: "numeric",
      month: "numeric",
      day: "numeric",
    });

    const toEn = (str) => str.replace(/[۰-۹]/g, (d) => "۰۱۲۳۴۵۶۷۸۹".indexOf(d));
    const parts = formatter.formatToParts(calendarDate);

    const currentMonth = parseInt(
      toEn(parts.find((p) => p.type === "month").value),
    );
    const currentDay = parseInt(
      toEn(parts.find((p) => p.type === "day").value),
    );

    const firstDay = new Date(calendarDate);
    firstDay.setDate(firstDay.getDate() - (currentDay - 1));

    let targetDate = new Date(firstDay);

    if (offset > 0) {
      // Go to the next month
      for (let i = 0; i < 40; i++) {
        targetDate.setDate(targetDate.getDate() + 1);
        const testMonth = parseInt(
          toEn(
            formatter.formatToParts(targetDate).find((p) => p.type === "month")
              .value,
          ),
        );
        if (testMonth !== currentMonth) {
          const testDay = parseInt(
            toEn(
              formatter.formatToParts(targetDate).find((p) => p.type === "day")
                .value,
            ),
          );
          targetDate.setDate(targetDate.getDate() + (15 - testDay));
          break;
        }
      }
    } else {
      // Go to the previous month
      targetDate.setDate(targetDate.getDate() - 1);
      const prevDay = parseInt(
        toEn(
          formatter.formatToParts(targetDate).find((p) => p.type === "day")
            .value,
        ),
      );
      targetDate.setDate(targetDate.getDate() + (15 - prevDay));
    }

    calendarDate = targetDate;
    renderCalendar();
  }

  const calPrev = document.getElementById("calPrev");
  const calNext = document.getElementById("calNext");

  if (calPrev) calPrev.addEventListener("click", () => changeMonth(-1));
  if (calNext) calNext.addEventListener("click", () => changeMonth(1));

  // ==========================================
  // Final confirmation button (with Nonce and improved UX)
  // ==========================================
  confirmBtn.addEventListener("click", async function () {
    // 1. Check login
    if (!DATA.isLoggedIn) {
      // Save temporary reservation
      localStorage.setItem(
        "pending_booking",
        JSON.stringify({
          consultationId: selectedConsultation.id,
          date: selectedBookingTime.date,
          startTime: selectedBookingTime.startTime,
          endTime: selectedBookingTime.endTime,
        }),
      );

      // Open login modal
      const authOpenBtn = document.querySelector("[data-auth-open]");
      if (authOpenBtn) {
        authOpenBtn.click();
      } else {
        alert("برای ادامه لطفاً وارد حساب کاربری شوید.");
      }
      return;
    }

    // 2. Mobile check (no prompt!)
    const mobile = DATA.currentUser && DATA.currentUser.mobile;

    if (!mobile) {
      // Redirect to user account page
      const result = confirm(
        "برای ثبت رزرو نیاز به شماره موبایل دارید.\nآیا می‌خواهید به صفحه تنظیمات حساب کاربری بروید؟",
      );
      if (result) {
        window.location.href = "/account/";
      }
      return;
    }

    // 3. Sending the request to the server
    confirmBtn.disabled = true;
    const originalText = confirmBtn.textContent;
    confirmBtn.textContent = "در حال ثبت...";

    const formData = new FormData();
    formData.append("action", "create_lawyer_booking");
    formData.append("consultation_id", selectedConsultation.id);
    formData.append("booking_date", selectedBookingTime.date);
    formData.append("start_time", selectedBookingTime.startTime);
    formData.append("end_time", selectedBookingTime.endTime);

    // Add nonce for security
    if (typeof bookingConfig !== "undefined" && bookingConfig.bookingNonce) {
      formData.append("nonce", bookingConfig.bookingNonce);
    }

    try {
      const ajaxUrl =
        typeof bookingConfig !== "undefined" && bookingConfig.ajaxUrl
          ? bookingConfig.ajaxUrl
          : DATA.ajaxUrl || "/wp-admin/admin-ajax.php";

      const res = await fetch(ajaxUrl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      });

      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }

      const data = await res.json();

      if (data.success) {
        alert("رزرو با موفقیت ثبت شد.\nکد پیگیری: " + data.data.booking_id);
        confirmBtn.textContent = "تأیید و پرداخت";
        confirmBtn.disabled = false;

        // TODO: در فاز بعدی، هدایت به درگاه پرداخت
        // if (data.data.payment_url) {
        //   window.location.href = data.data.payment_url;
        // }
      } else {
        alert(data.data.message || "خطایی در ثبت رزرو رخ داد.");
        confirmBtn.disabled = false;
        confirmBtn.textContent = originalText;
      }
    } catch (e) {
      console.error("Booking error:", e);
      alert("ارتباط با سرور برقرار نشد. لطفاً دوباره تلاش کنید.");
      confirmBtn.disabled = false;
      confirmBtn.textContent = originalText;
    }
  });

  // ==========================================
  // Initial render
  // ==========================================
  renderCalendar();
});

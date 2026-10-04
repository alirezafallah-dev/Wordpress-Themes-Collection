document.addEventListener("DOMContentLoaded", () => {
  /* =====================================================
   * ELEMENTS
   * ===================================================== */

  const modal = document.getElementById("authModal");

  if (!modal) {
    return;
  }

  const openButtons = document.querySelectorAll("[data-auth-open]");
  const closeButtons = modal.querySelectorAll("[data-auth-close]");
  const stepElements = modal.querySelectorAll("[data-auth-step]");
  const goButtons = modal.querySelectorAll("[data-auth-go]");
  const backButtons = modal.querySelectorAll("[data-auth-back]");

  const mobileForm = document.getElementById("authMobileForm");
  const otpForm = document.getElementById("authOtpForm");

  const mobileInput = document.getElementById("authMobile");
  const mobileError = document.getElementById("authMobileError");
  const otpError = document.getElementById("authOtpError");

  const mobileDisplay = document.getElementById("authMobileDisplay");

  const otpInputs = modal.querySelectorAll(".auth-otp-input");

  const resendButton = document.getElementById("authResendButton");
  const resendTimer = document.getElementById("authResendTimer");

  /* =====================================================
   * STATE
   * ===================================================== */

  let currentMobile = "";
  let countdownInterval = null;
  let countdownSeconds = 60;

  /* =====================================================
   * MODAL
   * ===================================================== */

  function openModal() {
    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");

    document.body.classList.add("auth-modal-open");

    resetForms();
    showStep("methods");

    setTimeout(() => {
      const firstFocusableElement = modal.querySelector(
        ".auth-method-button, .auth-modal-close",
      );

      if (firstFocusableElement) {
        firstFocusableElement.focus();
      }
    }, 100);
  }

  function closeModal() {
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");

    document.body.classList.remove("auth-modal-open");

    stopCountdown();
    resetForms();
  }

  openButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();
      openModal();
    });
  });

  closeButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();
      closeModal();
    });
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && modal.classList.contains("is-open")) {
      closeModal();
    }
  });

  /* =====================================================
   * STEP NAVIGATION
   * ===================================================== */

  function showStep(stepName) {
    stepElements.forEach((step) => {
      const isActive = step.dataset.authStep === stepName;

      step.hidden = !isActive;

      if (isActive) {
        requestAnimationFrame(() => {
          step.classList.add("is-active");
        });
      } else {
        step.classList.remove("is-active");
      }
    });
  }

  goButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const targetStep = button.dataset.authGo;

      if (!targetStep) {
        return;
      }

      showStep(targetStep);

      if (targetStep === "mobile") {
        setTimeout(() => {
          mobileInput?.focus();
        }, 100);
      }
    });
  });

  backButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const targetStep = button.dataset.authBack;

      if (!targetStep) {
        return;
      }

      showStep(targetStep);

      if (targetStep === "mobile") {
        setTimeout(() => {
          mobileInput?.focus();
        }, 100);
      }
    });
  });

  /* =====================================================
   * NUMBER NORMALIZATION
   * ===================================================== */

  function normalizePersianNumbers(value) {
    return String(value)
      .replace(/[۰-۹]/g, (digit) => {
        return String("۰۱۲۳۴۵۶۷۸۹".indexOf(digit));
      })
      .replace(/[٠-٩]/g, (digit) => {
        return String("٠١٢٣٤٥٦٧٨٩".indexOf(digit));
      });
  }

  /* =====================================================
   * MOBILE NORMALIZATION
   * ===================================================== */

  function normalizeMobile(value) {
    let mobile = normalizePersianNumbers(value);

    mobile = mobile.replace(/\D/g, "");

    if (mobile.startsWith("0098")) {
      mobile = "0" + mobile.substring(4);
    } else if (mobile.startsWith("98")) {
      mobile = "0" + mobile.substring(2);
    }

    return mobile;
  }

  /* =====================================================
   * MOBILE VALIDATION
   * ===================================================== */

  function validateMobile() {
    clearMobileError();

    const mobile = normalizeMobile(mobileInput.value);

    mobileInput.value = mobile;

    if (!mobile) {
      showMobileError("لطفاً شماره موبایل خود را وارد کنید.");

      return false;
    }

    if (!/^09\d{9}$/.test(mobile)) {
      showMobileError("شماره موبایل وارد شده صحیح نیست.");

      return false;
    }

    return true;
  }

  /* =====================================================
   * MOBILE INPUT
   * ===================================================== */

  mobileInput.addEventListener("input", () => {
    clearMobileError();

    let value = normalizePersianNumbers(mobileInput.value);

    value = value.replace(/\D/g, "");

    mobileInput.value = value.substring(0, 11);
  });

  /* =====================================================
   * FORMAT MOBILE
   * ===================================================== */

  function formatMobile(mobile) {
    if (!mobile) {
      return "";
    }

    return mobile.replace(/^(\d{4})(\d{3})(\d{4})$/, "$1 $2 $3");
  }

  /* =====================================================
   * MOBILE ERRORS
   * ===================================================== */

  function showMobileError(message) {
    mobileError.textContent = message;

    mobileInput.classList.add("has-error");
  }

  function clearMobileError() {
    mobileError.textContent = "";

    mobileInput.classList.remove("has-error");
  }

  /* =====================================================
   * SEND OTP
   * ===================================================== */

  async function sendOtp() {
    const formData = new FormData();

    formData.append("action", "auth_send_otp");

    formData.append("nonce", authGoogleConfig.smsNonce);

    formData.append("mobile", currentMobile);

    const response = await fetch(authGoogleConfig.ajaxUrl, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    return response.json();
  }

  /* =====================================================
   * MOBILE FORM
   * ===================================================== */

  mobileForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    if (!validateMobile()) {
      return;
    }

    currentMobile = normalizeMobile(mobileInput.value);

    mobileDisplay.textContent = formatMobile(currentMobile);

    const submitButton = mobileForm.querySelector('button[type="submit"]');

    if (submitButton) {
      submitButton.disabled = true;
    }

    try {
      const result = await sendOtp();

      if (!result.success) {
        showMobileError(
          result.data?.message || "ارسال کد تأیید با خطا مواجه شد.",
        );

        return;
      }

      showStep("otp");

      clearOtpInputs();

      startCountdown();

      setTimeout(() => {
        otpInputs[0]?.focus();
      }, 100);
    } catch (error) {
      console.error("SMS OTP request failed:", error);

      showMobileError("ارتباط با سرور برقرار نشد. لطفاً دوباره تلاش کنید.");
    } finally {
      if (submitButton) {
        submitButton.disabled = false;
      }
    }
  });

  /* =====================================================
   * OTP INPUT
   * ===================================================== */

  otpInputs.forEach((input, index) => {
    input.addEventListener("input", (event) => {
      let value = normalizePersianNumbers(event.target.value);

      value = value.replace(/\D/g, "");

      input.value = value.substring(0, 1);

      clearOtpError();

      if (input.value && index < otpInputs.length - 1) {
        otpInputs[index + 1].focus();
      }
    });

    input.addEventListener("keydown", (event) => {
      if (event.key === "Backspace" && !input.value && index > 0) {
        otpInputs[index - 1].focus();
      }

      if (event.key === "ArrowLeft" && index > 0) {
        otpInputs[index - 1].focus();
      }

      if (event.key === "ArrowRight" && index < otpInputs.length - 1) {
        otpInputs[index + 1].focus();
      }
    });

    input.addEventListener("paste", (event) => {
      event.preventDefault();

      const pastedData = event.clipboardData.getData("text");

      const code = normalizePersianNumbers(pastedData).replace(/\D/g, "");

      if (!code) {
        return;
      }

      const digits = code.substring(0, otpInputs.length).split("");

      digits.forEach((digit, digitIndex) => {
        if (otpInputs[digitIndex]) {
          otpInputs[digitIndex].value = digit;
        }
      });

      const lastIndex = Math.min(digits.length, otpInputs.length) - 1;

      if (lastIndex >= 0) {
        otpInputs[lastIndex].focus();
      }

      clearOtpError();
    });
  });

  /* =====================================================
   * GET OTP CODE
   * ===================================================== */

  function getOtpCode() {
    let code = "";

    otpInputs.forEach((input) => {
      code += input.value;
    });

    return code;
  }

  /* =====================================================
   * OTP ERRORS
   * ===================================================== */

  function showOtpError(message) {
    otpError.textContent = message;

    otpInputs.forEach((input) => {
      input.classList.add("has-error");
    });
  }

  function clearOtpError() {
    otpError.textContent = "";

    otpInputs.forEach((input) => {
      input.classList.remove("has-error");
    });
  }

  /* =====================================================
   * CLEAR OTP
   * ===================================================== */

  function clearOtpInputs() {
    otpInputs.forEach((input) => {
      input.value = "";
    });

    clearOtpError();
  }

  /* =====================================================
   * VERIFY OTP
   * ===================================================== */

  otpForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const otp = getOtpCode();

    clearOtpError();

    if (otp.length !== 6) {
      showOtpError("لطفاً کد تأیید ۶ رقمی را وارد کنید.");

      return;
    }

    const submitButton = otpForm.querySelector('button[type="submit"]');

    if (submitButton) {
      submitButton.disabled = true;
    }

    try {
      const formData = new FormData();

      formData.append("action", "auth_verify_otp");

      formData.append("nonce", authGoogleConfig.smsNonce);

      formData.append("mobile", currentMobile);

      formData.append("otp", otp);

      const response = await fetch(authGoogleConfig.ajaxUrl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      });

      const result = await response.json();

      if (!result.success) {
        showOtpError(result.data?.message || "کد تأیید صحیح نیست.");

        return;
      }

      stopCountdown();

      showStep("success");

      setTimeout(() => {
        window.location.reload();
      }, 800);
    } catch (error) {
      console.error("OTP verification request failed:", error);

      showOtpError("ارتباط با سرور برقرار نشد. لطفاً دوباره تلاش کنید.");
    } finally {
      if (submitButton) {
        submitButton.disabled = false;
      }
    }
  });

  /* =====================================================
   * COUNTDOWN
   * ===================================================== */

  function startCountdown() {
    stopCountdown();

    countdownSeconds = 60;

    resendButton.disabled = true;

    updateCountdown();

    countdownInterval = setInterval(() => {
      countdownSeconds--;

      updateCountdown();

      if (countdownSeconds <= 0) {
        stopCountdown();

        resendButton.disabled = false;

        resendTimer.textContent = "";
      }
    }, 1000);
  }

  function updateCountdown() {
    const seconds = String(countdownSeconds).padStart(2, "0");

    resendTimer.textContent = `00:${seconds}`;
  }

  function stopCountdown() {
    if (countdownInterval) {
      clearInterval(countdownInterval);

      countdownInterval = null;
    }
  }

  /* =====================================================
   * RESEND OTP
   * ===================================================== */

  resendButton.addEventListener("click", async () => {
    if (resendButton.disabled || !currentMobile) {
      return;
    }

    resendButton.disabled = true;

    try {
      const result = await sendOtp();

      if (!result.success) {
        showOtpError(result.data?.message || "ارسال مجدد کد با خطا مواجه شد.");

        resendButton.disabled = false;

        return;
      }

      clearOtpInputs();

      startCountdown();

      setTimeout(() => {
        otpInputs[0]?.focus();
      }, 100);
    } catch (error) {
      console.error("Resend OTP request failed:", error);

      showOtpError("ارتباط با سرور برقرار نشد. لطفاً دوباره تلاش کنید.");

      resendButton.disabled = false;
    }
  });

  /* =====================================================
   * RESET
   * ===================================================== */

  function resetForms() {
    mobileInput.value = "";

    currentMobile = "";

    clearMobileError();

    clearOtpInputs();

    stopCountdown();

    resendButton.disabled = true;

    resendTimer.textContent = "00:60";
  }

  /* =====================================================
   * INITIAL STATE
   * ===================================================== */

  modal.setAttribute("aria-hidden", "true");
});

/* =========================================================
 * GOOGLE SIGN IN
 * ========================================================= */

function initializeGoogleSignIn() {
  const googleButton = document.getElementById("googleSignInButton");

  if (!googleButton) {
    return;
  }

  if (
    typeof google === "undefined" ||
    !google.accounts ||
    !google.accounts.id
  ) {
    return;
  }

  google.accounts.id.initialize({
    client_id: authGoogleConfig.clientId,

    callback: handleGoogleCredential,

    ux_mode: "popup",
  });

  google.accounts.id.renderButton(googleButton, {
    type: "standard",
    theme: "outline",
    size: "large",
    text: "continue_with",
    shape: "rectangular",
    logo_alignment: "left",
    width: 360,
  });
}

/* =========================================================
 * GOOGLE CREDENTIAL
 * ========================================================= */

async function handleGoogleCredential(response) {
  if (!response || !response.credential) {
    return;
  }

  try {
    const formData = new FormData();

    formData.append("action", "auth_google_login");

    formData.append("nonce", authGoogleConfig.googleNonce);

    formData.append("credential", response.credential);

    const request = await fetch(authGoogleConfig.ajaxUrl, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    const result = await request.json();

    if (!result.success) {
      console.error(result.data?.message || "Google login failed.");

      return;
    }

    const modal = document.getElementById("authModal");

    if (modal) {
      modal.classList.remove("is-open");

      modal.setAttribute("aria-hidden", "true");

      document.body.classList.remove("auth-modal-open");
    }

    window.location.reload();
  } catch (error) {
    console.error("Google login request failed:", error);
  }
}

/* =========================================================
 * WAIT FOR GOOGLE SDK
 * ========================================================= */

function waitForGoogleSDK() {
  if (typeof google !== "undefined" && google.accounts && google.accounts.id) {
    initializeGoogleSignIn();

    return;
  }

  setTimeout(waitForGoogleSDK, 100);
}

waitForGoogleSDK();

/* =========================================================
 * USER ACCOUNT DROPDOWN
 * ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  const accountToggle = document.getElementById("userAccountToggle");

  const accountDropdown = document.getElementById("userAccountDropdown");

  if (!accountToggle || !accountDropdown) {
    return;
  }

  function openAccountDropdown() {
    accountDropdown.hidden = false;

    accountToggle.setAttribute("aria-expanded", "true");

    requestAnimationFrame(() => {
      accountDropdown.classList.add("is-open");
    });
  }

  function closeAccountDropdown() {
    accountDropdown.classList.remove("is-open");

    accountToggle.setAttribute("aria-expanded", "false");

    setTimeout(() => {
      accountDropdown.hidden = true;
    }, 150);
  }

  accountToggle.addEventListener("click", (event) => {
    event.stopPropagation();

    const isOpen = accountToggle.getAttribute("aria-expanded") === "true";

    if (isOpen) {
      closeAccountDropdown();
    } else {
      openAccountDropdown();
    }
  });

  document.addEventListener("click", (event) => {
    if (
      !accountDropdown.contains(event.target) &&
      !accountToggle.contains(event.target)
    ) {
      closeAccountDropdown();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeAccountDropdown();
    }
  });
});

/* =========================================================
 * LOGOUT
 * ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  const logoutButton = document.querySelector(".user-logout-btn");

  if (!logoutButton) {
    return;
  }

  logoutButton.addEventListener("click", async () => {
    if (logoutButton.disabled) {
      return;
    }

    logoutButton.disabled = true;

    try {
      const formData = new FormData();

      formData.append("action", "auth_logout");

      formData.append("nonce", authGoogleConfig.logoutNonce);

      const response = await fetch(authGoogleConfig.ajaxUrl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      });

      const result = await response.json();

      if (!result.success) {
        console.error(result.data?.message || "Logout failed.");

        logoutButton.disabled = false;

        return;
      }

      window.location.reload();
    } catch (error) {
      console.error("Logout request failed:", error);

      logoutButton.disabled = false;
    }
  });
});

/* =========================================================
  Account Page Logout
========================================================= */
document.addEventListener("DOMContentLoaded", () => {
  const logoutButton = document.getElementById("authLogoutButton");

  if (!logoutButton) {
    return;
  }

  logoutButton.addEventListener("click", async () => {
    logoutButton.disabled = true;
    logoutButton.textContent = "در حال خروج...";

    const formData = new FormData();

    formData.append("action", "auth_logout");

    formData.append("nonce", authGoogleConfig.logoutNonce);

    try {
      const response = await fetch(authGoogleConfig.ajaxUrl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      });

      const result = await response.json();

      if (!result.success) {
        throw new Error(result.data?.message || "خروج از حساب انجام نشد.");
      }

      window.location.href = "/drmosalmani/";
    } catch (error) {
      console.error("Logout error:", error);

      logoutButton.disabled = false;

      logoutButton.textContent = "خروج از حساب";

      alert(error.message || "خطایی هنگام خروج رخ داد.");
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const saveButton = document.getElementById("accountSaveButton");
  const nameInput = document.getElementById("accountDisplayName");
  const messageBox = document.getElementById("accountMessage");

  if (!saveButton || !nameInput || !messageBox) {
    return;
  }

  saveButton.addEventListener("click", async () => {
    const displayName = nameInput.value.trim();

    if (!displayName) {
      messageBox.textContent = "نام نمی‌تواند خالی باشد.";
      messageBox.className = "account-message error";
      return;
    }

    saveButton.disabled = true;
    saveButton.textContent = "در حال ذخیره...";

    const formData = new FormData();

    formData.append("action", "auth_update_account");

    formData.append("nonce", authGoogleConfig.accountNonce);

    formData.append("display_name", displayName);

    try {
      const response = await fetch(authGoogleConfig.ajaxUrl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      });

      const result = await response.json();

      if (!result.success) {
        throw new Error(result.data?.message || "ذخیره تغییرات انجام نشد.");
      }

      messageBox.textContent = result.data.message;

      messageBox.className = "account-message success";
    } catch (error) {
      console.error("Account update error:", error);

      messageBox.textContent = error.message;

      messageBox.className = "account-message error";
    } finally {
      saveButton.disabled = false;
      saveButton.textContent = "ذخیره تغییرات";
    }
  });
});

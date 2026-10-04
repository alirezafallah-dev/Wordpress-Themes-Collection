<?php
/**
 * Authentication Modal
 *
 * Login / Register via Mobile OTP and Google
 */
?>

<div class="auth-modal" id="authModal" aria-hidden="true">

    <!-- Overlay -->
    <div class="auth-modal-overlay" data-auth-close></div>

    <!-- Modal -->
    <div
        class="auth-modal-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="authModalTitle"
    >

        <!-- Close -->
        <button
            type="button"
            class="auth-modal-close"
            data-auth-close
            aria-label="<?php esc_attr_e('بستن', 'your-theme-textdomain'); ?>"
        >
            <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path
                    d="M6 6L18 18M18 6L6 18"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
            </svg>
        </button>


        <!-- ==========================================
             STEP 1
             Authentication Methods
        =========================================== -->

        <div class="auth-step auth-step-methods is-active" data-auth-step="methods">

            <!-- Logo -->
            <div class="auth-modal-logo">
                <img
                    src="<?php echo esc_url(
                        get_template_directory_uri() . '/assets/img/logo.png'
                    ); ?>"
                    alt="<?php bloginfo('name'); ?>"
                >
            </div>


            <!-- Heading -->
            <div class="auth-modal-heading">

                <h2 id="authModalTitle">
                    <?php pll_e('ورود به حساب کاربری'); ?>
                </h2>

                <p>
                    <?php pll_e('برای ادامه یکی از روش‌های ورود را انتخاب کنید.'); ?>
                </p>

            </div>


            <!-- Authentication Methods -->
            <div class="auth-methods">


                <!-- Google -->
                <div class="auth-google-wrapper">

                    <!--
                        Google Identity Services
                        بعداً دکمه واقعی Google اینجا قرار می‌گیرد.
                    -->

                    <div
                        id="googleSignInButton"
                        class="google-sign-in-button"
                    ></div>

                </div>


                <!-- Divider -->
                <div class="auth-divider">

                    <span></span>

                    <strong>
                        <?php pll_e('یا'); ?>
                    </strong>

                    <span></span>

                </div>


                <!-- Mobile -->
                <button
                    type="button"
                    class="auth-method-button auth-mobile-button"
                    data-auth-go="mobile"
                >

                    <span class="auth-method-icon">

                        <svg
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <rect
                                x="6"
                                y="2.5"
                                width="12"
                                height="19"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />

                            <path
                                d="M10 18.5H14"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>

                    </span>


                    <span class="auth-method-content">

                        <strong>
                            <?php pll_e('ورود با شماره موبایل'); ?>
                        </strong>

                        <small>
                            <?php pll_e('بدون نیاز به رمز عبور'); ?>
                        </small>

                    </span>


                    <span class="auth-method-arrow">

                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                d="M9 18L15 12L9 6"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                    </span>

                </button>

            </div>


            <!-- Footer -->
            <div class="auth-modal-footer">

                <p>
                    <?php
                    pll_e(
                        'با ورود یا ثبت‌نام، با قوانین و حریم خصوصی سایت موافقت می‌کنید.'
                    );
                    ?>
                </p>

            </div>

        </div>


        <!-- ==========================================
             STEP 2
             Mobile Number
        =========================================== -->

        <div
            class="auth-step auth-step-mobile"
            data-auth-step="mobile"
            hidden
        >

            <!-- Back -->
            <button
                type="button"
                class="auth-back-button"
                data-auth-back="methods"
            >

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M15 18L9 12L15 6"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

                <span>
                    <?php pll_e('بازگشت'); ?>
                </span>

            </button>


            <!-- Icon -->
            <div class="auth-step-icon">

                <svg
                    width="28"
                    height="28"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <rect
                        x="6"
                        y="2.5"
                        width="12"
                        height="19"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.7"
                    />

                    <path
                        d="M10 18.5H14"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                    />
                </svg>

            </div>


            <!-- Heading -->
            <div class="auth-modal-heading">

                <h2>
                    <?php pll_e('شماره موبایل خود را وارد کنید'); ?>
                </h2>

                <p>
                    <?php
                    pll_e(
                        'برای ورود یا ایجاد حساب کاربری، شماره موبایل خود را وارد کنید.'
                    );
                    ?>
                </p>

            </div>


            <!-- Mobile Form -->
            <form
                class="auth-mobile-form"
                id="authMobileForm"
                novalidate
            >

                <div class="auth-form-group">

                    <label for="authMobile">
                        <?php pll_e('شماره موبایل'); ?>
                    </label>

                    <div class="auth-input-wrapper">

                        <input
                            type="tel"
                            id="authMobile"
                            name="mobile"
                            inputmode="numeric"
                            autocomplete="tel"
                            placeholder="0912 345 6789"
                            maxlength="11"
                        >

                    </div>

                    <small
                        class="auth-field-error"
                        id="authMobileError"
                        role="alert"
                    ></small>

                </div>


                <button
                    type="submit"
                    class="auth-submit-button"
                >

                    <span class="auth-submit-text">
                        <?php pll_e('دریافت کد تأیید'); ?>
                    </span>

                    <span
                        class="auth-submit-loading"
                        aria-hidden="true"
                    >
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>

                </button>

            </form>

        </div>


        <!-- ==========================================
             STEP 3
             OTP
        =========================================== -->

        <div
            class="auth-step auth-step-otp"
            data-auth-step="otp"
            hidden
        >

            <!-- Back -->
            <button
                type="button"
                class="auth-back-button"
                data-auth-back="mobile"
            >

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M15 18L9 12L15 6"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

                <span>
                    <?php pll_e('تغییر شماره'); ?>
                </span>

            </button>


            <!-- Icon -->
            <div class="auth-step-icon">

                <svg
                    width="28"
                    height="28"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M20 11.5C20 15.6421 16.4183 19 12 19C10.745 19 9.56615 18.725 8.6 18.25L4 19.5L5.3 15.45C4.47496 14.3184 4 12.9475 4 11.5C4 7.35786 7.58172 4 12 4C16.4183 4 20 7.35786 20 11.5Z"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linejoin="round"
                    />

                </svg>

            </div>


            <!-- Heading -->
            <div class="auth-modal-heading">

                <h2>
                    <?php pll_e('کد تأیید را وارد کنید'); ?>
                </h2>

                <p>

                    <?php pll_e('کد تأیید برای شماره'); ?>

                    <strong
                        id="authMobileDisplay"
                        dir="ltr"
                    ></strong>

                    <?php pll_e('ارسال شد.'); ?>

                </p>

            </div>


            <!-- OTP Form -->
            <form
                class="auth-otp-form"
                id="authOtpForm"
            >

                <div
                    class="auth-otp-inputs"
                    dir="ltr"
                >

                    <?php for ($i = 1; $i <= 6; $i++) : ?>

                        <input
                            type="text"
                            class="auth-otp-input"
                            maxlength="1"
                            inputmode="numeric"
                            autocomplete="<?php echo $i === 1 ? 'one-time-code' : 'off'; ?>"
                            data-otp-index="<?php echo $i; ?>"
                        >

                    <?php endfor; ?>

                </div>


                <small
                    class="auth-field-error"
                    id="authOtpError"
                    role="alert"
                ></small>


                <button
                    type="submit"
                    class="auth-submit-button"
                >

                    <span class="auth-submit-text">
                        <?php pll_e('تأیید و ورود'); ?>
                    </span>

                    <span
                        class="auth-submit-loading"
                        aria-hidden="true"
                    >
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>

                </button>

            </form>


            <!-- Resend -->
            <div class="auth-resend">

                <span class="auth-resend-text">
                    <?php pll_e('کد را دریافت نکردید؟'); ?>
                </span>

                <button
                    type="button"
                    class="auth-resend-button"
                    id="authResendButton"
                    disabled
                >
                    <?php pll_e('ارسال مجدد'); ?>
                </button>

                <span
                    class="auth-resend-timer"
                    id="authResendTimer"
                    dir="ltr"
                >
                    00:60
                </span>

            </div>

        </div>


        <!-- ==========================================
             SUCCESS
        =========================================== -->

        <div
            class="auth-step auth-step-success"
            data-auth-step="success"
            hidden
        >

            <div class="auth-success-icon">

                <svg
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M5 12.5L9.5 17L19 7"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

            </div>

            <div class="auth-modal-heading">

                <h2>
                    <?php pll_e('ورود موفقیت‌آمیز بود'); ?>
                </h2>

                <p>
                    <?php
                    pll_e(
                        'با موفقیت وارد حساب کاربری خود شدید.'
                    );
                    ?>
                </p>

            </div>

        </div>

    </div>

</div>
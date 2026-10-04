<?php
/*
Template Name: Contact
*/

get_header();

$contact_status = isset($_GET['contact_status']) ? sanitize_key($_GET['contact_status']) : '';
$contact_error = isset($GLOBALS['contact_form_error']) ? $GLOBALS['contact_form_error'] : '';
$contact_old = isset($GLOBALS['contact_form_old']) ? $GLOBALS['contact_form_old'] : array();

$office_phone = get_theme_mod('office_phone', '');
$office_email = get_theme_mod('office_email', get_option('admin_email'));
$office_address = get_theme_mod('office_address', '');
$office_map_url = get_theme_mod('office_map_url', '#');
?>

<main class="contact-page">

    <section class="contact-hero">
        <div class="container">
            <div class="contact-hero-inner">
                <div class="contact-hero-label">
                    <span class="contact-eyebrow"><?php pll_e('ارتباط با ما'); ?></span>
                    <span class="contact-hero-number">01</span>
                </div>

                <div class="contact-hero-content">
                    <h1>
                        <?php pll_e('برای ارتباط و دریافت'); ?>
                        <span><?php pll_e('مشاوره حقوقی با ما در تماس باشید'); ?></span>
                    </h1>

                    <p>
                        <?php pll_e('اگر نیاز به مشاوره حقوقی، بررسی اولیه پرونده یا دریافت اطلاعات بیشتر دارید، می‌توانید از طریق راه‌های ارتباطی زیر با ما در تماس باشید.'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-main">
        <div class="container">
            <div class="contact-grid">

                <div class="contact-info">

                    <div class="contact-section-heading">
                        <span class="contact-eyebrow"><?php pll_e('اطلاعات تماس'); ?></span>

                        <h2>
                            <?php pll_e('راه‌های ارتباطی'); ?>
                        </h2>

                        <p>
                            <?php pll_e('برای ارتباط با دفتر و دریافت اطلاعات بیشتر، می‌توانید از طریق راه‌های زیر با ما در تماس باشید.'); ?>
                        </p>
                    </div>

                    <div class="contact-info-list">

                        <?php if ($office_phone) : ?>
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $office_phone)); ?>" class="contact-info-item">
                                <span class="contact-info-icon">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M21 16.92V19.92C21 20.48 20.53 20.93 19.97 20.9C9.12 20.27 3.73 14.88 3.1 4.03C3.07 3.47 3.52 3 4.08 3H7.08C7.57 3 7.98 3.35 8.06 3.83C8.19 4.62 8.42 5.37 8.72 6.09C8.85 6.4 8.78 6.76 8.54 7L7.27 8.27C8.42 10.5 10.5 12.58 12.73 13.73L14 12.46C14.24 12.22 14.6 12.15 14.91 12.28C15.63 12.58 16.38 12.81 17.17 12.94C17.65 13.02 18 13.43 18 13.92V16.92C18 17.47 17.55 17.92 17 17.92" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>

                                <span class="contact-info-text">
                                    <small><?php pll_e('تلفن دفتر'); ?></small>
                                    <strong><?php echo esc_html($office_phone); ?></strong>
                                </span>

                                <span class="contact-info-arrow">↗</span>
                            </a>
                        <?php endif; ?>

                        <?php if ($office_email) : ?>
                            <a href="mailto:<?php echo esc_attr($office_email); ?>" class="contact-info-item">
                                <span class="contact-info-icon">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M3 7L12 13L21 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>

                                <span class="contact-info-text">
                                    <small><?php pll_e('ایمیل دفتر'); ?></small>
                                    <strong><?php echo esc_html($office_email); ?></strong>
                                </span>

                                <span class="contact-info-arrow">↗</span>
                            </a>
                        <?php endif; ?>

                        <div class="contact-info-item">
                            <span class="contact-info-icon">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>

                            <span class="contact-info-text">
                                <small><?php pll_e('ساعات کاری'); ?></small>
                                <strong><?php pll_e('شنبه تا پنجشنبه، ۹ تا ۱۸'); ?></strong>
                            </span>
                        </div>

                    </div>

                    <div class="contact-social">
                        <span><?php pll_e('ما را دنبال کنید'); ?></span>

                        <div class="contact-social-links">
                            <a href="#" aria-label="Instagram">IG</a>
                            <a href="#" aria-label="LinkedIn">IN</a>
                        </div>
                    </div>

                </div>

                <div class="contact-form-box">

                    <div class="contact-form-header">
                        <span class="contact-eyebrow"><?php pll_e('ارسال پیام'); ?></span>

                        <h2>
                            <?php pll_e('چطور می‌توانیم'); ?>
                            <span><?php pll_e('به شما کمک کنیم؟'); ?></span>
                        </h2>

                        <p>
                            <?php pll_e('فرم زیر را تکمیل کنید. درخواست شما پس از بررسی برای دفتر ارسال خواهد شد.'); ?>
                        </p>
                    </div>

                    <?php if ($contact_status === 'success') : ?>
                        <div class="contact-alert contact-alert-success">
                            <span class="contact-alert-icon">✓</span>
                            <div>
                                <strong><?php pll_e('پیام شما با موفقیت ارسال شد.'); ?></strong>
                                <p><?php pll_e('در اولین فرصت درخواست شما بررسی و با شما تماس گرفته خواهد شد.'); ?></p>
                            </div>
                        </div>
                    <?php elseif ($contact_status === 'error') : ?>
                        <div class="contact-alert contact-alert-error">
                            <span class="contact-alert-icon">!</span>
                            <div>
                                <strong><?php pll_e('ارسال پیام انجام نشد.'); ?></strong>
                                <p><?php pll_e('لطفاً اطلاعات فرم را بررسی کرده و دوباره تلاش کنید.'); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($contact_error) : ?>
                        <div class="contact-alert contact-alert-error">
                            <span class="contact-alert-icon">!</span>
                            <div>
                                <strong><?php echo esc_html($contact_error); ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form class="contact-form" action="<?php echo esc_url(get_permalink()); ?>" method="post">

                        <?php wp_nonce_field('contact_form_submit', 'contact_nonce'); ?>

                        <div class="contact-honeypot" aria-hidden="true">
                            <label for="contact-website"><?php pll_e('وب‌سایت'); ?></label>
                            <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <input type="hidden" name="contact_form" value="1">

                        <div class="contact-form-row">

                            <div class="contact-field">
                                <input
                                    type="text"
                                    id="contact-name"
                                    name="name"
                                    value="<?php echo isset($contact_old['name']) ? esc_attr($contact_old['name']) : ''; ?>"
                                    placeholder=" "
                                    autocomplete="name"
                                    required
                                >
                                <label for="contact-name"><?php pll_e('نام و نام خانوادگی'); ?></label>
                            </div>

                            <div class="contact-field">
                                <input
                                    type="tel"
                                    id="contact-phone"
                                    name="phone"
                                    value="<?php echo isset($contact_old['phone']) ? esc_attr($contact_old['phone']) : ''; ?>"
                                    placeholder=" "
                                    autocomplete="tel"
                                    required
                                >
                                <label for="contact-phone"><?php pll_e('شماره تماس'); ?></label>
                            </div>

                        </div>

                        <div class="contact-form-row">

                            <div class="contact-field">
                                <input
                                    type="email"
                                    id="contact-email"
                                    name="email"
                                    value="<?php echo isset($contact_old['email']) ? esc_attr($contact_old['email']) : ''; ?>"
                                    placeholder=" "
                                    autocomplete="email"
                                >
                                <label for="contact-email"><?php pll_e('ایمیل'); ?></label>
                            </div>

                            <div class="contact-field">
                                <select id="contact-subject" name="subject" required>
                                    <option value="" disabled <?php selected(isset($contact_old['subject']) ? $contact_old['subject'] : '', ''); ?>>
                                        <?php pll_e('موضوع پیام'); ?>
                                    </option>
                                    <option value="consultation" <?php selected(isset($contact_old['subject']) ? $contact_old['subject'] : '', 'consultation'); ?>>
                                        <?php pll_e('مشاوره حقوقی'); ?>
                                    </option>
                                    <option value="case" <?php selected(isset($contact_old['subject']) ? $contact_old['subject'] : '', 'case'); ?>>
                                        <?php pll_e('پرونده حقوقی'); ?>
                                    </option>
                                    <option value="cooperation" <?php selected(isset($contact_old['subject']) ? $contact_old['subject'] : '', 'cooperation'); ?>>
                                        <?php pll_e('همکاری'); ?>
                                    </option>
                                    <option value="other" <?php selected(isset($contact_old['subject']) ? $contact_old['subject'] : '', 'other'); ?>>
                                        <?php pll_e('سایر موارد'); ?>
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="contact-field contact-field-message">
                            <textarea
                                id="contact-message"
                                name="message"
                                rows="6"
                                placeholder=" "
                                required
                            ><?php echo isset($contact_old['message']) ? esc_textarea($contact_old['message']) : ''; ?></textarea>
                            <label for="contact-message"><?php pll_e('پیام شما'); ?></label>
                        </div>

                        <div class="contact-form-footer">

                            <p>
                                <?php pll_e('اطلاعات ارسال‌شده محرمانه تلقی شده و صرفاً برای بررسی درخواست شما استفاده می‌شود.'); ?>
                            </p>

                            <button type="submit" class="contact-submit">
                                <?php pll_e('ارسال پیام'); ?>
                                <span>←</span>
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </div>
    </section>

    <section class="contact-location">
        <div class="container">

            <div class="contact-location-heading">
                <div>
                    <span class="contact-eyebrow"><?php pll_e('دفتر ما'); ?></span>

                    <h2>
                        <?php pll_e('در دفتر'); ?>
                        <span><?php pll_e('در خدمت شما هستیم'); ?></span>
                    </h2>
                </div>

                <p>
                    <?php pll_e('برای مراجعه حضوری، لطفاً پیش از مراجعه با دفتر هماهنگ کنید.'); ?>
                </p>
            </div>

            <div class="contact-location-card">

                <div class="contact-map">
                    <div class="contact-map-placeholder">
                        <div class="contact-map-pin">
                            <span></span>
                        </div>

                        <span><?php pll_e('موقعیت دفتر'); ?></span>
                    </div>
                </div>

                <div class="contact-address">

                    <span class="contact-address-number">02</span>

                    <div class="contact-address-content">

                        <span class="contact-eyebrow"><?php pll_e('آدرس دفتر'); ?></span>

                        <h3>
                            <?php pll_e('دفتر وکالت دکتر منصور مسلمانی'); ?>
                        </h3>

                        <p>
                            <?php
                            echo $office_address
                                ? esc_html($office_address)
                                : esc_html__('آدرس دفتر در این قسمت قرار می‌گیرد.', 'your-theme');
                            ?>
                        </p>

                        <div class="contact-address-meta">

                            <div>
                                <small><?php pll_e('ساعات مراجعه'); ?></small>
                                <strong><?php pll_e('با هماهنگی قبلی'); ?></strong>
                            </div>

                            <div>
                                <small><?php pll_e('تلفن'); ?></small>
                                <strong><?php echo esc_html($office_phone); ?></strong>
                            </div>

                        </div>

                        <a href="<?php echo esc_url($office_map_url); ?>" class="contact-map-button" target="_blank" rel="noopener">
                            <?php pll_e('مسیریابی در نقشه'); ?>
                            <span>↗</span>
                        </a>

                    </div>

                </div>

            </div>

        </div>
    </section>

    <section class="contact-cta">
        <div class="container">

            <div class="contact-cta-inner">

                <div>
                    <span class="contact-eyebrow"><?php pll_e('مشاوره حقوقی'); ?></span>

                    <h2>
                        <?php pll_e('موضوع حقوقی خود را'); ?>
                        <span><?php pll_e('با ما در میان بگذارید'); ?></span>
                    </h2>

                    <p>
                        <?php pll_e('اگر برای تصمیم‌گیری درباره یک موضوع حقوقی نیاز به بررسی تخصصی دارید، درخواست خود را برای ما ارسال کنید.'); ?>
                    </p>
                </div>

                <a href="#contact-name" class="contact-cta-button">
                    <?php pll_e('شروع درخواست'); ?>
                    <span>←</span>
                </a>

            </div>

        </div>
    </section>

</main>

<?php get_footer(); ?>
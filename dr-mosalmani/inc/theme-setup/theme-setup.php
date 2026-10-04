<?php
function mytheme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    // Merge two register_nav_menus into one call
    register_nav_menus([
        'main_menu'   => 'Main Menu',
        'footer_menu' => 'منوی فوتر',
    ]);
}
add_action('after_setup_theme', 'mytheme_setup');

/**
 * Widgets
 */
function mytheme_widgets() {

    register_sidebar([
        'name' => 'Footer Widget',
        'id' => 'footer_1',
        'before_widget' => '<div class="footer-box">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ]);
}
add_action('widgets_init', 'mytheme_widgets');

function mytheme_assets() {
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();
    
    // ─────────────────────────────────────
    // 1. Main stylesheet (style.css)
    // ─────────────────────────────────────
    wp_enqueue_style(
        'mytheme-style',
        get_stylesheet_uri(),
        [],
        file_exists($theme_dir . '/style.css') ? filemtime($theme_dir . '/style.css') : '1.0.0'
    );
    
    // ─────────────────────────────────────
    // 2. Main CSS (بدون responsive)
    // ─────────────────────────────────────
    $main_css_path = $theme_dir . '/assets/css/main.css';
    wp_enqueue_style(
        'mytheme-main',
        $theme_uri . '/assets/css/main.css',
        ['mytheme-style'],
        file_exists($main_css_path) ? filemtime($main_css_path) : '1.0.0'
    );
    
    // ─────────────────────────────────────
    // 3. Page-specific CSS files
    // ─────────────────────────────────────
    if (is_page('terms')) {
        wp_enqueue_style(
            'terms-css',
            $theme_uri . '/assets/css/components/terms.css',
            ['mytheme-main'],
            '2.0.0'
        );
    }
    
    if (is_page('about-us')) {
        wp_enqueue_style(
            'about-us-css',
            $theme_uri . '/assets/css/components/about-us.css',
            ['mytheme-main'],
            '2.0.0'
        );
    }
    
    if (is_page('contact-us')) {
        wp_enqueue_style(
            'contact-us-css',
            $theme_uri . '/assets/css/components/contact-us.css',
            ['mytheme-main'],
            '2.0.0'
        );
    }
    
    if (is_page_template('page-consultation-booking.php')) {
        wp_enqueue_style(
            'booking-css',
            $theme_uri . '/assets/css/components/consultation-booking.css',
            ['mytheme-main'],
            '2.0.0'
        );
        wp_enqueue_script(
            'booking-js',
            $theme_uri . '/assets/js/booking.js',
            [],
            '2.0.0',
            true
        );
        wp_localize_script('booking-js', 'bookingConfig', [
            'ajaxUrl'      => admin_url('admin-ajax.php'),
            'bookingNonce' => wp_create_nonce('create_lawyer_booking'),
        ]);
    }
    
    if (is_single()) {
        wp_enqueue_style(
            'single-css',
            $theme_uri . '/assets/css/components/single.css',
            ['mytheme-main'],
            '2.0.0'
        );
    }
    
    if (is_page('account') || is_page_template('page-account.php')) {
        wp_enqueue_style(
            'account-css',
            $theme_uri . '/assets/css/components/account.css',
            ['mytheme-main'],
            '2.1.0'
        );
        wp_enqueue_script(
            'account-js',
            $theme_uri . '/assets/js/account.js',
            [],
            '2.1.0',
            true
        );
        wp_localize_script('account-js', 'accountConfig', [
            'ajaxUrl'      => admin_url('admin-ajax.php'),
            'accountNonce' => wp_create_nonce('auth_update_account'),
            'mobileNonce'  => wp_create_nonce('auth_sms'),
        ]);
    }
    
    // ─────────────────────────────────────
    // 4. RESPONSIVE CSS - Always the last file! 
    // This must be loaded after all components.
    // ─────────────────────────────────────
    $responsive_path = $theme_dir . '/assets/css/responsive.css';
    wp_enqueue_style(
        'mytheme-responsive',
        $theme_uri . '/assets/css/responsive.css',
        ['mytheme-main'], // وابستگی فقط به main
        file_exists($responsive_path) ? filemtime($responsive_path) : '2.0.0'
    );
    
    // ─────────────────────────────────────
    // 5. Main JS
    // ─────────────────────────────────────
    $main_js_path = $theme_dir . '/assets/js/main.js';
    wp_enqueue_script(
        'mytheme-main-js',
        $theme_uri . '/assets/js/main.js',
        [],
        file_exists($main_js_path) ? filemtime($main_js_path) : '1.0.0',
        true
    );
    
    wp_enqueue_script(
        'google-identity-services',
        'https://accounts.google.com/gsi/client',
        [],
        null,
        false
    );
    
    wp_enqueue_script(
        'auth-modal',
        $theme_uri . '/assets/js/auth-modal.js',
        ['google-identity-services'],
        '1.0.0',
        true
    );
    
    wp_localize_script('auth-modal', 'authGoogleConfig', [
        'clientId'     => defined('LAWYER_GOOGLE_CLIENT_ID') ? LAWYER_GOOGLE_CLIENT_ID : '',
        'ajaxUrl'      => admin_url('admin-ajax.php'),
        'googleNonce'  => wp_create_nonce('auth_google_login'),
        'smsNonce'     => wp_create_nonce('auth_sms'),
        'logoutNonce'  => wp_create_nonce('auth_logout'),
        'accountNonce' => wp_create_nonce('auth_update_account'),
    ]);
}
add_action('wp_enqueue_scripts', 'mytheme_assets');
// Admin assets
add_action('admin_enqueue_scripts', function($hook) {
    if (strpos($hook, 'theme-settings') !== false) {
        wp_enqueue_media();
        
        $admin_js_path = get_template_directory() . '/assets/js/admin.js';
        $admin_js_ver  = file_exists($admin_js_path) ? filemtime($admin_js_path) : '1.0.0';
        
        wp_enqueue_script('mytheme-admin-js', get_template_directory_uri() . '/assets/js/admin.js', ['jquery'], $admin_js_ver, true);
        
        // Nonce injection for admin.js
        wp_localize_script('mytheme-admin-js', 'themeSettingsConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('save_home_image_nonce'),
        ]);
        
        $admin_css_path = get_template_directory() . '/assets/css/admin.css';
        $admin_css_ver  = file_exists($admin_css_path) ? filemtime($admin_css_path) : '1.0.0';
        wp_enqueue_style('mytheme-admin-css', get_template_directory_uri() . '/assets/css/admin.css', [], $admin_css_ver);
    }
});

/**
 * Remove admin bar from frontend
*/
add_filter('show_admin_bar', '__return_false');

add_action('admin_enqueue_scripts', function($hook) {

    if (strpos($hook, 'theme-settings') === false) return;

    wp_enqueue_media();

    wp_enqueue_script(
        'mytheme-admin-js',
        get_template_directory_uri() . '/assets/js/admin.js',
        ['jquery'],
        filemtime(get_template_directory() . '/assets/js/admin.js'),
        true
    );

    wp_localize_script('mytheme-admin-js', 'ajaxurl', admin_url('admin-ajax.php'));

    wp_enqueue_style(
        'mytheme-admin-css',
        get_template_directory_uri() . '/assets/css/admin.css',
        [],
        filemtime(get_template_directory() . '/assets/css/admin.css')
    );

    if ($hook !== 'comment.php') {
        return;
    }

    wp_enqueue_script(
        'comment-admin',
        get_template_directory_uri() . '/assets/js/comment-admin.js',
        ['jquery'],
        '1.0',
        true
    );
});

/**
 * Admin Theme Styles
 */
add_action('admin_enqueue_scripts', 'lawyer_admin_theme_styles');
function lawyer_admin_theme_styles() {
    wp_enqueue_style(
        'lawyer-admin-theme',
        get_template_directory_uri() . '/assets/css/admin.css',
        [],
        '1.0.0'
    );
}

/**
 * Custom Login Logo (Optional)
 */
add_action('login_head', 'lawyer_custom_login_logo');
function lawyer_custom_login_logo() {
    $logo_url = get_template_directory_uri() . '/assets/img/logo.png';
    ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo esc_url($logo_url); ?>);
        }
    </style>
    <?php
}

/**
 * Admin Color Scheme
 */
add_filter('admin_body_class', 'lawyer_admin_body_class');
function lawyer_admin_body_class($classes) {
    $classes .= ' lawyer-admin-theme';
    return $classes;
}
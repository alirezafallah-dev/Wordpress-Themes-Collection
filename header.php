<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); // ✅ اضافه شد برای سازگاری با پلاگین‌ها و Accessibility ?>

<header class="site-header">
    <div class="container">
        <div class="header-inner flex mt-4">
            <!-- Logo -->
            <div class="header-logo flex">
                <a href="<?php echo esc_url(home_url('/')); // ✅ Escaping اضافه شد ?>">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logo.png'); ?>" 
                         alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                </a>

                <div class="logo-title flex ms-2">
                    <strong class="dr-name-title"><?php echo esc_html(get_bloginfo('name')); ?></strong>
                    <span><?php pll_e('وکیل پایه یک دادگستری'); ?></span>
                </div>
                            
                <?php if (function_exists('pll_the_languages')) :
                    $languages = pll_the_languages(array('raw' => 1));
                    $current = null;

                    foreach ($languages as $lang) {
                        if ($lang['current_lang']) {
                            $current = $lang;
                            break;
                        }
                    }
                ?>
                <div class="lang-switcher-mobile me-9">
                    <button class="lang-active-mobile" type="button">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/flags/' . esc_attr($current['slug']) . '.svg'); // ✅ Escaping اضافه شد ?>" 
                             alt="<?php echo esc_attr($current['name'] ?? 'Language'); ?>">
                        
                        <svg class="lang-arrow" width="14" height="14" viewBox="0 0 24 24">
                            <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </button>
                    
                    <div class="lang-dropdown">
                        <?php foreach ($languages as $lang) :
                            if (!$lang['current_lang']) : ?>
                                <a href="<?php echo esc_url($lang['url']); ?>" class="lang-item">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/flags/' . esc_attr($lang['slug']) . '.svg'); ?>" 
                                         alt="<?php echo esc_attr($lang['name']); ?>">
                                    <span><?php echo esc_html($lang['name']); ?></span>
                                </a>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="header-buttons flex">
                <a class="call-button" href="<?php echo esc_attr('tel:' . preg_replace('/[^0-9+]/', '', pll__('02112345678'))); // ✅ Escaping اضافه شد ?>">
                    <span><?php pll_e('تلفن'); ?></span>
                    <!-- ... SVG ... -->
                </a>

                <!-- User Button -->
                <?php if (is_user_logged_in()) : ?>
                    <?php
                    $current_user = wp_get_current_user();
                    $auth_mobile = get_user_meta($current_user->ID, 'auth_mobile', true);
                    
                    $user_display_name = !empty($auth_mobile)
                        ? $auth_mobile
                        : (!empty($current_user->display_name)
                            ? $current_user->display_name
                            : $current_user->user_login
                        );
                    ?>

                    <div class="user-account-wrapper">
                        <button
                            type="button"
                            class="user-login-btn user-account-btn"
                            id="userAccountToggle"
                            aria-expanded="false"
                            aria-controls="userAccountDropdown"
                        >
                            <span class="user-account-name">
                                <?php echo esc_html($user_display_name); ?>
                            </span>
                            <!-- ... SVG ... -->
                        </button>

                        <div
                            class="user-account-dropdown"
                            id="userAccountDropdown"
                            hidden
                        >
                            <div class="user-account-header">
                                <span class="user-account-welcome">
                                    <?php pll_e('حساب کاربری'); ?>
                                </span>
                                <strong>
                                    <?php echo esc_html($user_display_name); ?>
                                </strong>
                            </div>

                            <div class="user-account-menu">
                                <?php 
                                $account_page = get_page_by_path('account');
                                $account_url = $account_page ? get_permalink($account_page->ID) : home_url('/');
                                ?>
                                <a href="<?php echo esc_url($account_url); ?>">
                                    <?php pll_e('حساب کاربری'); ?>
                                </a>

                                <a href="#">
                                    <?php pll_e('رزروهای من'); ?>
                                </a>

                                <button
                                    type="button"
                                    class="user-logout-btn"
                                >
                                    <?php pll_e('خروج از حساب'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                <?php else : ?>
                    <button
                        type="button"
                        class="user-login-btn"
                        data-auth-open
                        aria-label="<?php esc_attr_e('ورود به حساب کاربری', 'Dr.Mosalmani'); // ✅ Text domain یکسان شد ?>"
                    >
                        <!-- ... SVG ... -->
                    </button>
                <?php endif; ?>

                <?php 
                $booking_page = get_page_by_path('consultation-booking');
                $booking_url = $booking_page ? get_permalink($booking_page->ID) : home_url('/');
                ?>
                <a
                    href="<?php echo esc_url($booking_url); ?>"
                    class="reserve-time-btn"
                >
                    <?php pll_e('رزرو وقت'); ?>
                </a>
            </div>
        </div>

        <div class="header-menu flex">

            <nav class="nav-header-menu">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'main_menu',
                        'container' => false,
                        'menu_class' => 'nav-menu flex ms-9 pin-5',
                        'fallback_cb' => false,
                    ]);
                    ?>
            </nav>

            <?php if (function_exists('pll_the_languages')) :
                $languages = pll_the_languages(array('raw' => 1));
                $current = null;

                foreach ($languages as $lang) {
                    if ($lang['current_lang']) {
                        $current = $lang;
                        break;
                    }
                }
            ?>
            <div class="lang-switcher me-9">
                <!-- active language -->
                <button class="lang-active" type="button">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/flags/<?php echo $current['slug']; ?>.svg" alt="">
                    <!-- <span><?php echo esc_html($current['name']); ?></span> -->

                    <!-- arrow -->
                    <svg class="lang-arrow" width="14" height="14" viewBox="0 0 24 24">
                        <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
                <!-- dropdown -->
                <div class="lang-dropdown">

                    <?php foreach ($languages as $lang) :
                        if (!$lang['current_lang']) : ?>
                            <a href="<?php echo esc_url($lang['url']); ?>" class="lang-item">

                                <img src="<?php echo get_template_directory_uri(); ?>/assets/flags/<?php echo $lang['slug']; ?>.svg" alt="">

                                <span><?php echo esc_html($lang['name']); ?></span>

                            </a>
                    <?php endif; endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

    </div>
</header>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav" aria-label="<?php esc_attr_e('منوی موبایل', 'your-theme-textdomain'); ?>">

    <!-- Home -->
    <a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-nav-item" aria-label="<?php esc_attr_e('خانه', 'your-theme-textdomain'); ?>">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z" stroke="#151515" stroke-width="1.5"></path> <path d="M15 18H9" stroke="#151515" stroke-width="1.5" stroke-linecap="round"></path> </g></svg>
        <span><?php pll_e('خانه'); ?></span>
    </a>

    <!-- Menu -->
    <button type="button" class="mobile-nav-item" id="mobileMenuToggle" aria-expanded="false" aria-controls="mobileMenuDrawer" >
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 12H8.00901M12.0045 12H12.0135M15.991 12H16" stroke="#04060d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12Z" stroke="#04060d" stroke-width="1.5"></path> </g></svg>
        <span><?php pll_e('منو'); ?></span>
    </button>

    <!-- Call -->
    <a href="tel:<?php pll_e('تلفن'); ?>" class="mobile-nav-item" aria-label="<?php esc_attr_e('تماس', 'your-theme-textdomain'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none"><path d="M18.3083 15.275C18.3083 15.575 18.2416 15.8833 18.1 16.1833C17.9583 16.4833 17.775 16.7667 17.5333 17.0333C17.125 17.4833 16.675 17.8083 16.1666 18.0167C15.6666 18.225 15.125 18.3333 14.5416 18.3333C13.6916 18.3333 12.7833 18.1333 11.825 17.725C10.8666 17.3167 9.90829 16.7667 8.95829 16.075C7.99996 15.375 7.09163 14.6 6.22496 13.7417C5.36663 12.875 4.59163 11.9667 3.89996 11.0167C3.21663 10.0667 2.66663 9.11667 2.26663 8.17501C1.86663 7.22501 1.66663 6.31667 1.66663 5.45001C1.66663 4.88334 1.76663 4.34167 1.96663 3.84167C2.16663 3.33334 2.48329 2.86667 2.92496 2.45001C3.45829 1.92501 4.04163 1.66667 4.65829 1.66667C4.89163 1.66667 5.12496 1.71667 5.33329 1.81667C5.54996 1.91667 5.74163 2.06667 5.89163 2.28334L7.82496 5.00834C7.97496 5.21667 8.08329 5.40834 8.15829 5.59167C8.23329 5.76667 8.27496 5.94167 8.27496 6.10001C8.27496 6.30001 8.21663 6.50001 8.09996 6.69167C7.99163 6.88334 7.83329 7.08334 7.63329 7.28334L6.99996 7.94167C6.90829 8.03334 6.86663 8.14167 6.86663 8.27501C6.86663 8.34167 6.87496 8.40001 6.89163 8.46667C6.91663 8.53334 6.94163 8.58334 6.95829 8.63334C7.10829 8.90834 7.36663 9.26667 7.73329 9.70001C8.10829 10.1333 8.50829 10.575 8.94163 11.0167C9.39163 11.4583 9.82496 11.8667 10.2666 12.2417C10.7 12.6083 11.0583 12.8583 11.3416 13.0083C11.3833 13.025 11.4333 13.05 11.4916 13.075C11.5583 13.1 11.625 13.1083 11.7 13.1083C11.8416 13.1083 11.95 13.0583 12.0416 12.9667L12.675 12.3417C12.8833 12.1333 13.0833 11.975 13.275 11.875C13.4666 11.7583 13.6583 11.7 13.8666 11.7C14.025 11.7 14.1916 11.7333 14.375 11.8083C14.5583 11.8833 14.75 11.9917 14.9583 12.1333L17.7166 14.0917C17.9333 14.2417 18.0833 14.4167 18.175 14.625C18.2583 14.8333 18.3083 15.0417 18.3083 15.275Z" stroke="#303030" stroke-width="1.5" stroke-miterlimit="10"></path><path opacity="0.4" d="M15.4167 7.49999C15.4167 6.99999 15.025 6.23333 14.4417 5.60833C13.9083 5.03333 13.2 4.58333 12.5 4.58333" stroke="#313131" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path opacity="0.4" d="M18.3333 7.50001C18.3333 4.27501 15.725 1.66667 12.5 1.66667" stroke="#222" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        <span><?php pll_e('تماس'); ?></span>
    </a>

    <!-- Account -->
    <?php if (is_user_logged_in()) : ?>

        <!-- Logged in -->
        <a
            href="<?php echo esc_url(get_permalink(get_page_by_path('account'))); ?>"
            class="mobile-nav-item"
            aria-label="<?php esc_attr_e('حساب کاربری', 'your-theme-textdomain'); ?>"
        >
            <!-- User Icon -->
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle cx="12" cy="6" r="4" stroke="#03040a" stroke-width="1.5"></circle> <path d="M20 17.5C20 19.9853 20 22 12 22C4 22 4 19.9853 4 17.5C4 15.0147 7.58172 13 12 13C16.4183 13 20 15.0147 20 17.5Z" stroke="#03040a" stroke-width="1.5"></path> </g></svg>

            <span><?php pll_e('حساب'); ?></span>
        </a>

    <?php else : ?>

        <!-- Guest -->
        <button
            type="button"
            class="mobile-nav-item"
            data-auth-open
            aria-label="<?php esc_attr_e('ورود به حساب کاربری', 'your-theme-textdomain'); ?>"
        >
            <!-- User Icon -->
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Z"/>
                <path d="M3.5 21a8.5 8.5 0 0 1 17 0"/>
            </svg>

            <span><?php pll_e('ورود'); ?></span>
        </button>

    <?php endif; ?>

</nav>

<!-- Mobile Menu Drawer -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" aria-hidden="true"></div>

<aside class="mobile-menu-drawer" id="mobileMenuDrawer" aria-hidden="true">

    <div class="mobile-menu-header">

        <div class="mobile-menu-title">
            <strong><?php pll_e('منوی سایت'); ?></strong>
        </div>

        <button
            type="button"
            class="mobile-menu-close"
            id="mobileMenuClose"
            aria-label="<?php esc_attr_e('بستن منو', 'your-theme-textdomain'); ?>"
        >
            <span></span>
            <span></span>
        </button>

    </div>


    <div class="mobile-menu-content">

        <?php
        wp_nav_menu([
            'theme_location' => 'main_menu',
            'container'      => false,
            'menu_class'     => 'mobile-nav-menu',
            'fallback_cb'    => false,
        ]);
        ?>

    </div>

</aside>

<?php get_template_part('template-parts/auth-modal'); ?>

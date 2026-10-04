<?php get_header(); ?>

<main class="site-main">
    <div class="container">

        <div class="about-section flex">
            <div class="text-about-section">
                <h1 class="main-title-abus mb-4"><?php pll_e('دکتر منصور مسلمانی وکیل پایه یک دادگستری'); ?></h1>
                <p class="main-resume-summary mb-4"><?php pll_e('خلاصه ای از رزومه شما'); ?></p>
                <p class="main-description mb-4"><?php pll_e('خلاصه توضیحات شما'); ?></p>

                <?php
                $page = get_page_by_path('resume');
                $url = '';
                if ($page) {
                    if (function_exists('pll_get_post')) {
                        $translated_id = pll_get_post($page->ID);
                        $url = $translated_id ? get_permalink($translated_id) : get_permalink($page->ID);
                    } else {
                        $url = get_permalink($page->ID);
                    }
                }
                ?>
                <a href="<?php echo esc_url($url); ?>" class="btn-resume <?php echo empty($url) ? 'is-disabled' : ''; ?>">
                    <?php pll_e('مشاهده رزومه کامل'); ?>
                </a>
            </div>
            
            <div class="pic-about-section">
                <div>
                    <?php
                        $image = get_option('home_page_image');

                        // اگر عکس آپلود نشده → دیفالت
                        if (!$image) {
                            $image = get_template_directory_uri() . '/assets/img/image.png';
                        }
                    ?>
                    <img src="<?php echo esc_url($image); ?>" alt="تصویر صفحه اصلی">

                    <div class="tm-rd-1">
                        <strong class="pictures-title"><?php pll_e('دکتر منصور مسلمانی'); ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="services-section grid">
            <div class="services-continar flex">
                <div class="servive-picture">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/law-of-fairness.webp" alt="تصویر خدمات">
                </div>

                <div class="serivces-box flex">
                    <img src="" alt="" class="services-icon mb-4">
                    <h4 class="service-title mb-4"><?php pll_e('خدمات ما'); ?></h4>
                    <div class="service-items grid">
                        <div class="svc-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/legal-consultation.webp" alt="" class="svc-icon">
                            <h5 class="svc-title"><?php pll_e('مشاوره حقوقی'); ?></h5>
                        </div>

                        <div class="svc-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/civil-litigation.webp" alt="" class="svc-icon">
                            <h5 class="svc-title"><?php pll_e('دعاوی حقوقی'); ?></h5>
                        </div>

                        <div class="svc-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/criminal-litigation.webp" alt="" class="svc-icon">
                            <h5 class="svc-title"><?php pll_e('دعاوی کیفری'); ?></h5>
                        </div>

                        <div class="svc-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/family-law.webp" alt="" class="svc-icon">
                            <h5 class="svc-title"><?php pll_e('حقوق خانواده'); ?></h5>
                        </div>

                        <div class="svc-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/real-estate.webp" alt="" class="svc-icon">
                            <h5 class="svc-title"><?php pll_e('قراردادها و امور تجاری'); ?></h5>
                        </div>

                        <div class="svc-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/commercial-contracts.webp" alt="" class="svc-icon">
                            <h5 class="svc-title"><?php pll_e('املاک و مستغلات'); ?></h5>
                        </div>
                        
                        <div class="svc-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/corporate-law.webp" alt="" class="svc-icon">
                            <h5 class="svc-title"><?php pll_e('حقوق شرکت‌ها و کسب‌وکار'); ?></h5>
                        </div>

                        <div class="svc-item">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/dispute-resolution.webp" alt="" class="svc-icon">
                            <h5 class="svc-title"><?php pll_e('حل اختلاف و داوری'); ?></h5>
                        </div>

                    </div>
                </div>

            </div>
            <div class="about-services pbl-3 pin-4 mt-4">
                <p class="about-services-desc"><?php pll_e('توضیحات خدمات ما'); ?></p>
            </div>
        </div>

        <div class="reservation-section grid mt-7">
            <div class="reserve-box">
                <div class="reserve-borderbox flex">
                    <div class="reserve-title-section">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/picture-2.webp" alt="">
                    </div>
                    <div class="reserve-button-section">
                        <button type="button" class="reserve-time-btn"><?php pll_e('رزرو وقت'); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><g opacity="0.4"><path d="M12.3203 9.32031L9.76031 11.8803L12.3203 14.4403" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M20 11.8809L9.83 11.8809" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path></g><path d="M11.8199 20C7.39995 20 3.81995 17 3.81995 12C3.81995 7 7.39995 4 11.8199 4" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path></svg>                        
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="articles-section grid">
            <!-- header -->
            <div class="articles-header">
                <div class="articles-nav">
                    <button class="art-btn next">‹</button>
                    <button class="art-btn prev">›</button>
                </div>
                <div class="tm-rd-2"></div>
            </div>
            <!-- slider -->
            <div class="articles-wrapper">
                <div class="articles-track">
                    <?php
                    $posts = get_posts([
                        'numberposts' => 8
                    ]);

                    foreach ($posts as $post) :
                        setup_postdata($post);
                        get_template_part('template-parts/article-card');
                    endforeach;
                    wp_reset_postdata(); ?>
                </div>
            </div>

        </div>

        <div class="line">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/line.webp" alt="">
        </div>

    </div>
</main>

<?php get_footer(); ?>
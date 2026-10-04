<?php get_header(); ?>

<main class="about-page">

    <section class="about-hero">
        <div class="container">
            <div class="about-hero-grid">

                <div class="about-hero-content">
                    <div class="about-label">
                        <?php pll_e('درباره وکیل'); ?>
                    </div>

                    <span class="about-kicker">
                        <?php pll_e('دکتر منصور مسلمانی'); ?>
                    </span>

                    <h1>
                        <?php pll_e('وکیل پایه یک دادگستری'); ?>
                        <span><?php pll_e('با رویکردی دقیق و حرفه‌ای'); ?></span>
                    </h1>

                    <p>
                        <?php pll_e('در هر پرونده، شناخت دقیق مسئله، بررسی مستندات و انتخاب آگاهانه مسیر حقوقی، پایه اصلی تصمیم‌گیری است.'); ?>
                    </p>

                    <div class="about-hero-actions">
                        <a href="#lawyer-resume" class="about-btn about-btn-link">
                            <?php pll_e('مشاهده رزومه'); ?>
                            <span>↓</span>
                        </a>
                    </div>
                </div>

                <div class="pic-about-section">
                <div>
                    <?php
                        $image = get_option('home_page_image');

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
        </div>
    </section>

    <section class="lawyer-resume" id="lawyer-resume">
        <div class="container">

            <div class="section-intro">
                <div>
                    <span class="section-label">
                        <?php pll_e('رزومه حرفه‌ای'); ?>
                    </span>

                    <h2>
                        <?php pll_e('درباره دکتر منصور مسلمانی'); ?>
                    </h2>

                    <p>
                        <?php pll_e('سوابق، تخصص و مسیر حرفه‌ای وکیل در یک نگاه.'); ?>
                    </p>
                </div>
            </div>

            <div class="resume-layout">

                <div class="resume-summary">
                    <span class="resume-number">PROFILE</span>

                    <h3>
                        <?php pll_e('وکیل پایه یک دادگستری'); ?>
                    </h3>

                    <p>
                        <?php pll_e('فعال در حوزه ارائه خدمات و مشاوره حقوقی با تمرکز بر بررسی دقیق پرونده، تحلیل مستندات و انتخاب مسیر مناسب برای هر موضوع.'); ?>
                    </p>

                    <div class="resume-meta">

                        <div>
                            <span><?php pll_e('نام'); ?></span>
                            <strong><?php pll_e('دکتر منصور مسلمانی'); ?></strong>
                        </div>

                        <div>
                            <span><?php pll_e('سمت'); ?></span>
                            <strong><?php pll_e('وکیل پایه یک دادگستری'); ?></strong>
                        </div>

                        <div>
                            <span><?php pll_e('حوزه فعالیت'); ?></span>
                            <strong><?php pll_e('خدمات و مشاوره حقوقی'); ?></strong>
                        </div>

                    </div>
                </div>

                <div class="resume-details">

                    <div class="resume-row">
                        <div class="resume-row-title">
                            <?php pll_e('تحصیلات'); ?>
                        </div>

                        <div class="resume-row-content">
                            <h3><?php pll_e('سوابق تحصیلی'); ?></h3>

                            <p>
                                <?php pll_e('مدرک و رشته تحصیلی وکیل در این بخش درج می‌شود.'); ?>
                            </p>
                        </div>
                    </div>

                    <div class="resume-row">
                        <div class="resume-row-title">
                            <?php pll_e('پروانه وکالت'); ?>
                        </div>

                        <div class="resume-row-content">
                            <h3><?php pll_e('وکیل پایه یک دادگستری'); ?></h3>

                            <p>
                                <?php pll_e('اطلاعات مربوط به پروانه وکالت و مرجع صادرکننده در این بخش قابل درج است.'); ?>
                            </p>
                        </div>
                    </div>

                    <div class="resume-row">
                        <div class="resume-row-title">
                            <?php pll_e('سوابق حرفه‌ای'); ?>
                        </div>

                        <div class="resume-row-content">
                            <h3><?php pll_e('تجربه و فعالیت حرفه‌ای'); ?></h3>

                            <p>
                                <?php pll_e('سوابق کاری، فعالیت‌های حرفه‌ای و تجربه‌های مرتبط با حوزه حقوقی در این بخش قرار می‌گیرد.'); ?>
                            </p>
                        </div>
                    </div>

                    <div class="resume-row">
                        <div class="resume-row-title">
                            <?php pll_e('حوزه‌های تخصصی'); ?>
                        </div>

                        <div class="resume-row-content">
                            <div class="expertise-list">
                                <span><?php pll_e('مشاوره حقوقی'); ?></span>
                                <span><?php pll_e('دعاوی حقوقی'); ?></span>
                                <span><?php pll_e('دعاوی کیفری'); ?></span>
                                <span><?php pll_e('امور قراردادها'); ?></span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <section class="lawyer-approach">
        <div class="container">

            <div class="approach-grid">

                <div class="approach-heading">
                    <span class="section-label">
                        <?php pll_e('رویکرد حرفه‌ای'); ?>
                    </span>

                    <h2>
                        <?php pll_e('هر پرونده، نیازمند شناخت خودش است.'); ?>
                    </h2>
                </div>

                <div class="approach-content">

                    <p>
                        <?php pll_e('در فعالیت حرفه‌ای، هیچ پرونده‌ای صرفاً بر اساس یک الگوی ثابت بررسی نمی‌شود. شرایط، اسناد، خواسته موکل و جزئیات هر موضوع باید به‌صورت مستقل ارزیابی شود.'); ?>
                    </p>

                    <p>
                        <?php pll_e('هدف، ارائه راهکاری است که با واقعیت‌های پرونده و مسیرهای قانونی موجود بیشترین تناسب را داشته باشد.'); ?>
                    </p>

                    <div class="approach-points">
                        <div>
                            <strong><?php pll_e('تحلیل دقیق'); ?></strong>
                        </div>

                        <div>
                            <strong><?php pll_e('ارتباط شفاف'); ?></strong>
                        </div>

                        <div>
                            <strong><?php pll_e('پیگیری مسئولانه'); ?></strong>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <section class="institute">
        <div class="container">

            <div class="institute-head">
                <div>
                    <span class="section-label">
                        <?php pll_e('مؤسسه'); ?>
                    </span>

                    <h2>
                        <?php pll_e('درباره مؤسسه'); ?>
                    </h2>
                </div>
            </div>

            <div class="institute-grid">

                <div class="institute-image">
                    <img
                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/institute-about.jpg'); ?>"
                        alt="<?php echo esc_attr__('مؤسسه حقوقی', 'textdomain'); ?>"
                    >

                    <div class="institute-image-label">
                        <?php pll_e('مؤسسه حقوقی'); ?>
                    </div>
                </div>

                <div class="institute-content">

                    <span class="institute-overline">
                        <?php pll_e('یک مجموعه حقوقی، یک مسیر روشن'); ?>
                    </span>

                    <h3>
                        <?php pll_e('تخصص، نظم و ارتباط شفاف در کنار یکدیگر'); ?>
                    </h3>

                    <p>
                        <?php pll_e('مؤسسه با هدف ارائه خدمات حقوقی منظم و حرفه‌ای شکل گرفته است؛ جایی که هر پرونده ابتدا به‌درستی بررسی می‌شود و سپس مسیر مناسب برای آن انتخاب خواهد شد.'); ?>
                    </p>

                    <p>
                        <?php pll_e('تمرکز مجموعه بر ایجاد ارتباط روشن با موکل، بررسی مستندات و پیگیری منظم امور حقوقی است.'); ?>
                    </p>

                    <div class="institute-features">

                        <div>
                            <strong><?php pll_e('بررسی پرونده'); ?></strong>
                            <small><?php pll_e('شناخت دقیق موضوع و مستندات'); ?></small>
                        </div>

                        <div>
                            <strong><?php pll_e('ارائه راهکار'); ?></strong>
                            <small><?php pll_e('بررسی مسیرهای قابل اجرا'); ?></small>
                        </div>

                        <div>
                            <strong><?php pll_e('پیگیری'); ?></strong>
                            <small><?php pll_e('همراهی در مراحل رسیدگی'); ?></small>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>

    <section class="about-process">
        <div class="container">

            <div class="process-head">

                <div>
                    <span class="section-label">
                        <?php pll_e('نحوه رسیدگی'); ?>
                    </span>

                    <h2>
                        <?php pll_e('از اولین گفت‌وگو'); ?>
                        <span><?php pll_e('تا پیگیری پرونده'); ?></span>
                    </h2>
                </div>

                <p>
                    <?php pll_e('روند رسیدگی بر پایه شناخت دقیق موضوع، بررسی مستندات و انتخاب آگاهانه مسیر حقوقی شکل می‌گیرد.'); ?>
                </p>

            </div>

            <div class="process-list">

                <div class="process-item">
                    <div>
                        <small><?php pll_e('مرحله اول'); ?></small>
                        <h3><?php pll_e('شنیدن و شناخت موضوع'); ?></h3>
                        <p><?php pll_e('موضوع، خواسته موکل و شرایط پرونده بررسی می‌شود.'); ?></p>
                    </div>
                </div>

                <div class="process-item">
                    <div>
                        <small><?php pll_e('مرحله دوم'); ?></small>
                        <h3><?php pll_e('بررسی اسناد و مستندات'); ?></h3>
                        <p><?php pll_e('مدارک موجود بررسی و نکات مهم حقوقی مشخص می‌شوند.'); ?></p>
                    </div>
                </div>

                <div class="process-item">
                    <div>
                        <small><?php pll_e('مرحله سوم'); ?></small>
                        <h3><?php pll_e('ارائه راهکار حقوقی'); ?></h3>
                        <p><?php pll_e('گزینه‌های قابل اجرا بررسی و مسیر پیشنهادی توضیح داده می‌شود.'); ?></p>
                    </div>
                </div>

                <div class="process-item">
                    <div>
                        <small><?php pll_e('مرحله چهارم'); ?></small>
                        <h3><?php pll_e('پیگیری و همراهی'); ?></h3>
                        <p><?php pll_e('در صورت پذیرش پرونده، روند رسیدگی با پیگیری منظم دنبال می‌شود.'); ?></p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="about-trust">
        <div class="container">

            <div class="trust-grid">

                <div class="trust-content">
                    <span class="section-label">
                        <?php pll_e('اعتماد'); ?>
                    </span>

                    <h2>
                        <?php pll_e('قرار نیست درباره پرونده خود'); ?>
                        <span><?php pll_e('در تاریکی تصمیم بگیرید.'); ?></span>
                    </h2>

                    <p>
                        <?php pll_e('شما باید بدانید پرونده در چه وضعیتی قرار دارد، چه گزینه‌هایی وجود دارد و هر تصمیم بر چه مبنایی گرفته می‌شود. ارتباط روشن، بخشی از یک رابطه حرفه‌ای میان وکیل و موکل است.'); ?>
                    </p>
                </div>

                <div class="trust-list">

                    <div>
                        <p><?php pll_e('شنیدن دقیق مسئله و دغدغه‌های موکل'); ?></p>
                    </div>

                    <div>
                        <p><?php pll_e('بررسی مستندات پیش از ارائه راهکار'); ?></p>
                    </div>

                    <div>
                        <p><?php pll_e('توضیح روشن مسیر و گزینه‌های حقوقی'); ?></p>
                    </div>

                    <div>
                        <p><?php pll_e('حفظ محرمانگی اطلاعات و اسناد'); ?></p>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <section class="about-cta">
        <div class="container">

            <span class="section-label">
                <?php pll_e('شروع یک گفت‌وگوی حقوقی'); ?>
            </span>

            <h2>
                <?php pll_e('اگر درباره پرونده خود سؤال یا نگرانی دارید،'); ?>
                <span><?php pll_e('صحبت کنیم.'); ?></span>
            </h2>

            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="about-btn about-btn-primary">
                <?php pll_e('تماس با ما'); ?>
                <span>↗</span>
            </a>

        </div>
    </section>

</main>

<?php get_footer(); ?>
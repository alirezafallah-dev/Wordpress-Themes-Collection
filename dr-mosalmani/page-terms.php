<?php get_header(); ?>

<?php
$legal_last_updated = get_post_meta(
    get_the_ID(),
    'legal_last_updated',
    true
);

if (!$legal_last_updated) {
    $legal_last_updated = get_the_modified_date(
        'Y/m/d',
        get_the_ID()
    );
}
?>

<main class="legal-page">

    <section class="legal-hero">
        <div class="container">
            <div class="legal-hero-grid">

                <div class="legal-hero-meta">
                    <span class="legal-eyebrow">
                        <?php pll_e('قوانین و مقررات'); ?>
                    </span>

                    <span class="legal-hero-index">
                        01 / 01
                    </span>
                </div>

                <div class="legal-hero-content">

                    <span class="legal-overline">
                        <?php pll_e('شرایط استفاده از وب‌سایت'); ?>
                    </span>

                    <h1>
                        <?php pll_e('چارچوب استفاده از'); ?>
                        <span>
                            <?php pll_e('خدمات و محتوای سایت'); ?>
                        </span>
                    </h1>

                    <div class="legal-hero-bottom">

                        <p>
                            <?php pll_e('این صفحه شرایط و ضوابط استفاده از وب‌سایت، محتوای منتشرشده و خدمات ارائه‌شده توسط دفتر دکتر منصور مسلمانی را توضیح می‌دهد. لطفاً پیش از استفاده از خدمات سایت، این مقررات را مطالعه کنید.'); ?>
                        </p>

                        <div class="legal-update">
                            <span>
                                <?php pll_e('آخرین بروزرسانی'); ?>
                            </span>

                            <strong>
                                <?php echo esc_html($legal_last_updated); ?>
                            </strong>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>

    <section class="legal-content">
        <div class="container">

            <div class="legal-layout">

                <aside class="legal-sidebar">

                    <span>
                        <?php pll_e('در این صفحه'); ?>
                    </span>

                    <nav>
                        <a href="#general">
                            <?php pll_e('۱. کلیات'); ?>
                        </a>

                        <a href="#services">
                            <?php pll_e('۲. خدمات حقوقی'); ?>
                        </a>

                        <a href="#content">
                            <?php pll_e('۳. محتوای سایت'); ?>
                        </a>

                        <a href="#privacy">
                            <?php pll_e('۴. اطلاعات کاربران'); ?>
                        </a>

                        <a href="#responsibility">
                            <?php pll_e('۵. مسئولیت‌ها'); ?>
                        </a>

                        <a href="#changes">
                            <?php pll_e('۶. تغییر قوانین'); ?>
                        </a>

                        <a href="#contact">
                            <?php pll_e('۷. ارتباط با ما'); ?>
                        </a>
                    </nav>

                </aside>

                <div class="legal-articles">

                    <article class="legal-article" id="general">

                        <div class="legal-article-number">
                            01
                        </div>

                        <div class="legal-article-content">

                            <span class="legal-article-label">
                                <?php pll_e('کلیات'); ?>
                            </span>

                            <h2>
                                <?php pll_e('استفاده از این وب‌سایت به معنای پذیرش مقررات آن است.'); ?>
                            </h2>

                            <p>
                                <?php pll_e('وب‌سایت دکتر منصور مسلمانی با هدف ارائه اطلاعات عمومی حقوقی، معرفی خدمات و ایجاد امکان ارتباط با دفتر طراحی شده است. استفاده از بخش‌های مختلف سایت به معنای پذیرش شرایط و مقررات مندرج در این صفحه است.'); ?>
                            </p>

                            <p>
                                <?php pll_e('کاربران موظف هستند هنگام استفاده از سایت، قوانین جاری و حقوق سایر اشخاص را رعایت کرده و از استفاده نادرست یا غیرقانونی از امکانات سایت خودداری کنند.'); ?>
                            </p>

                        </div>

                    </article>

                    <article class="legal-article" id="services">

                        <div class="legal-article-number">
                            02
                        </div>

                        <div class="legal-article-content">

                            <span class="legal-article-label">
                                <?php pll_e('خدمات حقوقی'); ?>
                            </span>

                            <h2>
                                <?php pll_e('اطلاعات سایت جایگزین مشاوره تخصصی حقوقی نیست.'); ?>
                            </h2>

                            <p>
                                <?php pll_e('مطالب، مقالات و اطلاعات منتشرشده در این وب‌سایت با هدف آگاهی و اطلاع‌رسانی عمومی ارائه می‌شوند و نباید به‌تنهایی مبنای تصمیم‌گیری حقوقی در یک پرونده خاص قرار گیرند.'); ?>
                            </p>

                            <p>
                                <?php pll_e('بررسی دقیق یک موضوع حقوقی نیازمند شناخت شرایط، اسناد، مدارک و جزئیات همان پرونده است. بنابراین ایجاد رابطه وکالت یا ارائه نظر حقوقی تخصصی، منوط به بررسی موضوع و توافق جداگانه میان وکیل و موکل خواهد بود.'); ?>
                            </p>

                        </div>

                    </article>

                    <article class="legal-article" id="content">

                        <div class="legal-article-number">
                            03
                        </div>

                        <div class="legal-article-content">

                            <span class="legal-article-label">
                                <?php pll_e('محتوای سایت'); ?>
                            </span>

                            <h2>
                                <?php pll_e('محتوای منتشرشده برای استفاده قانونی و اطلاع‌رسانی است.'); ?>
                            </h2>

                            <p>
                                <?php pll_e('محتوای متنی، گرافیکی، تصویری و سایر مطالب منتشرشده در سایت متعلق به این مجموعه یا منتشرشده با مجوز قانونی است و استفاده، بازنشر یا بهره‌برداری تجاری از آن‌ها بدون اجازه قبلی مجاز نیست.'); ?>
                            </p>

                            <p>
                                <?php pll_e('تلاش می‌شود اطلاعات سایت با دقت تهیه و بروزرسانی شود؛ با این حال، تغییر قوانین و مقررات ممکن است باعث شود برخی مطالب در گذر زمان نیازمند اصلاح یا بروزرسانی باشند.'); ?>
                            </p>

                        </div>

                    </article>

                    <article class="legal-article" id="privacy">

                        <div class="legal-article-number">
                            04
                        </div>

                        <div class="legal-article-content">

                            <span class="legal-article-label">
                                <?php pll_e('اطلاعات کاربران'); ?>
                            </span>

                            <h2>
                                <?php pll_e('حفظ محرمانگی اطلاعات کاربران برای ما اهمیت دارد.'); ?>
                            </h2>

                            <p>
                                <?php pll_e('اطلاعاتی که کاربران از طریق فرم‌های ارتباطی یا سایر روش‌های موجود در سایت در اختیار مجموعه قرار می‌دهند، صرفاً در چارچوب ارتباط و ارائه خدمات مورد استفاده قرار می‌گیرند.'); ?>
                            </p>

                            <p>
                                <?php pll_e('کاربران نیز باید در هنگام ارسال اطلاعات، از صحت اطلاعات ارائه‌شده اطمینان حاصل کنند و از ارسال اطلاعاتی که ارتباطی با موضوع درخواست آن‌ها ندارد خودداری کنند.'); ?>
                            </p>

                        </div>

                    </article>

                    <article class="legal-article" id="responsibility">

                        <div class="legal-article-number">
                            05
                        </div>

                        <div class="legal-article-content">

                            <span class="legal-article-label">
                                <?php pll_e('مسئولیت‌ها'); ?>
                            </span>

                            <h2>
                                <?php pll_e('هر تصمیم حقوقی باید بر اساس شرایط واقعی پرونده گرفته شود.'); ?>
                            </h2>

                            <p>
                                <?php pll_e('اطلاعات عمومی موجود در سایت نمی‌تواند جایگزین بررسی اسناد و شرایط اختصاصی یک پرونده شود. کاربران مسئول تصمیم‌هایی هستند که صرفاً بر اساس مطالب عمومی سایت و بدون دریافت مشاوره تخصصی اتخاذ می‌کنند.'); ?>
                            </p>

                            <p>
                                <?php pll_e('ارسال درخواست مشاوره یا اطلاعات از طریق سایت نیز به‌تنهایی به معنای پذیرش پرونده یا ایجاد رابطه وکالت نیست و پذیرش هر پرونده پس از بررسی موضوع و توافق طرفین انجام خواهد شد.'); ?>
                            </p>

                        </div>

                    </article>

                    <article class="legal-article" id="changes">

                        <div class="legal-article-number">
                            06
                        </div>

                        <div class="legal-article-content">

                            <span class="legal-article-label">
                                <?php pll_e('بروزرسانی قوانین'); ?>
                            </span>

                            <h2>
                                <?php pll_e('ممکن است این مقررات در طول زمان تغییر کند.'); ?>
                            </h2>

                            <p>
                                <?php pll_e('با توجه به تغییر قوانین، مقررات، خدمات یا ساختار وب‌سایت، ممکن است مفاد این صفحه در آینده اصلاح یا بروزرسانی شود. نسخه جدید مقررات پس از انتشار در همین صفحه قابل مشاهده خواهد بود.'); ?>
                            </p>

                            <div class="legal-update-box">

                                <span>
                                    <?php pll_e('آخرین بروزرسانی قوانین'); ?>
                                </span>

                                <strong>
                                    <?php echo esc_html($legal_last_updated); ?>
                                </strong>

                            </div>

                        </div>

                    </article>

                    <article class="legal-article" id="contact">

                        <div class="legal-article-number">
                            07
                        </div>

                        <div class="legal-article-content">

                            <span class="legal-article-label">
                                <?php pll_e('ارتباط با ما'); ?>
                            </span>

                            <h2>
                                <?php pll_e('اگر درباره این مقررات سؤالی دارید، با ما در ارتباط باشید.'); ?>
                            </h2>

                            <p>
                                <?php pll_e('در صورت وجود هرگونه سؤال درباره شرایط استفاده از سایت، خدمات حقوقی یا نحوه ارتباط با مجموعه، می‌توانید از طریق صفحه تماس با ما با دفتر دکتر منصور مسلمانی ارتباط برقرار کنید.'); ?>
                            </p>

                            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="legal-contact-button">
                                <span>
                                    <?php pll_e('تماس با ما'); ?>
                                </span>
                                <i>←</i>
                            </a>

                        </div>

                    </article>

                </div>

            </div>

        </div>
    </section>

    <section class="legal-footer-note">
        <div class="container">

            <div class="legal-footer-note-inner">

                <span>
                    <?php pll_e('آخرین بروزرسانی'); ?>
                </span>

                <strong>
                    <?php echo esc_html($legal_last_updated); ?>
                </strong>

            </div>

        </div>
    </section>

</main>

<?php get_footer(); ?>
<?php

function theme_register_strings() {

    if (function_exists('pll_register_string')) {

        // Header
        pll_register_string('reserve', 'رزرو وقت');
        pll_register_string('login', 'ورود / ثبت نام');
        pll_register_string('title', 'وکیل پایه یک دادگستری');
        pll_register_string('call-button', 'تلفن');

        // Main Page
        pll_register_string('main-title-abus', 'دکتر منصور مسلمانی وکیل پایه یک دادگستری');
        pll_register_string('btn-resume', 'مشاهده رزومه کامل');
        pll_register_string('main-resume-summary', 'خلاصه ای از رزومه شما');
        pll_register_string('main-description', 'خلاصه توضیحات شما');
        pll_register_string('pictures-title', 'دکتر منصور مسلمانی');
        pll_register_string('article-more-button', 'ادامه مطلب');

        // Services Section
        pll_register_string('service-title', 'خدمات ما');
        pll_register_string('about-services-desc', 'توضیحات خدمات ما');

        pll_register_string('service-legal-consultation', 'مشاوره حقوقی');
        pll_register_string('service-civil-litigation', 'دعاوی حقوقی');
        pll_register_string('service-criminal-litigation', 'دعاوی کیفری');
        pll_register_string('service-family-law', 'حقوق خانواده');
        pll_register_string('service-real-estate', 'املاک و مستغلات');
        pll_register_string('service-commercial-contracts', 'قراردادها و امور تجاری');
        pll_register_string('service-corporate-law', 'حقوق شرکت‌ها و کسب‌وکار');
        pll_register_string('service-dispute-resolution', 'حل اختلاف و داوری');

        // Footer
        pll_register_string('footer-quick-access', 'دسترسی سریع');
        pll_register_string('footer-services', 'خدمات');
        pll_register_string('footer-useful-links', 'سامانه‌های مرتبط');
        pll_register_string('footer-contact-us', 'ارتباط با ما');
        pll_register_string('location-address', 'آدرس موسسه');
        pll_register_string('footer-mail-address', 'ایمیل');
        pll_register_string('footer-copyright', 'تمامی حقوق محفوظ است');
        
        // Single Page
        pll_register_string('post-reading-time', 'زمان مطالعه');
        pll_register_string('post-reading-time-min', 'دقیقه');
        pll_register_string('post-updated', 'آخرین بروزرسانی');
        pll_register_string('newest-posts', 'جدیدترین مقالات');
        pll_register_string('categories-posts', 'دسته‌بندی‌ها');
        pll_register_string('tags-posts', 'برچسب‌ها');

        // Comments
        pll_register_string('comments-header', 'دیدگاه‌ها');
        pll_register_string('no-comments', 'هنوز دیدگاهی ثبت نشده است.');
        pll_register_string('comment_submit', 'ثبت دیدگاه', 'Theme');
        pll_register_string('comment_author', 'نام و نام خانوادگی', 'Theme');
        pll_register_string('comment_phone', 'شماره تماس', 'Theme');
        pll_register_string('comment_text', 'متن دیدگاه...', 'Theme');
        pll_register_string('comment_cookies', 'اطلاعات من برای ارسال دیدگاه‌های بعدی ذخیره شود.', 'Theme');
        pll_register_string('comment_logged_in', 'شما وارد حساب کاربری خود شده‌اید و می‌توانید دیدگاهتان را ثبت کنید.', 'Theme');
        pll_register_string('comment_notes_before', 'نظر، تجربه یا پرسش خود را بنویسید. دیدگاه شما پس از بررسی منتشر خواهد شد.', 'Theme');
        pll_register_string('comment_notes_after', 'از اینکه زمان گذاشتید و دیدگاه خود را با ما به اشتراک می‌گذارید، سپاسگزاریم.', 'Theme');

        // Archive Page
        pll_register_string('archive_search', 'جستجو');
        pll_register_string('archive_search_placeholder', 'جستجو...');
        pll_register_string('archive_categories', 'دسته بندی ها');
        pll_register_string('archive_all', 'همه');
        pll_register_string('archive_posts', 'پست');
        pll_register_string('archive_newest', 'جدیدترین');
        pll_register_string('archive_oldest', 'قدیمی ترین');
        pll_register_string('archive_title', 'حروف الفبا');
        pll_register_string('archive_most_commented', 'بیشترین نظر');
        pll_register_string('archive_read_more', 'Read More');
        pll_register_string('archive_no_posts', 'هیچ مطلبی پیدا نشد.');

        // Account Dashboard Page
        pll_register_string('account-dashboard', 'پیشخوان');
        pll_register_string('account-bookings', 'رزروهای من');
        pll_register_string('account-support', 'پشتیبانی');
        pll_register_string('account-profile', 'تنظیمات حساب');
        pll_register_string('account-welcome', 'سلام، %s');
        pll_register_string('account-welcome-desc', 'به پنل کاربری دفتر وکالت خوش آمدید.');
        pll_register_string('account-total-bookings', 'کل رزروها');
        pll_register_string('account-paid-bookings', 'پرداخت شده');
        pll_register_string('account-open-tickets', 'تیکت‌های باز');
        pll_register_string('account-bookings-title', 'رزروهای من');
        pll_register_string('account-bookings-desc', 'تاریخچه و وضعیت نوبت‌های مشاوره شما.');
        pll_register_string('account-consultation-type', 'نوع مشاوره');
        pll_register_string('account-date-time', 'تاریخ و ساعت');
        pll_register_string('account-price', 'مبلغ');
        pll_register_string('account-booking-status', 'وضعیت رزرو');
        pll_register_string('account-payment-status', 'وضعیت پرداخت');
        pll_register_string('account-payment-paid', 'پرداخت شده');
        pll_register_string('account-payment-pending', 'در انتظار پرداخت');
        pll_register_string('account-booking-confirmed', 'تایید شده');
        pll_register_string('account-booking-cancelled', 'لغو شده');
        pll_register_string('account-booking-pending', 'در انتظار');
        pll_register_string('account-unknown', 'نامشخص');
        pll_register_string('account-to', 'تا');
        pll_register_string('account-toman', 'تومان');
        pll_register_string('account-no-bookings', 'هنوز رزروی ثبت نکرده‌اید.');
        pll_register_string('account-support-title', 'ارتباط با پشتیبانی');
        pll_register_string('account-support-desc', 'پیام‌های ارسالی شما به دفتر وکالت.');
        pll_register_string('account-send-new-message', 'ارسال پیام جدید');
        pll_register_string('account-subject', 'موضوع');
        pll_register_string('account-subject-placeholder', 'مثلاً: سوال درباره پرونده...');
        pll_register_string('account-message-text', 'متن پیام');
        pll_register_string('account-message-placeholder', 'پیام خود را بنویسید...');
        pll_register_string('account-send-message', 'ارسال پیام');
        pll_register_string('account-recent-messages', 'پیام‌های اخیر');
        pll_register_string('account-ticket-pending', 'در انتظار بررسی');
        pll_register_string('account-ticket-answered', 'پاسخ داده شده');
        pll_register_string('account-no-messages', 'پیامی ثبت نشده است.');
        pll_register_string('account-profile-title', 'تنظیمات حساب کاربری');
        pll_register_string('account-profile-desc', 'اطلاعات شخصی خود را مدیریت کنید.');
        pll_register_string('account-full-name', 'نام و نام خانوادگی');
        pll_register_string('account-mobile', 'شماره موبایل');
        pll_register_string('account-mobile-hint', 'برای تغییر شماره، شماره جدید را وارد و ذخیره کنید.');
        pll_register_string('account-email', 'ایمیل');
        pll_register_string('account-not-registered', 'ثبت نشده');
        pll_register_string('account-google-status', 'وضعیت اتصال به گوگل');
        pll_register_string('account-google-connected', 'متصل است');
        pll_register_string('account-google-disconnected', 'متصل نیست');
        pll_register_string('account-save-changes', 'ذخیره تغییرات');
        pll_register_string('account-logout', 'خروج از حساب');
    }
}
add_action('init', 'theme_register_strings');
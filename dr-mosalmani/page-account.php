<?php
/**
 * Template Name: Account Dashboard
 */
get_header();

if (!is_user_logged_in()) {
    wp_safe_redirect(home_url('/'));
    exit;
}

$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$user_name = $current_user->display_name;
$user_email = $current_user->user_email;
$user_mobile = get_user_meta($user_id, 'auth_mobile', true);
$google_id = get_user_meta($user_id, 'google_id', true);
$google_picture = get_user_meta($user_id, 'google_picture', true);

global $wpdb;

// Retrieve user bookings - by consultation type
$bookings = $wpdb->get_results($wpdb->prepare("
    SELECT b.*, c.title as consultation_title, c.consultation_type
    FROM {$wpdb->prefix}lawyer_bookings b
    LEFT JOIN {$wpdb->prefix}lawyer_consultation_types c ON b.consultation_type_id = c.id
    WHERE b.user_id = %d
    ORDER BY b.created_at DESC
    LIMIT 10
", $user_id));

// Retrieve user tickets
$tickets = $wpdb->get_results($wpdb->prepare("
    SELECT * FROM {$wpdb->prefix}lawyer_support_tickets
    WHERE user_id = %d
    ORDER BY created_at DESC
    LIMIT 5
", $user_id));

// Quick Stats
$total_bookings = count($bookings);
$paid_bookings = count(array_filter($bookings, fn($b) => $b->payment_status === 'paid'));
$open_tickets = count(array_filter($tickets, fn($t) => $t->status === 'open'));
?>

<main class="dashboard-page">
    <div class="container">
        <div class="dashboard-wrapper">
            
            <!-- Sidebar -->
            <aside class="dashboard-sidebar">
                <div class="sidebar-profile">
                    <?php if (!empty($google_picture)) : ?>
                        <img src="<?php echo esc_url($google_picture); ?>" alt="<?php echo esc_attr($user_name); ?>" class="profile-avatar">
                    <?php else : ?>
                        <div class="profile-avatar placeholder"><?php echo esc_html(mb_substr($user_name, 0, 1)); ?></div>
                    <?php endif; ?>
                    <div class="profile-info">
                        <strong><?php echo esc_html($user_name); ?></strong>
                        <span><?php echo esc_html($user_mobile ?: $user_email); ?></span>
                    </div>
                </div>

                <nav class="sidebar-nav">
                    <button type="button" class="nav-item active" data-tab="overview">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        <?php pll_e('پیشخوان'); ?>
                    </button>
                    <button type="button" class="nav-item" data-tab="bookings">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <?php pll_e('رزروهای من'); ?>
                        <?php if ($total_bookings > 0) : ?><span class="nav-badge"><?php echo $total_bookings; ?></span><?php endif; ?>
                    </button>
                    <button type="button" class="nav-item" data-tab="support">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <?php pll_e('پشتیبانی'); ?>
                        <?php if ($open_tickets > 0) : ?><span class="nav-badge"><?php echo $open_tickets; ?></span><?php endif; ?>
                    </button>
                    <button type="button" class="nav-item" data-tab="profile">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <?php pll_e('تنظیمات حساب'); ?>
                    </button>
                </nav>

                <div class="sidebar-footer">
                    <button type="button" id="authLogoutButton" class="logout-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                        <?php pll_e('خروج از حساب'); ?>
                    </button>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="dashboard-content">
                
                <!-- Tab 1: Overview -->
                <section class="tab-content active" id="tab-overview">
                    <div class="content-header">
                        <h1><?php printf(pll__('سلام، %s'), esc_html($user_name)); ?></h1>
                        <p><?php pll_e('به پنل کاربری دفتر وکالت خوش آمدید.'); ?></p>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon bg-gold"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
                            <div class="stat-info"><span><?php pll_e('کل رزروها'); ?></span><strong><?php echo $total_bookings; ?></strong></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon bg-green"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 6L9 17l-5-5"/></svg></div>
                            <div class="stat-info"><span><?php pll_e('پرداخت شده'); ?></span><strong><?php echo $paid_bookings; ?></strong></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon bg-blue"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></div>
                            <div class="stat-info"><span><?php pll_e('تیکت‌های باز'); ?></span><strong><?php echo $open_tickets; ?></strong></div>
                        </div>
                    </div>
                </section>

                <!-- Tab 2: Bookings -->
                <section class="tab-content" id="tab-bookings" hidden>
                    <div class="content-header">
                        <h1><?php pll_e('رزروهای من'); ?></h1>
                        <p><?php pll_e('تاریخچه و وضعیت نوبت‌های مشاوره شما.'); ?></p>
                    </div>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><?php pll_e('نوع مشاوره'); ?></th>
                                    <th><?php pll_e('شیوه برگزاری'); ?></th>
                                    <th><?php pll_e('تاریخ و ساعت'); ?></th>
                                    <th><?php pll_e('مبلغ'); ?></th>
                                    <th><?php pll_e('وضعیت رزرو'); ?></th>
                                    <th><?php pll_e('وضعیت پرداخت'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bookings)) : foreach ($bookings as $b) :
                                    $pay_badge  = $b->payment_status === 'paid' ? 'badge-success' : 'badge-warning';
                                    $pay_text   = $b->payment_status === 'paid' ? pll__('پرداخت شده') : pll__('در انتظار پرداخت');
                                    $book_badge = $b->booking_status === 'confirmed' ? 'badge-success' : ($b->booking_status === 'cancelled' ? 'badge-danger' : 'badge-info');
                                    $book_text  = $b->booking_status === 'confirmed' ? pll__('تایید شده') : ($b->booking_status === 'cancelled' ? pll__('لغو شده') : pll__('در انتظار'));

                                    // Type of consultation
                                    $c_type = isset($b->consultation_type) ? $b->consultation_type : 'online';
                                    $type_badge_class = ($c_type === 'offline') ? 'badge-offline' : 'badge-online';
                                    $type_icon = ($c_type === 'offline') ? '🏛️' : '📞';
                                    $type_text = ($c_type === 'offline') ? pll__('حضوری') : pll__('تلفنی / آنلاین');
                                ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($b->consultation_title ?: pll__('نامشخص')); ?></strong></td>
                                        <td>
                                            <span class="status-badge <?php echo $type_badge_class; ?>">
                                                <?php echo $type_icon . ' ' . $type_text; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo esc_html($b->booking_date); ?><br>
                                            <small><?php echo substr($b->start_time, 0, 5) . ' ' . pll__('تا') . ' ' . substr($b->end_time, 0, 5); ?></small>
                                        </td>
                                        <td><?php echo number_format($b->price); ?> <?php pll_e('تومان'); ?></td>
                                        <td><span class="status-badge <?php echo $book_badge; ?>"><?php echo $book_text; ?></span></td>
                                        <td><span class="status-badge <?php echo $pay_badge; ?>"><?php echo $pay_text; ?></span></td>
                                    </tr>
                                <?php endforeach; else : ?>
                                    <tr><td colspan="6" class="empty-state"><?php pll_e('هنوز رزروی ثبت نکرده‌اید.'); ?></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Tab 3: Support -->
                <section class="tab-content" id="tab-support" hidden>
                    <div class="content-header">
                        <h1><?php pll_e('ارتباط با پشتیبانی'); ?></h1>
                        <p><?php pll_e('پیام‌های ارسالی شما به دفتر وکالت.'); ?></p>
                    </div>
                    
                    <!-- View 1: Ticket List -->
                    <div class="support-view active" id="supportListView">
                        <div class="support-list-actions">
                            <button type="button" class="btn-primary" id="showNewTicketBtn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                                <?php pll_e('تیکت جدید'); ?>
                            </button>
                        </div>
                        
                        <div class="tickets-list-wrapper">
                            <?php if (!empty($tickets)) : ?>
                                <?php foreach ($tickets as $t) : 
                                    $status_class = $t->status === 'open' ? 'badge-info' : ($t->status === 'answered' ? 'badge-success' : 'badge-danger');
                                    $status_text = $t->status === 'open' ? pll__('در انتظار بررسی') : ($t->status === 'answered' ? pll__('پاسخ داده شده') : pll__('بسته شده'));
                                ?>
                                    <div class="ticket-card" data-ticket-id="<?php echo $t->id; ?>">
                                        <div class="ticket-card-body">
                                            <div class="ticket-card-header">
                                                <strong class="ticket-subject"><?php echo esc_html($t->subject); ?></strong>
                                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                            </div>
                                            <div class="ticket-card-meta">
                                                <span><?php echo esc_html(wp_trim_words($t->message, 10, '...')); ?></span>
                                                <small><?php echo esc_html($t->created_at); ?></small>
                                            </div>
                                        </div>
                                        <div class="ticket-card-arrow">←</div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="empty-state"><?php pll_e('هنوز تیکتی ثبت نکرده‌اید.'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- View 2: Ticket Detail (Conversation) -->
                    <div class="support-view" id="supportDetailView" hidden>
                        <button type="button" class="btn-back" id="backToListBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                            <?php pll_e('بازگشت به لیست'); ?>
                        </button>
                        <h3 class="detail-subject" id="detailSubject"></h3>
                        <div class="conversation-box" id="conversationBox"></div>
                        
                        <form class="reply-form" id="replyForm">
                            <input type="hidden" name="ticket_id" id="replyTicketId">
                            <?php wp_nonce_field('ticket_reply_nonce', 'reply_nonce'); ?>
                            <div class="form-group">
                                <textarea name="message" rows="3" required placeholder="<?php pll_e('پاسخ خود را بنویسید...'); ?>"></textarea>
                            </div>
                            <button type="submit" class="btn-primary btn-sm"><?php pll_e('ارسال پاسخ'); ?></button>
                        </form>
                    </div>

                    <!-- View 3: New Ticket -->
                    <div class="support-view" id="supportNewView" hidden>
                        <button type="button" class="btn-back" id="backFromNewBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                            <?php pll_e('بازگشت به لیست'); ?>
                        </button>
                        <h3 class="detail-subject"><?php pll_e('ارسال تیکت جدید'); ?></h3>
                        <form id="newTicketForm" class="new-ticket-form">
                            <?php wp_nonce_field('support_ticket_nonce', 'ticket_nonce'); ?>
                            <div class="form-group">
                                <label><?php pll_e('موضوع'); ?></label>
                                <input type="text" name="subject" required placeholder=" ">
                            </div>
                            <div class="form-group">
                                <label><?php pll_e('متن پیام'); ?></label>
                                <textarea name="message" rows="5" required placeholder=" "></textarea>
                            </div>
                            <button type="submit" class="btn-primary"><?php pll_e('ارسال پیام'); ?></button>
                            <div id="newTicketMessage" class="form-feedback"></div>
                        </form>
                    </div>
                </section>

                <script type="application/json" id="support-tickets-data">
                    <?php
                        $tickets_data = [];
                        global $wpdb;
                        
                        // دریافت تمام تیکت‌های کاربر (نه فقط ۵ تا)
                        $all_tickets = $wpdb->get_results($wpdb->prepare("
                            SELECT * FROM {$wpdb->prefix}lawyer_support_tickets
                            WHERE user_id = %d
                            ORDER BY created_at DESC
                        ", $user_id));
                        
                        if (!empty($all_tickets)) {
                            foreach ($all_tickets as $t) {
                                $replies = $wpdb->get_results($wpdb->prepare(
                                    "SELECT * FROM {$wpdb->prefix}lawyer_support_replies WHERE ticket_id = %d ORDER BY created_at ASC",
                                    $t->id
                                ));
                                $tickets_data[] = [
                                    'id' => (int)$t->id,
                                    'subject' => $t->subject,
                                    'message' => $t->message,
                                    'status' => $t->status,
                                    'created_at' => $t->created_at,
                                    'replies' => $replies ?: []
                                ];
                            }
                        }
                        echo wp_json_encode($tickets_data, JSON_UNESCAPED_UNICODE);
                    ?>
                </script>

                <!-- Tab 4: Profile -->
                <section class="tab-content" id="tab-profile" hidden>
                    <div class="content-header">
                        <h1><?php pll_e('تنظیمات حساب کاربری'); ?></h1>
                        <p><?php pll_e('اطلاعات شخصی خود را مدیریت کنید.'); ?></p>
                    </div>
                    <div class="profile-form-box">
                        <div class="form-group">
                            <input 
                                type="text" 
                                id="accountDisplayName" 
                                value="<?php echo esc_attr($user_name); ?>"
                                placeholder=" "
                            >
                            <label for="accountDisplayName"><?php pll_e('نام و نام خانوادگی'); ?></label>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <input 
                                    type="tel" 
                                    id="accountMobile" 
                                    value="<?php echo esc_attr($user_mobile); ?>" 
                                    placeholder=" "
                                    maxlength="11"
                                    dir="ltr"
                                    style="text-align: left;"
                                >
                                <label for="accountMobile"><?php pll_e('شماره موبایل'); ?></label>
                                <small class="form-hint"><?php pll_e('برای تغییر شماره، شماره جدید را وارد و ذخیره کنید.'); ?></small>
                            </div>
                            <div class="form-group">
                                <input 
                                    type="text" 
                                    value="<?php echo esc_attr($user_email ?: pll__('ثبت نشده')); ?>" 
                                    placeholder=" "
                                    disabled
                                >
                                <label><?php pll_e('ایمیل'); ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input 
                                type="text" 
                                value="<?php echo !empty($google_id) ? pll__('متصل است') : pll__('متصل نیست'); ?>" 
                                placeholder=" "
                                disabled
                            >
                            <label><?php pll_e('وضعیت اتصال به گوگل'); ?></label>
                        </div>
                        <button type="button" id="accountSaveButton" class="btn-primary"><?php pll_e('ذخیره تغییرات'); ?></button>
                        <div id="accountMessage" class="form-feedback"></div>
                    </div>
                </section>

            </main>
        </div>
    </div>
</main>

<?php get_footer(); ?>
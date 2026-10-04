<?php
/**
 * Template Name: Consultation Booking
 */
get_header();

global $wpdb;

// 1. Receiving various types of consultation (categorized by type)
$all_consultations = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}lawyer_consultation_types WHERE status = 'active' ORDER BY consultation_type ASC, price ASC"
);

// گروه‌بندی مشاوره‌ها
$online_consultations  = [];
$offline_consultations = [];

foreach ($all_consultations as $type) {
    $type_key = ($type->consultation_type === 'offline') ? 'offline' : 'online';
    if ($type_key === 'online') {
        $online_consultations[] = $type;
    } else {
        $offline_consultations[] = $type;
    }
}

// 2. Retrieve business hours
$working_hours = $wpdb->get_results(
    "SELECT day_of_week, is_active, start_time, end_time FROM {$wpdb->prefix}lawyer_working_hours ORDER BY day_of_week ASC",
    ARRAY_A
);
$working_hours_map = [];
foreach ($working_hours as $hour) {
    $working_hours_map[(int)$hour['day_of_week']] = [
        'is_active'  => (int)$hour['is_active'],
        'start_time' => substr($hour['start_time'], 0, 5),
        'end_time'   => substr($hour['end_time'], 0, 5),
    ];
}

// 3. Retrieve existing reservations
$booked_slots = $wpdb->get_results(
    "SELECT booking_date, start_time, end_time FROM {$wpdb->prefix}lawyer_bookings
     WHERE booking_status IN ('pending', 'confirmed') AND payment_status IN ('pending', 'paid')",
    ARRAY_A
);

// 4. List of official holidays in Iran
$iran_holidays = [
    '1405/06/08','1405/08/22','1405/10/02','1405/10/16','1405/11/04','1405/11/22',
    '1405/12/09','1405/12/19','1405/12/20','1405/12/29',
    '1406/01/01','1406/01/02','1406/01/03','1406/01/04','1406/01/12','1406/01/13',
    '1406/01/14','1406/02/27','1406/03/04','1406/03/14','1406/03/15','1406/03/24',
    '1406/03/25','1406/05/13','1406/06/08','1406/06/16','1406/07/22','1406/09/01',
    '1406/10/01','1406/10/24','1406/11/15','1406/11/22','1406/12/09','1406/12/19',
    '1406/12/20','1406/12/29',
    '1407/01/01','1407/01/02','1407/01/03','1407/01/04','1407/01/12','1407/01/13',
    '1407/02/16','1407/02/24','1407/03/14','1407/03/15','1407/04/13','1407/04/21',
    '1407/04/30','1407/05/18','1407/07/30','1407/09/10','1407/09/24','1407/10/11',
    '1407/11/17','1407/11/22','1407/11/27','1407/11/28','1407/12/21','1407/12/29',
    '1408/01/01','1408/01/02','1408/01/03','1408/01/04','1408/01/12','1408/01/13',
    '1408/02/05','1408/02/13','1408/03/03','1408/03/04','1408/03/14','1408/03/15',
    '1408/04/13','1408/04/21','1408/04/30','1408/05/08','1408/07/20','1408/08/30',
    '1408/09/14','1408/10/01','1408/11/06','1408/11/16','1408/11/17','1408/11/22',
    '1408/12/10','1408/12/29',
    '1409/01/01','1409/01/02','1409/01/03','1409/01/04','1409/01/12','1409/01/13',
    '1409/01/24','1409/02/01','1409/02/22','1409/02/23','1409/03/14','1409/03/15',
    '1409/04/01','1409/04/09','1409/04/18','1409/04/27','1409/07/09','1409/08/18',
    '1409/09/02','1409/09/20','1409/10/25','1409/11/05','1409/11/06','1409/11/22',
    '1409/11/29','1409/12/29',
];

// 5. User Information
$current_user_data = null;
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $current_user_data = [
        'name'   => $user->display_name,
        'email'  => $user->user_email,
        'mobile' => get_user_meta($user->ID, 'auth_mobile', true),
    ];
}
?>
<main class="booking-app">
    <div class="container flex">
        <!-- Sidebar: Consultation Types -->
        <aside class="booking-sidebar">
            <div class="sidebar-header">
                <h3>نوع مشاوره</h3>
                <span class="sidebar-subtitle">یکی از خدمات را انتخاب کنید</span>
            </div>

            <?php if (empty($all_consultations)) : ?>
                <div class="empty-sidebar-state">
                    <p>📭 هیچ نوع مشاوره فعالی وجود ندارد.</p>
                </div>
            <?php else : ?>

                <!-- Group 1: Online Consultations -->
                <?php if (!empty($online_consultations)) : ?>
                    <div class="consultation-group">
                        <div class="group-header online">
                            <span class="group-icon">📞</span>
                            <div class="group-info">
                                <strong>مشاوره تلفنی / آنلاین</strong>
                                <small><?php echo count($online_consultations); ?> خدمت موجود</small>
                            </div>
                        </div>
                        <ul class="consultation-list">
                            <?php foreach ($online_consultations as $type) : ?>
                                <li class="consultation-item"
                                    data-id="<?php echo $type->id; ?>"
                                    data-duration="<?php echo $type->duration; ?>"
                                    data-price="<?php echo $type->price; ?>"
                                    data-title="<?php echo esc_attr($type->title); ?>"
                                    data-type="online">
                                    <div class="item-badge online">📞</div>
                                    <div class="item-info">
                                        <strong><?php echo esc_html($type->title); ?></strong>
                                        <span>⏱️ <?php echo $type->duration; ?> دقیقه</span>
                                    </div>
                                    <div class="item-price">
                                        <?php echo number_format($type->price); ?>
                                        <small>تومان</small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Group 2: Offline Consultations -->
                <?php if (!empty($offline_consultations)) : ?>
                    <div class="consultation-group">
                        <div class="group-header offline">
                            <span class="group-icon">🏛️</span>
                            <div class="group-info">
                                <strong>مشاوره حضوری</strong>
                                <small><?php echo count($offline_consultations); ?> خدمت موجود</small>
                            </div>
                        </div>
                        <ul class="consultation-list">
                            <?php foreach ($offline_consultations as $type) : ?>
                                <li class="consultation-item"
                                    data-id="<?php echo $type->id; ?>"
                                    data-duration="<?php echo $type->duration; ?>"
                                    data-price="<?php echo $type->price; ?>"
                                    data-title="<?php echo esc_attr($type->title); ?>"
                                    data-type="offline">
                                    <div class="item-badge offline">🏛️</div>
                                    <div class="item-info">
                                        <strong><?php echo esc_html($type->title); ?></strong>
                                        <span>⏱️ <?php echo $type->duration; ?> دقیقه</span>
                                    </div>
                                    <div class="item-price">
                                        <?php echo number_format($type->price); ?>
                                        <small>تومان</small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            <!-- Sidebar Footer Info -->
            <div class="sidebar-footer-info">
                <div class="info-box">
                    <div class="info-icon">🔒</div>
                    <div class="info-text">
                        <strong>پرداخت امن</strong>
                        <small>درگاه معتبر بانکی</small>
                    </div>
                </div>
                <div class="info-box">
                    <div class="info-icon">📱</div>
                    <div class="info-text">
                        <strong>پشتیبانی ۲۴/۷</strong>
                        <small>از طریق پنل کاربری</small>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Area -->
        <section class="booking-main">
            <!-- Header & Switcher -->
            <div class="main-header">
                <h2>انتخاب زمان مشاوره</h2>
                <div class="calendar-switcher">
                    <button type="button" data-cal="persian" class="active">شمسی</button>
                    <button type="button" data-cal="gregory">میلادی</button>
                    <button type="button" data-cal="islamic">قمری</button>
                </div>
            </div>

            <!-- Calendar & Time Grid -->
            <div class="main-content">
                <div class="calendar-section">
                    <div class="calendar-nav">
                        <button type="button" id="calPrev">‹</button>
                        <span id="calTitle"></span>
                        <button type="button" id="calNext">›</button>
                    </div>
                    <div class="calendar-weekdays">
                        <span>ش</span><span>ی</span><span>د</span><span>س</span><span>چ</span><span>پ</span><span>ج</span>
                    </div>
                    <div class="calendar-grid" id="calGrid"></div>
                </div>

                <div class="time-section">
                    <h4>ساعات موجود</h4>
                    <div class="time-slots" id="timeSlots">
                        <p class="empty-state">لطفاً ابتدا نوع مشاوره و یک روز را انتخاب کنید.</p>
                    </div>
                </div>
            </div>

            <!-- Footer: Summary & Confirm -->
            <div class="main-footer">
                <div class="summary-box" id="summaryBox" hidden>
                    <div class="summary-item type-row">
                        <span>نوع:</span>
                        <strong id="sumTypeCategory">-</strong>
                    </div>
                    <div class="summary-item"><span>مشاوره:</span> <strong id="sumType">-</strong></div>
                    <div class="summary-item"><span>تاریخ:</span> <strong id="sumDate">-</strong></div>
                    <div class="summary-item"><span>ساعت:</span> <strong id="sumTime">-</strong></div>
                    <div class="summary-item total"><span>مبلغ:</span> <strong id="sumPrice">-</strong></div>
                </div>
                <button type="button" id="confirmBookingBtn" class="btn-confirm" disabled>تأیید و پرداخت</button>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>

<!-- Safe data injection into JS -->
<script type="application/json" id="booking-data">
<?php
echo wp_json_encode([
    'ajaxUrl'      => admin_url('admin-ajax.php'),
    'isLoggedIn'   => is_user_logged_in(),
    'currentUser'  => $current_user_data,
    'workingHours' => $working_hours_map,
    'bookedSlots'  => $booked_slots,
    'holidays'     => $iran_holidays,
]);
?>
</script>
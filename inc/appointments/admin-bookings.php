<?php
/**
 * =========================================================
 * Admin - Professional Bookings Management
 * =========================================================
 */

// 1. Register the menu
add_action('admin_menu', 'lawyer_bookings_admin_menu');
function lawyer_bookings_admin_menu() {
    add_submenu_page(
        'lawyer-consultation-types',
        'مدیریت رزروها',
        'لیست رزروها',
        'manage_options',
        'lawyer-bookings',
        'lawyer_bookings_router'
    );
}

// 2. Router (List or Edit)
function lawyer_bookings_router() {
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
    if ($action === 'edit' && isset($_GET['id'])) {
        lawyer_bookings_render_edit_page();
    } else {
        lawyer_bookings_render_list_page();
    }
}

// 3. Processing actions (delete, change status, save edit)
add_action('admin_init', 'lawyer_bookings_handle_actions');
function lawyer_bookings_handle_actions() {
    if (!is_admin() || !current_user_can('manage_options')) return;
    if (!isset($_GET['page']) || $_GET['page'] !== 'lawyer-bookings') return;

    global $wpdb;
    $table = $wpdb->prefix . 'lawyer_bookings';

    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    $id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field($_GET['_wpnonce']) : '';
    $redirect_url = admin_url('admin.php?page=lawyer-bookings');

    // Delete
    if ($action === 'delete' && $id && wp_verify_nonce($nonce, 'delete_booking_' . $id)) {
        $wpdb->delete($table, ['id' => $id], ['%d']);
        wp_safe_redirect(add_query_arg('msg', 'deleted', $redirect_url));
        exit;
    }

    // Quickly change booking status
    if ($action === 'set_status' && $id && isset($_GET['status']) && wp_verify_nonce($nonce, 'set_status_' . $id)) {
        $wpdb->update($table, ['booking_status' => sanitize_text_field($_GET['status'])], ['id' => $id], ['%s'], ['%d']);
        wp_safe_redirect(add_query_arg('msg', 'updated', $redirect_url));
        exit;
    }

    // Quickly change payment status
    if ($action === 'set_payment' && $id && isset($_GET['status']) && wp_verify_nonce($nonce, 'set_payment_' . $id)) {
        $wpdb->update($table, ['payment_status' => sanitize_text_field($_GET['status'])], ['id' => $id], ['%s'], ['%d']);
        wp_safe_redirect(add_query_arg('msg', 'updated', $redirect_url));
        exit;
    }

    // Save edit form
    if ($action === 'update_booking' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $post_id = isset($_POST['booking_id']) ? absint($_POST['booking_id']) : 0;
        if (!wp_verify_nonce($_POST['update_nonce'], 'update_booking_' . $post_id)) wp_die('درخواست نامعتبر است.');

        $data = [
            'consultation_type_id' => absint($_POST['consultation_type_id']),
            'booking_date'         => sanitize_text_field($_POST['booking_date']),
            'start_time'           => sanitize_text_field($_POST['start_time']) . ':00',
            'end_time'             => sanitize_text_field($_POST['end_time']) . ':00',
            'price'                => floatval($_POST['price']),
            'client_name'          => sanitize_text_field($_POST['client_name']),
            'client_mobile'        => sanitize_text_field($_POST['client_mobile']),
            'booking_status'       => sanitize_text_field($_POST['booking_status']),
            'payment_status'       => sanitize_text_field($_POST['payment_status']),
        ];
        $format = ['%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s'];
        $wpdb->update($table, $data, ['id' => $post_id], $format, ['%d']);
        wp_safe_redirect(add_query_arg('msg', 'updated', $redirect_url));
        exit;
    }
}

// 4. Render the list page
function lawyer_bookings_render_list_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'lawyer_bookings';
    $c_table = $wpdb->prefix . 'lawyer_consultation_types';

    // Filters
    $filter_status = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : '';
    $filter_payment = isset($_GET['filter_payment']) ? sanitize_text_field($_GET['filter_payment']) : '';
    $filter_type = isset($_GET['filter_type']) ? sanitize_text_field($_GET['filter_type']) : '';

    $where = "1=1";
    if ($filter_status) $where .= $wpdb->prepare(" AND b.booking_status = %s", $filter_status);
    if ($filter_payment) $where .= $wpdb->prepare(" AND b.payment_status = %s", $filter_payment);
    if ($filter_type && in_array($filter_type, ['online', 'offline'])) {
        $where .= $wpdb->prepare(" AND c.consultation_type = %s", $filter_type);
    }

    // Retrieve consultation_type from the table as well
    $bookings = $wpdb->get_results("
        SELECT b.*, c.title as consultation_title, c.consultation_type
        FROM {$table} b
        LEFT JOIN {$c_table} c ON b.consultation_type_id = c.id
        WHERE {$where}
        ORDER BY b.created_at DESC
    ");
    ?>
    <style>
        .lawyer-panel-wrap { margin: 20px 20px 20px 0; font-family: Tahoma, sans-serif; }
        .lawyer-panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .lawyer-panel-header h1 { font-size: 22px; font-weight: 600; color: #1d2327; margin: 0; }
        .lawyer-filters { display: flex; gap: 10px; background: #fff; padding: 15px; border: 1px solid #c3c4c7; border-radius: 8px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .lawyer-filters select { padding: 6px 10px; border-radius: 4px; border: 1px solid #8c8f94; }
        .lawyer-table-wrap { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; overflow: hidden; }
        .lawyer-table { width: 100%; border-collapse: collapse; }
        .lawyer-table th { background: #f6f7f7; padding: 12px 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #c3c4c7; font-size: 13px; }
        .lawyer-table td { padding: 14px 15px; border-bottom: 1px solid #f0f0f1; vertical-align: middle; font-size: 13px; }
        .lawyer-table tr:last-child td { border-bottom: none; }
        .lawyer-table tr:hover { background: #f9f9f9; }
        .lawyer-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-pending { background: #fcf0e3; color: #9a6700; }
        .badge-confirmed { background: #e6f6e6; color: #007117; }
        .badge-cancelled { background: #fce1e1; color: #8a0000; }
        .badge-paid { background: #e6f6e6; color: #007117; }
        .badge-unpaid { background: #fce1e1; color: #8a0000; }

        /* Consultation Type Badge */
        .badge-consultation-type { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-online { background: #e6f6e6; color: #007117; }
        .badge-offline { background: #e5e5ff; color: #0a4b78; }

        .lawyer-actions a { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; text-decoration: none; margin-left: 4px; border: 1px solid transparent; }
        .act-edit { background: #f0f6fc; color: #0a4b78; border-color: #c3c4c7; }
        .act-confirm { color: #007117; }
        .act-cancel { color: #9a6700; }
        .act-paid { color: #007117; }
        .act-delete { color: #8a0000; }
        .lawyer-actions a:hover { background: #f6f7f7; border-color: #8c8f94; }
        .client-info strong { display: block; font-size: 13px; color: #1d2327; }
        .client-info small { color: #646970; font-size: 12px; direction: ltr; display: inline-block; }
        .date-cell { direction: rtl; text-align: right; }
        .date-cell small { display: block; color: #646970; font-size: 12px; margin-top: 2px; }
    </style>

    <div class="lawyer-panel-wrap">
        <div class="lawyer-panel-header">
            <h1>مدیریت رزروهای مشاوره</h1>
        </div>

        <?php if (isset($_GET['msg'])) : ?>
            <div class="notice notice-success is-dismissible"><p>عملیات با موفقیت انجام شد.</p></div>
        <?php endif; ?>

        <form method="get" class="lawyer-filters">
            <input type="hidden" name="page" value="lawyer-bookings">
            <select name="filter_status">
                <option value="">همه وضعیت‌های رزرو</option>
                <option value="pending" <?php selected($filter_status, 'pending'); ?>>در انتظار</option>
                <option value="confirmed" <?php selected($filter_status, 'confirmed'); ?>>تایید شده</option>
                <option value="cancelled" <?php selected($filter_status, 'cancelled'); ?>>لغو شده</option>
            </select>
            <select name="filter_payment">
                <option value="">همه وضعیت‌های پرداخت</option>
                <option value="pending" <?php selected($filter_payment, 'pending'); ?>>پرداخت نشده</option>
                <option value="paid" <?php selected($filter_payment, 'paid'); ?>>پرداخت شده</option>
            </select>
            <select name="filter_type">
                <option value="">همه انواع مشاوره</option>
                <option value="online" <?php selected($filter_type, 'online'); ?>>تلفنی / آنلاین</option>
                <option value="offline" <?php selected($filter_type, 'offline'); ?>>حضوری</option>
            </select>
            <button type="submit" class="button">اعمال فیلتر</button>
        </form>

        <div class="lawyer-table-wrap">
            <table class="lawyer-table">
                <thead>
                    <tr>
                        <th>مشتری</th>
                        <th>نوع مشاوره</th>
                        <th>شیوه برگزاری</th>
                        <th>تاریخ و ساعت</th>
                        <th>مبلغ</th>
                        <th>وضعیت پرداخت</th>
                        <th>وضعیت رزرو</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)) : foreach ($bookings as $b) :
                        $base_url = admin_url('admin.php?page=lawyer-bookings');
                        $status_class = 'badge-' . $b->booking_status;
                        $status_text = $b->booking_status === 'confirmed' ? 'تایید شده' : ($b->booking_status === 'cancelled' ? 'لغو شده' : 'در انتظار');
                        $pay_class = 'badge-' . $b->payment_status;
                        $pay_text = $b->payment_status === 'paid' ? 'پرداخت شده' : 'پرداخت نشده';

                        // Consultation Type
                        $consultation_type = isset($b->consultation_type) ? $b->consultation_type : 'online';
                        $type_badge_class = ($consultation_type === 'offline') ? 'badge-offline' : 'badge-online';
                        $type_icon = ($consultation_type === 'offline') ? '🏛️' : '📞';
                        $type_text = ($consultation_type === 'offline') ? 'حضوری' : 'تلفنی / آنلاین';

                        // Convert date to Shamsi using wp_date (compatible with Parsi Date)
                        $timestamp = strtotime($b->booking_date);
                        $persian_date = wp_date('Y/m/d', $timestamp);

                        // Persian time format
                        $start_time_fa = wp_date('H:i', strtotime($b->start_time));
                        $end_time_fa = wp_date('H:i', strtotime($b->end_time));
                        $time_range = $start_time_fa . ' تا ' . $end_time_fa;
                    ?>
                        <tr>
                            <td class="client-info">
                                <strong><?php echo esc_html($b->client_name ?: 'نامشخص'); ?></strong>
                                <small><?php echo esc_html($b->client_mobile ?: '-'); ?></small>
                            </td>
                            <td><?php echo esc_html($b->consultation_title ?: 'نامشخص'); ?></td>
                            <td>
                                <span class="badge-consultation-type <?php echo $type_badge_class; ?>">
                                    <?php echo $type_icon . ' ' . $type_text; ?>
                                </span>
                            </td>
                            <td class="date-cell">
                                <?php echo esc_html($persian_date); ?>
                                <small><?php echo esc_html($time_range); ?></small>
                            </td>
                            <td><?php echo number_format($b->price); ?> تومان</td>
                            <td>
                                <span class="lawyer-badge <?php echo $pay_class; ?>"><?php echo $pay_text; ?></span>
                                <?php if ($b->payment_status !== 'paid') : ?>
                                    <a href="<?php echo wp_nonce_url(add_query_arg(['action' => 'set_payment', 'id' => $b->id, 'status' => 'paid'], $base_url), 'set_payment_' . $b->id); ?>" class="act-paid" title="تغییر به پرداخت شده">✓</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="lawyer-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td class="lawyer-actions">
                                <a href="<?php echo add_query_arg(['action' => 'edit', 'id' => $b->id], $base_url); ?>" class="act-edit">ویرایش</a>
                                <?php if ($b->booking_status !== 'confirmed') : ?>
                                    <a href="<?php echo wp_nonce_url(add_query_arg(['action' => 'set_status', 'id' => $b->id, 'status' => 'confirmed'], $base_url), 'set_status_' . $b->id); ?>" class="act-confirm">تایید</a>
                                <?php endif; ?>
                                <?php if ($b->booking_status !== 'cancelled') : ?>
                                    <a href="<?php echo wp_nonce_url(add_query_arg(['action' => 'set_status', 'id' => $b->id, 'status' => 'cancelled'], $base_url), 'set_status_' . $b->id); ?>" class="act-cancel">لغو</a>
                                <?php endif; ?>
                                <a href="<?php echo wp_nonce_url(add_query_arg(['action' => 'delete', 'id' => $b->id], $base_url), 'delete_booking_' . $b->id); ?>" class="act-delete" onclick="return confirm('آیا مطمئن هستید؟');">حذف</a>
                            </td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="8" style="text-align:center; padding:30px; color:#646970;">هنوز رزروی ثبت نشده است.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

// 5. Render the edit page
function lawyer_bookings_render_edit_page() {
    global $wpdb;
    $id = absint($_GET['id']);
    $table = $wpdb->prefix . 'lawyer_bookings';
    $c_table = $wpdb->prefix . 'lawyer_consultation_types';
    $booking = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id));
    if (!$booking) wp_die('رزرو مورد نظر یافت نشد.');
    $consultations = $wpdb->get_results("SELECT id, title FROM {$c_table} WHERE status = 'active'");
    ?>
    <style>
        .lawyer-edit-wrap { max-width: 800px; margin: 20px 20px 20px 0; font-family: Tahoma, sans-serif; }
        .lawyer-edit-box { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; padding: 25px; }
        .lawyer-edit-box h2 { margin-top: 0; border-bottom: 1px solid #f0f0f1; padding-bottom: 15px; }
        .lawyer-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .lawyer-form-group { margin-bottom: 20px; }
        .lawyer-form-group.full { grid-column: 1 / -1; }
        .lawyer-form-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 13px; }
        .lawyer-form-group input, .lawyer-form-group select { width: 100%; padding: 8px 10px; border: 1px solid #8c8f94; border-radius: 4px; }
        .lawyer-form-actions { margin-top: 20px; display: flex; gap: 10px; }
    </style>
    <div class="lawyer-edit-wrap">
        <div class="lawyer-edit-box">
            <h2>ویرایش رزرو شماره <?php echo $id; ?></h2>
            <form method="post" action="<?php echo admin_url('admin.php?page=lawyer-bookings&action=update_booking'); ?>">
                <?php wp_nonce_field('update_booking_' . $id, 'update_nonce'); ?>
                <input type="hidden" name="booking_id" value="<?php echo $id; ?>">
                <div class="lawyer-form-grid">
                    <div class="lawyer-form-group">
                        <label>نام مشتری</label>
                        <input type="text" name="client_name" value="<?php echo esc_attr($booking->client_name); ?>" required>
                    </div>
                    <div class="lawyer-form-group">
                        <label>شماره موبایل</label>
                        <input type="text" name="client_mobile" value="<?php echo esc_attr($booking->client_mobile); ?>" required>
                    </div>
                    <div class="lawyer-form-group">
                        <label>نوع مشاوره</label>
                        <select name="consultation_type_id">
                            <?php foreach ($consultations as $c) : ?>
                                <option value="<?php echo $c->id; ?>" <?php selected($booking->consultation_type_id, $c->id); ?>><?php echo esc_html($c->title); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="lawyer-form-group">
                        <label>مبلغ (تومان)</label>
                        <input type="number" name="price" value="<?php echo esc_attr($booking->price); ?>" required>
                    </div>
                    <div class="lawyer-form-group">
                        <label>تاریخ</label>
                        <input type="date" name="booking_date" value="<?php echo esc_attr($booking->booking_date); ?>" required>
                    </div>
                    <div class="lawyer-form-group">
                        <label>ساعت شروع و پایان</label>
                        <div style="display:flex; gap:10px;">
                            <input type="time" name="start_time" value="<?php echo esc_attr(substr($booking->start_time, 0, 5)); ?>" required>
                            <input type="time" name="end_time" value="<?php echo esc_attr(substr($booking->end_time, 0, 5)); ?>" required>
                        </div>
                    </div>
                    <div class="lawyer-form-group">
                        <label>وضعیت رزرو</label>
                        <select name="booking_status">
                            <option value="pending" <?php selected($booking->booking_status, 'pending'); ?>>در انتظار</option>
                            <option value="confirmed" <?php selected($booking->booking_status, 'confirmed'); ?>>تایید شده</option>
                            <option value="cancelled" <?php selected($booking->booking_status, 'cancelled'); ?>>لغو شده</option>
                        </select>
                    </div>
                    <div class="lawyer-form-group">
                        <label>وضعیت پرداخت</label>
                        <select name="payment_status">
                            <option value="pending" <?php selected($booking->payment_status, 'pending'); ?>>پرداخت نشده</option>
                            <option value="paid" <?php selected($booking->payment_status, 'paid'); ?>>پرداخت شده</option>
                        </select>
                    </div>
                </div>
                <div class="lawyer-form-actions">
                    <button type="submit" class="button button-primary">ذخیره تغییرات</button>
                    <a href="<?php echo admin_url('admin.php?page=lawyer-bookings'); ?>" class="button">انصراف</a>
                </div>
            </form>
        </div>
    </div>
    <?php
}
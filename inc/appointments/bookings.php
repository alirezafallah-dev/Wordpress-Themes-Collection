<?php
/**
* =========================================================
* Bookings
* =========================================================
*/

// Move from admin_init to after_setup_theme (better performance)
add_action('after_setup_theme', 'lawyer_create_bookings_table');

function lawyer_create_bookings_table() {
    global $wpdb;
    $table_name    = $wpdb->prefix . 'lawyer_bookings';
    $charset_collate = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    
    $sql = "CREATE TABLE {$table_name} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        consultation_type_id bigint(20) unsigned NOT NULL,
        booking_date date NOT NULL,
        start_time time NOT NULL,
        end_time time NOT NULL,
        price decimal(15,2) NOT NULL DEFAULT 0,
        payment_status varchar(20) NOT NULL DEFAULT 'pending',
        booking_status varchar(20) NOT NULL DEFAULT 'pending',
        client_name varchar(100) DEFAULT NULL,
        client_mobile varchar(20) DEFAULT NULL,
        payment_id varchar(100) DEFAULT NULL,
        tracking_code varchar(100) DEFAULT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY consultation_type_id (consultation_type_id),
        KEY booking_date (booking_date),
        KEY booking_status (booking_status),
        KEY payment_status (payment_status)
    ) {$charset_collate};";
    
    dbDelta($sql);
}

add_action('wp_ajax_create_lawyer_booking', 'lawyer_create_booking');

function lawyer_create_booking() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'ابتدا وارد حساب کاربری شوید.'], 401);
    }
    
    // Nonce Verification
    if (!isset($_POST['nonce']) || !wp_verify_nonce(
        sanitize_text_field(wp_unslash($_POST['nonce'])),
        'create_lawyer_booking'
    )) {
        wp_send_json_error(['message' => 'درخواست نامعتبر است.'], 403);
    }
    
    $user_id         = get_current_user_id();
    $consultation_id = isset($_POST['consultation_id']) ? absint($_POST['consultation_id']) : 0;
    $booking_date    = isset($_POST['booking_date']) ? sanitize_text_field(wp_unslash($_POST['booking_date'])) : '';
    $start_time      = isset($_POST['start_time']) ? sanitize_text_field(wp_unslash($_POST['start_time'])) : '';
    $end_time        = isset($_POST['end_time']) ? sanitize_text_field(wp_unslash($_POST['end_time'])) : '';
    
    if (!$consultation_id || !$booking_date || !$start_time || !$end_time) {
        wp_send_json_error(['message' => 'اطلاعات رزرو کامل نیست.'], 400);
    }
    
    // Date and time format validation
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $booking_date)) {
        wp_send_json_error(['message' => 'فرمت تاریخ نامعتبر است.'], 400);
    }
    if (!preg_match('/^\d{2}:\d{2}$/', $start_time) || !preg_match('/^\d{2}:\d{2}$/', $end_time)) {
        wp_send_json_error(['message' => 'فرمت زمان نامعتبر است.'], 400);
    }
    
    global $wpdb;
    $consultation_table = $wpdb->prefix . 'lawyer_consultation_types';
    $bookings_table     = $wpdb->prefix . 'lawyer_bookings';
    
    $consultation = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$consultation_table} WHERE id = %d AND status = 'active'",
        $consultation_id
    ));
    
    if (!$consultation) {
        wp_send_json_error(['message' => 'نوع مشاوره معتبر نیست یا غیرفعال شده است.'], 400);
    }
    
    // Resolving race conditions using database transactions and SELECT FOR UPDATE
    $wpdb->query('START TRANSACTION');
    
    try {
        // Locking the slot to prevent simultaneous booking
        $existing_booking = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$bookings_table}
             WHERE booking_date = %s
             AND booking_status != 'cancelled'
             AND start_time < %s
             AND end_time > %s
             FOR UPDATE
             LIMIT 1",
            $booking_date,
            $end_time,
            $start_time
        ));
        
        if ($existing_booking) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error([
                'message' => 'متأسفانه این زمان همین الان رزرو شد. لطفاً زمان دیگری را انتخاب کنید.'
            ], 409);
        }
        
        // Retrieve user information
        $user          = wp_get_current_user();
        $client_name   = $user->display_name;
        $client_mobile = get_user_meta($user->ID, 'auth_mobile', true);
        
        $inserted = $wpdb->insert(
            $bookings_table,
            [
                'user_id'              => $user_id,
                'consultation_type_id' => $consultation_id,
                'booking_date'         => $booking_date,
                'start_time'           => $start_time . ':00',
                'end_time'             => $end_time . ':00',
                'price'                => $consultation->price,
                'payment_status'       => 'pending',
                'booking_status'       => 'pending',
                'client_name'          => $client_name,
                'client_mobile'        => $client_mobile,
            ],
            ['%d', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s']
        );
        
        if ($inserted === false) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['message' => 'ثبت رزرو به دلیل خطای دیتابیس انجام نشد.'], 500);
        }
        
        $wpdb->query('COMMIT');
        
        $booking_id = $wpdb->insert_id;
        
        wp_send_json_success([
            'booking_id' => $booking_id,
            'price'      => (float) $consultation->price,
            'title'      => $consultation->title,
            'message'    => 'رزرو با موفقیت ثبت شد. در حال انتقال به درگاه پرداخت...',
            'next_step'  => 'payment',
        ]);
        
    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        error_log('Booking creation error: ' . $e->getMessage());
        wp_send_json_error(['message' => 'خطای سیستمی در ثبت رزرو.'], 500);
    }
}

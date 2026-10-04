<?php
// Move from admin_init to after_setup_theme
add_action('after_setup_theme', 'lawyer_create_support_tables');

function lawyer_create_support_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    
    $table_tickets = $wpdb->prefix . 'lawyer_support_tickets';
    $sql_tickets   = "CREATE TABLE {$table_tickets} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        subject varchar(255) NOT NULL,
        message text NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'open',
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY status (status)
    ) {$charset_collate};";
    dbDelta($sql_tickets);
    
    $table_replies = $wpdb->prefix . 'lawyer_support_replies';
    $sql_replies   = "CREATE TABLE {$table_replies} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        ticket_id bigint(20) unsigned NOT NULL,
        author_id bigint(20) unsigned NOT NULL,
        author_role varchar(20) NOT NULL DEFAULT 'user',
        message text NOT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY ticket_id (ticket_id)
    ) {$charset_collate};";
    dbDelta($sql_replies);
}

add_action('wp_ajax_submit_support_ticket', 'lawyer_submit_support_ticket');
function lawyer_submit_support_ticket() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'ابتدا وارد حساب کاربری شوید.'], 401);
    }
    
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'support_ticket_nonce')) {
        wp_send_json_error(['message' => 'درخواست نامعتبر است.'], 403);
    }
    
    $user_id = get_current_user_id();
    
    // Rate Limiting: Maximum of 3 tickets per 10 minutes
    $rate_key = 'ticket_rate_' . $user_id;
    $ticket_count = (int) get_transient($rate_key);
    if ($ticket_count >= 3) {
        wp_send_json_error([
            'message' => 'تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً چند دقیقه صبر کنید.'
        ], 429);
    }
    
    $subject = isset($_POST['subject']) ? sanitize_text_field(wp_unslash($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    
    if (empty($subject) || empty($message)) {
        wp_send_json_error(['message' => 'لطفاً موضوع و متن پیام را وارد کنید.'], 400);
    }
    
    global $wpdb;
    $table = $wpdb->prefix . 'lawyer_support_tickets';
    
    $inserted = $wpdb->insert(
        $table,
        [
            'user_id' => $user_id,
            'subject' => $subject,
            'message' => $message,
            'status'  => 'open',
        ],
        ['%d', '%s', '%s', '%s']
    );
    
    if ($inserted) {
        // Increment rate limit counter
        set_transient($rate_key, $ticket_count + 1, 10 * MINUTE_IN_SECONDS);
        wp_send_json_success(['message' => 'پیام شما با موفقیت برای پشتیبانی ارسال شد.']);
    } else {
        wp_send_json_error(['message' => 'خطا در ثبت پیام.'], 500);
    }
}

add_action('wp_ajax_submit_ticket_reply', 'lawyer_submit_ticket_reply');
function lawyer_submit_ticket_reply() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'ابتدا وارد حساب کاربری شوید.'], 401);
    }
    
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'ticket_reply_nonce')) {
        wp_send_json_error(['message' => 'درخواست نامعتبر است.'], 403);
    }
    
    $user_id   = get_current_user_id();
    $ticket_id = isset($_POST['ticket_id']) ? absint($_POST['ticket_id']) : 0;
    $message   = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    
    if (!$ticket_id || empty($message)) {
        wp_send_json_error(['message' => 'اطلاعات ناقص است.'], 400);
    }
    
    global $wpdb;
    $tickets_table = $wpdb->prefix . 'lawyer_support_tickets';
    $replies_table = $wpdb->prefix . 'lawyer_support_replies';
    
    $ticket = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$tickets_table} WHERE id = %d AND user_id = %d",
        $ticket_id, $user_id
    ));
    
    if (!$ticket) {
        wp_send_json_error(['message' => 'دسترسی غیرمجاز.'], 403);
    }
    
    $inserted = $wpdb->insert(
        $replies_table,
        [
            'ticket_id'   => $ticket_id,
            'author_id'   => $user_id,
            'author_role' => 'user',
            'message'     => $message,
        ],
        ['%d', '%d', '%s', '%s']
    );
    
    if ($inserted) {
        // Improvement: Reopen the ticket only if it hasn't been closed by an admin
        // (If an admin closed the ticket, the user should not be able to reopen it)
        if ($ticket->status === 'closed') {
            // Check whether the admin has already granted permission to open
            // Here, we use the 'answered' status as an indicator
            $wpdb->update(
                $tickets_table,
                ['status' => 'open'],
                ['id' => $ticket_id],
                ['%s'],
                ['%d']
            );
        }
        wp_send_json_success(['message' => 'پاسخ شما ثبت شد.']);
    } else {
        wp_send_json_error(['message' => 'خطا در ثبت پاسخ.'], 500);
    }
}
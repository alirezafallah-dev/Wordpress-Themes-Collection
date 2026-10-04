<?php
/**
 * =========================================================
 * Admin - Support Tickets Management
 * =========================================================
 */

// 1. Register the menu
add_action('admin_menu', 'lawyer_support_admin_menu');
function lawyer_support_admin_menu() {
    add_menu_page(
        'پشتیبانی',
        'پشتیبانی',
        'manage_options',
        'lawyer-support',
        'lawyer_support_router',
        'dashicons-editor-comments',
        31
    );
}

// 2. Router (list or details)
function lawyer_support_router() {
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
    if ($action === 'view' && isset($_GET['id'])) {
        lawyer_support_render_ticket_details();
    } else {
        lawyer_support_render_list_page();
    }
}

// 3. Processing actions (deletion, status change, admin response)
add_action('admin_init', 'lawyer_support_handle_actions');
function lawyer_support_handle_actions() {
    if (!is_admin() || !current_user_can('manage_options')) return;
    if (!isset($_GET['page']) || $_GET['page'] !== 'lawyer-support') return;

    global $wpdb;
    $tickets_table = $wpdb->prefix . 'lawyer_support_tickets';
    $replies_table = $wpdb->prefix . 'lawyer_support_replies';
    
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    $id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field($_GET['_wpnonce']) : '';
    $redirect_url = admin_url('admin.php?page=lawyer-support');

    // Delete ticket
    if ($action === 'delete' && $id && wp_verify_nonce($nonce, 'delete_ticket_' . $id)) {
        $wpdb->delete($tickets_table, ['id' => $id], ['%d']);
        $wpdb->delete($replies_table, ['ticket_id' => $id], ['%d']);
        wp_safe_redirect(add_query_arg('msg', 'deleted', $redirect_url));
        exit;
    }

    // Toggle status (close/open)
    if ($action === 'toggle_status' && $id && isset($_GET['status']) && wp_verify_nonce($nonce, 'toggle_status_' . $id)) {
        $new_status = sanitize_text_field($_GET['status']);
        $wpdb->update($tickets_table, ['status' => $new_status], ['id' => $id], ['%s'], ['%d']);
        wp_safe_redirect(add_query_arg('msg', 'updated', admin_url('admin.php?page=lawyer-support&action=view&id=' . $id)));
        exit;
    }

    // Save admin response (via POST)
    if ($action === 'submit_reply' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $post_ticket_id = isset($_POST['ticket_id']) ? absint($_POST['ticket_id']) : 0;
        if (!wp_verify_nonce($_POST['reply_nonce'], 'admin_reply_' . $post_ticket_id)) wp_die('درخواست نامعتبر است.');

        $message = isset($_POST['admin_message']) ? sanitize_textarea_field(wp_unslash($_POST['admin_message'])) : '';
        if ($post_ticket_id && !empty($message)) {
            $wpdb->insert(
                $replies_table,
                [
                    'ticket_id'   => $post_ticket_id,
                    'author_id'   => get_current_user_id(),
                    'author_role' => 'admin',
                    'message'     => $message,
                ],
                ['%d', '%d', '%s', '%s']
            );
            // Change status to 'Answered'
            $wpdb->update($tickets_table, ['status' => 'answered'], ['id' => $post_ticket_id], ['%s'], ['%d']);
        }
        wp_safe_redirect(add_query_arg('msg', 'replied', admin_url('admin.php?page=lawyer-support&action=view&id=' . $post_ticket_id)));
        exit;
    }
}

// 4. Render the ticket list
function lawyer_support_render_list_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'lawyer_support_tickets';
    
    $tickets = $wpdb->get_results("SELECT * FROM {$table} ORDER BY created_at DESC");
    ?>
    <style>
        .support-panel-wrap { margin: 20px 20px 20px 0; font-family: Tahoma, sans-serif; }
        .support-panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .support-panel-header h1 { font-size: 22px; font-weight: 600; color: #1d2327; margin: 0; }
        .support-table-wrap { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; overflow: hidden; }
        .support-table { width: 100%; border-collapse: collapse; }
        .support-table th { background: #f6f7f7; padding: 12px 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #c3c4c7; }
        .support-table td { padding: 14px 15px; border-bottom: 1px solid #f0f0f1; vertical-align: middle; }
        .support-table tr:last-child td { border-bottom: none; }
        .support-table tr:hover { background: #f9f9f9; }
        .support-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-open { background: #fcf0e3; color: #9a6700; }
        .badge-answered { background: #e6f6e6; color: #007117; }
        .badge-closed { background: #f0f0f1; color: #50575e; }
        .support-actions a { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; text-decoration: none; margin-left: 4px; border: 1px solid transparent; }
        .act-view { background: #f0f6fc; color: #0a4b78; border-color: #c3c4c7; }
        .act-delete { color: #8a0000; }
        .support-actions a:hover { background: #f6f7f7; border-color: #8c8f94; }
        .ticket-subject strong { display: block; font-size: 13px; color: #1d2327; margin-bottom: 2px; }
        .ticket-subject small { color: #646970; font-size: 12px; }
    </style>

    <div class="support-panel-wrap">
        <div class="support-panel-header">
            <h1>مدیریت تیکت‌های پشتیبانی</h1>
        </div>

        <?php if (isset($_GET['msg'])) : ?>
            <div class="notice notice-success is-dismissible"><p>عملیات با موفقیت انجام شد.</p></div>
        <?php endif; ?>

        <div class="support-table-wrap">
            <table class="support-table">
                <thead>
                    <tr>
                        <th>کاربر</th>
                        <th>موضوع</th>
                        <th>تاریخ</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)) : foreach ($tickets as $t) : 
                        $user = get_userdata($t->user_id);
                        $user_name = $user ? $user->display_name : 'کاربر حذف شده';
                        $base_url = admin_url('admin.php?page=lawyer-support');
                        $status_class = 'badge-' . $t->status;
                        $status_text = $t->status === 'open' ? 'در انتظار' : ($t->status === 'answered' ? 'پاسخ داده شده' : 'بسته شده');
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($user_name); ?></strong></td>
                            <td class="ticket-subject">
                                <strong><?php echo esc_html($t->subject); ?></strong>
                                <small><?php echo esc_html(wp_trim_words($t->message, 10, '...')); ?></small>
                            </td>
                            <td><?php echo esc_html($t->created_at); ?></td>
                            <td><span class="support-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                            <td class="support-actions">
                                <a href="<?php echo add_query_arg(['action' => 'view', 'id' => $t->id], $base_url); ?>" class="act-view">مشاهده و پاسخ</a>
                                <a href="<?php echo wp_nonce_url(add_query_arg(['action' => 'delete', 'id' => $t->id], $base_url), 'delete_ticket_' . $t->id); ?>" class="act-delete" onclick="return confirm('آیا مطمئن هستید؟');">حذف</a>
                            </td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5" style="text-align:center; padding:30px; color:#646970;">هنوز تیکتی ثبت نشده است.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

// 5. Render ticket details page (conversation)
function lawyer_support_render_ticket_details() {
    global $wpdb;
    $id = absint($_GET['id']);
    $tickets_table = $wpdb->prefix . 'lawyer_support_tickets';
    $replies_table = $wpdb->prefix . 'lawyer_support_replies';
    
    $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tickets_table} WHERE id = %d", $id));
    if (!$ticket) wp_die('تیکت یافت نشد.');

    $replies = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$replies_table} WHERE ticket_id = %d ORDER BY created_at ASC", $id));
    $user = get_userdata($ticket->user_id);
    $user_name = $user ? $user->display_name : 'کاربر حذف شده';
    
    $base_url = admin_url('admin.php?page=lawyer-support');
    $toggle_status = $ticket->status === 'closed' ? 'open' : 'closed';
    $toggle_text = $ticket->status === 'closed' ? 'باز کردن مجدد' : 'بستن تیکت';
    ?>
    <style>
        .ticket-detail-wrap { max-width: 900px; margin: 20px 20px 20px 0; font-family: Tahoma, sans-serif; }
        .ticket-detail-header { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .ticket-detail-header h2 { margin: 0 0 10px 0; font-size: 18px; }
        .ticket-meta { display: flex; gap: 20px; color: #646970; font-size: 13px; }
        .ticket-conversation { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .conversation-item { padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #f0f0f1; }
        .conversation-item.user-msg { background: #f0f6fc; border-color: #c3d9ed; }
        .conversation-item.admin-msg { background: #f6f7f7; border-color: #c3c4c7; }
        .conversation-header { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 12px; color: #646970; }
        .conversation-header strong { color: #1d2327; font-size: 13px; }
        .conversation-body { font-size: 14px; line-height: 1.8; color: #1d2327; }
        .admin-reply-box { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; padding: 20px; }
        .admin-reply-box h3 { margin-top: 0; font-size: 16px; }
        .admin-reply-box textarea { width: 100%; padding: 10px; border: 1px solid #8c8f94; border-radius: 4px; font-family: inherit; font-size: 14px; }
        .admin-reply-actions { margin-top: 15px; display: flex; gap: 10px; }
    </style>

    <div class="ticket-detail-wrap">
        <div class="ticket-detail-header">
            <h2><?php echo esc_html($ticket->subject); ?></h2>
            <div class="ticket-meta">
                <span>ارسال کننده: <strong><?php echo esc_html($user_name); ?></strong></span>
                <span>تاریخ: <?php echo esc_html($ticket->created_at); ?></span>
                <span>وضعیت: <strong><?php echo esc_html($ticket->status === 'open' ? 'در انتظار' : ($ticket->status === 'answered' ? 'پاسخ داده شده' : 'بسته شده')); ?></strong></span>
            </div>
        </div>

        <div class="ticket-conversation">
            <h3>تاریخچه مکالمه</h3>
            
            <!-- Initial user message -->
            <div class="conversation-item user-msg">
                <div class="conversation-header">
                    <strong><?php echo esc_html($user_name); ?> (کاربر)</strong>
                    <span><?php echo esc_html($ticket->created_at); ?></span>
                </div>
                <div class="conversation-body"><?php echo nl2br(esc_html($ticket->message)); ?></div>
            </div>

            <!-- Responses -->
            <?php if (!empty($replies)) : foreach ($replies as $r) : 
                $author = $r->author_role === 'admin' ? 'پشتیبانی سایت' : esc_html($user_name);
                $class = $r->author_role === 'admin' ? 'admin-msg' : 'user-msg';
            ?>
                <div class="conversation-item <?php echo $class; ?>">
                    <div class="conversation-header">
                        <strong><?php echo $author; ?></strong>
                        <span><?php echo esc_html($r->created_at); ?></span>
                    </div>
                    <div class="conversation-body"><?php echo nl2br(esc_html($r->message)); ?></div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <?php if ($ticket->status !== 'closed') : ?>
            <div class="admin-reply-box">
                <h3>ارسال پاسخ به کاربر</h3>
                <form method="post" action="<?php echo admin_url('admin.php?page=lawyer-support&action=submit_reply'); ?>">
                    <?php wp_nonce_field('admin_reply_' . $id, 'reply_nonce'); ?>
                    <input type="hidden" name="ticket_id" value="<?php echo $id; ?>">
                    <textarea name="admin_message" rows="5" required placeholder="پاسخ خود را بنویسید..."></textarea>
                    <div class="admin-reply-actions">
                        <button type="submit" class="button button-primary">ارسال پاسخ</button>
                        <a href="<?php echo wp_nonce_url(add_query_arg(['action' => 'toggle_status', 'id' => $id, 'status' => $toggle_status], $base_url), 'toggle_status_' . $id); ?>" class="button"><?php echo $toggle_text; ?></a>
                        <a href="<?php echo $base_url; ?>" class="button">بازگشت به لیست</a>
                    </div>
                </form>
            </div>
        <?php else : ?>
            <div class="admin-reply-box">
                <p style="color:#646970;">این تیکت بسته شده است. برای ادامه مکالمه آن را باز کنید.</p>
                <a href="<?php echo wp_nonce_url(add_query_arg(['action' => 'toggle_status', 'id' => $id, 'status' => 'open'], $base_url), 'toggle_status_' . $id); ?>" class="button">باز کردن مجدد تیکت</a>
                <a href="<?php echo $base_url; ?>" class="button" style="margin-right:10px;">بازگشت به لیست</a>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
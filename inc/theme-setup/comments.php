<?php
/**
 * Comments
*/
function mytheme_comment_reply() {
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'mytheme_comment_reply');

// Remove website field
function mytheme_remove_comment_url($fields)
{
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields', 'mytheme_remove_comment_url');

// Save contact number
add_action('comment_post', function ($comment_id) {
    if (!empty($_POST['phone'])) {
        add_comment_meta(
            $comment_id,
            'phone',
            sanitize_text_field($_POST['phone'])
        );
    }
});

add_action('add_meta_boxes', function ($post_type, $post) {
    if ($post_type !== 'comment') {
        return;
    }
    add_meta_box(
        'comment-phone',
        'شماره تماس',
        function ($comment) {
            $phone = get_comment_meta($comment->comment_ID, 'phone', true);
            echo '<input type="text"
                    id="comment-phone"
                    name="comment-phone"
                    value="' . esc_attr($phone) . '"
                    class="widefat">';
        },
        'comment',
        'normal',
        'high'
    );
}, 10, 2);

add_action('edit_comment', function ($comment_id) {
    if (isset($_POST['comment-phone'])) {
        update_comment_meta(
            $comment_id,
            'phone',
            sanitize_text_field($_POST['comment-phone'])
        );
    }
});

add_filter('preprocess_comment', function ($commentdata) {
    $author = trim($_POST['author'] ?? '');
    $phone  = preg_replace('/[^0-9]/', '', $_POST['phone'] ?? '');

    if ($author === '') {
        wp_die(pll__('لطفاً نام و نام خانوادگی را وارد کنید.'));
    }

    if (!preg_match('/^[\p{L}\s]+$/u', $author)) {
        wp_die(pll__('نام و نام خانوادگی معتبر نیست.'));
    }

    if ($phone === '') {
        wp_die(pll__('لطفاً شماره تماس را وارد کنید.'));
    }

    if (!preg_match('/^09\d{9}$/', $phone)) {
        wp_die(pll__('شماره تماس معتبر نیست.'));
    }

    if (empty($commentdata['comment_author_email'])) {
        $commentdata['comment_author_email'] = $phone . '@comment.local';
    }
    return $commentdata;
});

add_filter('manage_edit-comments_columns', function ($columns) {
    $columns['phone'] = 'شماره تماس';
    return $columns;
});

add_action('manage_comments_custom_column', function ($column, $comment_ID) {
    if ($column === 'phone') {
        echo esc_html(
            get_comment_meta($comment_ID, 'phone', true)
        );
    }
}, 10, 2);

// Display user role in the comment list
function get_comment_author_role($comment) {
    if ($comment->user_id) {
        $user = get_userdata($comment->user_id);
        if ($user && !empty($user->roles)) {

            return translate_user_role(wp_roles()->roles[$user->roles[0]]['name']);
        }
    }
    return 'مهمان';
}

function my_comment_callback($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class('comment-item'); ?> id="comment-<?php comment_ID(); ?>">
        <div class="comment-body">
            <div class="comment-header">

                <div class="flex gap-4">
                    <div class="comment-avatar">
                        <?php echo get_avatar($comment, 60); ?>
                    </div>
                    <div class="comment-meta">
                        <div class="comment-author-row">
                            <span class="comment-author-name">
                                <?php comment_author(); ?>
                            </span>
                            <span class="comment-author-role">
                                <?php echo esc_html(get_comment_author_role($comment)); ?>
                            </span>
                        </div>
                        <div class="comment-date">
                            <?php echo get_comment_date(); ?>
                        </div>
                    </div>
                </div>
                <div class="comment-reply">
                    <?php
                    comment_reply_link(array_merge($args, [
                        'depth' => $depth,
                        'max_depth' => $args['max_depth'],
                        'reply_text' => pll__('پاسخ'),
                    ]));
                    ?>
                </div>
            </div>
            <?php if ($comment->comment_parent) : ?>
                <?php
                $parent_comment = get_comment($comment->comment_parent);
                ?>
                <div class="comment-parent">
                    <svg width="20px" height="20px" viewBox="0 0 32 32" id="i-reply" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" transform="matrix(1, 0, 0, -1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M10 6 L3 14 10 22 M3 14 L18 14 C26 14 30 18 30 26"></path> </g></svg>    
                    <?php pll_e('در پاسخ به'); ?>
                    <strong>
                        <?php echo esc_html($parent_comment->comment_author); ?>
                    </strong>
                </div>
            <?php endif; ?>
            <div class="comment-message">
                <?php comment_text(); ?>
            </div>
        </div>
    </li>
    <?php
}

function theme_handle_contact_form() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    if (!isset($_POST['contact_form']) || $_POST['contact_form'] !== '1') return;
    
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce(
        sanitize_text_field(wp_unslash($_POST['contact_nonce'])),
        'contact_form_submit'
    )) {
        $GLOBALS['contact_form_error'] = __('درخواست نامعتبر است.');
        return;
    }
    
    // Honeypot check
    if (!empty($_POST['website'])) {
        wp_safe_redirect(add_query_arg('contact_status', 'success', wp_get_referer() ?: home_url('/')));
        exit;
    }
    
    $name    = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $phone   = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    
    // Fix: Use sanitize_text_field instead of sanitize_key for Persian.
    $subject = isset($_POST['subject']) ? sanitize_text_field(wp_unslash($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    
    $GLOBALS['contact_form_old'] = [
        'name'    => $name,
        'phone'   => $phone,
        'email'   => $email,
        'subject' => $subject,
        'message' => $message,
    ];
    
    if (!$name || !$phone || !$subject || !$message) {
        $GLOBALS['contact_form_error'] = __('لطفاً تمام فیلدهای الزامی را تکمیل کنید.');
        return;
    }
    
    if ($email && !is_email($email)) {
        $GLOBALS['contact_form_error'] = __('لطفاً یک ایمیل معتبر وارد کنید.');
        return;
    }
    
    // Fix: Custom comparison against the allowed list (instead of using sanitize_key)
    $allowed_subjects = [
        'consultation' => 'مشاوره حقوقی',
        'case'         => 'پرونده حقوقی',
        'cooperation'  => 'همکاری',
        'other'        => 'سایر موارد',
    ];
    
    if (!array_key_exists($subject, $allowed_subjects)) {
        $GLOBALS['contact_form_error'] = __('موضوع پیام نامعتبر است.');
        return;
    }
    
    $office_email   = get_theme_mod('office_email', get_option('admin_email'));
    $subject_label  = $allowed_subjects[$subject];
    $mail_subject   = sprintf('[%s] %s', get_bloginfo('name'), $subject_label);
    
    $mail_body = "درخواست جدید از فرم تماس با ما\n"
        . "نام و نام خانوادگی: {$name}\n"
        . "شماره تماس: {$phone}\n"
        . "ایمیل: " . ($email ?: 'وارد نشده است') . "\n"
        . "موضوع: {$subject_label}\n"
        . "پیام:\n{$message}";
    
    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    if ($email) {
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    }
    
    $sent = wp_mail($office_email, $mail_subject, $mail_body, $headers);
    
    if (!$sent) {
        $GLOBALS['contact_form_error'] = __('در ارسال پیام مشکلی پیش آمد.');
        return;
    }
    
    $redirect_url = wp_get_referer() ?: home_url('/');
    $redirect_url = remove_query_arg('contact_status', $redirect_url);
    $redirect_url = add_query_arg('contact_status', 'success', $redirect_url);
    wp_safe_redirect($redirect_url);
    exit;
}
add_action('template_redirect', 'theme_handle_contact_form');
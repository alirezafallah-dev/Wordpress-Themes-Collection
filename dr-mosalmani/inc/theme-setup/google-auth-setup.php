<?php
define('LAWYER_GOOGLE_CLIENT_ID', '692777409279-p8hd9q7c0h9rou6l9pq0j11bqm3icq1d.apps.googleusercontent.com');

add_action('wp_ajax_nopriv_auth_google_login', 'auth_google_login');
add_action('wp_ajax_auth_google_login', 'auth_google_login');

function auth_google_login() {
    error_log('=== GOOGLE LOGIN START ===');
    
    if (!isset($_POST['nonce']) || !wp_verify_nonce(
        sanitize_text_field(wp_unslash($_POST['nonce'])),
        'auth_google_login'
    )) {
        error_log('GOOGLE LOGIN: Invalid nonce');
        wp_send_json_error(['message' => 'درخواست نامعتبر است.'], 403);
    }
    
    $credential = isset($_POST['credential']) ? trim(wp_unslash($_POST['credential'])) : '';
    if (empty($credential)) {
        wp_send_json_error(['message' => 'اطلاعات ورود از Google دریافت نشد.'], 400);
    }
    
    $response = wp_remote_get(
        'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential),
        ['timeout' => 15]
    );
    
    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'ارتباط با Google برقرار نشد.'], 500);
    }
    
    $status  = wp_remote_retrieve_response_code($response);
    $body    = wp_remote_retrieve_body($response);
    $payload = json_decode($body, true);
    
    if ($status !== 200 || !is_array($payload)) {
        wp_send_json_error(['message' => 'توکن Google معتبر نیست.'], 401);
    }
    
    if (empty($payload['sub']) || empty($payload['email']) || empty($payload['aud']) || empty($payload['iss'])) {
        wp_send_json_error(['message' => 'اطلاعات حساب Google کامل نیست.'], 400);
    }
    
    if ($payload['aud'] !== LAWYER_GOOGLE_CLIENT_ID) {
        wp_send_json_error(['message' => 'حساب Google معتبر نیست.'], 401);
    }
    
    if ($payload['iss'] !== 'https://accounts.google.com') {
        wp_send_json_error(['message' => 'توکن Google معتبر نیست.'], 401);
    }
    
    if (isset($payload['email_verified']) && $payload['email_verified'] !== 'true') {
        wp_send_json_error(['message' => 'ایمیل Google تأیید نشده است.'], 401);
    }
    
    $google_id = sanitize_text_field($payload['sub']);
    $email     = sanitize_email($payload['email']);
    $name      = sanitize_text_field($payload['name'] ?? '');
    $picture   = esc_url_raw($payload['picture'] ?? '');
    
    if (empty($google_id) || empty($email)) {
        wp_send_json_error(['message' => 'اطلاعات حساب Google کامل نیست.'], 400);
    }
    
    $user = get_user_by('email', $email);
    
    if ($user) {
        update_user_meta($user->ID, 'google_id', $google_id);
        if (!empty($picture)) {
            update_user_meta($user->ID, 'google_picture', $picture);
        }
        
        // wp_set_auth_cookie does not return a value.
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true, is_ssl());
        
        wp_send_json_success([
            'message' => 'ورود با Google با موفقیت انجام شد.',
            'user'    => [
                'id'      => $user->ID,
                'name'    => $user->display_name,
                'email'   => $user->user_email,
                'picture' => $picture,
            ],
        ]);
    }
    
    // Create new user
    $username_base = sanitize_user(current(explode('@', $email)), true);
    if (empty($username_base)) {
        $username_base = 'google_user';
    }
    
    $username = $username_base;
    $counter  = 1;
    while (username_exists($username)) {
        $username = $username_base . $counter;
        $counter++;
    }
    
    $password = wp_generate_password(32, true, true);
    $user_id  = wp_create_user($username, $password, $email);
    
    if (is_wp_error($user_id)) {
        error_log('GOOGLE LOGIN: wp_create_user ERROR: ' . $user_id->get_error_message());
        wp_send_json_error(['message' => 'ساخت حساب کاربری با خطا مواجه شد.'], 500);
    }
    
    wp_update_user([
        'ID'           => $user_id,
        'display_name' => $name,
        'first_name'   => $name,
        'role'         => 'subscriber',
    ]);
    
    update_user_meta($user_id, 'google_id', $google_id);
    if (!empty($picture)) {
        update_user_meta($user_id, 'google_picture', $picture);
    }
    
    $user = get_user_by('id', $user_id);
    if (!$user) {
        wp_send_json_error(['message' => 'کاربر ساخته شد اما دریافت اطلاعات آن ممکن نبود.'], 500);
    }
    
    // Remove name syncing with past reservations (new user has no reservations)    
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true, is_ssl());
    
    wp_send_json_success([
        'message' => 'حساب کاربری با موفقیت ایجاد و وارد شدید.',
        'user'    => [
            'id'      => $user->ID,
            'name'    => $user->display_name,
            'email'   => $user->user_email,
            'picture' => $picture,
        ],
    ]);
}